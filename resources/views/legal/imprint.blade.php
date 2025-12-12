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
                @if(setting('imprint_company') || setting('imprint_address') || setting('imprint_contact'))
                    @if(setting('imprint_company'))
                        <p><strong>{{ setting('imprint_company') }}</strong></p>
                    @endif
                    @if(setting('imprint_address'))
                        <p class="whitespace-pre-line">{{ setting('imprint_address') }}</p>
                    @endif
                    @if(setting('imprint_contact'))
                        <p class="whitespace-pre-line">{{ setting('imprint_contact') }}</p>
                    @endif
                @else
                    <p><strong>{{ __('app.operator') }}</strong><br>
                        {{ __('app.operator_placeholder') }}</p>
                @endif
                <p class="text-sm text-slate-600">{{ __('app.imprint_footer') }}</p>
            </div>
        </div>
    </section>
@endsection
