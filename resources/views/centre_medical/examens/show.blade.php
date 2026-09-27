{{-- resources/views/centre_medical/examens/show.blade.php --}}
@extends('centre.layouts.app')

@section('title')
    @lang('trans.examen')
@endsection

@section('contentheader')
    @lang('trans.examen')
@endsection

@section('contentheaderlink')
    <a href="{{ route('centre_medical.examens') }}">@lang('trans.rapports_medicaux')</a>
@endsection

@section('contentheaderactive')
    {{ $examen->demandeur->np ?? '' }}
@endsection

@section('content')
<div class="container-fluid">
    <div class="row justify-content-center">
        <div class="col-lg-9">
            <div class="card">
                <div class="card-body">
                    @include('examens_medicaux.partials.details', ['examen' => $examen])
                    <a href="{{ route('centre_medical.examens') }}" class="btn btn-secondary">@lang('trans.back')</a>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection
