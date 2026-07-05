<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">

<head>
    <meta charset="utf-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <title>@lang('login.login') | ANAC</title>
    <meta name="viewport" content="width=device-width, initial-scale=1">
    
    <!-- CSRF Token -->
    <meta name="csrf-token" content="{{ csrf_token() }}">
    
    <!-- Font Awesome -->
    <link rel="stylesheet" href="{{ asset('assets/admin/plugins/fontawesome-free/css/all.min.css') }}">
    <!-- Ionicons -->
    <link rel="stylesheet" href="{{ asset('assets/admin/fonts/ionicons/2.0.1/css/ionicons.min.css') }}">
    <!-- icheck bootstrap -->
    <link rel="stylesheet" href="{{ asset('assets/admin/plugins/icheck-bootstrap/icheck-bootstrap.min.css') }}">
    <!-- Theme style -->
    <link rel="stylesheet" href="{{ asset('assets/admin/dist/css/adminlte.min.css') }}">
    <!-- Google Font: Source Sans Pro -->
    <link rel="stylesheet" href="{{ asset('assets/admin/fonts/SansPro/SansPro.min.css') }}">
    <link href="{{ asset('assets/admin/imgs/logo.png') }}" rel="icon" type="image/png">
    
    <style>
        .login-box-msg,
        .register-box-msg {
            margin: 0;
            padding: 0 20px 20px;
            text-align: center;
            font-size: 1.5vw;
        }
        
        /* Loading indicator for form submission */
        .btn-loading {
            position: relative;
            pointer-events: none;
            color: transparent;
        }
        
        .btn-loading::after {
            content: '';
            position: absolute;
            left: 50%;
            top: 50%;
            width: 20px;
            height: 20px;
            margin: -10px 0 0 -10px;
            border: 2px solid #ffffff;
            border-radius: 50%;
            border-right-color: transparent;
            animation: spin 1s linear infinite;
        }
        
        @keyframes spin {
            to { transform: rotate(360deg); }
        }
    </style>
</head>

<body class="hold-transition login-page">
    <div class="login-box">
        <div class="login-logo">
            <img src="{{ asset('assets/admin/imgs/logo.png') }}" class="rounded-circle avatar avatar font-weight-bold" alt="Logo Image" />
        </div>
        <div class="card card-outline card-primary">
            <div class="card-header text-center">
                <a href="#" class="h1"><b>ANAC</b></a>
            </div>
            <div class="card-body login-card-body">
                @if (Session::has('error'))
                    <div class="alert alert-danger" role="alert">
                        {{ Session::get('error') }}
                    </div>
                @endif
                
                @if (Session::has('status'))
                    <div class="alert alert-success" role="alert">
                        {{ Session::get('status') }}
                    </div>
                @endif
                
                <p class="login-box-msg">@lang('login.login')</p>
                
                <form action="{{ route('login') }}" method="post" id="loginForm">
                    @csrf
                    
                    <div class="input-group mb-3">
                        <input type="text" name="email" class="form-control" placeholder="{{ __('login.email') }}" value="{{ old('email') }}" required autofocus>
                        <div class="input-group-append">
                            <div class="input-group-text">
                                <span class="fas fa-envelope"></span>
                            </div>
                        </div>
                    </div>
                    @error('email')
                        <span class="text-danger">{{ $message }}</span>
                    @enderror
                    
                    <div class="input-group mb-3">
                        <input type="password" name="password" class="form-control" placeholder="{{ __('login.password') }}" required>
                        <div class="input-group-append">
                            <div class="input-group-text">
                                <span class="fas fa-lock"></span>
                            </div>
                        </div>
                    </div>
                    @error('password')
                        <span class="text-danger">{{ $message }}</span>
                    @enderror
                    
                    <div class="row">
                        <div class="col-12">
                            <button type="submit" class="btn btn-primary btn-block btn-flat" id="submitBtn">
                                @lang('login.enter')
                            </button>
                        </div>
                    </div>
                </form>

                <p class="mb-1">
                    <a href="{{ route('register') }}" class="text-center">Register a new membership</a>
                </p>
                <p class="mb-1">
                    <a href="{{ route('password.request') }}" class="text-center">{{ __('Reset Password') }}</a>
                </p>
            </div>
        </div>
    </div>

    <!-- jQuery -->
    <script src="{{ asset('assets/admin/plugins/jquery/jquery.min.js') }}"></script>
    <!-- Bootstrap 4 -->
    <script src="{{ asset('assets/admin/plugins/bootstrap/js/bootstrap.bundle.min.js') }}"></script>
    
    <script>
        $(document).ready(function() {
            // Setup CSRF token for all requests
            $.ajaxSetup({
                headers: {
                    'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
                }
            });

            // Prevent form double submission
            $('#loginForm').on('submit', function() {
                $('#submitBtn').addClass('btn-loading').prop('disabled', true);
            });

            // Refresh CSRF token periodically
            function refreshCsrfToken() {
                $.ajax({
                    url: '/refresh-csrf',
                    type: 'GET',
                    success: function(data) {
                        $('meta[name="csrf-token"]').attr('content', data.token);
                        $('input[name="_token"]').val(data.token);
                        $.ajaxSetup({
                            headers: {
                                'X-CSRF-TOKEN': data.token
                            }
                        });
                    }
                });
            }

            // Refresh token every 20 minutes
            setInterval(refreshCsrfToken, 20 * 60 * 1000);
        });
    </script>
</body>
</html>