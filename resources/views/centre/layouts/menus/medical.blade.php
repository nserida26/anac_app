{{-- Menu du compte centre d'expertise médicale --}}
<li class="nav-item">
    <a href="{{ route('centre_medical.index') }}" class="nav-link {{ request()->routeIs('centre_medical.index') ? 'active' : '' }}">
        <i class="nav-icon fas fa-tachometer-alt"></i>
        <p>@lang('trans.dashboard')</p>
    </a>
</li>

<li class="nav-item">
    <a href="{{ route('centre_medical.medecins') }}" class="nav-link {{ request()->routeIs('centre_medical.medecins*') ? 'active' : '' }}">
        <i class="nav-icon fas fa-user-md"></i>
        <p>@lang('trans.medecins')</p>
    </a>
</li>

<li class="nav-item">
    <a href="{{ route('centre_medical.examinateurs') }}" class="nav-link {{ request()->routeIs('centre_medical.examinateurs*') ? 'active' : '' }}">
        <i class="nav-icon fas fa-user-check"></i>
        <p>@lang('trans.medical_examiners')</p>
    </a>
</li>
