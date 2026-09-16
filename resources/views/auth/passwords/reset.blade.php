<x-auth-layout title="{{ __('Reset password title') }}" route-name="password.reset" :route-params="['token' => $token, 'email' => $email]">

    {{-- Header --}}
    <div class="auth-header">
        <img src="{{ asset('assets/admin/imgs/logo.png') }}" alt="ANAC">
        <h1>{{ __('Reset password title') }}</h1>
        <p>{{ __('Reset password subtitle') }}</p>
    </div>

    {{-- Form --}}
    <form action="{{ route('password.update') }}" method="post" class="auth-form">
        @csrf

        <input type="hidden" name="token" value="{{ $token }}">

        <x-input name="email"
                 type="email"
                 icon="fas fa-envelope"
                 label="{{ __('Email') }}"
                 placeholder="{{ __('Enter your email') }}"
                 :value="$email ?? old('email')"
                 :error="$errors->first('email')"
                 :autofocus="true"
                 :required="true" />

        <x-input name="password"
                 type="password"
                 icon="fas fa-lock"
                 label="{{ __('New Password') }}"
                 placeholder="{{ __('Enter new password') }}"
                 :error="$errors->first('password')"
                 :required="true" />

        <x-input name="password_confirmation"
                 type="password"
                 icon="fas fa-lock"
                 label="{{ __('Confirm Password') }}"
                 placeholder="{{ __('Confirm your password') }}"
                 :required="true" />

        <button type="submit" class="auth-btn">
            <i class="fas fa-key"></i>
            {{ __('Reset password') }}
        </button>
    </form>

    {{-- Links --}}
    <div class="auth-links">
        <div class="auth-divider">{{ __('or') }}</div>
        <p><a href="{{ route('login') }}">{{ __('Back to login') }}</a></p>
    </div>

</x-auth-layout>
