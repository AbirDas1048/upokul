<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="description" content="Upokul Travels & Holidays offers premium video-led travel planning, visa support, hotel booking, and memorable holiday experiences.">
    <title>{{ config('app.name') }}</title>
    <link rel="icon" type="image/x-icon" href="{{ asset('images/favicon.ico') }}">

    <!-- Preconnect to speed up external resources used by shared sections -->
    <link rel="preconnect" href="https://images.unsplash.com">
    <link rel="preconnect" href="https://cdnjs.cloudflare.com">
    <link rel="preconnect" href="https://cdn.jsdelivr.net">

    @vite([
        'resources/css/demo3.css',
        'resources/js/global.js'
    ])
</head>

<body>

<!-- Navbar -->
@include('partials.sections.nav')

<!-- Hero Section -->
@include('partials.sections.demo3_hero')

<!-- About Section -->
@include('partials.sections.about')

<!-- Services Section -->
@include('partials.sections.services')

<!-- Counter Section -->
@include('partials.sections.trusted')

<!-- Gallery Section -->
@include('partials.sections.gallery')

<!-- Review Section -->
@include('partials.sections.reviews')

<!-- Contact Section -->
@include('partials.sections.contact')

<!-- Footer Section -->
@include('partials.sections.footer')

<!-- Floating WhatsApp Button -->
@include('partials.sections.whatsapp')

</body>
</html>
