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
    <section class="content container-fluid">
        <div class="row">
            <div class="col-md-12">
                <div class="card">
                    <div class="card-header">
                        <div class="float-left">
                            <span class="card-title">{{ __('Show') }} Type Document</span>
                        </div>
                        <div class="float-right">
                            <a class="btn btn-primary" href="{{ route('type-documents.index') }}"> {{ __('Back') }}</a>
                        </div>
                    </div>

                    <div class="card-body">

                        <div class="form-group">
                            <strong>Type Licence :</strong>
                            {{ $typeDocument->typeLicence->nom }}
                        </div>
                        <div class="form-group">
                            <strong>Type Demande :</strong>
                            {{ $typeDocument->typeDemande->nom_fr }}
                        </div>
                        <div class="form-group">
                            <strong>Nom Fr:</strong>
                            {{ $typeDocument->nom_fr }}
                        </div>
                        <div class="form-group">
                            <strong>Nom En:</strong>
                            {{ $typeDocument->nom_en }}
                        </div>

                    </div>
                </div>
            </div>
        </div>
    </section>
@endsection
