@php
    /*
     * Styling comes from plain static files (always available). The Vite bundle only adds behaviour
     * (password toggle, double-submit guard); if it cannot be loaded — missing build, or a stale
     * public/hot pointing at a dev server that is not running — the page still looks right.
     */
    try {
        $viteTags = app(\Illuminate\Foundation\Vite::class)(['resources/js/admin.js']);
    } catch (\Throwable) {
        $viteTags = '';
    }
@endphp
<!DOCTYPE html>
<html lang="en" data-bs-theme="light">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="robots" content="noindex, nofollow">
    <title>@yield('title') · @hasSection('product'){{ config('product.name') }}@else{{ config('app.name') }}@endif</title>
    <link rel="icon" href="@hasSection('product'){{ asset(config('product.favicon')) }}@else{{ $site->image('favicon') }}@endif">
    <link rel="stylesheet" href="{{ asset('css/bootstrap.min.css') }}">
    <link rel="stylesheet" href="{{ asset('vendor/fontawesome/all.min.css') }}">
    <link rel="stylesheet" href="{{ asset('css/admin-auth.css') }}">
    {!! $viteTags !!}
</head>
<body class="admin ad-auth @yield('body-class')">
    @yield('content')
</body>
</html>
