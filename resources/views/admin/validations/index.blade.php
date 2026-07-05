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
                    <div class="card-header">@lang('trans.validations')</div>
                    <div class="card-body">
                        <div class="table-responsive">
                            <table class="table table-bordered table-striped" id="validations">
                                <thead>
                                    <tr>

                                        <th>@lang('trans.category')</th>
                                        <th>@lang('trans.type')</th>
                                        <th>@lang('trans.license_number')</th>
                                        <th>@lang('trans.fl_name')</th>
                                        <th>@lang('trans.date')</th>
                                        <th>@lang('trans.start_date')</th>
                                        <th>@lang('trans.end_date')</th>
                                        <th>@lang('trans.actions')</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @foreach ($validations as $validation)
                                        <tr>
                                            <td>{{ $validation->typeLicence->categorie }}</td>
                                            <td>{{ $validation->typeLicence->nom }}</td>
                                            <td>{{ $validation->num_licence }}</td>
                                            <td>{{ $validation->demande->demandeur->np ?? 'NR' }}</td>
                                            <td>{{ date('d/m/Y', strtotime($validation->date_delivrance_licence)) }}</td>
                                            <td>{{ date('d/m/Y', strtotime($validation->date_debut_validite)) }}</td>
                                            <td>{{ date('d/m/Y', strtotime($validation->date_fin_validite)) }}</td>
                                            
                                            <td>
                                                <a href="{{ route('admin.validation', $validation) }}"
                                                                    class="btn btn-primary btn-sm" target="_blank">
                                                                    @lang('trans.print')
                                                                </a>
                                                <form action="{{ route('validations.supprimer', $validation) }}" method="POST"
                                                    class="d-inline">
                                                    @csrf
                                                    @method('PATCH')
                                                    <button type="submit" class="btn btn-danger btn-sm"
                                                        onclick="return confirm('Confirmer la suppression  de la validation ?')">@lang('trans.delete')</button>
                                                </form>
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
            $('#validations').DataTable({
                "paging": true,
                "lengthChange": false,
                "searching": true,
                "ordering": true,
                "info": true,
                "autoWidth": false,
                "responsive": true,
                "columnDefs": [{
                        "targets": 7,
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
