
@extends('layouts.admin')
@section('title')
    @lang('trans.dashboard_admin')
@endsection
@section('contentheader')
    @lang('trans.dashboard_admin')
@endsection
@section('contentheaderlink')
    <a href="">
        @lang('trans.dashboard_admin') </a>
@endsection
@section('contentheaderactive')
    Gestion des Aéroports
@endsection
@section('content')

<div class="container-fluid">
    <div class="row">
        <div class="col-md-12">
            <div class="card">
                <div class="card-header bg-primary text-white">
                    <div class="d-flex justify-content-between align-items-center">
                        <h3 class="card-title">
                            <i class="fas fa-plane-departure"></i> Liste des Aéroports
                        </h3>
                        <div>
                            <a href="{{ route('aeroports.create') }}" class="btn btn-light btn-sm">
                                <i class="fas fa-plus"></i> Nouveau
                            </a>
                        </div>
                    </div>
                </div>
                
                <div class="card-body">
                    <!-- Filtres -->
                    <form method="GET" class="mb-4">
                        <div class="row">
                            <div class="col-md-4">
                                <div class="input-group">
                                    <input type="text" name="search" class="form-control" 
                                           placeholder="Rechercher..." value="{{ request('search') }}">
                                    <div class="input-group-append">
                                        <button class="btn btn-outline-primary" type="submit">
                                            <i class="fas fa-search"></i>
                                        </button>
                                    </div>
                                </div>
                            </div>
                            <div class="col-md-3">
                                <select name="pays_id" class="form-control" onchange="this.form.submit()">
                                    <option value="">Tous les pays</option>
                                    @foreach($pays as $pay)
                                        <option value="{{ $pay->id }}" {{ request('pays_id') == $pay->id ? 'selected' : '' }}>
                                            {{ $pay->nom }}
                                        </option>
                                    @endforeach
                                </select>
                            </div>
                            <div class="col-md-2">
                                <select name="sort" class="form-control" onchange="this.form.submit()">
                                    <option value="nom" {{ request('sort') == 'nom' ? 'selected' : '' }}>Nom</option>
                                    <option value="codeIATA" {{ request('sort') == 'codeIATA' ? 'selected' : '' }}>Code IATA</option>
                                    <option value="codeICAO" {{ request('sort') == 'codeICAO' ? 'selected' : '' }}>Code ICAO</option>
                                    <option value="ville" {{ request('sort') == 'ville' ? 'selected' : '' }}>Ville</option>
                                </select>
                            </div>
                            <div class="col-md-2">
                                <select name="direction" class="form-control" onchange="this.form.submit()">
                                    <option value="asc" {{ request('direction') == 'asc' ? 'selected' : '' }}>Croissant</option>
                                    <option value="desc" {{ request('direction') == 'desc' ? 'selected' : '' }}>Décroissant</option>
                                </select>
                            </div>
                            <div class="col-md-1">
                                <a href="{{ route('aeroports.index') }}" class="btn btn-secondary btn-block">
                                    <i class="fas fa-redo"></i>
                                </a>
                            </div>
                        </div>
                    </form>

                    <!-- Table -->
                    <div class="table-responsive">
                        <table class="table table-hover table-striped">
                            <thead class="thead-dark">
                                <tr>
                                    <th>ID</th>
                                    <th>Nom</th>
                                    <th>Codes</th>
                                    <th>Ville</th>
                                    <th>Pays</th>
                                    <th>Coordonnées</th>
                                    <th>Créé le</th>
                                    <th>Actions</th>
                                </tr>
                            </thead>
                            <tbody>
                                @forelse($aeroports as $aeroport)
                                    <tr>
                                        <td>{{ $aeroport->id }}</td>
                                        <td>
                                            <strong>{{ $aeroport->nom }}</strong>
                                            @if($aeroport->latitude && $aeroport->longitude)
                                                <span class="badge badge-success ml-2" title="Coordonnées disponibles">
                                                    <i class="fas fa-map-marker-alt"></i>
                                                </span>
                                            @endif
                                        </td>
                                        <td>
                                            <span class="badge badge-primary">{{ $aeroport->codeIATA }}</span>
                                            <span class="badge badge-secondary">{{ $aeroport->codeICAO }}</span>
                                        </td>
                                        <td>{{ $aeroport->ville }}</td>
                                        <td>
                                            <span class="flag-icon flag-icon-{{ strtolower($aeroport->pays->code ?? '') }} mr-1"></span>
                                            {{ $aeroport->pays->nom ?? 'N/A' }}
                                        </td>
                                        <td>
                                            @if($aeroport->latitude && $aeroport->longitude)
                                                {{ $aeroport->latitude }}, {{ $aeroport->longitude }}
                                            @else
                                                <span class="text-muted">Non définies</span>
                                            @endif
                                        </td>
                                        <td>{{ $aeroport->created_at->format('d/m/Y') }}</td>
                                        <td>
                                            <div class="btn-group btn-group-sm">
                                                <a href="{{ route('aeroports.show', $aeroport) }}" 
                                                   class="btn btn-info" title="Voir">
                                                    <i class="fas fa-eye"></i>
                                                </a>
                                                <a href="{{ route('aeroports.edit', $aeroport) }}" 
                                                   class="btn btn-warning" title="Modifier">
                                                    <i class="fas fa-edit"></i>
                                                </a>
                                                <form action="{{ route('aeroports.destroy', $aeroport) }}" 
                                                      method="POST" class="d-inline"
                                                      onsubmit="return confirm('Supprimer cet aéroport ?')">
                                                    @csrf
                                                    @method('DELETE')
                                                    <button type="submit" class="btn btn-danger" title="Supprimer">
                                                        <i class="fas fa-trash"></i>
                                                    </button>
                                                </form>
                                            </div>
                                        </td>
                                    </tr>
                                @empty
                                    <tr>
                                        <td colspan="8" class="text-center text-muted">
                                            <i class="fas fa-plane-slash fa-2x mb-3"></i>
                                            <p>Aucun aéroport trouvé</p>
                                        </td>
                                    </tr>
                                @endforelse
                            </tbody>
                        </table>
                    </div>

                    <!-- Pagination -->
                    <div class="d-flex justify-content-center">
                        {{ $aeroports->links() }}
                    </div>

                    <!-- Stats -->
                    <div class="row mt-4">
                        <div class="col-md-3">
                            <div class="info-box bg-gradient-info">
                                <span class="info-box-icon"><i class="fas fa-plane"></i></span>
                                <div class="info-box-content">
                                    <span class="info-box-text">Total Aéroports</span>
                                    <span class="info-box-number">{{ $aeroports->total() }}</span>
                                </div>
                            </div>
                        </div>
                        <div class="col-md-3">
                            <div class="info-box bg-gradient-success">
                                <span class="info-box-icon"><i class="fas fa-map-marker-alt"></i></span>
                                <div class="info-box-content">
                                    <span class="info-box-text">Avec coordonnées</span>
                                    <span class="info-box-number">
                                        {{ \App\Models\Aeroport::whereNotNull('latitude')->whereNotNull('longitude')->count() }}
                                    </span>
                                </div>
                            </div>
                        </div>
                        <div class="col-md-3">
                            <div class="info-box bg-gradient-warning">
                                <span class="info-box-icon"><i class="fas fa-globe-africa"></i></span>
                                <div class="info-box-content">
                                    <span class="info-box-text">Pays différents</span>
                                    <span class="info-box-number">
                                        {{ \App\Models\Aeroport::distinct('pays_id')->count('pays_id') }}
                                    </span>
                                </div>
                            </div>
                        </div>
                        <div class="col-md-3">
                            <div class="info-box bg-gradient-danger">
                                <span class="info-box-icon"><i class="fas fa-building"></i></span>
                                <div class="info-box-content">
                                    <span class="info-box-text">Villes différentes</span>
                                    <span class="info-box-number">
                                        {{ \App\Models\Aeroport::distinct('ville')->count('ville') }}
                                    </span>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- Modal Import -->
