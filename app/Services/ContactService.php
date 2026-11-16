<?php

namespace App\Services;

use App\Jobs\SendContactMailJob;
use App\Models\ContactMail;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Validator;
use Illuminate\Validation\Validator as ValidatorInstance;

class ContactService
{
    const NAME_LENGTH = 100;

    const EMAIL_LENGTH = 150;

    const PHONE_LENGTH = 20;

    const SUBJECT_LENGTH = 100;

    const MESSAGE_LENGTH = 2000;

    const MAIL_SEND_STATUS_PENDING = 0;

    const MAIL_SEND_STATUS_SUCCESS = 1;

    const MAIL_SEND_STATUS_FAILED = 2;

    const MAIL_SEND_STATUS_PROCESSING = 3;

    /** Shown to a real person who tripped one of the bot checks (bots get no hint beyond this). */
    private const SPAM_MESSAGES = [
        ContactSpamGuard::VERDICT_TOKEN => 'Your session has expired. Please refresh the page and try again.',
        ContactSpamGuard::VERDICT_TOO_FAST => 'Please take a moment to complete the form, then try again.',
        ContactSpamGuard::VERDICT_CAPTCHA => 'Please complete the verification challenge and try again.',
    ];

    public function store(Request $request): array
    {
        $guard = new ContactSpamGuard;

        // 1. Honeypot. A person never sees this field; a bot fills it. Answer "success" and
        //    store/send nothing, so the bot gets no signal about what to change.
        if ($guard->isHoneypotTripped($request)) {
            return $this->respond(ResponseCodeAndMessage::SUCCESS, []);
        }

        // 2. Clean + validate, then 3. signed token / fill-time / optional Turnstile challenge.
        $validator = $this->validator($request);
        $rejection = $validator->fails() ? $validator->errors() : $this->spamRejection($guard, $request);

        if ($rejection !== null) {
            return $this->respond(ResponseCodeAndMessage::BAD_REQUEST, $rejection);
        }

        // Only validated fields are used from here on; nothing else from the request is trusted or echoed.
        return $this->saveOnce($guard, $validator->validated(), $request);
    }

    /**
     * 4. Double-submit / replay: the same message from the same address is stored once. The
     *    duplicate reservation is released if storing fails, so an honest retry still works.
     */
    private function saveOnce(ContactSpamGuard $guard, array $data, Request $request): array
    {
        if ($guard->isDuplicate($data['email'], $data['mail_message'])) {
            return $this->respond(ResponseCodeAndMessage::SUCCESS, $data); // already received
        }

        try {
            $contactMail = $this->persistAndQueue($data, $request);
        } catch (\Throwable $e) {
            $guard->forgetDuplicate($data['email'], $data['mail_message']);

            throw $e;
        }

        if (! $contactMail->exists) {
            $guard->forgetDuplicate($data['email'], $data['mail_message']);

            return $this->respond(ResponseCodeAndMessage::INTERNAL_SERVER_ERROR, []);
        }

        return $this->respond(ResponseCodeAndMessage::SUCCESS, $data);
    }

    /**
     * Stores the contact and queues its e-mail job so that nothing is ever left saved-but-unqueued
     * from the visitor's point of view:
     *
     *  - `database` queue: the job row lives in the same database, so row and job commit in ONE
     *    transaction; if queuing fails, the contact is rolled back with it.
     *  - any other queue (redis, sqs, sync...): the job must NOT be published before the contact is
     *    committed (a worker could pick it up and fail to find the model), so it is dispatched after
     *    the commit; if that dispatch fails the contact is removed again (compensation). A crash
     *    between commit and dispatch leaves a Pending row that the scheduled `contact:resend-failed`
     *    command queues.
     */
    private function persistAndQueue(array $data, Request $request): ContactMail
    {
        $atomic = config('queue.connections.'.config('queue.default').'.driver') === 'database';

        $contactMail = DB::transaction(function () use ($data, $request, $atomic) {
            // `database` queue: the job is written in this very transaction, so the marker is too.
            $contactMail = ContactMail::saveContactMail(self::prepareContactData($data, $request, $atomic));

            if ($atomic && $contactMail->exists) {
                SendContactMailJob::dispatch($contactMail);
            }

            return $contactMail;
        });

        if (! $atomic && $contactMail->exists) {
            $this->dispatchAfterCommit($contactMail);
        }

        return $contactMail;
    }

    private function dispatchAfterCommit(ContactMail $contactMail): void
    {
        try {
            SendContactMailJob::dispatch($contactMail);
        } catch (\Throwable $e) {
            $contactMail->delete(); // not queued, so do not leave an orphan; the visitor can retry

            throw $e;
        }

        $this->recordQueued($contactMail);
    }

