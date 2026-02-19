<?php

namespace App\Services;

use App\Models\ContactMail;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Facades\Validator;

class ContactService
{
    const NAME_LENGTH = 100;
    const EMAIL_LENGTH = 150;
    const PHONE_LENGTH = 20;
    const SUBJECT_LENGTH = 100;
    const MESSAGE_LENGTH = 2000;
    public function store(Request $request): array
    {
        $statusCode = ResponseCodeAndMessage::SUCCESS;
        $data = [];

        $validator = Validator::make($request->all(), $this->getRules(), $this->getMessages());

        if ($validator->fails()) {
            $statusCode = ResponseCodeAndMessage::BAD_REQUEST;
            $data = $validator->errors();
        }

        if($statusCode === ResponseCodeAndMessage::SUCCESS){
            $prepareContactData = self::prepareContactData($request);
            if(ContactMail::saveContactMail($prepareContactData)){
                $data = $request->except(['_token']);
            }
        }

        return [$statusCode, ResponseCodeAndMessage::MESSAGES[$statusCode], $data];
    }

    protected function getRules(): array
    {
        return [
            'name'    => 'required|string|max:' . self::NAME_LENGTH ,
            'email'   => 'required|email|max:' . self::EMAIL_LENGTH,
            'phone'   => 'required|string|max:' . self::PHONE_LENGTH,
            'subject' => 'required|string|max:' . self::SUBJECT_LENGTH,
            'mail_message' => 'required|string|max:' . self::MESSAGE_LENGTH,
        ];
    }

    protected function getMessages(): array
    {
        return [
            'name.required' => 'Please enter your name.',
            'name.max'      => 'Name may not be greater than :max characters.',

            'email.required' => 'Please enter your email address.',
            'email.email'    => 'Please provide a valid email address.',
            'email.max'      => 'Email may not be greater than :max characters.',

            'phone.required' => 'Please enter your phone number.',
            'phone.max'      => 'Phone number may not be greater than :max characters.',

            'subject.required' => 'Please enter a subject.',
            'subject.max'      => 'Subject may not be greater than :max characters.',

            'mail_message.required' => 'Please enter your message.',
            'mail_message.max'      => 'Message may not be greater than :max characters.',
        ];
    }

    private static function prepareContactData($request): array
    {
        return [
            'name' => $request->name ?? '',
            'email' => $request->email ?? '',
            'phone' => $request->phone ?? '',
            'subject' => $request->subject ?? '',
            'mail_message' => $request->mail_message ?? '',
            'request_source' => GlobalFunction::getRequestSource($request),
        ];
    }
}
