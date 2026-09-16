<x-auth-layout title="{{ __('Verify email title') }}" route-name="verification.notice">

    {{-- Header --}}
    <div class="auth-header">
        <img src="{{ asset('assets/admin/imgs/logo.png') }}" alt="ANAC">
        <h1>{{ __('Verify email title') }}</h1>
        <p>{{ __('Verify email subtitle') }}</p>
    </div>

    {{-- Alert --}}
    <x-auth-alert type="info" icon="fas fa-envelope-open-text">
        {{ __('Verify email message') }}
    </x-auth-alert>

    @if (Session::has('verification-link-sent'))
        <x-auth-alert type="success">{{ __('Verification email sent') }}</x-auth-alert>
    @endif

    {{-- Resend Form --}}
    <form action="{{ route('verification.send') }}" method="post" class="auth-form">
        @csrf
        <button type="submit" class="auth-btn">
            <i class="fas fa-redo"></i>
            {{ __('Resend verification email') }}
        </button>
    </form>

    {{-- Links --}}
    <div class="auth-links">
        <div class="auth-divider">{{ __('or') }}</div>
        <form method="POST" action="{{ route('logout') }}">
            @csrf
            <button type="submit" class="auth-btn" style="background: transparent; border: 1.5px solid var(--anac-gray-200); color: var(--anac-gray-800);">
                <i class="fas fa-sign-out-alt"></i>
                {{ __('Logout') }}
            </button>
        </form>
    </div>

</x-auth-layout>
