<!DOCTYPE html>
<!--
   This is a starter template page. Use this page to start your new project from
   scratch. This page gets rid of all links and provides the needed markup only.
   -->
<html lang="{{ LaravelLocalization::getCurrentLocale() }}">

<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta http-equiv="x-ua-compatible" content="ie=edge">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>@yield('title') | ANAC</title>
    <link href="{{ asset('assets/admin/imgs/logo.png') }}" rel="icon" type="image/png">
    <!-- Font Awesome Icons -->
    <link rel="stylesheet" href="{{ asset('assets/admin/plugins/fontawesome-free/css/all.min.css') }}">

    @stack('css')
    <style>
        #scrollTopBtn {
            display: none;
            position: fixed;
            bottom: 20px;
            right: 20px;
            z-index: 1000;
            border: none;
            outline: none;
            background-color: #007bff;
            color: white;
            cursor: pointer;
            padding: 10px 15px;
            border-radius: 50%;
            font-size: 18px;
        }

        #scrollTopBtn:hover {
            background-color: #0056b3;
        }
    </style>
    <!-- Theme style -->
    <link rel="stylesheet" href="{{ asset('assets/admin/dist/css/adminlte.min.css') }}">
</head>

<body class="hold-transition layout-top-nav">

    <div class="wrapper">
        <!-- Navbar -->
        <nav class="main-header navbar navbar-expand-lg navbar-white navbar-light">
<button class="navbar-toggler order-1" type="button" data-toggle="collapse"
    data-target="#navbarCollapse" aria-controls="navbarCollapse"
    aria-expanded="false" aria-label="Toggle navigation">
    <span class="navbar-toggler-icon"></span>
</button>

            <a href="{{ route('user') }}" class="navbar-brand">
                <img src="{{ asset('assets/admin/imgs/logo.png') }}" alt="ANAC Logo"
                    class="brand-image img-circle elevation-3" style="opacity: .8">
                <span class="brand-text font-weight-light">ANAC</span>
            </a>
            <div class="collapse navbar-collapse order-3" id="navbarCollapse">
                <!-- Left navbar links -->
                <ul class="navbar-nav">
@if(auth()->user()->demandeur && auth()->user()->demandeur->is_examinateur)
    <li class="nav-item">
        <a href="{{ route('demandeur.dashboard') }}" class="nav-link">@lang('trans.examinateur_dashboard')</a>
    </li>

@elseif(auth()->user()->demandeur && auth()->user()->demandeur->is_instructeur)
    <li class="nav-item">
        <a href="{{ route('demandeur.dashboard') }}" class="nav-link">@lang('trans.instructor_dashboard')</a>
    </li>
