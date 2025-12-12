@extends('layouts.app')

@section('title', __('app.login'))

@section('content')
    <section class="bg-gradient-to-br from-slate-900 via-slate-800 to-slate-700 text-white">
        <div class="mx-auto max-w-6xl px-4 py-16">
            <div class="max-w-3xl">
                <p class="uppercase tracking-[0.25em] text-xs text-slate-300 mb-3">{{ setting('site_name', __('app.ensemble')) }}</p>
                <h1 class="text-3xl md:text-4xl font-semibold mb-4">{{ __('app.login_title') }}</h1>
                <p class="text-slate-200">{{ __('app.login_subtitle') }}</p>
            </div>
        </div>
    </section>

    <section class="-mt-12 pb-16">
        <div class="mx-auto max-w-6xl px-4">
            <div class="bg-white shadow-xl shadow-slate-200/60 border border-slate-100 rounded-3xl p-8 md:p-10">
                <form method="POST" action="{{ route('login') }}" class="grid gap-6">
                    @csrf
                    <div class="grid gap-4 md:grid-cols-2">
                        <label class="form-field">
                            <span class="form-label">{{ __('app.username') }}</span>
                            <input type="text" name="username" value="{{ old('username') }}" autocomplete="username"
                                   class="input" required autofocus>
                        </label>
                        <label class="form-field">
                            <span class="form-label">{{ __('app.password') }}</span>
                            <input type="password" name="password" autocomplete="current-password" class="input" required>
                        </label>
                    </div>
                    <div class="flex items-center justify-between gap-4 flex-wrap">
                        <label class="inline-flex items-center gap-2 text-sm text-slate-600">
                            <input type="checkbox" name="remember" class="h-4 w-4 rounded border-slate-300 text-sky-600 focus:ring-sky-500" {{ old('remember') ? 'checked' : '' }}>
                            <span>{{ __('app.remember_me') }}</span>
                        </label>
                        <button type="submit" class="btn-primary">
                            {{ __('app.login') }}
                        </button>
                    </div>
                    @if($errors->any())
                        <div class="rounded-2xl border border-amber-200 bg-amber-50 text-amber-900 px-4 py-3 text-sm">
                            {{ __('app.login_error') }}
                        </div>
                    @endif
                </form>
            </div>
        </div>
    </section>
@endsection
