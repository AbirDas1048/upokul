<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="theme-color" content="#072f31">
    <title>{{ config('app.name') }} — Choose a design</title>
    <link rel="icon" type="image/x-icon" href="{{ asset('images/favicon.ico') }}">
    <link rel="stylesheet" href="{{ asset('css/welcome.css') }}">
</head>
<body>
<main class="card">
    <img src="{{ asset('images/Logo.png') }}" alt="{{ config('app.name') }} logo" class="logo" width="120" height="120">
    <h1 class="app-name">{{ config('app.name') }}</h1>
    <p class="tagline">Spreading smiles across the globe</p>

    <div class="buttons">
        <a href="{{ route('demo1') }}" class="btn"><b>Demo 1 · Aurora</b><span>Bright &amp; airy, video hero</span></a>
        <a href="{{ route('demo2') }}" class="btn"><b>Demo 2 · Noir</b><span>Dark, editorial &amp; elegant</span></a>
        <a href="{{ route('demo3') }}" class="btn"><b>Demo 3 · Glass</b><span>Light luxe with frosted glass</span></a>
    </div>
</main>
</body>
</html>
