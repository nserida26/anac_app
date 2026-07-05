{{-- resources/views/centre/layouts/app.blade.php --}}
<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>@yield('title') - @lang('trans.training_center')</title>
    
    <!-- Bootstrap 4 -->
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap@4.6.2/dist/css/bootstrap.min.css">
    <!-- Font Awesome -->
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/5.15.4/css/all.min.css">
    <!-- Toastr -->
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/toastr.js/latest/toastr.min.css">
    <!-- Select2 -->
    <link href="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/css/select2.min.css" rel="stylesheet" />
    <!-- AdminLTE -->
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/admin-lte@3.2/dist/css/adminlte.min.css">
    
    @stack('css')
</head>
<body class="hold-transition sidebar-mini layout-fixed">
    <div class="wrapper">
        <!-- Navbar -->
        <nav class="main-header navbar navbar-expand navbar-white navbar-light">
            <ul class="navbar-nav">
                <li class="nav-item">
                    <a class="nav-link" data-widget="pushmenu" href="#" role="button">
                        <i class="fas fa-bars"></i>
                    </a>
                </li>
            </ul>
            
            <ul class="navbar-nav ml-auto">
                <li class="nav-item dropdown">
                    <a class="nav-link" data-toggle="dropdown" href="#">
                        <i class="fas fa-user"></i> {{ Auth::user()->email }}
                    </a>
                    <div class="dropdown-menu dropdown-menu-right">
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
                        <form method="POST" action="{{ route('logout') }}">
                            @csrf
                            <button type="submit" class="dropdown-item">
                                <i class="fas fa-sign-out-alt"></i> @lang('trans.logout')
                            </button>
                        </form>
                    </div>
                </li>
            </ul>
        </nav>
        
        <!-- Sidebar -->
        <aside class="main-sidebar sidebar-dark-primary elevation-4">
            <a href="{{ route('centre.index') }}" class="brand-link">
                <span class="brand-text font-weight-light">@lang('trans.training_center')</span>
            </a>
            
            <div class="sidebar">
                <nav class="mt-2">
                    <ul class="nav nav-pills nav-sidebar flex-column" data-widget="treeview" role="menu">
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
                    </ul>
                </nav>
            </div>
        </aside>
        
        <!-- Content Wrapper -->
        <div class="content-wrapper">
            <div class="content-header">
                <div class="container-fluid">
                    <div class="row mb-2">
                        <div class="col-sm-6">
                            <h1 class="m-0">@yield('contentheader')</h1>
                        </div>
                        <div class="col-sm-6">
                            <ol class="breadcrumb float-sm-right">
                                <li class="breadcrumb-item">
                                    <a href="{{ route('centre.index') }}">@lang('trans.home')</a>
                                </li>
                                @hasSection('contentheaderlink')
                                    <li class="breadcrumb-item">@yield('contentheaderlink')</li>
                                @endif
                                <li class="breadcrumb-item active">@yield('contentheaderactive')</li>
                            </ol>
                        </div>
                    </div>
                </div>
            </div>
            
            <section class="content">
                <div class="container-fluid">
                    @yield('content')
                </div>
            </section>
        </div>
        
        <!-- Footer -->
        <footer class="main-footer">
            <strong>@lang('trans.copyright') &copy; {{ date('Y') }} ANAC.</strong>
            @lang('trans.all_rights_reserved')
        </footer>
    </div>
    
    <!-- Scripts -->
    <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@4.6.2/dist/js/bootstrap.bundle.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/admin-lte@3.2/dist/js/adminlte.min.js"></script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/toastr.js/latest/toastr.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/js/select2.min.js"></script>
    
    <script>
        // Configuration Toastr
        toastr.options = {
            "closeButton": true,
            "progressBar": true,
            "positionClass": "toast-top-right",
            "timeOut": "5000"
        };
        
        // Affichage des messages flash
        @if(session('success'))
            toastr.success("{{ session('success') }}");
        @endif
        
        @if(session('error'))
            toastr.error("{{ session('error') }}");
        @endif
        
        @if(session('warning'))
            toastr.warning("{{ session('warning') }}");
        @endif
        
        @if(session('info'))
            toastr.info("{{ session('info') }}");
        @endif
        
        // CSRF Token pour AJAX
        $.ajaxSetup({
            headers: {
                'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
            }
        });
    </script>
    
    @stack('script')
    @stack('custom')
</body>
</html>