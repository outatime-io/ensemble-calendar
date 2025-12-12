<!doctype html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>{{ config('app.name') }} | @yield('title', __('app.calendar'))</title>
    @vite(['resources/css/app.css', 'resources/js/app.js'])
</head>
<body class="min-h-screen bg-slate-50 text-slate-900 antialiased">
<div class="min-h-screen flex flex-col">
    <header class="border-b border-slate-200 bg-white/80 backdrop-blur">
        <div class="mx-auto max-w-6xl px-4 py-4 flex items-center justify-between gap-4">
            <div class="flex items-center gap-3">
                <div class="h-10 w-10 rounded-xl bg-gradient-to-br from-sky-500 to-indigo-500 shadow-md shadow-sky-200 flex items-center justify-center text-white font-semibold">
                    EC
                </div>
                <div>
                    <div class="text-sm uppercase tracking-[0.2em] text-slate-500">{{ __('app.ensemble') }}</div>
                    <div class="text-lg font-semibold text-slate-900">{{ config('app.name') }}</div>
                </div>
            </div>
            <nav class="flex items-center gap-3 text-sm font-medium text-slate-700">
                @auth
                    <a href="{{ route('calendar.index') }}" class="nav-link">{{ __('app.calendar') }}</a>
                @endauth
                @auth
                    <form method="POST" action="{{ route('logout') }}">
                        @csrf
                        <button type="submit" class="nav-pill">{{ __('app.logout') }}</button>
                    </form>
                @endauth
                @guest
                    <a href="{{ route('login') }}" class="nav-pill">{{ __('app.login') }}</a>
                @endguest
            </nav>
        </div>
    </header>

    <main class="flex-1">
        @yield('content')
    </main>

    <footer class="border-t border-slate-200 bg-white/80">
        <div class="mx-auto max-w-6xl px-4 py-6 text-sm text-slate-500 flex flex-wrap gap-4 items-center justify-between">
            <div>&copy; {{ now()->year }} {{ config('app.name') }}</div>
            <div class="flex gap-3">
                <a href="{{ route('legal.imprint') }}" class="nav-link">{{ __('app.imprint') }}</a>
                <a href="{{ route('legal.privacy') }}" class="nav-link">{{ __('app.privacy') }}</a>
                <a href="{{ route('legal.data-deletion') }}" class="nav-link">{{ __('app.data_deletion') }}</a>
            </div>
        </div>
    </footer>
</div>

<x-cookie-banner />
</body>
</html>
