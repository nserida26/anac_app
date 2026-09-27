{{-- Menu du compte centre de formation --}}
<li class="nav-item">
    <a href="{{ route('centre.index') }}" class="nav-link {{ request()->routeIs('centre.index') ? 'active' : '' }}">
        <i class="nav-icon fas fa-tachometer-alt"></i>
        <p>@lang('trans.dashboard')</p>
    </a>
</li>

<li class="nav-item">
    <a href="{{ route('centre.create') }}" class="nav-link {{ request()->routeIs('centre.create') ? 'active' : '' }}">
        <i class="nav-icon fas fa-plus-circle"></i>
        <p>@lang('trans.add_training')</p>
    </a>
</li>

<li class="nav-item">
    <a href="{{ route('centre.instructeurs') }}" class="nav-link {{ request()->routeIs('centre.instructeurs*') ? 'active' : '' }}">
        <i class="nav-icon fas fa-chalkboard-teacher"></i>
        <p>@lang('trans.instructors')</p>
    </a>
</li>

<li class="nav-item">
    <a href="{{ route('centre.examinateurs') }}" class="nav-link {{ request()->routeIs('centre.examinateurs*') ? 'active' : '' }}">
        <i class="nav-icon fas fa-user-check"></i>
        <p>@lang('trans.examiners')</p>
    </a>
</li>

<li class="nav-item">
    <a href="{{ route('centre.dispositifs') }}" class="nav-link {{ request()->routeIs('centre.dispositifs*') ? 'active' : '' }}">
        <i class="nav-icon fas fa-microchip"></i>
        <p>@lang('trans.training_devices')</p>
    </a>
</li>

<li class="nav-item">
    <a href="{{ route('centre.licences') }}" class="nav-link {{ request()->routeIs('centre.licences*') ? 'active' : '' }}">
        <i class="nav-icon fas fa-certificate"></i>
        <p>@lang('trans.licences')</p>
    </a>
</li>
