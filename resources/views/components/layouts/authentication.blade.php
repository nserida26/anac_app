@props(['title' => 'Connexion'])

<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}" class="scroll-smooth">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>{{ $title }} | {{ config('app.name') }}</title>
    <meta name="description" content="ANAC - Agence Nationale de l'Aviation Civile">
    <link rel="icon" href="{{ asset('assets/admin/imgs/logo.png') }}" type="image/png">

    <!-- Google Fonts -->
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@300;400;500;600;700;800&display=swap" rel="stylesheet">

    <!-- Tailwind CSS -->
    <script src="https://cdn.tailwindcss.com"></script>
    <script>
        tailwind.config = {
            theme: {
                extend: {
                    fontFamily: { sans: ['Inter','sans-serif'] },
                    colors: {
                        navy: { 950:'#060f28', 900:'#0a1a43', 800:'#0e2258', 700:'#142e70', 600:'#1a3a8a' },
                        gold:  { 300:'#eecb60', 400:'#d4af37', 500:'#c9a84c', 600:'#b08a2e' },
                    },
                }
            }
        }
    </script>

    <!-- Custom CSS -->
    <link href="{{ asset('assets/custom.css') }}" rel="stylesheet">
</head>
<body class="antialiased">

    <div class="auth-page">
        <!-- Orbs -->
        <div class="auth-orb-1" aria-hidden="true"></div>
        <div class="auth-orb-2" aria-hidden="true"></div>

        <!-- Auth Card -->
        <div class="auth-card">
            <!-- Logo -->
            <div class="text-center mb-8">
                <a href="{{ url('/') }}" class="inline-flex items-center gap-3">
                    <div class="p-1.5 rounded-lg" style="background:rgba(201,168,76,0.1);border:1px solid rgba(201,168,76,0.2)">
                        <img src="{{ asset('assets/admin/imgs/logo.png') }}" alt="ANAC" class="h-12 w-auto">
                    </div>
                    <div class="text-left">
                        <p class="text-white font-bold text-lg leading-none">ANAC</p>
                        <p class="text-[#c9a84c] text-[9px] tracking-[0.18em] uppercase opacity-75">Aviation Civile</p>
                    </div>
                </a>
            </div>

            <!-- Title -->
            <div class="text-center mb-6">
                <h1 class="text-xl font-bold text-white mb-1">{{ $title }}</h1>
                @if(isset($description))
                    <p class="text-sm" style="color:rgba(148,163,184,0.8)">{{ $description }}</p>
                @endif
            </div>

            <!-- Slot (login/register form) -->
            {{ $slot }}

            <!-- Footer -->
            @if(isset($footer))
                <div class="auth-footer">
                    {{ $footer }}
                </div>
            @endif
        </div>

        <!-- Copyright -->
        <div class="absolute bottom-4 left-0 right-0 text-center">
            <p class="text-xs" style="color:rgba(148,163,184,0.35)">
                &copy; {{ date('Y') }} {{ config('app.name') }} — Tous droits réservés
            </p>
        </div>
    </div>

</body>
</html>
