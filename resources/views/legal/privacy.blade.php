@extends('layouts.app')

@section('title', __('app.privacy'))

@section('content')
    <section class="bg-gradient-to-br from-slate-900 via-slate-800 to-slate-700 text-white">
        <div class="mx-auto max-w-5xl px-4 py-12">
            <h1 class="text-3xl font-semibold mb-2">{{ __('app.privacy') }}</h1>
            <p class="text-slate-200">{{ __('app.privacy_subtitle') }}</p>
        </div>
    </section>
    <section class="-mt-10 pb-16">
        <div class="mx-auto max-w-5xl px-4">
            <div class="bg-white rounded-3xl shadow-xl shadow-slate-200/60 border border-slate-100 p-8 space-y-4 leading-relaxed text-slate-800">
                <p>{{ __('app.privacy_intro') }}</p>
                <ul class="list-disc list-inside space-y-2">
                    <li>{{ __('app.privacy_data_login') }}</li>
                    <li>{{ __('app.privacy_data_calendar') }}</li>
                    <li>{{ __('app.privacy_data_logs') }}</li>
                </ul>
                <p class="text-sm text-slate-600">{{ __('app.privacy_contact') }}</p>
            </div>
        </div>
    </section>
@endsection