@endif

                </ul>


            </div>
            <!-- Right navbar links -->

            <ul class="order-1 order-md-3 navbar-nav navbar-no-expand ml-auto">
                <li class="nav-item dropdown no-arrow">
                    <a class="nav-link dropdown-toggle" href="#" id="userDropdown" role="button"
                        data-toggle="dropdown" aria-haspopup="true" aria-expanded="false">
                        <!-- Rida -->
                        
                        @if(!empty(auth()->user()->demandeur->photo))
                            <img src="{{ asset('uploads/'. auth()->user()->demandeur->photo) }}"
                            class="img-profile rounded-circle avatar user-image" width="32px" height="32px"
                            alt="User Image" />
                        @else
                            <img src="{{ asset('/assets/admin/imgs/default.png') }}"
                            class="img-profile rounded-circle avatar user-image" width="32px" height="32px"
                            alt="User Image" />
                        @endif

                    </a>
                    <!-- Dropdown - User Information -->
                    <div class="dropdown-menu dropdown-menu-right shadow animated--grow-in"
                        aria-labelledby="userDropdown">
                        <a class="dropdown-item" href="{{ url('user/profile') }}">
                            <i class="fas fa-user fa-sm fa-fw mr-2 text-gray-400"></i>
                            @if(auth()->user()->user_type == 'licence')
                            {{ __('trans.profile') }}
                            @else
                            {{ __('trans.company_profile') }}
                            @endif
                        </a>

                        <a class="dropdown-item" data-toggle="modal" data-target="#passwordUpdateModal"
                            data-user-id="{{ auth()->user()->id }}">
                            <i class="fas fa-edit fa-sm fa-fw mr-2 text-gray-400"></i>
                            {{ __('trans.change_password') }}
                        </a>
                        <div class="dropdown-divider"></div>
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
        <!-- /.navbar -->
        <!-- Main Sidebar Container -->
        {{-- @include('admin.includes.sidebar') --}}
        <!--  End Main Sidebar Container -->
        <!-- Content Wrapper. Contains page content -->
        @include('admin.includes.content')
        <!-- /.content-wrapper -->
        @include('admin.includes.footer')
        <!-- Main Footer -->
    </div>
    <!-- ./wrapper -->
    <!-- Password Update Modal -->
    <div class="modal fade" id="passwordUpdateModal" tabindex="-1" role="dialog"
        aria-labelledby="passwordUpdateModalLabel">
        <div class="modal-dialog" role="document">
            <div class="modal-content">
                <div class="modal-header">
                    <h4 class="modal-title" id="passwordUpdateModalLabel">{{ trans('trans.update_password') }} </h4>
                    <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                        <span aria-hidden="true">&times;</span>
                    </button>
                </div>
                <form id="passwordUpdateForm" method="POST">
                    @csrf
                    @method('PUT')
                    <div class="modal-body">
                        <div class="form-group">
                            <label for="current_password">{{ trans('trans.current_password') }}</label>
                            <input type="password" class="form-control" id="current_password" name="current_password"
                                required>
                        </div>
                        <div class="form-group">
                            <label for="new_password">{{ trans('trans.new_password') }}</label>
                            <input type="password" class="form-control" id="new_password" name="new_password" required>
                        </div>
                        <div class="form-group">
                            <label for="new_password_confirmation">{{ trans('trans.confirm_password') }}</label>
                            <input type="password" class="form-control" id="new_password_confirmation"
                                name="new_password_confirmation" required>
                        </div>
                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-default"
                            data-dismiss="modal">{{ trans('close') }}</button>
                        <button type="submit" class="btn btn-primary">{{ trans('trans.update') }}</button>
                    </div>
                </form>
            </div>
        </div>
    </div>
    <div class="modal fade" id="pdfModal" tabindex="-1" role="dialog" aria-labelledby="pdfModalLabel"
        aria-hidden="true">
        <div class="modal-dialog modal-lg">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="pdfModalLabel">{{ trans('trans.pdf_preview') }}</h5>
                </div>
                <div class="modal-body">
                    <iframe id="pdfViewer" src="" width="100%" height="500px"></iframe>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary"
                        data-dismiss="modal">{{ trans('trans.close') }}</button>
                </div>
            </div>
        </div>
    </div>

    <!-- Logout Modal-->
    <div class="modal fade" id="logoutModal" tabindex="-1" role="dialog" aria-labelledby="exampleModalLabel"
        aria-hidden="true">
        <div class="modal-dialog" role="document">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="exampleModalLabel">{{ __('Ready to Leave?') }}</h5>
                    <button class="close" type="button" data-dismiss="modal" aria-label="Close">
                        <span aria-hidden="true">×</span>
                    </button>
                </div>
                <div class="modal-body">{{ trans('trans.logout_word') }}</div>
                <div class="modal-footer">
                    <button class="btn btn-link" type="button"
                        data-dismiss="modal">{{ __('trans.cancel') }}</button>


                    <a href="{{ route('logout') }}" class="btn btn-danger">{{ trans('trans.logout') }}</a>
                </div>
            </div>
        </div>
    </div>
    <button id="scrollTopBtn" class="btn btn-primary" title="Retour en haut">
        <i class="fas fa-arrow-up"></i>
    </button>
    <!-- REQUIRED SCRIPTS -->
    <!-- jQuery -->
    <script src="{{ asset('assets/admin/plugins/jquery/jquery.min.js') }}"></script>
    <!-- Bootstrap 4 -->
    <script src="{{ asset('assets/admin/plugins/bootstrap/js/bootstrap.bundle.min.js') }}"></script>
    @stack('script')
    <!-- AdminLTE App -->
    <script src="{{ asset('assets/admin/dist/js/adminlte.min.js') }}"></script>

    @stack('custom')
    <script>
        function openPdfModal(pdfUrl) {
            console.log(pdfUrl);

            $("#pdfViewer").attr("src", pdfUrl);
            $("#pdfModal").modal("show");
        }
    </script>
    <script>
        document.addEventListener("DOMContentLoaded", function() {
            let scrollTopBtn = document.getElementById("scrollTopBtn");

            window.onscroll = function() {
                if (document.body.scrollTop > 100 || document.documentElement.scrollTop > 100) {
                    scrollTopBtn.style.display = "block";
                } else {
                    scrollTopBtn.style.display = "none";
                }
            };

            scrollTopBtn.onclick = function() {
                window.scrollTo({
                    top: 0,
                    behavior: "smooth"
                });
            };
        });
        $(document).ready(function() {
            $('#passwordUpdateForm').submit(function(e) {
                e.preventDefault();

                var form = $(this);
                var url = form.attr('action');

                $.ajax({
                    type: "PUT",
                    url: url,
                    data: form.serialize(),
                    dataType: 'json',
                    success: function(response) {
                        $('#passwordUpdateModal').modal('hide');
                        form.trigger("reset");

                        // Show success message (using Toastr as example)

                        toastr.success("{{ trans('trans.password_updated') }}");

                    },
                    error: function(xhr) {
                        var errors = xhr.responseJSON.errors;
                        var errorMessages = '';

                        $.each(errors, function(key, value) {
                            errorMessages += value[0] + '\n';
                        });

                        // Show error message
                        toastr.error("{{ trans('trans.password_mismatch') }}");
                    }
                });
            });

            // Set the form action when modal is shown
            $('#passwordUpdateModal').on('show.bs.modal', function(event) {
                var button = $(event.relatedTarget); // Button that triggered the modal
                var userId = button.data('user-id'); // Extract info from data-* attributes
                var form = $('#passwordUpdateForm');
                form.attr('action', '/users/' + userId + '/password');
            });
        });
    </script>


</body>

</html>