<div class="modal fade" id="importModal" tabindex="-1">
    <div class="modal-dialog">
        <div class="modal-content">
            <form action="{{ route('aeroports.import') }}" method="POST" enctype="multipart/form-data">
                @csrf
                <div class="modal-header">
                    <h5 class="modal-title">
                        <i class="fas fa-file-import"></i> Importer des aéroports
                    </h5>
                    <button type="button" class="close" data-dismiss="modal">
                        <span>&times;</span>
                    </button>
                </div>
                <div class="modal-body">
                    <div class="form-group">
                        <label>Fichier CSV</label>
                        <input type="file" name="file" class="form-control-file" accept=".csv,.txt" required>
                        <small class="form-text text-muted">
                            Format attendu: nom,codeIATA,codeICAO,pays_id,ville,latitude,longitude
                        </small>
                    </div>
                    <div class="alert alert-info">
                        <i class="fas fa-info-circle"></i>
                        Téléchargez le 
                        <a href="{{ asset('templates/aeroports_template.csv') }}" target="_blank">
                            modèle de fichier CSV
                        </a>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-dismiss="modal">Annuler</button>
                    <button type="submit" class="btn btn-primary">Importer</button>
                </div>
            </form>
        </div>
    </div>
</div>

@if(session('import_errors'))
<div class="modal fade" id="importErrorsModal" tabindex="-1">
    <div class="modal-dialog modal-lg">
        <div class="modal-content">
            <div class="modal-header bg-warning">
                <h5 class="modal-title">
                    <i class="fas fa-exclamation-triangle"></i> Erreurs d'import
                </h5>
                <button type="button" class="close" data-dismiss="modal">
                    <span>&times;</span>
                </button>
            </div>
            <div class="modal-body">
                <div class="alert alert-warning">
                    <strong>Certaines lignes n'ont pas pu être importées:</strong>
                </div>
                <ul class="list-group">
                    @foreach(session('import_errors') as $error)
                        <li class="list-group-item list-group-item-danger">{{ $error }}</li>
                    @endforeach
                </ul>
            </div>
        </div>
    </div>
</div>
@push('scripts')
<script>
    $(function() {
        $('#importErrorsModal').modal('show');
    });
</script>
@endpush
@endif
@endsection

@push('styles')
<style>
    .flag-icon {
        width: 1.5em;
        height: 1em;
        background-size: contain;
        background-position: 50%;
        background-repeat: no-repeat;
        display: inline-block;
    }
    .info-box {
        color: white;
        border-radius: 5px;
        margin-bottom: 10px;
    }
</style>
@endpush