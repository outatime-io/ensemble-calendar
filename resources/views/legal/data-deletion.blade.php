@extends('layouts.app')

@section('title', __('app.data_deletion'))

@section('content')
    <section class="bg-gradient-to-br from-slate-900 via-slate-800 to-slate-700 text-white">
        <div class="mx-auto max-w-5xl px-4 py-12">
            <h1 class="text-3xl font-semibold mb-2">{{ __('app.data_deletion') }}</h1>
            <p class="text-slate-200">{{ __('app.data_deletion_subtitle') }}</p>
        </div>
    </section>
    <section class="-mt-10 pb-16">
        <div class="mx-auto max-w-5xl px-4">
            <div class="bg-white rounded-3xl shadow-xl shadow-slate-200/60 border border-slate-100 p-8 space-y-4 leading-relaxed text-slate-800">
                <p>{{ __('app.data_deletion_body') }}</p>
                <p class="text-sm text-slate-600">{{ __('app.data_deletion_contact') }}</p>
            </div>
        </div>
    </section>
@endsection
