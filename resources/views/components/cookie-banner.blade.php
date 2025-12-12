<div data-cookie-banner class="cookie-banner hidden">
    <div class="cookie-inner">
        <div class="text-sm text-slate-800 leading-relaxed">
            {{ __('app.cookie_consent_message') }}
            <a href="{{ route('legal.privacy') }}" class="underline font-semibold">{{ __('app.privacy') }}</a>
        </div>
        <button type="button" data-accept-cookies class="nav-pill bg-slate-900 text-white hover:bg-slate-800">
            {{ __('app.accept') }}
        </button>
    </div>
</div>
