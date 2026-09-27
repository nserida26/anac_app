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
            <div class="col-lg-6">
                <div class="card">
                    <div class="card-header">@lang('trans.examen')</div>
                    <div class="card-body">
                        @include('examens_medicaux.partials.details', ['examen' => $examen])
                    </div>
                </div>
            </div>

            <div class="col-lg-6">
                <div class="card card-primary card-outline">
                    <div class="card-header">@lang('trans.avis_de_l_evaluateur')</div>
                    <div class="card-body">
                        <form action="{{ route('evaluateur.update', $examen) }}" method="POST" enctype="multipart/form-data">
                            @csrf

                            <div class="form-group">
                                <label>@lang('trans.avis') <span class="text-danger">*</span></label>
                                @foreach (['valide' => 'trans.avis_valide', 'reserve' => 'trans.avis_reserve', 'suggestion' => 'trans.avis_suggestion'] as $valeur => $libelle)
                                    <div class="custom-control custom-radio">
                                        <input type="radio" id="avis_{{ $valeur }}" name="avis_evaluateur" value="{{ $valeur }}"
                                            class="custom-control-input" required
                                            {{ old('avis_evaluateur', $examen->avis_evaluateur) === $valeur ? 'checked' : '' }}>
                                        <label class="custom-control-label" for="avis_{{ $valeur }}">@lang($libelle)</label>
                                    </div>
                                @endforeach
                            </div>

                            <div class="form-group">
                                <label for="observations_evaluateur">@lang('trans.observations')</label>
                                <textarea name="observations_evaluateur" id="observations_evaluateur" class="form-control" rows="4">{{ old('observations_evaluateur', $examen->observations_evaluateur) }}</textarea>
                                <small class="form-text text-muted">@lang('trans.observations_obligatoires_reserve')</small>
                            </div>

                            <div class="form-group">
                                <label for="validite_evaluateur">@lang('trans.validity_evaluator') <span class="text-danger">*</span></label>
                                <input type="number" min="1" max="{{ (int) $examen->validite }}" name="validite_evaluateur" id="validite_evaluateur"
                                    class="form-control" required
                                    value="{{ old('validite_evaluateur', $examen->validite_evaluateur ?? $examen->validite) }}">
                                <small class="form-text text-muted">
                                    @lang('trans.validite_reduction_seulement', ['max' => (int) $examen->validite])
                                </small>
                            </div>

                            <div class="form-group">
                                <label for="rapport_evaluateur">@lang('trans.report_by_evaluator')</label>
                                <input type="file" name="rapport_evaluateur" id="rapport_evaluateur" class="form-control-file"
                                    accept=".pdf,.jpg,.jpeg,.png">
                            </div>

                            <button type="submit" class="btn btn-success"><i class="fas fa-save"></i> @lang('trans.save')</button>
                            <a href="{{ route('evaluateur') }}" class="btn btn-secondary">@lang('trans.cancel')</a>
                        </form>
                    </div>
                </div>
            </div>
        </div>
    </div>
@endsection
