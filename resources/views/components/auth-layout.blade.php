@props([
    'title',
    'routeName',
    'routeParams' => [],
])

<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta http-equiv="x-UA-Compatible" content="ie=edge">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>{{ $title }} — ANAC</title>

    <link href="{{ asset('assets/admin/imgs/logo.png') }}" rel="icon" type="image/png">
    <link href="{{ asset('assets/admin/fonts/SansPro/SansPro.min.css') }}" rel="stylesheet">
    <link rel="stylesheet" href="{{ asset('assets/admin/plugins/fontawesome-free/css/all.min.css') }}">
    <link rel="stylesheet" href="{{ asset('css/auth.css') }}">
</head>

<body>
    <div class="auth-layout">

        {{-- ═══ BRAND PANEL (Left) ═══ --}}
        <div class="auth-brand">
            <div class="auth-brand-content">
                <img src="{{ asset('assets/admin/imgs/logo.png') }}" alt="ANAC" class="auth-brand-logo">
                <h2 class="auth-brand-title">{{ __('Welcome title') }}</h2>
                <p class="auth-brand-desc">{{ __('Welcome description') }}</p>
            </div>
        </div>

        {{-- ═══ FORM PANEL (Right) ═══ --}}
        <div class="auth-form-panel">
            {{-- Back to Home --}}
            <a href="{{ route('welcome') }}" class="auth-back">
                <i class="fas fa-arrow-left"></i>
                {{ __('Back to home') }}
            </a>

            {{-- Language Switcher --}}
            <div class="auth-lang">
                @php $currentLocale = app()->getLocale(); @endphp
                @foreach(LaravelLocalization::getSupportedLocales() as $localeCode => $properties)
                    <a href="{{ LaravelLocalization::getLocalizedURL($localeCode, route($routeName, $routeParams)) }}"
                       class="lang-btn {{ $currentLocale === $localeCode ? 'active' : '' }}">
                        {{ strtoupper($localeCode) }}
                    </a>
                @endforeach
            </div>

            <div class="auth-form-container">
                {{ $slot }}
            </div>
        </div>
    </div>

    <script>
    (function() {
        var forms = document.querySelectorAll('.auth-form');
        forms.forEach(function(form) {
            form.addEventListener('submit', function() {
                var btn = form.querySelector('.auth-btn');
                if (btn) {
                    btn.classList.add('auth-btn--loading');
                    btn.disabled = true;
                }
            });
        });
    })();
    </script>
</body>
</html>
