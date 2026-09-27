@extends('sec.layouts.app')
@section('title')
    @lang('trans.examen')
@endsection
@section('contentheader')
    @lang('trans.examen')
@endsection
@section('contentheaderlink')
    <a href="{{ route('sma') }}">SMA</a>
@endsection
@section('contentheaderactive')
    @lang('trans.examen')
@endsection
@section('content')
    <div class="container-fluid">
        <div class="row justify-content-center">
            <div class="col-lg-9">
                <div class="card">
                    <div class="card-header bg-primary text-white">@lang('trans.examen')</div>
                    <div class="card-body">
                        @include('examens_medicaux.partials.details', ['examen' => $examen])

                        <div class="text-right">
                            @can('validerSma', $examen)
                                <form action="{{ route('sma.valider_examen', $examen) }}" method="POST" class="d-inline">
                                    @csrf
                                    @method('PATCH')
                                    <button type="submit" class="btn btn-warning" onclick="return confirm(@json(__('trans.confirmer_validation_sma')))">
                                        <i class="fas fa-check"></i> @lang('trans.validate')
                                    </button>
                                </form>
                            @endcan
                            <a href="{{ route('sma') }}" class="btn btn-secondary">@lang('trans.back')</a>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
@endsection
