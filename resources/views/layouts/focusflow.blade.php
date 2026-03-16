<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
    <head>
        <meta charset="utf-8">
        <meta name="viewport" content="width=device-width, initial-scale=1">
        <meta name="csrf-token" content="{{ csrf_token() }}">

        <title>{{ $pageTitle ?? config('app.name', 'FocusFlow') }} | {{ config('app.name', 'FocusFlow') }}</title>

        <link rel="preconnect" href="https://fonts.bunny.net">
        <link href="https://fonts.bunny.net/css?family=manrope:400,500,600,700,800&display=swap" rel="stylesheet" />

        @vite(['resources/css/app.css', 'resources/js/app.js'])
    </head>
    <body class="min-h-screen text-slate-900 antialiased">
        <div x-data="{ sidebarOpen: false }" class="relative min-h-screen lg:grid lg:grid-cols-[320px_minmax(0,1fr)]">
            <div
                x-cloak
                x-show="sidebarOpen"
                class="fixed inset-0 z-30 bg-slate-950/35 backdrop-blur-sm lg:hidden"
                @click="sidebarOpen = false"
            ></div>

            <aside
                :class="sidebarOpen ? 'translate-x-0' : '-translate-x-full lg:translate-x-0'"
                class="fixed inset-y-0 left-0 z-40 w-[320px] transform transition duration-200 ease-out lg:static lg:w-auto"
            >
                <div class="h-full p-3 lg:p-4">
                    <div class="focusflow-sidebar-shell flex h-full flex-col overflow-hidden rounded-[30px]">
                        @include('focusflow.partials.sidebar')
                    </div>
                </div>
            </aside>

            <div class="flex min-h-screen flex-col">
                <div class="sticky top-0 z-20 border-b border-white/70 bg-white/70 backdrop-blur lg:hidden">
                    <div class="flex items-center justify-between px-4 py-4">
                        <button
                            type="button"
                            @click="sidebarOpen = true"
                            class="focusflow-icon-button h-10 w-10 rounded-xl"
                        >
                            <svg class="h-5 w-5" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8">
                                <path stroke-linecap="round" stroke-linejoin="round" d="M4 7h16M4 12h16M4 17h16" />
                            </svg>
                        </button>

                        <div class="text-center">
                            <p class="text-sm font-bold text-slate-900">{{ config('app.name', 'FocusFlow') }}</p>
                            <p class="text-xs text-slate-500">{{ auth()->user()->name }}</p>
                        </div>

                        <a href="{{ route('profile.edit') }}" class="focusflow-icon-button h-10 w-10 rounded-xl">
                            <svg class="h-5 w-5" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8">
                                <path stroke-linecap="round" stroke-linejoin="round" d="M15.75 7.5a3.75 3.75 0 1 1-7.5 0 3.75 3.75 0 0 1 7.5 0Z" />
                                <path stroke-linecap="round" stroke-linejoin="round" d="M4.5 20.12a7.5 7.5 0 0 1 15 0" />
                            </svg>
                        </a>
                    </div>
                </div>

                <main class="flex-1 p-4 pb-8 sm:p-6 lg:p-8">
                    @yield('content')
                </main>
            </div>
        </div>
    </body>
</html>
