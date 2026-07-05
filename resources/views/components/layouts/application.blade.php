@props(['title' => 'ANAC', 'description' => 'ANAC'])

<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}" class="scroll-smooth">

<head>
    <meta charset="utf-8" />
    <title>{{ $title }} | {{ config('app.name') }}</title>
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="description" content="{{ $description }}" />
    <meta name="author" content="ANAC" />

    <!-- App favicon -->
    <link rel="shortcut icon" href="{{ asset('assets/admin/imgs/logo.png') }}">

    <!-- Google Fonts -->
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>

    <!-- Tailwind CSS (CDN) -->
    <script src="https://cdn.tailwindcss.com"></script>
    <script>
        tailwind.config = {
            theme: {
                extend: {
                    fontFamily: { sans: ['Inter', 'sans-serif'] },
                    colors: {
                        navy: { 950: '#060f28', 900: '#0a1a43', 800: '#0e2258', 700: '#142e70', 600: '#1a3a8a' },
                        gold: { 300: '#eecb60', 400: '#d4af37', 500: '#c9a84c', 600: '#b08a2e' },
                    },
                },
            },
        }
    </script>

    <!-- Bootstrap 5 CSS (for modals, etc.) -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">

    <!-- Alpine.js for dropdowns & mobile nav -->
    <script defer src="https://unpkg.com/alpinejs@3.x.x/dist/cdn.min.js"></script>
    <link href="{{ asset('assets/admin/plugins/sweetalert2/sweetalert2.min.css') }}" rel="stylesheet">

    @stack('css')    <!-- Select2 CSS -->
    <link href="{{ asset('assets/admin/plugins/select2/css/select2.min.css') }}" rel="stylesheet">
    <style>
        /* Force-hide original select after Select2 init */
        select.select2-hidden-accessible {
            display: none !important;
        }
        /* Style Select2 to match Bootstrap 5 form controls */
        .select2-container--default .select2-selection--single {
            height: 42px;
            border: 1px solid #d1d5db;
            border-radius: 0.5rem;
        }
        .select2-container--default .select2-selection--single .select2-selection__rendered {
            line-height: 40px;
            padding-left: 12px;
            color: #1e293b;
            font-size: 0.875rem;
        }
        .select2-container--default .select2-selection--single .select2-selection__arrow {
            height: 40px;
        }
        .select2-container--default .select2-selection--multiple {
            min-height: 42px;
            border: 1px solid #d1d5db;
            border-radius: 0.5rem;
        }
        .select2-container--default.select2-container--focus .select2-selection--single,
        .select2-container--default.select2-container--focus .select2-selection--multiple {
            border-color: #d4af37;
            box-shadow: 0 0 0 0.2rem rgba(212, 175, 55, 0.25);
        }
        .select2-container--default .select2-results__option--highlighted {
            background-color: #d4af37 !important;
            color: #fff !important;
        }
        .select2-container--open {
            z-index: 9999 !important;
        }
        .modal-open .select2-container--open {
            z-index: 1060 !important;
        }
    </style>

    <!-- Custom Theme CSS (load last to override libraries like select2, etc.) -->
    <link href="{{ asset('assets/custom.css') }}" rel="stylesheet">
</head>

<body class="app-body">

    <div class="app-layout-wrapper" x-data="{ sidebarOpen: false, langOpen: false, userOpen: false }">

        {{-- Mobile Nav Overlay --}}
        <div x-cloak x-show="sidebarOpen"
             x-transition:enter="transition-opacity ease-linear duration-200"
             x-transition:enter-start="opacity-0"
             x-transition:enter-end="opacity-100"
             x-transition:leave="transition-opacity ease-linear duration-200"
             x-transition:leave-start="opacity-100"
             x-transition:leave-end="opacity-0"
             class="mobile-nav-overlay"
             @@click="sidebarOpen = false"></div>

        {{-- Mobile Nav Panel --}}
        <div x-cloak x-show="sidebarOpen"
             x-transition:enter="transition ease-in-out duration-200 transform"
             x-transition:enter-start="-translate-x-full"
             x-transition:enter-end="translate-x-0"
             x-transition:leave="transition ease-in-out duration-200 transform"
             x-transition:leave-start="translate-x-0"
             x-transition:leave-end="-translate-x-full"
             class="mobile-nav-panel"
             @@click.away="sidebarOpen = false">
            <button @@click="sidebarOpen = false" class="mobile-nav-close">&times;</button>
            <div class="mt-8">
                <x-layouts.application.top-navigation />
            </div>
        </div>

        {{-- Topbar --}}
        <x-layouts.application.top-bar />

        {{-- Top Navigation --}}
        <x-layouts.application.top-navigation />

        {{-- Main Content --}}
        <main class="app-content">
            {{ $slot }}
        </main>

        {{-- Footer --}}
        <x-layouts.application.footer />


    </div>

    <div class="modal fade" id="pdfModal" tabindex="-1" role="dialog" aria-labelledby="pdfModalLabel"
         aria-hidden="true">
        <div class="modal-dialog modal-lg">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="pdfModalLabel">PDF Preview</h5>
                </div>
                <div class="modal-body">
                    <iframe id="pdfViewer" src="" width="100%" height="500px"></iframe>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Close</button>
                </div>
            </div>
        </div>
    </div>

    {{-- Hyper JS bundles (for existing DataTables, Select2, etc.) --}}
    <script src="{{ asset('assets/hyper/js/vendor.min.js') }}"></script>
    <script src="{{ asset('assets/hyper/js/app.min.js') }}"></script>
    <script src="{{ asset('assets/hyper/js/vendor/apexcharts.min.js') }}"></script>
    <script src="{{ asset('assets/hyper/js/vendor/jquery-jvectormap-1.2.2.min.js') }}"></script>
    <script src="{{ asset('assets/hyper/js/vendor/jquery-jvectormap-world-mill-en.js') }}"></script>
    <script src="{{ asset('assets/hyper/js/pages/demo.dashboard.js') }}"></script>
    <script src="{{ asset('assets/admin/plugins/sweetalert2/sweetalert2.all.min.js') }}"></script>

    <script>
        function openPdfModal(pdfUrl) {
            console.log(pdfUrl);

            $("#pdfViewer").attr("src", pdfUrl);
            $("#pdfModal").modal("show");
        }
    </script>

    @stack('script')
    @stack('custom')
</body>

</html>
