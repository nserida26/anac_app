@props([
    'title',
    'pageTitle' => null,
])

@php
    $pageTitle = $pageTitle ?? $title;
    $demandeur = auth()->user()->demandeur;
@endphp
<!DOCTYPE html>
<html lang="{{ LaravelLocalization::getCurrentLocale() }}">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta http-equiv="x-ua-compatible" content="ie=edge">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>{{ $pageTitle }} | ANAC</title>

    <link href="{{ asset('assets/admin/imgs/logo.png') }}" rel="icon" type="image/png">
    <link href="{{ asset('assets/admin/fonts/SansPro/SansPro.min.css') }}" rel="stylesheet">
    <link rel="stylesheet" href="{{ asset('assets/admin/plugins/fontawesome-free/css/all.min.css') }}">
    <link rel="stylesheet" href="{{ asset('assets/admin/css/bootstrap-4.0.0-dist/css/bootstrap.css') }}">
    <link rel="stylesheet" href="{{ asset('css/user-app.css') }}">

    @stack('css')
</head>
<body class="anac-app">

    {{-- ═══ TOPBAR ═══ --}}
    <nav class="anac-topbar">
        <a href="{{ route('user') }}" class="anac-topbar__brand">
            <img src="{{ asset('assets/admin/imgs/logo.png') }}" alt="ANAC">
            <span>ANAC</span>
        </a>

        <span class="anac-topbar__title">{{ $pageTitle }}</span>

        <div class="anac-topbar__actions">
            @if ($demandeur && $demandeur->estExaminateurDesigne())
                <a href="{{ route('demandeur.dashboard') }}" class="anac-topbar__link">
                    @lang('trans.examinateur_dashboard')
                </a>
            @elseif ($demandeur && $demandeur->is_instructeur)
                <a href="{{ route('demandeur.dashboard') }}" class="anac-topbar__link">
                    @lang('trans.instructor_dashboard')
                </a>
            @endif

            {{-- Language switcher (même modèle que les pages d'authentification) --}}
            <div class="anac-lang">
                @foreach (LaravelLocalization::getSupportedLocales() as $localeCode => $properties)
                    <a href="{{ LaravelLocalization::getLocalizedURL($localeCode, null, [], true) }}"
                       rel="alternate" hreflang="{{ $localeCode }}"
                       class="lang-btn {{ LaravelLocalization::getCurrentLocale() === $localeCode ? 'active' : '' }}">
                        {{ strtoupper($localeCode) }}
                    </a>
                @endforeach
            </div>

            {{-- User dropdown --}}
            <div class="dropdown anac-user-menu">
                <button type="button" class="dropdown-toggle" id="userDropdown" data-toggle="dropdown"
                        aria-haspopup="true" aria-expanded="false">
                    <img src="{{ !empty($demandeur->photo) ? asset('uploads/' . $demandeur->photo) : asset('assets/admin/imgs/default.png') }}"
                         alt="{{ auth()->user()->name }}">
                </button>

                <div class="dropdown-menu dropdown-menu-right" aria-labelledby="userDropdown">
                    <a class="dropdown-item" href="{{ url('user/profile') }}">
                        <i class="fas fa-user"></i>
                        {{ auth()->user()->user_type === 'licence' ? __('trans.profile') : __('trans.company_profile') }}
                    </a>
                    <a class="dropdown-item" href="#" data-toggle="modal" data-target="#passwordUpdateModal"
                       data-user-id="{{ auth()->user()->id }}">
                        <i class="fas fa-key"></i>
                        {{ __('trans.change_password') }}
                    </a>

                    <div class="dropdown-divider"></div>

                    <a class="dropdown-item text-danger" href="#" data-toggle="modal" data-target="#logoutModal">
                        <i class="fas fa-sign-out-alt"></i>
                        @lang('trans.logout')
                    </a>
                </div>
            </div>
        </div>
    </nav>
    {{-- ═══ /TOPBAR ═══ --}}

    {{-- ═══ MAIN ═══ --}}
    <main class="anac-main">
        <div class="anac-page-header">
            <h1>{{ $pageTitle }}</h1>
        </div>

        {{-- Flash messages --}}
        @if (session('success'))
            <x-auth-alert type="success" :pre-line="true" :closable="true">{{ session('success') }}</x-auth-alert>
        @endif
        @if (session('error'))
            <x-auth-alert type="error" :pre-line="true" :closable="true">{{ session('error') }}</x-auth-alert>
        @endif
        @if (session('warning'))
            <x-auth-alert type="info" :pre-line="true" :closable="true">{{ session('warning') }}</x-auth-alert>
        @endif
        @if (session('status'))
            <x-auth-alert type="success" :closable="true">{{ session('status') }}</x-auth-alert>
        @endif

        {{ $slot }}
    </main>

    <footer class="anac-footer">
        <strong>Copyright &copy; {{ date('Y') }} <a href="https://anac.mr">ANAC</a>.</strong>
        @lang('trans.all_rights_reserved')
    </footer>
    {{-- ═══ /MAIN ═══ --}}

    {{-- ═══ MODALS ═══ --}}
    @include('user.layouts.partials.modals')

    <button id="scrollTopBtn" class="btn btn-primary" title="@lang('trans.back_to_top')">
        <i class="fas fa-arrow-up"></i>
    </button>

    {{-- ═══ SCRIPTS ═══ --}}
    <script src="{{ asset('assets/admin/plugins/jquery/jquery.min.js') }}"></script>
    <script src="{{ asset('assets/admin/plugins/bootstrap/js/bootstrap.bundle.min.js') }}"></script>

    @stack('script')

    <script>
        function openPdfModal(pdfUrl) {
            $("#pdfViewer").attr("src", pdfUrl);
            $("#pdfModal").modal("show");
        }
    </script>

    @stack('custom')

    <script>
        // Scroll to top
        (function() {
            var scrollTopBtn = document.getElementById("scrollTopBtn");

            window.onscroll = function() {
                scrollTopBtn.style.display =
                    (document.body.scrollTop > 100 || document.documentElement.scrollTop > 100) ? "block" : "none";
            };

            scrollTopBtn.onclick = function() {
                window.scrollTo({ top: 0, behavior: "smooth" });
            };
        })();

        // Password update (AJAX)
        $(document).ready(function() {
            $('#passwordUpdateModal').on('show.bs.modal', function(event) {
                var userId = $(event.relatedTarget).data('user-id');
                $('#passwordUpdateForm').attr('action', '/users/' + userId + '/password');
            });

            $('#passwordUpdateForm').submit(function(e) {
                e.preventDefault();

                var form = $(this);

                $.ajax({
                    type: "PUT",
                    url: form.attr('action'),
                    data: form.serialize(),
                    dataType: 'json',
                    success: function() {
                        $('#passwordUpdateModal').modal('hide');
                        form.trigger("reset");
                        toastr.success("{{ trans('trans.password_updated') }}");
                    },
                    error: function(xhr) {
                        var errors = xhr.responseJSON.errors;
                        var errorMessages = '';

                        $.each(errors, function(key, value) {
                            errorMessages += value[0] + '\n';
                        });

                        toastr.error(errorMessages || "{{ trans('trans.password_mismatch') }}");
                    }
                });
            });
        });
    </script>
</body>
</html>
