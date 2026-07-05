{{-- resources/views/centre/formations/rapports.blade.php --}}
@extends('centre.layouts.app')
@section('title', 'Rapports de formation')
@section('contentheader', 'Rapports')
@section('contentheaderlink')
    <a href="{{ route('centre.dashboard') }}">Tableau de bord</a> / 
    <a href="{{ route('centre.formations.index') }}">Formations</a> / Rapports
@endsection

@section('content')
<div class="container-fluid">
    <div class="row">
        <div class="col-md-12">
            <div class="card">
                <div class="card-header">
                    <h3 class="card-title">Formation : {{ $formation->intitule }}</h3>
                </div>
                <div class="card-body">
                    <div class="row">
                        {{-- Rapport de réalisation --}}
                        <div class="col-md-6">
                            <div class="card">
                                <div class="card-header bg-primary text-white">
                                    <h5 class="mb-0">Rapport de réalisation</h5>
                                </div>
                                <div class="card-body">
                                    @php
                                        $rapportRealisation = $formation->rapports->where('type', 'realisation')->first();
                                    @endphp
                                    
                                    @if($rapportRealisation)
                                        <div class="alert alert-success">
                                            <i class="fas fa-check-circle"></i> Rapport soumis le {{ $rapportRealisation->date_soumission->format('d/m/Y H:i') }}
                                            <br>
                                            <a href="{{ asset('uploads/' . $rapportRealisation->fichier_path) }}" target="_blank" class="btn btn-sm btn-info mt-2">
                                                <i class="fas fa-download"></i> Voir le rapport
                                            </a>
                                        </div>
                                    @else
                                        <form action="{{ route('centre.formations.rapports.store', $formation) }}" method="POST" enctype="multipart/form-data">
                                            @csrf
                                            <input type="hidden" name="type" value="realisation">
                                            <div class="form-group">
                                                <label>Télécharger le rapport de réalisation</label>
                                                <input type="file" name="fichier" class="form-control" accept=".pdf" required>
                                            </div>
                                            <div class="form-group">
                                                <label>Commentaire</label>
                                                <textarea name="commentaire" class="form-control" rows="3"></textarea>
                                            </div>
                                            <button type="submit" class="btn btn-primary">Soumettre</button>
                                        </form>
                                    @endif
                                </div>
                            </div>
                        </div>

                        {{-- Rapport d'examen --}}
                        <div class="col-md-6">
                            <div class="card">
                                <div class="card-header bg-warning text-white">
                                    <h5 class="mb-0">Rapport d'examen/contrôle</h5>
                                </div>
                                <div class="card-body">
                                    @php
                                        $rapportExamen = $formation->rapports->where('type', 'examen')->first();
                                    @endphp
                                    
                                    @if($rapportExamen)
                                        <div class="alert alert-success">
                                            <i class="fas fa-check-circle"></i> Rapport soumis le {{ $rapportExamen->date_soumission->format('d/m/Y H:i') }}
                                            <br>
                                            <a href="{{ asset('uploads/' . $rapportExamen->fichier_path) }}" target="_blank" class="btn btn-sm btn-info mt-2">
                                                <i class="fas fa-download"></i> Voir le rapport
                                            </a>
                                        </div>
                                    @else
                                        <form action="{{ route('centre.formations.rapports.store', $formation) }}" method="POST" enctype="multipart/form-data">
                                            @csrf
                                            <input type="hidden" name="type" value="examen">
                                            <div class="form-group">
                                                <label>Télécharger le rapport d'examen</label>
                                                <input type="file" name="fichier" class="form-control" accept=".pdf" required>
                                            </div>
                                            <div class="form-group">
                                                <label>Commentaire</label>
                                                <textarea name="commentaire" class="form-control" rows="3"></textarea>
                                            </div>
                                            <button type="submit" class="btn btn-primary">Soumettre</button>
                                        </form>
                                    @endif
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection