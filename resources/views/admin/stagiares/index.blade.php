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
                    <div class="card-header">
                        @lang('trans.cards')
                        @if($dateFrom || $dateTo)
                            <span class="badge badge-info ml-2">@lang('trans.date_range'): {{ $dateFrom }} - {{ $dateTo }}</span>
                        @endif
                    </div>
                    <div class="card-body">
                        <div class="filter-section" style="background:#f8f9fa;padding:15px;border-radius:5px;margin-bottom:20px;">
                            <form method="GET" action="{{ route('cartes') }}" class="row align-items-end">
                                <div class="col-md-3">
                                    <div class="form-group mb-0">
                                        <label>@lang('trans.from')</label>
                                        <input type="date" name="date_from" class="form-control" value="{{ $dateFrom }}">
                                    </div>
                                </div>
                                <div class="col-md-3">
                                    <div class="form-group mb-0">
                                        <label>@lang('trans.to')</label>
                                        <input type="date" name="date_to" class="form-control" value="{{ $dateTo }}">
                                    </div>
                                </div>
                                <div class="col-md-3">
                                    <button type="submit" class="btn btn-primary">
                                        <i class="fas fa-filter"></i> @lang('trans.apply_filters')
                                    </button>
                                    <a href="{{ route('cartes') }}" class="btn btn-secondary">@lang('trans.reset_filters')</a>
                                </div>
                            </form>
                        </div>
                        <div class="table-responsive">
                            <table class="table table-bordered table-striped" id="cartes">
                                <thead>
                                    <tr>
                                        <th>@lang('trans.card_number')</th>
                                        <th>@lang('trans.fl_name')</th>
                                        <th>@lang('trans.dob')</th>
                                        <th>@lang('trans.address')</th>
                                        <th>@lang('trans.nationality')</th>

                                        <th>@lang('trans.actions')</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @foreach ($cartes as $carte)
                                        <tr>

                                            <td>{{ $carte->numero_carte }}</td>
                                            <td>{{ $carte->np }}</td>
                                            <td>{{ date('d/m/Y', strtotime($carte->date_naissance)) }}</td>
                                            <td>{{ $carte->adresse }}</td>
                                            <td>{{ strtoupper($carte->nationalite) }}</td>
                                            <td>

                                                <a href="{{ route('cartes.imprimer', $carte->demande->id) }}"
                                                        class="btn btn-primary btn-sm"
                                                        target="_blank">@lang('trans.print')</a>
                                                <form action="{{ route('cartes.supprimer', $carte) }}" method="POST"
                                                    class="d-inline">
                                                    @csrf
                                                    @method('PATCH')
                                                    <button type="submit" class="btn btn-danger btn-sm"
                                                        onclick="return confirm('Confirmer la suppression  de la carte ?')">@lang('trans.delete')</button>
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
            var table = $('#cartes').DataTable({
                "paging": true,
                "lengthChange": false,
                "searching": true,
                "ordering": true,
                "info": true,
                "autoWidth": false,
                "responsive": true,
                "columnDefs": [{
                        "targets": [5],
                        "orderable": false,
                        "searchable": false
                    }
                ]

            });

            // Add simple search inputs for other columns
            table.columns().every(function(index) {
                if (index !== 5) {
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
