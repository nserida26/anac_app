<header class="app-topbar">
    <div class="app-topbar-inner">

        {{-- Left: Logo + Mobile Toggle --}}
        <div class="app-topbar-left">
            <button @@click="sidebarOpen = !sidebarOpen" class="mobile-toggle" aria-label="Toggle navigation">
                <div class="lines">
                    <span></span>
                    <span></span>
                    <span></span>
                </div>
            </button>

            <a href="{{ route('user') }}" class="flex items-center gap-3 no-underline">
                <div class="p-1.5 rounded-lg" style="background:rgba(201,168,76,0.1);border:1px solid rgba(201,168,76,0.2)">
                    <img src="{{ asset('assets/admin/imgs/logo.png') }}" alt="" class="h-9 w-auto drop-shadow-lg">
                </div>
                <div class="flex flex-col max-lg:hidden">
                    <span class="text-white font-bold text-sm leading-none tracking-wider uppercase">{{ config('app.name') }}</span>
                    <span class="text-[#c9a84c] text-[9px] tracking-[0.2em] uppercase opacity-75">Management System</span>
                </div>
            </a>
        </div>

        {{-- Right: Language Switcher + User Menu --}}
        <div class="app-topbar-right">

            {{-- Language Switcher --}}
            @php
                $currentLocale = LaravelLocalization::getCurrentLocale();
                $locales = LaravelLocalization::getSupportedLocales();
            @endphp
            <div class="relative" x-data="{ open: false }">
                <button @@click="open = !open" @@click.away="open = false" class="topbar-lang-btn">
                    @if($currentLocale == 'en')
                        <img src="{{ asset('assets/hyper/images/flags/us.jpg') }}" alt="" class="dropdown-flag">
                    @else
                        <img src="{{ asset('assets/hyper/images/flags/fr.jpg') }}" alt="" class="dropdown-flag">
                    @endif
                    <span>{{ $locales[$currentLocale]['native'] }}</span>
                    <svg class="w-3 h-3 opacity-50" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7"/>
                    </svg>
                </button>

                <div x-show="open" x-cloak class="dropdown-menu-custom">
                    @foreach($locales as $localeCode => $properties)
                        @if($localeCode !== $currentLocale && in_array($localeCode, ['en', 'fr']))
                            <a rel="alternate" hreflang="{{ $localeCode }}"
                               href="{{ LaravelLocalization::getLocalizedURL($localeCode, null, [], true) }}"
                               class="dropdown-item-custom">
                                @if($localeCode == 'en')
                                    <img src="{{ asset('assets/hyper/images/flags/us.jpg') }}" alt="" class="dropdown-flag">
                                @else
                                    <img src="{{ asset('assets/hyper/images/flags/fr.jpg') }}" alt="" class="dropdown-flag">
                                @endif
                                <span>{{ $properties['native'] }}</span>
                            </a>
                        @endif
                    @endforeach
                </div>
            </div>

            {{-- User Dropdown --}}
            <div class="relative" x-data="{ open: false }">
                <button @@click="open = !open" @@click.away="open = false" class="topbar-user-btn">
                    <span class="topbar-user-avatar flex items-center justify-center bg-navy-700 text-gold-500 text-xs font-bold">
                        {{ strtoupper(substr(Auth::user()->name ?? 'U', 0, 1)) }}
                    </span>
                    <span class="max-lg:hidden">
                        <div class="topbar-user-name">{{ Auth::user()->name ?? 'User' }}</div>
                        <div class="topbar-user-role">{{ Auth::user()->user_type ?? '' }}</div>
                    </span>
                    <svg class="w-3 h-3 text-white/50" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7"/>
                    </svg>
                </button>

                <div x-show="open" x-cloak class="dropdown-menu-custom">
                    <div class="dropdown-header-custom">Bienvenue</div>

                    <a href="{{ route('user.profile') }}" class="dropdown-item-custom">
                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z"/>
                        </svg>
                        <span>{{ __('Profile') }}</span>
                    </a>

                    <div class="dropdown-divider"></div>

                    <button onclick="document.getElementById('logout-form').submit()" class="dropdown-item-custom danger">
                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 16l4-4m0 0l-4-4m4 4H7m6 4v1a3 3 0 01-3 3H6a3 3 0 01-3-3V7a3 3 0 013-3h4a3 3 0 013 3v1"/>
                        </svg>
                        <span>{{ __('Logout') }}</span>
                    </button>

                    <form action="{{ route('logout') }}" method="post" id="logout-form" class="hidden">
                        @csrf
                    </form>
                </div>
            </div>

        </div>
    </div>
</header>