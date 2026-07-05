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
        <div class="">
            <div class="col-md-12">



                <div class="card card-default">
                    <div class="card-header">
                        <span class="card-title">{{ __('Update') }} Compagny</span>
                    </div>
                    <div class="card-body">
                        <form method="POST" action="{{ route('compagnies.update', $compagny->id) }}" role="form"
                            enctype="multipart/form-data">
                            {{ method_field('PATCH') }}
                            @csrf

                            @include('admin.compagnies.form')

                        </form>
                    </div>
                </div>
            </div>
        </div>
    </section>
@endsection
