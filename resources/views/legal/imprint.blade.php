@extends('layouts.app')

@section('title', __('app.imprint'))

@section('content')
    <section class="bg-gradient-to-br from-slate-900 via-slate-800 to-slate-700 text-white">
        <div class="mx-auto max-w-5xl px-4 py-12">
            <h1 class="text-3xl font-semibold mb-2">{{ __('app.imprint') }}</h1>
            <p class="text-slate-200">{{ __('app.imprint_subtitle') }}</p>
        </div>
    </section>
    <section class="-mt-10 pb-16">
        <div class="mx-auto max-w-5xl px-4">
            <div class="bg-white rounded-3xl shadow-xl shadow-slate-200/60 border border-slate-100 p-8 space-y-4 leading-relaxed text-slate-800">
                <p><strong>{{ __('app.operator') }}</strong><br>
                    {{ __('app.operator_placeholder') }}</p>
                <p class="text-sm text-slate-600">{{ __('app.imprint_footer') }}</p>
            </div>
        </div>
    </section>
@endsection
