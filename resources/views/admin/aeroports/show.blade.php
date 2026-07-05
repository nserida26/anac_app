
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
    {{$aeroport->nom}}
@endsection
@section('content')
<div class="container-fluid">
    <div class="row">
        <div class="col-md-12">
            <div class="card">
                <div class="card-header bg-info text-white">
                    <div class="d-flex justify-content-between align-items-center">
                        <h3 class="card-title">
                            <i class="fas fa-plane"></i> {{ $aeroport->nom }}
                        </h3>
                        <div>
                            <a href="{{ route('aeroports.edit', $aeroport) }}" class="btn btn-light btn-sm">
                                <i class="fas fa-edit"></i> Modifier
                            </a>
                            <a href="{{ route('aeroports.index') }}" class="btn btn-light btn-sm ml-2">
                                <i class="fas fa-list"></i> Liste
                            </a>
                        </div>
                    </div>
                </div>
                
                <div class="card-body">
                    <div class="row">
                        <div class="col-md-6">
                            <h5><i class="fas fa-info-circle"></i> Informations Générales</h5>
                            <table class="table table-sm">
                                <tr>
                                    <th width="40%">Nom:</th>
                                    <td>{{ $aeroport->nom }}</td>
                                </tr>
                                <tr>
                                    <th>Code IATA:</th>
                                    <td>
                                        <span class="badge badge-primary">{{ $aeroport->codeIATA }}</span>
                                    </td>
                                </tr>
                                <tr>
                                    <th>Code ICAO:</th>
                                    <td>
                                        <span class="badge badge-secondary">{{ $aeroport->codeICAO }}</span>
                                    </td>
                                </tr>
                                <tr>
                                    <th>Ville:</th>
                                    <td>{{ $aeroport->ville }}</td>
                                </tr>
                                <tr>
                                    <th>Pays:</th>
                                    <td>
                                        <span class="flag-icon flag-icon-{{ strtolower($aeroport->pays->code ?? '') }} mr-1"></span>
                                        {{ $aeroport->pays->nom ?? 'N/A' }}
                                    </td>
                                </tr>
                            </table>
                        </div>
                        
                        <div class="col-md-6">
                            <h5><i class="fas fa-map-marked-alt"></i> Localisation</h5>
                            <table class="table table-sm">
                                <tr>
                                    <th width="40%">Latitude:</th>
                                    <td>
                                        {{ $aeroport->latitude ?? 'Non définie' }}
                                        @if($aeroport->latitude)
                                            <button class="btn btn-sm btn-outline-info ml-2 copy-btn" 
                                                    data-text="{{ $aeroport->latitude }}">
                                                <i class="fas fa-copy"></i>
                                            </button>
                                        @endif
                                    </td>
                                </tr>
                                <tr>
                                    <th>Longitude:</th>
                                    <td>
                                        {{ $aeroport->longitude ?? 'Non définie' }}
                                        @if($aeroport->longitude)
                                            <button class="btn btn-sm btn-outline-info ml-2 copy-btn" 
                                                    data-text="{{ $aeroport->longitude }}">
                                                <i class="fas fa-copy"></i>
                                            </button>
                                        @endif
                                    </td>
                                </tr>
                                <tr>
                                    <th>Google Maps:</th>
                                    <td>
                                        @if($aeroport->latitude && $aeroport->longitude)
                                            <a href="https://maps.google.com/?q={{ $aeroport->latitude }},{{ $aeroport->longitude }}" 
                                               target="_blank" class="btn btn-sm btn-danger">
                                                <i class="fas fa-map-marker-alt"></i> Voir sur Google Maps
                                            </a>
                                        @else
                                            <span class="text-muted">Coordonnées non disponibles</span>
                                        @endif
                                    </td>
                                </tr>
                                <tr>
                                    <th>Créé le:</th>
                                    <td>{{ $aeroport->created_at->format('d/m/Y H:i') }}</td>
                                </tr>
                                <tr>
                                    <th>Mis à jour:</th>
                                    <td>{{ $aeroport->updated_at->format('d/m/Y H:i') }}</td>
                                </tr>
                            </table>
                        </div>
                    </div>
                    
                
                </div>
            </div>
        </div>
        
    </div>
</div>

@endsection

@push('scripts')


<script>
    // Copier dans le presse-papier
    $('.copy-btn').click(function() {
        const text = $(this).data('text');
        navigator.clipboard.writeText(text).then(() => {
            toastr.success('Copié dans le presse-papier');
        });
    });
    
    function copyAeroportInfo() {
        const info = `{{ $aeroport->nom }} ({{ $aeroport->codeIATA }}/{{ $aeroport->codeICAO }})
Ville: {{ $aeroport->ville }}
Pays: {{ $aeroport->pays->nom ?? 'N/A' }}
Coordonnées: {{ $aeroport->latitude ?? 'N/A' }}, {{ $aeroport->longitude ?? 'N/A' }}
        `;
        navigator.clipboard.writeText(info).then(() => {
            toastr.success('Informations copiées');
        });
    }

</script>
@endpush