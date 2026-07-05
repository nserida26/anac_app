@extends('agent.layouts.app')
@section('title')
    @lang('trans.dashboard_agent')
@endsection
@section('contentheader')
    @lang('trans.dashboard_agent')
@endsection
@section('contentheaderlink')
    <a href="{{ route('agent') }}">
        @lang('trans.dashboard_agent') </a>
@endsection
@section('contentheaderactive')
    @lang('trans.dashboard_agent')
@endsection
@push('css')
    <link rel="stylesheet" href="{{ asset('assets/admin/plugins/datatables-bs4/css/dataTables.bootstrap4.min.css') }}">
    <link rel="stylesheet" href="{{ asset('assets/admin/plugins/datatables-responsive/css/responsive.bootstrap4.min.css') }}">
@endpush
@section('content')
    <div class="container-fluid">
        <div class="row">
            <div class="col-lg-12 order-lg-12">
                <div class="card">
                    <div class="card-header">@lang('trans.folder')</div>
                    <div class="card-body">
                        <form action="{{ route('agent.dossier', $demandeur->id) }}" method="POST"
                            enctype="multipart/form-data">
                            @csrf
                            <div class="form-group">

                                <input type="file" class="form-control" id="dossier" name="dossier"
                                    accept="application/pdf">
                            </div>
                            <button type="submit" class="btn btn-success float-right"><i class="fas fa-plus"></i>
                                @lang('trans.send')</button>
                        </form>
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
@endpush
