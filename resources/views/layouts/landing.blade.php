<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>{{ $title ?? config('app.name') }} - Analisis Kuantitatif Trading Saham</title>
    <link rel="icon" href="{{ asset('favicon_.ico') }}" sizes="any">
    <link rel="icon" href="{{ asset('favicon_.svg') }}" type="image/svg+xml">
    <link rel="apple-touch-icon" href="{{ asset('apple-touch-icon.png') }}">
    <link rel="preconnect" href="https://fonts.bunny.net">
    <link href="https://fonts.bunny.net/css?family=instrument-sans:400,500,600,700&display=swap" rel="stylesheet" />
    @vite(['resources/css/app.css', 'resources/js/app.js'])
    @stack('styles')
</head>
<body class="bg-zinc-50 dark:bg-zinc-950 text-zinc-900 dark:text-zinc-100 antialiased font-sans">
    @include('partials.landing-nav')

    <main>
        {{ $slot }}
    </main>

    @include('partials.landing-footer')
</body>
</html>
