@extends('examinateur.layouts.app')
@section('title')
    @lang('trans.dashboard_examiner')
@endsection
@section('contentheader')
    @lang('trans.dashboard_examiner')
@endsection
@section('contentheaderlink')
    <a href="{{route('examinateur')}}">
        @lang('trans.dashboard_examiner') </a>
@endsection
@section('contentheaderactive')
    @lang('trans.dashboard_examiner')
@endsection
@section('content')
    <div class="container">
        <div class="row justify-content-center">
            <div class="col-md-8">
                <div class="card">
                    <div class="card-header">
                        <h4>📋 Nouvel examen médical</h4>
                    </div>
                    <div class="card-body">
                        @if (isset($licenceNumber) && $licenceNumber)
                            <div class="alert alert-info">
                                <strong>🔑 Licence associée :</strong> {{ $licenceNumber }}
                            </div>
                        @endif

                        <div class="alert alert-secondary">
                            <strong>👤 Demandeur :</strong> {{ $demandeur->np }}<br>
                            <strong>📅 Date naissance :</strong> {{ $demandeur->date_naissance }}
                        </div>

                        @include('examens_medicaux.partials.formulaire', [
                            'action' => route('examinateur.store'),
                            'demandeur' => $demandeur,
                            'annulation' => route('examinateur.search-licence'),
                        ])
                    </div>
                </div>
            </div>
        </div>
    </div>
@endsection
