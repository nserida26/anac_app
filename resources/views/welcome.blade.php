<!DOCTYPE html>
<html lang="{{ LaravelLocalization::getCurrentLocale() }}">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta http-equiv="x-UA-Compatible" content="ie=edge">
    <meta name="description" content="{{ __('Welcome description') }}">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>{{ __('Welcome title') }} — ANAC</title>

    <!-- Favicon -->
    <link href="{{ asset('assets/admin/imgs/logo.png') }}" rel="icon" type="image/png">

    <!-- Fonts -->
    <link href="{{ asset('assets/admin/fonts/SansPro/SansPro.min.css') }}" rel="stylesheet">

    <!-- AdminLTE (includes Bootstrap 4) -->
    <link rel="stylesheet" href="{{ asset('assets/admin/dist/css/adminlte.min.css') }}">
    <!-- Font Awesome -->
    <link rel="stylesheet" href="{{ asset('assets/admin/plugins/fontawesome-free/css/all.min.css') }}">
    <!-- Custom Styles -->
    <link rel="stylesheet" href="{{ asset('css/welcome.css') }}">
</head>

<body>

    <!-- ══════════════════════════════════════════════════════════════
         NAVBAR
         ══════════════════════════════════════════════════════════════ -->
    <nav class="welcome-navbar" id="navbar">
        <div class="container">
            <div class="d-flex justify-content-between align-items-center">
                <!-- Logo -->
                <a href="{{ route('welcome') }}" class="navbar-brand mb-0">
                    <img src="{{ asset('assets/admin/imgs/logo.png') }}" alt="ANAC">
                </a>

                <!-- Right Side -->
                <div class="d-flex align-items-center">
                    <!-- Language Switcher -->
                    <div class="lang-switcher">
                        @php $currentLocale = app()->getLocale(); @endphp
                        @foreach(LaravelLocalization::getSupportedLocales() as $localeCode => $properties)
                            <a href="{{ LaravelLocalization::getLocalizedURL($localeCode, route('welcome')) }}"
                               class="lang-btn {{ $currentLocale === $localeCode ? 'active' : '' }}">
                                {{ strtoupper($localeCode) }}
                            </a>
                        @endforeach
                    </div>

                    <!-- Login -->
                    <a href="{{ route('login') }}" class="nav-link btn-nav">
                        <i class="fas fa-sign-in-alt mr-1"></i>
                        {{ __('Login') }}
                    </a>

                    <!-- Register -->
                    <a href="{{ route('register') }}" class="nav-link btn-nav btn-nav--filled ml-1">
                        {{ __('Register') }}
                    </a>
                </div>
            </div>
        </div>
    </nav>

    <!-- ══════════════════════════════════════════════════════════════
         HERO SECTION
         ══════════════════════════════════════════════════════════════ -->
    <section class="hero-section">
        <div class="hero-content">
            <img src="{{ asset('assets/admin/imgs/logo.png') }}" alt="ANAC Logo" class="hero-logo">

            <h1 class="hero-title">{{ __('Welcome title') }}</h1>

            <p class="hero-subtitle">{{ __('Welcome description') }}</p>

            <div class="hero-buttons">
                <a href="{{ route('register') }}" class="btn-anac btn-anac--primary">
                    <i class="fas fa-rocket"></i>
                    {{ __('Register') }}
                </a>
                <a href="{{ route('login') }}" class="btn-anac btn-anac--outline">
                    <i class="fas fa-sign-in-alt"></i>
                    {{ __('Login') }}
                </a>
            </div>
        </div>

        <!-- Scroll Indicator -->
        <div class="scroll-indicator">
            <a href="#services" aria-label="Scroll to services">
                <i class="fas fa-chevron-down"></i>
            </a>
        </div>
    </section>

    <!-- ══════════════════════════════════════════════════════════════
         SERVICES SECTION
         ══════════════════════════════════════════════════════════════ -->
    <section class="services-section" id="services">
        <div class="container">
            <div class="section-header reveal">
                <span class="section-label">{{ __('Management') }}</span>
                <h2 class="section-title">{{ __('Services') }}</h2>
                <div class="section-divider"></div>
            </div>

            <div class="row">
                <!-- Service 1 -->
                <div class="col-lg-4 col-md-6 mb-4 reveal">
                    <div class="service-card">
                        <div class="service-icon service-icon--blue">
                            <i class="fas fa-plane-departure"></i>
                        </div>
                        <h3 class="service-title">{{ __('Authorizations') }}</h3>
                        <p class="service-desc">{{ __('Authorizations desc') }}</p>
                    </div>
                </div>

                <!-- Service 2 -->
                <div class="col-lg-4 col-md-6 mb-4 reveal">
                    <div class="service-card">
                        <div class="service-icon service-icon--gold">
                            <i class="fas fa-id-card"></i>
                        </div>
                        <h3 class="service-title">{{ __('Licenses') }}</h3>
                        <p class="service-desc">{{ __('Licenses desc') }}</p>
                    </div>
                </div>

                <!-- Service 3 -->
                <div class="col-lg-4 col-md-6 mb-4 reveal">
                    <div class="service-card">
                        <div class="service-icon service-icon--teal">
                            <i class="fas fa-credit-card"></i>
                        </div>
                        <h3 class="service-title">{{ __('Online Payment') }}</h3>
                        <p class="service-desc">{{ __('Online Payment desc') }}</p>
                    </div>
                </div>
            </div>
        </div>
    </section>

    <!-- ══════════════════════════════════════════════════════════════
         ABOUT SECTION
         ══════════════════════════════════════════════════════════════ -->
    <section class="about-section" id="about">
        <div class="container">
            <div class="about-content">
                <!-- Image -->
                <div class="about-image reveal">
                    <img src="{{ asset('assets/admin/imgs/anac.jpg') }}" alt="{{ __('About ANAC') }}">
                </div>

                <!-- Text -->
                <div class="about-text reveal">
                    <span class="section-label">{{ __('About') }}</span>
                    <h2 class="section-title">{{ __('About ANAC') }}</h2>
                    <p>{{ __('About description') }}</p>

                    <ul class="about-features">
                        <li>
                            <i class="fas fa-check-circle"></i>
                            <span>{{ __('Secure request management') }}</span>
                        </li>
                        <li>
                            <i class="fas fa-check-circle"></i>
                            <span>{{ __('License issuance and tracking') }}</span>
                        </li>
                        <li>
                            <i class="fas fa-check-circle"></i>
                            <span>{{ __('Secure online payment') }}</span>
                        </li>
                        <li>
                            <i class="fas fa-check-circle"></i>
                            <span>{{ __('ICAO international standards compliance') }}</span>
                        </li>
                    </ul>

                    <a href="{{ route('register') }}" class="btn-anac btn-anac--dark">
                        {{ __('Get Started') }}
                        <i class="fas fa-arrow-right"></i>
                    </a>
                </div>
            </div>
        </div>
    </section>

    <!-- ══════════════════════════════════════════════════════════════
         CTA SECTION
         ══════════════════════════════════════════════════════════════ -->
    <section class="cta-section">
        <div class="container reveal">
            <h2 class="section-title">{{ __('Ready to start?') }}</h2>
            <p>{{ __('Create your account and access our online services.') }}</p>
            <a href="{{ route('register') }}" class="btn-anac btn-anac--primary">
                <i class="fas fa-user-plus"></i>
                {{ __('Register') }}
            </a>
        </div>
    </section>

    <!-- ══════════════════════════════════════════════════════════════
         FOOTER
         ══════════════════════════════════════════════════════════════ -->
    <footer class="welcome-footer">
        <div class="container">
            <div class="footer-content">
                <div class="footer-brand">
                    <img src="{{ asset('assets/admin/imgs/logo.png') }}" alt="ANAC">
                    <span>{{ __('ANAC Mauritania') }}</span>
                </div>
                <div class="footer-links">
                    <a href="{{ route('login') }}">{{ __('Login') }}</a>
                    <a href="{{ route('register') }}">{{ __('Register') }}</a>
                    <a href="{{ route('password.request') }}">{{ __('Reset Password') }}</a>
                </div>
            </div>
            <div class="footer-bottom">
                <p>&copy; {{ date('Y') }} {{ __('ANAC Mauritania') }}. {{ __('All rights reserved.') }}</p>
            </div>
        </div>
    </footer>

    <!-- ══════════════════════════════════════════════════════════════
         SCRIPTS (Vanilla JS — no jQuery needed)
         ══════════════════════════════════════════════════════════════ -->
    <script>
    (function () {
        var navbar = document.getElementById('navbar');

        // ── Navbar scroll effect ──
        function handleScroll() {
            navbar.classList.toggle('scrolled', window.pageYOffset > 50);
        }

        window.addEventListener('scroll', handleScroll, { passive: true });
        handleScroll();

        // ── Scroll reveal ──
        var reveals = document.querySelectorAll('.reveal');

        function revealOnScroll() {
            var h = window.innerHeight;
            for (var i = 0; i < reveals.length; i++) {
                if (reveals[i].getBoundingClientRect().top < h - 120) {
                    reveals[i].classList.add('visible');
                }
            }
        }

        window.addEventListener('scroll', revealOnScroll, { passive: true });
        window.addEventListener('load', revealOnScroll);

        // ── Smooth scroll for anchor links ──
        document.querySelectorAll('a[href^="#"]').forEach(function (a) {
            a.addEventListener('click', function (e) {
                var target = document.querySelector(this.getAttribute('href'));
                if (target) {
                    e.preventDefault();
                    target.scrollIntoView({ behavior: 'smooth', block: 'start' });
                }
            });
        });
    })();
    </script>

</body>
</html>
