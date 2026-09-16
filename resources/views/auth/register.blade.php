<x-auth-layout title="{{ __('Register title') }}" route-name="register">

    {{-- Header --}}
    <div class="auth-header">
        <img src="{{ asset('assets/admin/imgs/logo.png') }}" alt="ANAC">
        <h1>{{ __('Register title') }}</h1>
        <p>{{ __('Register subtitle') }}</p>
    </div>

    {{-- Alerts --}}
    @if (Session::has('error'))
        <x-auth-alert type="error">{{ Session::get('error') }}</x-auth-alert>
    @endif

    {{-- Form --}}
    <form action="{{ route('register') }}" method="post" class="auth-form">
        @csrf

        {{-- WhatsApp --}}
        <div class="form-group">
            <label for="whatsapp">{{ __('WhatsApp') }}</label>
            <div class="whatsapp-group">
                <select name="country_code" class="whatsapp-select" id="country_code">
                    @include('layouts.includes.countries')
                </select>
                <input type="text" name="whatsapp" id="whatsapp" class="whatsapp-input"
                       placeholder="{{ __('Enter your WhatsApp number') }}"
                       value="{{ old('whatsapp') }}" maxlength="8" required>
            </div>
            <div class="whatsapp-icon-hint">
                <i class="fab fa-whatsapp" style="color: #25D366; margin-right: 4px;"></i>
                <span>{{ __('8 digits required') }}</span>
            </div>
            @error('whatsapp') <span class="text-danger">{{ $message }}</span> @enderror
            @error('country_code') <span class="text-danger">{{ $message }}</span> @enderror
        </div>

        <x-input name="email"
                 type="email"
                 icon="fas fa-envelope"
                 label="{{ __('Email') }}"
                 placeholder="{{ __('Enter your email') }}"
                 :value="old('email')"
                 :error="$errors->first('email')"
                 :required="true" />

        <x-input name="password"
                 type="password"
                 icon="fas fa-lock"
                 label="{{ __('Password') }}"
                 placeholder="{{ __('Enter your password') }}"
                 :error="$errors->first('password')"
                 :required="true" />

        <x-input name="password_confirmation"
                 type="password"
                 icon="fas fa-lock"
                 label="{{ __('Confirm Password') }}"
                 placeholder="{{ __('Confirm your password') }}"
                 :required="true" />

        <x-input name="user_type"
                 type="select"
                 icon="fas fa-user-tag"
                 label="{{ __('Account type') }}"
                 :error="$errors->first('user_type')"
                 :required="true">
            <option value="autorisation" {{ old('user_type') === 'autorisation' ? 'selected' : '' }}>
                {{ __('Authorization type') }}
            </option>
            <option value="licence" {{ old('user_type') === 'licence' ? 'selected' : '' }}>
                {{ __('License type') }}
            </option>
        </x-input>

        <button type="submit" class="auth-btn">
            <i class="fas fa-user-plus"></i>
            {{ __('Create account') }}
        </button>
    </form>

    {{-- Links --}}
    <div class="auth-links">
        <div class="auth-divider">{{ __('or') }}</div>
        <p>
            {{ __('Already have an account?') }}
            <a href="{{ route('login') }}">{{ __('Sign in') }}</a>
        </p>
    </div>

</x-auth-layout>
