<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
    <head>
        <meta charset="utf-8">
        <meta name="viewport" content="width=device-width, initial-scale=1">
        <meta name="csrf-token" content="{{ csrf_token() }}">

        <title>{{ config('app.name', 'FocusFlow') }}</title>

        <link rel="preconnect" href="https://fonts.bunny.net">
        <link href="https://fonts.bunny.net/css?family=manrope:400,500,600,700,800&display=swap" rel="stylesheet" />

        @vite(['resources/css/app.css', 'resources/js/app.js'])
    </head>
    <body class="antialiased">
        <div class="min-h-screen bg-transparent">
            @include('layouts.navigation')

            @isset($header)
                <header class="px-4 pt-6 sm:px-6 lg:px-8">
                    <div class="focusflow-panel mx-auto max-w-7xl px-6 py-5">
                        {{ $header }}
                    </div>
                </header>
            @endisset

            <main class="pb-8 pt-6">
                {{ $slot }}
            </main>
        </div>
    </body>
</html>
