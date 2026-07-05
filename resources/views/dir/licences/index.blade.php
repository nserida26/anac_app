@extends('dir.layouts.app')
@section('title')
    @lang('trans.dashboard_dir', ['role' => strtoupper(auth()->user()->getRoleNames()->first() ?? '')])
@endsection
@section('contentheader')
    @lang('trans.dashboard_dir', ['role' => strtoupper(auth()->user()->getRoleNames()->first() ?? '')])
@endsection
@section('contentheaderlink')
    @if (auth()->user()->hasRole('dsv'))
        <a href="{{ route('dsv') }}">
            @lang('trans.dashboard_dir', ['role' => strtoupper(auth()->user()->getRoleNames()->first() ?? '')]) </a>
    @endif
    @if (auth()->user()->hasRole('dg'))
        <a href="{{ route('dg') }}">
            @lang('trans.dashboard_dir', ['role' => strtoupper(auth()->user()->getRoleNames()->first() ?? '')]) </a>
    @endif
    @if (auth()->user()->hasRole('dta'))
        <a href="{{ route('dta') }}">
            @lang('trans.dashboard_dir', ['role' => strtoupper(auth()->user()->getRoleNames()->first() ?? '')]) </a>
    @endif
    @if (auth()->user()->hasRole('dsna'))
        <a href="{{ route('dsna') }}">
            @lang('trans.dashboard_dir', ['role' => strtoupper(auth()->user()->getRoleNames()->first() ?? '')])</a>
    @endif
    @if (auth()->user()->hasRole('dsad'))
        <a href="{{ route('dsad') }}">
            @lang('trans.dashboard_dir', ['role' => strtoupper(auth()->user()->getRoleNames()->first() ?? '')]) </a>
    @endif
@endsection
@section('contentheaderactive')
    @lang('trans.dashboard_dir', ['role' => strtoupper(auth()->user()->getRoleNames()->first() ?? '')])
@endsection
@push('css')
    <link rel="stylesheet" href="{{ asset('assets/admin/plugins/datatables-bs4/css/dataTables.bootstrap4.min.css') }}">
    <link rel="stylesheet" href="{{ asset('assets/admin/plugins/datatables-responsive/css/responsive.bootstrap4.min.css') }}">
@endpush
@section('content')
    <div class="container-fluid">
        @if ($licences->isNotEmpty())
            <div class="row">

                <div class="col-md-12">
                    <div class="card">
                        <div class="card-header">@lang('trans.licenses')</div>
                        <div class="card-body">
                            <div class="table-responsive">
                                <table class="table table-bordered table-striped" id="licenses">
                                    <thead>
                                        <tr>
                                            <th>@lang('trans.category')</th>
                                            <th>@lang('trans.type')</th>
                                            <th>@lang('trans.license_number')</th>
                                            <th>@lang('trans.expiration_date')</th>
                                            <th>@lang('trans.fl_name')</th>
                                            <th>@lang('trans.dob')</th>
                                            <th>@lang('trans.address')</th>
                                            <th>@lang('trans.nationality')</th>
                                            <th>@lang('trans.status')</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        @foreach ($licences as $licence)
                                            <tr>
                                                <td>{{ $licence->categorie_licence }}</td>
                                                <td>{{ $licence->type_licence }}</td>
                                                <td>{{ $licence->numero_licence }}</td>
                                                <td>{{ date('d/m/Y', strtotime($licence->date_expiration)) }}</td>
                                                <td>{{ $licence->np }}</td>
                                                <td>{{ date('d/m/Y', strtotime($licence->date_naissance)) }}</td>
                                                <td>{{ $licence->adresse }}</td>
                                                <td>{{ strtoupper($licence->nationalite) }}</td>

                                                <td>


                                                    @if (is_array($licence->expiry_status))
                                                        <span class="badge badge-success">@lang('trans.license_status.' . $licence->expiry_status['key'], $licence->expiry_status)</span>
                                                    @else
                                                        <span class="badge badge-danger">@lang('trans.license_status.' . $licence->expiry_status)</span>
                                                    @endif
                                                </td>
                                            </tr>
                                        @endforeach
                                    </tbody>
                                </table>

                            </div>

                        </div>
                    </div>
                </div>
            </div>
        @endif

    </div>
@endsection
@push('script')
    <script src="{{ asset('assets/admin/plugins/datatables/jquery.dataTables.min.js') }}"></script>
    <script src="{{ asset('assets/admin/plugins/datatables-bs4/js/dataTables.bootstrap4.min.js') }}"></script>
    <script src="{{ asset('assets/admin/plugins/datatables-responsive/js/dataTables.responsive.min.js') }}"></script>
    <script src="{{ asset('assets/admin/plugins/datatables-responsive/js/responsive.bootstrap4.min.js') }}"></script>
@endpush
@push('custom')
    <script>
        $(function() {
            $('#licenses').DataTable({
                "paging": true,
                "lengthChange": false,
                "searching": true,
                "ordering": true,
                "info": true,
                "autoWidth": false,
                "responsive": true,
                "columnDefs": [{
                        "targets": 5,
                        "orderable": false
                    },
                    {
                        "targets": 3,
                        "searchable": true
                    }
                ]

            });
        });
    </script>
@endpush