    /**
     * Durable "a job is queued" marker, written only once the job really is queued (query builder: no
     * model events). The contact is already committed AND queued at this point, so it must stay accepted
     * (and keep its duplicate reservation) whatever happens here: a failed marker write is only reported.
     * Without the marker the recovery command may queue the row once more, which the job's atomic claim
     * turns into a no-op, so the worst case is one redundant job, never a lost or doubled enquiry.
     */
    private function recordQueued(ContactMail $contactMail): void
    {
        try {
            ContactMail::query()->whereKey($contactMail->getKey())->update(['queued_at' => now()]);
        } catch (\Throwable $e) {
            Log::warning('Could not record queued_at for contact '.$contactMail->getKey().': '.$e->getMessage());
        }
    }

    /** @return array<string, array<int, string>>|null */
    private function spamRejection(ContactSpamGuard $guard, Request $request): ?array
    {
        $verdict = $guard->verdict($request);

        return $verdict === null ? null : ['form' => [self::SPAM_MESSAGES[$verdict]]];
    }

    private function validator(Request $request): ValidatorInstance
    {
        $this->sanitize($request);

        return Validator::make($request->all(), $this->getRules(), $this->getMessages());
    }

    private function respond(int $status, mixed $data): array
    {
        return [$status, ResponseCodeAndMessage::MESSAGES[$status], $data];
    }

    /**
     * Control characters (CR/LF included, which header injection into Reply-To / Subject relies
     * on) replaced by a space, then trimmed. Used for every single-line field AND by the rate
     * limiter, so throttling and storage always see the same canonical value.
     */
    public static function cleanSingleLine(string $value): string
    {
        return trim((string) preg_replace('/[\x00-\x1F\x7F\x{2028}\x{2029}]+/u', ' ', $value));
    }

    /** Canonical e-mail used to count submissions per address (never trust the raw input). */
    public static function canonicalEmail(mixed $value): string
    {
        return is_string($value) ? mb_strtolower(self::cleanSingleLine($value)) : '';
    }

    /**
     * The message keeps its line breaks and tabs. Output is always escaped, so this is defence
     * in depth, not the XSS protection itself.
     */
    private function sanitize(Request $request): void
    {
        $clean = [];

        foreach (['name', 'email', 'phone', 'subject'] as $field) {
            $value = $request->input($field);

            if (is_string($value)) {
                $clean[$field] = self::cleanSingleLine($value);
            }
        }

        $message = $request->input('mail_message');

        if (is_string($message)) {
            $clean['mail_message'] = trim((string) preg_replace('/[\x00-\x08\x0B\x0C\x0E-\x1F\x7F]/', '', $message));
        }

        $request->merge($clean);
    }

    protected function getRules(): array
    {
        $maxLinks = (int) config('custom.contact.max_links', 1);

        return [
            'name' => ['required', 'string', 'max:'.self::NAME_LENGTH, $this->maxLinks(0)],
            'email' => ['required', 'email:rfc', 'max:'.self::EMAIL_LENGTH],
            'phone' => ['required', 'string', 'max:'.self::PHONE_LENGTH, 'regex:/^[0-9+\-\s().]{5,'.self::PHONE_LENGTH.'}$/'],
            'subject' => ['required', 'string', 'max:'.self::SUBJECT_LENGTH, $this->maxLinks(0)],
            'mail_message' => ['required', 'string', 'max:'.self::MESSAGE_LENGTH, $this->maxLinks($maxLinks)],
        ];
    }

    /** Spam is mostly links, and the thank-you e-mail repeats what was typed. */
    private function maxLinks(int $max): \Closure
    {
        return function (string $attribute, mixed $value, \Closure $fail) use ($max): void {
            if (is_string($value) && preg_match_all('~(?:https?://|ftp://|www\.)~i', $value) > $max) {
                $fail($max === 0
                    ? 'Links are not allowed in this field.'
                    : 'Please remove the extra links from your message.');
            }
        };
    }

    protected function getMessages(): array
    {
        return [
            'name.required' => 'Please enter your name.',
            'name.max' => 'Name may not be greater than :max characters.',

            'email.required' => 'Please enter your email address.',
            'email.email' => 'Please provide a valid email address.',
            'email.max' => 'Email may not be greater than :max characters.',

            'phone.required' => 'Please enter your phone number.',
            'phone.max' => 'Phone number may not be greater than :max characters.',
            'phone.regex' => 'Please enter a valid phone number (digits, +, spaces, dashes and brackets only).',

            'subject.required' => 'Please enter a subject.',
            'subject.max' => 'Subject may not be greater than :max characters.',

            'mail_message.required' => 'Please enter your message.',
            'mail_message.max' => 'Message may not be greater than :max characters.',
        ];
    }

    private static function prepareContactData(array $data, Request $request, bool $queuedNow = false): array
    {
        return [
            'name' => $data['name'],
            'email' => $data['email'],
            'phone' => $data['phone'],
            'subject' => $data['subject'],
            'mail_message' => $data['mail_message'],
            'request_source' => GlobalFunction::getRequestSource($request),
            'status' => self::MAIL_SEND_STATUS_PENDING,
            'queued_at' => $queuedNow ? now() : null,
        ];
    }
}
