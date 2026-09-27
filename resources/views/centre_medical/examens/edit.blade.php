{{-- resources/views/centre_medical/examens/edit.blade.php --}}
@extends('centre.layouts.app')

@section('title')
    @lang('trans.update_medical_fitness')
@endsection

@section('contentheader')
    @lang('trans.update_medical_fitness') — {{ $examen->demandeur->np ?? '' }}
@endsection

@section('contentheaderlink')
    <a href="{{ route('centre_medical.examens') }}">@lang('trans.rapports_medicaux')</a>
@endsection

@section('contentheaderactive')
    @lang('trans.edit')
@endsection

@section('content')
<div class="container-fluid">
    <div class="row justify-content-center">
        <div class="col-lg-8">
            <div class="card card-primary card-outline">
                <div class="card-body">
                    @include('examens_medicaux.partials.formulaire', [
                        'action' => route('centre_medical.examens.update', $examen),
                        'examen' => $examen,
                        'examinateurs' => $examinateurs,
                        'annulation' => route('centre_medical.examens'),
                    ])
                </div>
            </div>
        </div>
    </div>
</div>
@endsection
