@extends('evaluateur.layouts.app')
@section('title')
    @lang('trans.dashboard_evaluator')
@endsection
@section('contentheader')
    @lang('trans.dashboard_evaluator')
@endsection
@section('contentheaderlink')
    <a href="{{ route('evaluateur') }}">
        @lang('trans.dashboard_evaluator') </a>
@endsection
@section('contentheaderactive')
    @lang('trans.dashboard_evaluator')
@endsection
@section('content')
    <div class="container-fluid">
        <div class="row">
            <div class="col-md-12">
                <div class="card">
                    <div class="card-header bg-primary text-white">
                        @lang('trans.exams')
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
