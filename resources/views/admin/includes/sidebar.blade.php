<aside class="main-sidebar sidebar-dark-primary elevation-4">
    <!-- Brand Logo -->
    <a href="" class="brand-link">
        <img src="{{ asset('/assets/admin/imgs/logo.png') }}" alt="MRT Logo" class="brand-image img-circle elevation-3"
            style="opacity: .8">
        <span class="brand-text font-weight-light">ANAC</span>
    </a>
    <!-- Sidebar -->
    <div class="sidebar">
        <!-- Sidebar user panel (optional) Rida -->
        <div class="user-panel mt-3 pb-3 mb-3 d-flex">
            <div class="image">
                <img src="{{ asset('/assets/admin/imgs/default.png') }}"
                    class="img-profile rounded-circle avatar user-image" width="160px" height="160px"
                    alt="User Image" />
            </div>
            <div class="info">
                <a href="#" class="d-block"></a>
            </div>
        </div>
        <!-- Sidebar Menu -->
        <nav class="mt-2">
            <ul class="nav nav-pills nav-sidebar flex-column" data-widget="treeview" role="menu"
                data-accordion="false">
                <!-- Add icons to the links using the .nav-icon class
               with font-awesome or any other icon font library -->
                <li class="nav-item {{ Request::segment(1) === '/admin' ? 'active' : null }}">
                    <a href="{{ route('dashboard') }}" class="nav-link">
                        <i class="nav-icon fas fa-tachometer-alt"></i>
                        <p>
                            @lang('trans.dashboard_admin')

                        </p>
                    </a>
                </li>
                @can('manage-dsv')
                    <li class="nav-item {{ Request::segment(1) === '/admin' ? 'active' : null }}">
                        <a href="{{ route('demandes') }}" class="nav-link">
                            <i class="nav-icon fas fa-users"></i>
                            <p>
                                @lang('trans.applications')

                            </p>
                        </a>
                    </li>
                    <li class="nav-item {{ Request::segment(1) === '/admin' ? 'active' : null }}">
                        <a href="{{ route('demandeurs') }}" class="nav-link">
                            <i class="nav-icon fas fa-folder"></i>
                            <p>
                                @lang('trans.applicants')

                            </p>
                        </a>
                    </li>
                    <li class="nav-item {{ Request::segment(1) === '/admin' ? 'active' : null }}">
                        <a href="{{ route('licences') }}" class="nav-link">
                            <i class="nav-icon fas fa-id-card"></i>
                            <p>
                                @lang('trans.licences')

                            </p>
                        </a>
                    </li>
                    <li class="nav-item {{ Request::segment(1) === '/admin' ? 'active' : null }}">
                        <a href="{{ route('validations') }}" class="nav-link">
                            <i class="nav-icon fas fa-file"></i>
                            <p>
                                @lang('trans.validations')

                            </p>
                        </a>
                    </li>
                    <li class="nav-item {{ Request::segment(1) === '/admin' ? 'active' : null }}">
                        <a href="{{ route('cartes') }}" class="nav-link">
                            <i class="nav-icon fas fa-id-card"></i>
                            <p>
                                @lang('trans.cards')

                            </p>
                        </a>
                    </li>
                @endcan
                @can('manage-vi')
                    <li class="nav-item {{ Request::segment(1) === '/admin' ? 'active' : null }}">
                        <a href="{{ route('demandeAutorisations') }}" class="nav-link">
                            <i class="nav-icon fas fa-folder"></i>
                            <p>
                                @lang('trans.applications')

                            </p>
                        </a>
                    </li>
                    <li class="nav-item {{ Request::segment(1) === '/admin' ? 'active' : null }}">
                        <a href="{{ route('autorisations') }}" class="nav-link">
                            <i class="nav-icon fas fa-file"></i>
                            <p>
                                @lang('trans.autorisations')

                            </p>
                        </a>
                    </li>
                @endcan
                @can('manage-vr')
                    <li class="nav-item {{ Request::segment(1) === '/admin' ? 'active' : null }}">
                        <a href="{{ route('demandeApprobations') }}" class="nav-link">
                            <i class="nav-icon fas fa-folder"></i>
                            <p>
                                @lang('trans.applications')

                            </p>
                        </a>
                    </li>
                    <li class="nav-item {{ Request::segment(1) === '/admin' ? 'active' : null }}">
                        <a href="{{ route('approbations') }}" class="nav-link">
                            <i class="nav-icon fas fa-file"></i>
                            <p>
                                @lang('trans.approbations')

                            </p>
                        </a>
                    </li>
                @endcan
                <li class="nav-item">
                    <a href="" class="nav-link">
                        <i class="nav-icon fas fa-cog"></i>
                        <p>@lang('trans.management')

                            <i class="fas fa-angle-left right"></i>
                        </p>
                    </a>
                    <ul class="nav nav-treeview">
                        <li class="nav-item">
                            <a href="{{ url('admin/users') }}" class="nav-link">
                                <i class="far fa-user nav-icon"></i>
                                <p>@lang('trans.users')</p>
                            </a>
                        </li>
                        <li class="nav-item">
                            <a href="{{ url('admin/roles') }}" class="nav-link ">
                                <i class="fas fa-user-shield nav-icon"></i>
                                <p>@lang('trans.roles')</p>
                            </a>
                        </li>
                        @can('manage-dsv')
                            <li class="nav-item">
                                <a href="{{ url('admin/evaluateurs') }}" class="nav-link ">
                                    <i class="far fa-user nav-icon"></i>
                                    <p>@lang('trans.evaluators')</p>
                                </a>
                            </li>
                            
                            <li class="nav-item">
                                <a href="#" class="nav-link ">
                                    <i class="fas fa-user-md nav-icon"></i>
                                    <p>@lang('trans.examiners_management') <i class="fas fa-angle-left right"></i></p>
                                    
                                </a>
                                <ul class="nav nav-treeview">
                                <li class="nav-item">
                                <a href="{{ url('admin/examinateurs') }}" class="nav-link ">
                                    <i class="fas fa-user-md nav-icon"></i>
                                    <p>@lang('trans.medical_examiners')</p>
                                </a>
                            </li>
                            
                            <li class="nav-item">
                                <a href="{{ route('admin.examinateurs.index') }}" class="nav-link ">
                                    
                                    <i class="fas fa-user nav-icon"></i>
                                    <p>@lang('trans.examiners')</p>
                                </a>
                            </li>
                                </ul>
                            </li>
                            

                            <li class="nav-item">
                                <a href="{{ url('admin/autorites') }}" class="nav-link ">
                                    <i class="fas fa-check nav-icon"></i>
                                    <p>@lang('trans.autorites')</p>
                                </a>
                            </li>
                            <li class="nav-item">
                                <a href="{{ url('admin/centre-formations') }}" class="nav-link ">
                                    <i class="fa fa-school nav-icon"></i>
                                    <p>@lang('trans.training_center')</p>
                                </a>
                            </li>
                            <li class="nav-item">
                                <a href="{{ url('admin/simulateurs') }}" class="nav-link ">
                                    <i class="fa fa-school nav-icon"></i>
                                    <p>@lang('trans.simulators')</p>
                                </a>
                            </li>
                            <li class="nav-item">
                                <a href="{{ url('admin/qualifications') }}" class="nav-link ">
                                    <i class="fas fa-star nav-icon"></i>
                                    <p>@lang('trans.qualifications')</p>
                                </a>
                            </li>
                            <li class="nav-item">
                                <a href="{{ route('checklists.index') }}" class="nav-link ">
                                    <i class="fa fa-check-double"></i>
                                    <p>@lang('trans.checklist')</p>
                                </a>
                            </li>
                            <li class="nav-item">
                                <a href="{{ url('admin/type-documents') }}" class="nav-link ">
                                    <i class="far fa-circle nav-icon"></i>
                                    <p>@lang('trans.type-documents')</p>
                                </a>
                            </li>
                            <li class="nav-item">
                                <a href="{{ url('admin/type-demandes') }}" class="nav-link ">
                                    <i class="fa fa-folder nav-icon"></i>
                                    <p>@lang('trans.type-applications')</p>
                                </a>
                            </li>
                            <li class="nav-item">
                                <a href="{{ url('admin/settings') }}" class="nav-link ">
                                    <i class="far fa-circle nav-icon"></i>
                                    <p>@lang('trans.settings')</p>
                                </a>
                            </li>
                        @endcan
                        @can('manage-vi')
                        <li class="nav-item">
                                <a href="{{ url('admin/aeroports') }}" class="nav-link ">
                                    <i class="fas fa-plane-departure"></i>
                                    <p>@lang('trans.aeroports')</p>
                                </a>
                            </li>
                        @endcan
                        <li class="nav-item">
                            <a href="{{ url('admin/compagnies') }}" class="nav-link ">
                                <i class="far fa-circle nav-icon"></i>
                                <p>@lang('trans.compagny')</p>
                            </a>
                        </li>
                        <li class="nav-item">
                            <a href="{{ url('admin/type-avions') }}" class="nav-link ">
                                <i class="fa fa-plane nav-icon"></i>
                                <p>@lang('trans.type-airplanes')</p>
                            </a>
                        </li>


                    </ul>
                </li>

            </ul>
        </nav>
        <!-- /.sidebar-menu -->
    </div>
    <!-- /.sidebar -->
</aside>
