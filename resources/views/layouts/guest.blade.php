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
    <body class="text-slate-900 antialiased">
        <div class="flex min-h-screen flex-col items-center justify-center px-4 py-8">
            <div>
                <a href="/">
                    <x-application-logo class="h-16 w-16 text-slate-900" />
                </a>
            </div>

            <div class="mt-5 text-center">
                <p class="text-sm font-bold uppercase tracking-[0.32em] text-sky-600">FocusFlow</p>
                <h1 class="mt-2 text-3xl font-extrabold tracking-tight text-slate-900">A calmer way to run your tasks</h1>
            </div>

            <div class="focusflow-panel mt-6 w-full max-w-md overflow-hidden px-6 py-6 sm:rounded-[28px]">
                {{ $slot }}
            </div>
        </div>
    </body>
</html>
