@extends('layouts.admin')
@section('title')
    @lang('trans.dashboard_admin')
@endsection
@section('contentheader')
    @lang('trans.dashboard_admin')
@endsection
@section('contentheaderlink')
    <a href="">
        @lang('trans.dashboard_admin') </a>
@endsection
@section('contentheaderactive')
    @lang('trans.dashboard_admin')
@endsection
@push('css')
    <link rel="stylesheet" href="{{ asset('assets/admin/plugins/datatables-bs4/css/dataTables.bootstrap4.min.css') }}">
    <link rel="stylesheet" href="{{ asset('assets/admin/plugins/datatables-responsive/css/responsive.bootstrap4.min.css') }}">
@endpush
@section('content')
    <div class="container-fluid">
        <div class="row">

            <div class="col-md-12">
                <div class="card">
                    <div class="card-header">@lang('trans.applicants')</div>
                    <div class="card-body">
                        <div class="table-responsive">
                            <table class="table table-bordered table-striped" id="demandeurs">
                                <thead>
                                    <tr>

                                        <th>@lang('trans.id')</th>
                                        <th>@lang('trans.photo')</th>
                                        <th>@lang('trans.fl_name')</th>
                                        
                                        <th>@lang('trans.dob')</th>
                                        <th>@lang('trans.address')</th>
                                        <th>@lang('trans.nationality')</th>
                                        <th>@lang('trans.actions')</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @foreach ($demandeurs as $demandeur)
                                        <tr>
                                            <td>{{ str_pad($demandeur->id, 4, '0', STR_PAD_LEFT) }}</td>
                                            <td><img src="{{ asset('/uploads/' . $demandeur->photo) }}" width="64"
                                                    height="64" class="card-img-top img-cover" alt=""></td>
                                            <td>{{ $demandeur->np }}</td>
                                            <td>{{ date('d/m/Y', strtotime($demandeur->date_naissance)) }}</td>
                                            <td>{{ $demandeur->adresse }}</td>
                                            <td>{{ strtoupper($demandeur->nationalite) }}</td>

                                            <td>
                                                <a href="{{ route('demandeurs.show', $demandeur) }}"
                                                    class="btn btn-info btn-sm">View</a>





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
            var table = $('#demandeurs').DataTable({
                "paging": true,
                "lengthChange": false,
                "searching": true,
                "ordering": true,
                "info": true,
                "autoWidth": false,
                "responsive": true,
                "columnDefs": [{
                        "targets": [1, 6],
                        "orderable": false,
                        "searchable": false
                    }
                ]

            });

            // Add simple search inputs for other columns (skip photo and actions)
            table.columns().every(function(index) {
                if (index !== 1 && index !== 6) {
                    var column = this;
                    var $input = $('<input type="text" placeholder="@lang("trans.search")" class="form-control form-control-sm">')
                        .appendTo($(column.header()))
                        .on('keyup change', function() {
                            column.search(this.value).draw();
                        });
                }
            });
        });
    </script>
@endpush
