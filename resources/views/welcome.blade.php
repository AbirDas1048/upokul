<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>{{ config('app.name') }}</title>
    <!-- Google Font -->
    <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@400;500;600&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="{{ asset('css/welcome.css') }}">
</head>
<body>

<div class="card">
    <!-- App Logo -->
    <img src="{{ asset('images/Logo.png') }}" alt="{{ config('app.name') }} logo" class="logo">

    <!-- App Name -->
    <div class="app-name">{{ config('app.name') }}</div>

    <!-- Buttons -->
    <div class="buttons">
        <a href="{{ route('demo1') }}" target="_blank" class="btn">Demo 1</a>
        <a href="{{ route('demo2') }}" target="_blank" class="btn">Demo 2</a>
        <a href="{{ route('demo3') }}" target="_blank" class="btn">Demo 3</a>
    </div>
</div>

</body>
</html>
