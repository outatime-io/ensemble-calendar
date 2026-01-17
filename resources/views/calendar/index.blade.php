@extends('layouts.app')

@section('title', __('app.calendar'))

@section('content')
    <section class="bg-gradient-to-br from-sky-500 via-indigo-500 to-slate-900 text-white">
        <div class="mx-auto max-w-6xl px-4 py-14 md:py-16">
            <div class="grid gap-8 md:grid-cols-2 md:items-end">
                <div class="space-y-4">
                    <p class="uppercase tracking-[0.25em] text-xs text-slate-200">{{ setting('site_name', __('app.ensemble')) }}</p>
                    <h1 class="text-3xl md:text-4xl font-semibold leading-tight">{{ __('app.calendar_title') }}</h1>
                    <p class="text-slate-100 text-lg">{{ __('app.calendar_subtitle') }}</p>
                    <div class="flex flex-wrap gap-3">
                        <a href="{{ route('calendar.index') }}"
                           class="nav-pill {{ $view === 'upcoming' ? 'bg-slate-900 text-white border-slate-900 hover:bg-slate-800' : '' }}">
                            {{ __('app.upcoming') }}
                        </a>
                        <a href="{{ route('calendar.index', ['view' => 'past']) }}"
                           class="nav-pill {{ $view === 'past' ? 'bg-slate-900 text-white border-slate-900 hover:bg-slate-800' : '' }}">
                            {{ __('app.past') }}
                        </a>
                        <a href="{{ route('calendar.index', ['view' => 'all']) }}"
                           class="nav-pill {{ $view === 'all' ? 'bg-slate-900 text-white border-slate-900 hover:bg-slate-800' : '' }}">
                            {{ __('app.all') }}
                        </a>
                    </div>
                </div>
                <div class="bg-white/10 border border-white/20 rounded-3xl p-6 shadow-xl shadow-slate-900/20 backdrop-blur">
                    <h2 class="text-lg font-semibold mb-3">{{ __('app.ics_subscribe_title') }}</h2>
                    <p class="text-sm text-slate-100 mb-4">{{ __('app.ics_instructions') }}</p>
                    @if($feedUrl)
                        <div class="flex flex-col gap-3">
                            <div class="bg-white/20 text-slate-900 rounded-2xl p-3 flex flex-col gap-3 sm:flex-row sm:items-center">
                                <input type="text" readonly value="{{ $feedUrl }}"
                                       class="flex-1 min-w-0 w-full bg-transparent text-sm font-mono text-white/90 focus:outline-none truncate"
                                       id="ics-url">
                                <button type="button"
                                        data-copy-target="#ics-url"
                                        data-default-text="{{ __('app.copy_link') }}"
                                        data-copied-text="{{ __('app.copied') }}"
                                        class="nav-pill w-full bg-white text-slate-900 hover:bg-slate-100 sm:w-auto">
                                    {{ __('app.copy_link') }}
                                </button>
                            </div>
                            <ul class="text-sm text-slate-100 space-y-1 list-disc list-inside">
                                <li>{{ __('app.ics_hint_ios') }}</li>
                                <li>{{ __('app.ics_hint_google') }}</li>
                                <li>{{ __('app.ics_hint_outlook') }}</li>
                            </ul>
                        </div>
                    @else
                        <div class="rounded-2xl border border-white/30 bg-white/10 px-4 py-3 text-sm text-white">
                            {{ __('app.feed_missing') }}
                        </div>
                    @endif
                </div>
            </div>
        </div>
    </section>

    <section class="-mt-8 pb-16 sm:-mt-12">
        <div class="mx-auto max-w-6xl px-4">
            @if($rehearsals->isEmpty())
                <div class="bg-white rounded-3xl shadow-xl shadow-slate-200/60 border border-slate-100 p-6 text-center sm:p-10">
                    <p class="text-xl font-semibold text-slate-900 mb-2">{{ __('app.no_rehearsals_title') }}</p>
                    <p class="text-slate-600">{{ __('app.no_rehearsals_body') }}</p>
                </div>
            @else
                <div class="grid gap-6">
                    @foreach($rehearsals as $rehearsal)
                        <article class="bg-white rounded-3xl shadow-lg shadow-slate-200/60 border border-slate-100 p-5 sm:p-6 md:p-7">
                            <div class="flex flex-col gap-4 sm:gap-6 md:flex-row md:items-start md:justify-between">
                                <div class="space-y-1">
                                    <p class="text-sm uppercase tracking-[0.2em] text-slate-500">{{ __('app.rehearsal') }}</p>
                                    <h2 class="text-2xl font-semibold text-slate-900">{{ $rehearsal->title }}</h2>
                                    <p class="text-slate-700 break-words">{{ $rehearsal->location_name }}</p>
                                    @if($rehearsal->location_address)
                                        <p class="text-sm text-slate-500 break-words">{{ $rehearsal->location_address }}</p>
                                    @endif
                                </div>
                                <div class="flex flex-col items-stretch gap-2 sm:flex-row sm:items-center sm:gap-3">
                                    <div class="tag w-full justify-center text-slate-900 bg-slate-100 border border-slate-200 sm:w-auto">
                                        {{ $rehearsal->dateRangeLabel() }}
                                    </div>
                                    @if($rehearsal->plan_path)
                                        <a href="{{ route('rehearsals.plan', $rehearsal) }}" class="nav-pill w-full sm:w-auto" target="_blank" rel="noopener">
                                            {{ __('app.download_plan') }}
                                        </a>
                                    @else
                                        <span class="text-sm text-slate-500 text-center sm:text-left">{{ __('app.no_plan_uploaded') }}</span>
                                    @endif
                                </div>
                            </div>

                            <div class="mt-4 grid gap-4 md:grid-cols-2 md:gap-6">
                                <div class="rounded-2xl border border-slate-100 bg-slate-50/60 p-4 sm:p-5">
                                    <h3 class="text-sm font-semibold text-slate-700 mb-3">{{ __('app.daily_schedule') }}</h3>
                                    <div class="space-y-2">
                                        @foreach($rehearsal->days as $day)
                                            <div class="rounded-xl bg-white px-4 py-3 shadow-sm border border-slate-100 space-y-2">
                                                <div class="flex flex-col gap-1">
                                                    <div class="font-medium text-slate-900">{{ $day->rehearsal_date->translatedFormat('d.m.Y, l') }}</div>
                                                    <div class="text-sm text-slate-600">
                                                        {{ $day->startDateTime($rehearsal->timezone)->translatedFormat('H:i') }} – {{ $day->endDateTime($rehearsal->timezone)->translatedFormat('H:i') }}
                                                    </div>
                                                </div>
                                                @if($day->notes)
                                                    <div class="border-t border-slate-100 pt-2 text-sm text-slate-600 leading-relaxed">{{ $day->notes }}</div>
                                                @endif
                                            </div>
                                        @endforeach
                                    </div>
                                </div>
                                <div class="rounded-2xl border border-slate-100 bg-slate-50/60 p-4 sm:p-5">
                                    <h3 class="text-sm font-semibold text-slate-700 mb-3">{{ __('app.notes') }}</h3>
                                    @if($rehearsal->notes)
                                        <p class="text-slate-700 leading-relaxed whitespace-pre-line">{{ $rehearsal->notes }}</p>
                                    @else
                                        <p class="text-slate-500 text-sm">{{ __('app.no_notes') }}</p>
                                    @endif
                                </div>
                            </div>
                        </article>
                    @endforeach
                </div>
            @endif
        </div>
    </section>
@endsection
