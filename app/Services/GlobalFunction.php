<?php

namespace App\Services;

use Illuminate\Http\Request;

class GlobalFunction
{
    public static function getRequestSource(Request $request)
    {

        return [
            'ip' => $request->ip() ?? '',
            'method' => $request->method() ?? '',
            'url' => $request->fullUrl() ?? '',
            'path' => $request->path() ?? '',
            'referrer' => $request->headers->get('referer') ?? '',
            'user_agent' => $request->userAgent() ?? '',
            'accept_lang' => $request->headers->get('accept-language') ?? '',
            'host' => $request->getHost() ?? '',
            'is_mobile' => $request->header('User-Agent') && str_contains(strtolower($request->userAgent()), 'mobile'),
            'is_ajax' => $request->ajax(),
            'is_secure' => $request->secure(),
            'timestamp' => now()->toDateTimeString(),
        ];
    }
}
