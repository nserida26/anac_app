<nav class="main-header navbar navbar-expand navbar-white navbar-light">
    <!-- Left navbar links  Rida -->
    <ul class="navbar-nav">

        <li class="nav-item">
            <a class="nav-link" data-widget="pushmenu" href="#"><i class="fas fa-bars"></i></a>
        </li>
        <li class="nav-item d-none d-sm-inline-block">


        </li>


    </ul>


    <!-- Right navbar links -->

    <ul
        class="{{ LaravelLocalization::getCurrentLocale() == 'fr' || LaravelLocalization::getCurrentLocale() == 'en' ? 'navbar-nav ml-auto' : 'navbar-nav' }}">

        <li class="nav-item dropdown no-arrow">
            <a class="nav-link dropdown-toggle" href="#" id="userDropdown" role="button" data-toggle="dropdown"
                aria-haspopup="true" aria-expanded="false">
                <!-- Rida -->
                <img src="{{ asset('/assets/admin/imgs/default.png') }}"
                    class="img-profile rounded-circle avatar user-image" width="32px" height="32px"
                    alt="User Image" />

            </a>
            <!-- Dropdown - User Information -->
            <div class="dropdown-menu dropdown-menu-right shadow animated--grow-in" aria-labelledby="userDropdown">
                @if (auth()->user()->can('manage-vi') || auth()->user()->can('manage-dsv'))
                    <a class="dropdown-item" href="{{ route('admin.sc') }}">
                        <i class="fas fa-user fa-sm fa-fw mr-2 text-gray-400"></i>
                        @lang('trans.signature')
                    </a>
                    <div class="dropdown-divider"></div>
                @endif

                @foreach (LaravelLocalization::getSupportedLocales() as $localeCode => $properties)
                    <a class="dropdown-item {{ LaravelLocalization::getCurrentLocale() == $localeCode ? 'active' : '' }}"
                        rel="alternate" hreflang="{{ $localeCode }}"
                        href="{{ LaravelLocalization::getLocalizedURL($localeCode, null, [], true) }}">
                        <i
                            class="far fa-circle nav-icon {{ LaravelLocalization::getCurrentLocale() == $localeCode ? 'fas' : '' }}"></i>
                        {{ $properties['native'] }}
                    </a>
                @endforeach

                <div class="dropdown-divider"></div>
                <a class="dropdown-item" href="#" data-toggle="modal" data-target="#logoutModal">
                    <i class="fas fa-sign-out-alt fa-sm fa-fw mr-2 text-gray-400"></i>
                    {{ __('Logout') }}
                </a>

            </div>
        </li>

    </ul>
</nav>
