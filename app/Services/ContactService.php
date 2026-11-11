<?php

namespace App\Services;

use App\Jobs\SendContactMailJob;
use App\Models\ContactMail;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;

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

        // 2. Clean the input (control characters, CR/LF -> no header injection) and validate.
        $this->sanitize($request);

        $validator = Validator::make($request->all(), $this->getRules(), $this->getMessages());

        if ($validator->fails()) {
            return $this->respond(ResponseCodeAndMessage::BAD_REQUEST, $validator->errors());
        }

        // 3. Signed form token / fill-time / optional Turnstile challenge.
        $verdict = $guard->verdict($request);

        if ($verdict !== null) {
            return $this->respond(ResponseCodeAndMessage::BAD_REQUEST, ['form' => [self::SPAM_MESSAGES[$verdict]]]);
        }

        // Only validated fields are used from here on; nothing else from the request is trusted or echoed.
        $data = $validator->validated();

        // 4. Double-submit / replay: the same message from the same address is accepted once.
        //    Answer "success" (it was already received) without saving or e-mailing again.
        if ($guard->isDuplicate($data['email'], $data['mail_message'])) {
            return $this->respond(ResponseCodeAndMessage::SUCCESS, $data);
        }

        $contactMail = ContactMail::saveContactMail(self::prepareContactData($data, $request));

        if (! $contactMail->exists) {
            return $this->respond(ResponseCodeAndMessage::INTERNAL_SERVER_ERROR, []);
        }

        SendContactMailJob::dispatch($contactMail);

        return $this->respond(ResponseCodeAndMessage::SUCCESS, $data);
    }

    private function respond(int $status, mixed $data): array
    {
        return [$status, ResponseCodeAndMessage::MESSAGES[$status], $data];
    }

    /**
     * Single-line fields lose every control character (including CR/LF, which are what header
     * injection into Reply-To / Subject relies on); the message keeps its line breaks and tabs.
     * Output is always escaped, so this is defence in depth, not the XSS protection itself.
     */
    private function sanitize(Request $request): void
    {
        $clean = [];

        foreach (['name', 'email', 'phone', 'subject'] as $field) {
            $value = $request->input($field);

            if (is_string($value)) {
                $clean[$field] = trim((string) preg_replace('/[\x00-\x1F\x7F\x{2028}\x{2029}]+/u', ' ', $value));
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

    private static function prepareContactData(array $data, Request $request): array
    {
        return [
            'name' => $data['name'],
            'email' => $data['email'],
            'phone' => $data['phone'],
            'subject' => $data['subject'],
            'mail_message' => $data['mail_message'],
            'request_source' => GlobalFunction::getRequestSource($request),
            'status' => self::MAIL_SEND_STATUS_PENDING,
        ];
    }
}
