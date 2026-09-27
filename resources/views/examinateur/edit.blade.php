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
                    <div class="card-header">@lang('trans.update_medical_fitness') — {{ $examen->demandeur->np ?? '' }}</div>
                    <div class="card-body">
                        @include('examens_medicaux.partials.formulaire', [
                            'action' => route('examinateur.update', $examen),
                            'examen' => $examen,
                            'annulation' => route('examinateur'),
                        ])
                    </div>
                </div>
            </div>
        </div>
    </div>
@endsection
