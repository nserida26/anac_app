<nav class="app-nav">
    <div class="app-nav-inner">
        <ul class="app-nav-list">
            <li class="app-nav-item">
                <a href="{{ route('user') }}" class="app-nav-link {{ request()->routeIs('user') || request()->routeIs('user.*') ? 'active' : '' }}">
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 12l2-2m0 0l7-7 7 7M5 10v10a1 1 0 001 1h3m10-11l2 2m-2-2v10a1 1 0 01-1 1h-3m-6 0a1 1 0 001-1v-4a1 1 0 011-1h2a1 1 0 011 1v4a1 1 0 001 1m-6 0h6"/>
                    </svg>
                    <span>@lang('trans.dashboard')</span>
                </a>
            </li>
            @if(Auth::user() && Auth::user()->user_type === 'licence')
            <li class="app-nav-item">                    <a href="{{ route('user.create') }}" class="app-nav-link {{ request()->routeIs('user.create') ? 'active' : '' }}">
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"/>
                    </svg>
                    <span>@lang('trans.license_applications')</span>
                </a>
            </li>
            @endif
            @if(Auth::user() && Auth::user()->user_type === 'autorisation')
            <li class="app-nav-item">                    <a href="{{ route('user') }}" class="app-nav-link {{ request()->routeIs('user') ? 'active' : '' }}">
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4m0 4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/>
                    </svg>
                    <span>@lang('trans.autorisations')</span>
                </a>
            </li>
            @endif
            <li class="app-nav-item">
                <a href="{{ route('user.profile') }}" class="app-nav-link {{ request()->routeIs('user.profile*') ? 'active' : '' }}">
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z"/>
                    </svg>
                    <span>@lang('trans.profile')</span>
                </a>
            </li>
        </ul>
    </div>
</nav>