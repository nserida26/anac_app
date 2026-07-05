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

                        <form action="{{ route('examinateur.store') }}" method="POST" enctype="multipart/form-data">
                            @csrf
                            <input type="hidden" name="demandeur_id" value="{{ $demandeur->id }}">
                            <input type="hidden" name="examinateur_id" value="{{ $examinateur->id }}">

                            <div class="form-group">
                                <label>Date de l'examen *</label>
                                <input type="date" name="date_examen" class="form-control" required
                                    value="{{ date('Y-m-d') }}">
                            </div>

                            <div class="form-group">
                                <label>Validité (en jours) *</label>
                                <input type="number" name="validite" class="form-control" required placeholder="Ex: 365">
                            </div>

                            <div class="form-group">
                                <label>Aptitude *</label>
                                <select name="aptitude" class="form-control" required>
                                    <option value="">Sélectionner</option>
                                    <option value="Apte">Apte</option>
                                    <option value="Inapte">Inapte</option>
                                </select>
                            </div>

                            <div class="form-group">
                                <label>Rapport médical (PDF, JPG, PNG) *</label>
                                <input type="file" name="rapport" class="form-control-file" required
                                    accept=".pdf,.jpg,.jpeg,.png">
                            </div>

                            <div class="form-group">
                                <label>Attestation (PDF, JPG, PNG) *</label>
                                <input type="file" name="attestation" class="form-control-file" required
                                    accept=".pdf,.jpg,.jpeg,.png">
                            </div>

                            <button type="submit" class="btn btn-primary">Enregistrer</button>
                            <a href="{{ route('examinateur.search-licence') }}" class="btn btn-secondary">Annuler</a>
                        </form>
                    </div>
                </div>
            </div>
        </div>
    </div>
@endsection
