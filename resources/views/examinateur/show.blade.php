@extends('examinateur.layouts.app')
@section('title')
    @lang('trans.dashboard_examiner')
@endsection
@section('contentheader')
    @lang('trans.dashboard_examiner')
@endsection
@section('contentheaderlink')
    <a href="{{ route('examinateur') }}">
        @lang('trans.dashboard_examiner') </a>
@endsection
@section('contentheaderactive')
    @lang('trans.dashboard_examiner')
@endsection
@section('content')
    <div class="container-fluid">
        <div class="row">
            <div class="col-md-12">
                <div class="card">
                    <div class="card-header bg-primary text-white">
                        @lang('trans.examen')
                    </div>
                    <div class="card-body">
                        <div class="row justify-content-center">
                            <div class="col-lg-9">
                                @include('examens_medicaux.partials.details', ['examen' => $examen])
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
@endsection
