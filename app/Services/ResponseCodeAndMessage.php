<?php

namespace App\Services;

class ResponseCodeAndMessage
{
    const SUCCESS = 200;
    const ERROR = 400;
    const UNAUTHORIZED = 401;
    const FORBIDDEN = 403;
    const NOT_FOUND = 404;
    const INTERNAL_SERVER_ERROR = 500;
    const BAD_REQUEST = 422;

    const MESSAGES = [
        self::SUCCESS => 'Success',
        self::ERROR => 'Error',
        self::UNAUTHORIZED => 'Unauthorized',
        self::FORBIDDEN => 'Forbidden',
        self::NOT_FOUND => 'Not Found',
        self::INTERNAL_SERVER_ERROR => 'Internal Server Error',
        self::BAD_REQUEST => 'Bad Request',
    ];
}
