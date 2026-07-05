{{-- resources/views/centre/agrements/index.blade.php --}}
@extends('centre.layouts.app')
@section('title', 'Gestion des agréments')
@section('contentheader', 'Agréments du centre')
@section('contentheaderlink')
    <a href="{{ route('centre.dashboard') }}">Tableau de bord</a> / Agréments
@endsection

@push('css')
    <link rel="stylesheet" href="{{ asset('assets/admin/plugins/sweetalert2/sweetalert2.min.css') }}">
@endpush

@section('content')
<div class="container-fluid">
    @if(session('error'))
        <div class="alert alert-danger alert-dismissible fade show" role="alert">
            <i class="fas fa-exclamation-triangle"></i> {{ session('error') }}
            <button type="button" class="close" data-dismiss="alert">×</button>
        </div>
    @endif

    @if(session('success'))
        <div class="alert alert-success alert-dismissible fade show" role="alert">
            {{ session('success') }}
            <button type="button" class="close" data-dismiss="alert">×</button>
        </div>
    @endif

    {{-- Alerte expiration --}}
    @if($dernierAgrement && $dernierAgrement->date_fin < now())
        <div class="alert alert-danger">
            <i class="fas fa-ban"></i> 
            <strong>Agrément expiré !</strong> Vous ne pouvez plus déclarer de nouvelles formations. Veuillez renouveler votre agrément.
        </div>
    @elseif($dernierAgrement && $dernierAgrement->date_fin->diffInDays(now()) <= 30)
        <div class="alert alert-warning">
            <i class="fas fa-clock"></i> 
            <strong>Attention :</strong> Votre agrément expire le {{ $dernierAgrement->date_fin->format('d/m/Y') }} (dans {{ $dernierAgrement->date_fin->diffInDays(now()) }} jours).
        </div>
    @endif

    <div class="row">
        <div class="col-md-8">
            <div class="card">
                <div class="card-header">
                    <h3 class="card-title">Historique des agréments</h3>
                </div>
                <div class="card-body">
                    <div class="table-responsive">
                        <table class="table table-bordered">
                            <thead>
                                <tr>
                                    <th>N° agrément</th>
                                    <th>Date début</th>
                                    <th>Date fin</th>
                                    <th>Statut</th>
                                    <th>Document</th>
                                </tr>
                            </thead>
                            <tbody>
                                @forelse($agrements as $agrement)
                                <tr>
                                    <td>{{ $agrement->numero_agrement }}</td>
                                    <td>{{ $agrement->date_debut->format('d/m/Y') }}</td>
                                    <td>{{ $agrement->date_fin->format('d/m/Y') }}</td>
                                    <td>
                                        @if($agrement->statut == 'valide')
                                            <span class="badge badge-success">Valide</span>
                                        @elseif($agrement->statut == 'expire')
                                            <span class="badge badge-danger">Expiré</span>
                                        @else
                                            <span class="badge badge-warning">Suspendu</span>
                                        @endif
                                    </td>
                                    <td>
                                        @if($agrement->document_path)
                                            <a href="{{ asset('uploads/' . $agrement->document_path) }}" target="_blank" class="btn btn-sm btn-info">
                                                <i class="fas fa-file-pdf"></i> Voir
                                            </a>
                                        @else
                                            -
                                        @endif
                                    </td>
                                </tr>
                                @empty
                                <tr>
                                    <td colspan="5" class="text-center">Aucun agrément enregistré</td>
                                </tr>
                                @endforelse
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        </div>

        <div class="col-md-4">
            <div class="card">
                <div class="card-header bg-success text-white">
                    <h3 class="card-title">Ajouter un agrément</h3>
                </div>
                <div class="card-body">
                    <form action="{{ route('centre.agrements.store') }}" method="POST" enctype="multipart/form-data">
                        @csrf
                        <div class="form-group">
                            <label>Numéro d'agrément *</label>
                            <input type="text" name="numero_agrement" class="form-control" required>
                        </div>
                        <div class="form-group">
                            <label>Date de début *</label>
                            <input type="date" name="date_debut" class="form-control" required>
                        </div>
                        <div class="form-group">
                            <label>Date de fin *</label>
                            <input type="date" name="date_fin" class="form-control" required>
                        </div>
                        <div class="form-group">
                            <label>Document d'agrément</label>
                            <input type="file" name="document" class="form-control" accept=".pdf">
                        </div>
                        <button type="submit" class="btn btn-success btn-block">
                            <i class="fas fa-save"></i> Enregistrer l'agrément
                        </button>
                    </form>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection