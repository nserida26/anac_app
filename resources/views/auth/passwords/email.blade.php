<x-auth-layout title="{{ __('Forgot password title') }}" route-name="password.request">

    {{-- Header --}}
    <div class="auth-header">
        <img src="{{ asset('assets/admin/imgs/logo.png') }}" alt="ANAC">
        <h1>{{ __('Forgot password title') }}</h1>
        <p>{{ __('Forgot password subtitle') }}</p>
    </div>

    {{-- Alerts --}}
    @if (Session::has('status'))
        <x-auth-alert type="success">{{ Session::get('status') }}</x-auth-alert>
    @endif

    {{-- Form --}}
    <form action="{{ route('password.email') }}" method="post" class="auth-form">
        @csrf

        <x-input name="email"
                 type="email"
                 icon="fas fa-envelope"
                 label="{{ __('Email') }}"
                 placeholder="{{ __('Enter your registered email') }}"
                 :value="old('email')"
                 :error="$errors->first('email')"
                 :autofocus="true"
                 :required="true" />

        <button type="submit" class="auth-btn">
            <i class="fas fa-paper-plane"></i>
            {{ __('Send reset link') }}
        </button>
    </form>

    {{-- Links --}}
    <div class="auth-links">
        <div class="auth-divider">{{ __('or') }}</div>
        <p><a href="{{ route('login') }}">{{ __('Back to login') }}</a></p>
    </div>

</x-auth-layout>
