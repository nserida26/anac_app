<x-auth-layout title="{{ __('Login title') }}" route-name="login">

    {{-- Header --}}
    <div class="auth-header">
        <img src="{{ asset('assets/admin/imgs/logo.png') }}" alt="ANAC">
        <h1>{{ __('Login title') }}</h1>
        <p>{{ __('Login subtitle') }}</p>
    </div>

    {{-- Alerts --}}
    @if (Session::has('error'))
        <x-auth-alert type="error">{{ Session::get('error') }}</x-auth-alert>
    @endif

    @if (Session::has('success'))
        <x-auth-alert type="success" :pre-line="true">{{ Session::get('success') }}</x-auth-alert>
    @endif

    @if (Session::has('status'))
        <x-auth-alert type="success">{{ Session::get('status') }}</x-auth-alert>
    @endif

    @if (Session::has('verified'))
        <x-auth-alert type="success">{{ __('Email verified') }}</x-auth-alert>
    @endif

    {{-- Form --}}
    <form action="{{ route('login') }}" method="post" class="auth-form">
        @csrf

        <x-input name="email"
                 type="text"
                 icon="fas fa-envelope"
                 label="{{ __('Email') }}"
                 placeholder="{{ __('Enter your email') }}"
                 :value="old('email')"
                 :error="$errors->first('email')"
                 :autofocus="true"
                 :required="true" />

        <x-input name="password"
                 type="password"
                 icon="fas fa-lock"
                 label="{{ __('Password') }}"
                 placeholder="{{ __('Enter your password') }}"
                 :error="$errors->first('password')"
                 :required="true" />

        <button type="submit" class="auth-btn">
            <i class="fas fa-sign-in-alt"></i>
            {{ __('Sign in') }}
        </button>
    </form>

    {{-- Links --}}
    <div class="auth-links">
        <p><a href="{{ route('password.request') }}">{{ __('Forgot password?') }}</a></p>
        <div class="auth-divider">{{ __('or') }}</div>
        <p>
            {{ __('No account yet?') }}
            <a href="{{ route('register') }}">{{ __('Create one now') }}</a>
        </p>
    </div>

</x-auth-layout>
