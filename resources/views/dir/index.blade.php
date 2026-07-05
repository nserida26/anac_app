@extends('dir.layouts.app')
@section('title')
    @lang('trans.dashboard_dir', ['role' => strtoupper(auth()->user()->getRoleNames()->first() ?? '')])
@endsection
@section('contentheader')
    @lang('trans.dashboard_dir', ['role' => strtoupper(auth()->user()->getRoleNames()->first() ?? '')])
@endsection
@section('contentheaderlink')
    @if (auth()->user()->hasRole('dsv'))
        <a href="{{ route('dsv') }}">
            @lang('trans.dashboard_dir', ['role' => strtoupper(auth()->user()->getRoleNames()->first() ?? '')]) </a>
    @endif
    @if (auth()->user()->hasRole('dg'))
        <a href="{{ route('dg') }}">
            @lang('trans.dashboard_dir', ['role' => strtoupper(auth()->user()->getRoleNames()->first() ?? '')])</a>
    @endif
    @if (auth()->user()->hasRole('dta'))
        <a href="{{ route('dta') }}">
            @lang('trans.dashboard_dir', ['role' => strtoupper(auth()->user()->getRoleNames()->first() ?? '')]) </a>
    @endif
    @if (auth()->user()->hasRole('dsna'))
        <a href="{{ route('dsna') }}">
            @lang('trans.dashboard_dir', ['role' => strtoupper(auth()->user()->getRoleNames()->first() ?? '')])</a>
    @endif
    @if (auth()->user()->hasRole('dsad'))
        <a href="{{ route('dsad') }}">
            @lang('trans.dashboard_dir', ['role' => strtoupper(auth()->user()->getRoleNames()->first() ?? '')])</a>
    @endif
@endsection
@section('contentheaderactive')
    @lang('trans.dashboard_dir', ['role' => strtoupper(auth()->user()->getRoleNames()->first() ?? '')])
@endsection

@push('css')
    <link rel="stylesheet" href="{{ asset('assets/admin/plugins/datatables-bs4/css/dataTables.bootstrap4.min.css') }}">
    <link rel="stylesheet" href="{{ asset('assets/admin/plugins/datatables-responsive/css/responsive.bootstrap4.min.css') }}">
    <style>
        .progress-group {
            display: flex;
            align-items: center;
        }
        .progress-text {
            width: 50px;
            margin-right: 10px;
            font-weight: bold;
        }
        .progress {
            flex-grow: 1;
            height: 20px;
            border-radius: 10px;
        }
        .progress-bar {
            border-radius: 10px;
            transition: width 0.6s ease;
        }
        .table-danger {
            background-color: #f8d7da !important;
        }
        .table-danger:hover {
            background-color: #f5c6cb !important;
        }
        .info-box {
            border-radius: 10px;
            color: white;
            box-shadow: 0 4px 6px rgba(0,0,0,0.1);
            margin-bottom: 10px;
        }
        .info-box-icon {
            border-radius: 10px 0 0 10px;
            display: flex;
            align-items: center;
            justify-content: center;
            font-size: 30px;
        }
        .small-box {
            border-radius: 10px;
            position: relative;
            display: block;
            margin-bottom: 20px;
            box-shadow: 0 1px 1px rgba(0,0,0,0.1);
        }
        .small-box > .inner {
            padding: 10px;
        }
        .small-box > .small-box-footer {
            position: relative;
            text-align: center;
            padding: 3px 0;
            color: #fff;
            color: rgba(255,255,255,0.8);
            display: block;
            z-index: 10;
            background: rgba(0,0,0,0.1);
            text-decoration: none;
        }
        .small-box .icon {
            position: absolute;
            top: -10px;
            right: 10px;
            z-index: 0;
            font-size: 90px;
            color: rgba(0,0,0,0.15);
        }
        .small-box:hover .icon {
            animation: pulse 0.5s;
        }
        @keyframes pulse {
            0% { transform: scale(1); }
            50% { transform: scale(1.1); }
            100% { transform: scale(1); }
        }
    </style>
    <style>
    /* Style pour les tabs */
    .nav-tabs .nav-link {
        border: 1px solid transparent;
        border-top-left-radius: 0.25rem;
        border-top-right-radius: 0.25rem;
        font-weight: 500;
        transition: all 0.3s ease;
    }
    
    .nav-tabs .nav-link:hover {
        border-color: #e9ecef #e9ecef #dee2e6;
        background-color: #f8f9fa;
    }
    
    .nav-tabs .nav-link.active {
        color: #495057;
        background-color: #fff;
        border-color: #dee2e6 #dee2e6 #fff;
        border-bottom: 3px solid #17a2b8;
    }
    
    .tab-content {
        border: 1px solid #dee2e6;
        border-top: none;
        padding: 20px;
        background-color: #fff;
        border-bottom-left-radius: 0.25rem;
        border-bottom-right-radius: 0.25rem;
    }
    
    /* Style pour les tableaux dans les tabs */
    #typeTable, #licenceTable {
        font-size: 0.9rem;
    }
    
    #typeTable th, #licenceTable th {
        border-top: none;
        background-color: #f8f9fa;
        font-weight: 600;
    }
    
    /* Style pour les graphiques */
    canvas {
        max-height: 300px;
    }
    
    /* Responsive */
    @media (max-width: 768px) {
        .nav-tabs .nav-link {
            font-size: 0.85rem;
            padding: 0.5rem 0.75rem;
        }
        
        .tab-content {
            padding: 15px;
        }
        
        .row > [class*="col-"] {
            margin-bottom: 15px;
        }
    }
</style>
@endpush

@section('content')
    <div class="container-fluid">
        
        <!-- ========== SECTION DTA ========== -->
        @if (auth()->user()->hasRole('dta'))
        <div class="row">
            <div class="col-12">
                <div class="card">
                    <div class="card-header bg-primary text-white">
                        <h3 class="card-title">
                            <i class="fas fa-plane"></i> Tableau de Bord - Direction des Transports Aériens (DTA)
                        </h3>
                    </div>
                    <div class="card-body">
                        <!-- Statistiques des autorisations -->
                        <div class="row mb-4">
                            <div class="col-md-3 col-sm-6">
                                <div class="small-box bg-info">
                                    <div class="inner">
                                        <h3>{{ $autorisations_stats['total'] ?? 0 }}</h3>
                                        <p>Demandes</p>
                                    </div>
                                    <div class="icon">
                                        <i class="fas fa-file-contract"></i>
                                    </div>
                                    <a href="{{ route('dir.demandeAutorisations') }}" class="small-box-footer">
                                        Voir toutes <i class="fas fa-arrow-circle-right"></i>
                                    </a>
                                </div>
                            </div>
                            <div class="col-md-3 col-sm-6">
                                <div class="small-box bg-success">
                                    <div class="inner">
                                        <h3>{{ $autorisations_stats['valides'] ?? 0 }}</h3>
                                        <p>Demandes validées</p>
                                    </div>
                                    <div class="icon">
                                        <i class="fas fa-check-circle"></i>
                                    </div>
                                    <a href="{{ route('dir.demandeAutorisations') }}" class="small-box-footer">
                                        Voir détails <i class="fas fa-arrow-circle-right"></i>
                                    </a>
                                </div>
                            </div>
                            <div class="col-md-3 col-sm-6">
                                <div class="small-box bg-warning">
                                    <div class="inner">
                                        <h3>{{ $autorisations_stats['en_cours'] ?? 0 }}</h3>
                                        <p>En cours de traitement</p>
                                    </div>
                                    <div class="icon">
                                        <i class="fas fa-clock"></i>
                                    </div>
                                    <a href="{{ route('dir.demandeAutorisations') }}" class="small-box-footer">
                                        Traiter <i class="fas fa-arrow-circle-right"></i>
                                    </a>
                                </div>
                            </div>
                            <div class="col-md-3 col-sm-6">
                                <div class="small-box bg-danger">
                                    <div class="inner">
                                        <h3>{{ $autorisations_stats['attente_signature'] ?? 0 }}</h3>
                                        <p>Autorisations</p>
                                    </div>
                                    <div class="icon">
                                        <i class="fas fa-signature"></i>
                                    </div>
                                    <a href="#table-autorisations" class="small-box-footer">
                                        Voir <i class="fas fa-arrow-circle-right"></i>
                                    </a>
                                </div>
                            </div>
                        </div>

                        <!-- Demandes en attente -->
                        @if(isset($demandes_en_attente) && $demandes_en_attente > 0)
                            <div class="alert alert-warning">
                                <h5>
                                    <i class="fas fa-exclamation-triangle"></i> 
                                    {{ $demandes_en_attente }} demande(s) en attente de traitement
                                </h5>
                                <p>Veuillez traiter ces demandes dans les plus brefs délais.</p>
                                <a href="{{ route('dir.demandeAutorisations') }}?status_filter=pending" 
                                   class="btn btn-warning">
                                    <i class="fas fa-tasks"></i> Voir les demandes en attente
                                </a>
                            </div>
                        @endif

                        <!-- Tableau des autorisations récentes -->
                        <div class="card" id="table-autorisations">
                            <div class="card-header bg-secondary text-white">
                                <h4 class="card-title">
                                    <i class="fas fa-history"></i> Autorisations
                                </h4>
                            </div>
                            <div class="card-body">
                                <div class="table-responsive">
                                    <table class="table table-hover">
                                        <thead>
                                            <tr>
                                                <th>Code</th>
                                                <th>Demande</th>
                                                <th>Date délivrance</th>
                                                <th>Date expiration</th>
                                                <th>Statut</th>
                                                <th>Signature</th>
                                            </tr>
                                        </thead>
                                        <tbody>
                                            @foreach($recent_autorisations ?? [] as $autorisation)
                                            <tr>
                                                <td> @if($autorisation->demande)
                                                        <a href="{{ route('autorisations.print', $autorisation->id) }}" target="_blank">
                                                            {{ $autorisation->code_autorisation }}
                                                        </a>
                                                        @else
                                                        <span class="text-muted">N/A</span>
                                                    @endif
                                                    
                                                </td>
                                                <td>
                                                    @if($autorisation->demande)
                                                        <strong>{{ $autorisation->demande->code }}</strong>
                                                    @else
                                                        <span class="text-muted">N/A</span>
                                                    @endif
                                                </td>
                                                <td>{{ $autorisation->date_delivrance ? \Carbon\Carbon::parse($autorisation->date_delivrance)->format('d/m/Y') : 'N/A' }}</td>
                                                <td>
                                                    @if($autorisation->date_expiration)
                                                        @if(\Carbon\Carbon::parse($autorisation->date_expiration)->isPast())
                                                            <span class="badge badge-danger">
                                                                {{ \Carbon\Carbon::parse($autorisation->date_expiration)->format('d/m/Y') }}
                                                            </span>
                                                        @elseif(\Carbon\Carbon::parse($autorisation->date_expiration)->diffInDays(now()) <= 30)
                                                            <span class="badge badge-warning">
                                                                {{ \Carbon\Carbon::parse($autorisation->date_expiration)->format('d/m/Y') }}
                                                            </span>
                                                        @else
                                                            <span class="badge badge-success">
                                                                {{ \Carbon\Carbon::parse($autorisation->date_expiration)->format('d/m/Y') }}
                                                            </span>
                                                        @endif
                                                    @else
                                                        <span class="text-muted">N/A</span>
                                                    @endif
                                                </td>
                                                <td>
                                                    <span class="badge badge-warning">{{ strtoupper($autorisation->statut) }}</span>
                                                </td>
                                                <td>
                                                    @if($autorisation->signature_dta)
                                                        <span class="badge badge-success"><i class="fas fa-check"></i> Signée</span>
                                                    @else
                                                        <span class="badge badge-warning"><i class="fas fa-times"></i> En attente</span>
                                                    @endif
                                                </td>
                                            </tr>
                                            @endforeach
                                        </tbody>
                                    </table>
                                </div>
                            </div>
                        </div>


                    </div>
                </div>
            </div>
        </div>
        @endif

        <!-- ========== SECTION DG ========== -->
        @if (auth()->user()->hasRole('dg'))
        <div class="row">
            <div class="col-12">
                <div class="card">
                    <div class="card-header bg-primary text-white">
                        <h3 class="card-title">
                            <i class="fas fa-landmark"></i> Tableau de Bord - Direction Générale (DG)
                        </h3>
                    </div>
                    <div class="card-body">
                        <!-- Double ligne de statistiques -->
                        <div class="row">
                            <!-- Première ligne : Autorisations -->
                            <div class="col-md-4 col-sm-6">
                                <div class="small-box bg-info">
                                    <div class="inner">
                                        <h3>{{ $autorisations_stats['total'] ?? 0 }}</h3>
                                        <p>Demandes d'autorisation</p>
                                    </div>
                                    <div class="icon">
                                        <i class="fas fa-file"></i>
                                    </div>
                                    <a href="{{ route('dir.demandeAutorisations') }}" class="small-box-footer">
                                        Voir <i class="fas fa-arrow-circle-right"></i>
                                    </a>
                                </div>
                            </div>
                            <div class="col-md-4 col-sm-6">
                                <div class="small-box bg-success">
                                    <div class="inner">
                                        <h3>{{ $autorisations_stats['valides'] ?? 0 }}</h3>
                                        <p>Demandes validées</p>
                                    </div>
                                    <div class="icon">
                                        <i class="fas fa-check-circle"></i>
                                    </div>
                                    <a href="{{ route('dir.demandeAutorisations') }}" class="small-box-footer">
                                        Voir <i class="fas fa-arrow-circle-right"></i>
                                    </a>
                                </div>
                            </div>
                            {{--<div class="col-md-4 col-sm-6">
                                <div class="small-box bg-warning">
                                    <div class="inner">
                                        <h3>{{ $autorisations_stats['attente_signature'] ?? 0 }}</h3>
                                        <p>À signer (Autor.)</p>
                                    </div>
                                    <div class="icon">
                                        <i class="fas fa-signature"></i>
                                    </div>
                                    <a href="{{ route('dir.demandeAutorisations') }}" class="small-box-footer">
                                        Voir <i class="fas fa-arrow-circle-right"></i>
                                    </a>
                                </div>
                            </div>--}}
                            <div class="col-md-4 col-sm-6">
                                <div class="small-box bg-danger">
                                    <div class="inner">
                                        <h3>{{ $autorisations_stats['expirees'] ?? 0 }}</h3>
                                        <p>Autorisations</p>
                                    </div>
                                    <div class="icon">
                                        <i class="fas fa-file-contract"></i>
                                    </div>
                                    <a href="{{ route('dir.demandeAutorisations') }}" class="small-box-footer">
                                        Voir <i class="fas fa-arrow-circle-right"></i>
                                    </a>
                                </div>
                            </div>

                            <!-- Deuxième ligne : Licences -->
                            <div class="col-md-3 col-sm-6">
                                <div class="small-box bg-warning">
                                    <div class="inner">
                                        <h3>{{ $licences_stats['total_demandes'] ?? 0 }}</h3>
                                        <p>Demandes de licence</p>
                                    </div>
                                    <div class="icon">
                                        <i class="fas fa-file-pdf"></i>
                                    </div>
                                    <a href="{{ route('dir.demandeLicences') }}" class="small-box-footer">
                                        Voir <i class="fas fa-arrow-circle-right"></i>
                                    </a>
                                </div>
                            </div>
                            <div class="col-md-3 col-sm-6">
                                <div class="small-box bg-secondary">
                                    <div class="inner">
                                        <h3>{{ $licences_stats['total'] ?? 0 }}</h3>
                                        <p>Licences</p>
                                    </div>
                                    <div class="icon">
                                        <i class="fas fa-id-card"></i>
                                    </div>
                                    <a href="{{ route('dir.licences') }}" class="small-box-footer">
                                        Voir <i class="fas fa-arrow-circle-right"></i>
                                    </a>
                                </div>
                            </div>
                            <div class="col-md-3 col-sm-6">
                                <div class="small-box bg-success">
                                    <div class="inner">
                                        <h3>{{ $licences_stats['valides'] ?? 0 }}</h3>
                                        <p>Licences valides</p>
                                    </div>
                                    <div class="icon">
                                        <i class="fas fa-id-card"></i>
                                    </div>
                                    <a href="{{ route('dir.licences') }}" class="small-box-footer">
                                        Voir <i class="fas fa-arrow-circle-right"></i>
                                    </a>
                                </div>
                            </div>
                            {{--<div class="col-md-3 col-sm-6">
                                <div class="small-box bg-warning">
                                    <div class="inner">
                                        <h3>{{ $licences_stats['attente_signature'] ?? 0 }}</h3>
                                        <p>À signer (Lic.)</p>
                                    </div>
                                    <div class="icon">
                                        <i class="fas fa-signature"></i>
                                    </div>
                                    <a href="{{ route('dir.demandeLicences') }}" class="small-box-footer">
                                        Signer <i class="fas fa-arrow-circle-right"></i>
                                    </a>
                                </div>
                            </div>--}}
                            <div class="col-md-3 col-sm-6">
                                <div class="small-box bg-danger">
                                    <div class="inner">
                                        <h3>{{ $licences_stats['expirant_bientot'] ?? 0 }}</h3>
                                        <p>Licences expirant</p>
                                    </div>
                                    <div class="icon">
                                        <i class="fas fa-clock"></i>
                                    </div>
                                    <a href="{{ route('dir.demandeLicences') }}" class="small-box-footer">
                                        Voir <i class="fas fa-arrow-circle-right"></i>
                                    </a>
                                </div>
                            </div>
                        </div>

                        <!-- Tableaux côte à côte -->
                        <div class="row mt-4">
                            <!-- Autorisations récentes -->
                            <div class="col-md-6">
                                <div class="card">
                                    <div class="card-header bg-info text-white">
                                        <h4 class="card-title">
                                            <i class="fas fa-file-contract"></i> Autorisations récentes
                                        </h4>
                                    </div>
                                    <div class="card-body">
                                        <div class="table-responsive">
                                            <table class="table table-sm table-hover">
                                                <thead>
                                                    <tr>
                                                        <th>Code</th>
                                                        <th>Statut</th>
                                                        <th>Signature</th>
                                                    </tr>
                                                </thead>
                                                <tbody>
                                                    @foreach($recent_autorisations ?? [] as $autorisation)
                                                    <tr>
                                                        <td>
                                                            <strong>{{ $autorisation->code_autorisation }}</strong>
                                                        </td>
                                                        <td>
                                                            <span class="badge badge-warning">{{ strtoupper($autorisation->statut) }}</span>
                                                        </td>
                                                        <td>
                                                            @if($autorisation->signature_dg)
                                                                <span class="badge badge-success"><i class="fas fa-check"></i></span>
                                                            @else
                                                                <span class="badge badge-danger"><i class="fas fa-times"></i></span>
                                                            @endif
                                                        </td>
                                                    </tr>
                                                    @endforeach
                                                </tbody>
                                            </table>
                                        </div>
                                    </div>
                                </div>
                            </div>

                            <!-- Licences récentes -->
                            <div class="col-md-6">
                                <div class="card">
                                    <div class="card-header bg-secondary text-white">
                                        <h4 class="card-title">
                                            <i class="fas fa-id-card"></i> Licences récentes
                                        </h4>
                                    </div>
                                    <div class="card-body">
                                        <div class="table-responsive">
                                            <table class="table table-sm table-hover">
                                                <thead>
                                                    <tr>
                                                        <th>Numéro</th>
                                                        <th>Catégorie</th>
                                                        <th>Expiration</th>
                                                    </tr>
                                                </thead>
                                                <tbody>
                                                    @foreach($recent_licences ?? [] as $licence)
                                                    <tr>
                                                        <td>
                                                            <strong>{{ $licence->numero_licence }}</strong>
                                                        </td>
                                                        <td>{{ $licence->categorie_licence }}</td>
                                                        <td>
                                                            @if($licence->date_expiration)
                                                                @if(\Carbon\Carbon::parse($licence->date_expiration)->isPast())
                                                                    <span class="badge badge-danger">
                                                                        {{ \Carbon\Carbon::parse($licence->date_expiration)->format('d/m/Y') }}
                                                                    </span>
                                                                @elseif(\Carbon\Carbon::parse($licence->date_expiration)->diffInDays(now()) <= 30)
                                                                    <span class="badge badge-warning">
                                                                        {{ \Carbon\Carbon::parse($licence->date_expiration)->format('d/m/Y') }}
                                                                    </span>
                                                                @else
                                                                    <span class="badge badge-success">
                                                                        {{ \Carbon\Carbon::parse($licence->date_expiration)->format('d/m/Y') }}
                                                                    </span>
                                                                @endif
                                                            @else
                                                                <span class="text-muted">N/A</span>
                                                            @endif
                                                        </td>
                                                    </tr>
                                                    @endforeach
                                                </tbody>
                                            </table>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>

                    </div>
                </div>
            </div>
        </div>
        @endif

        <!-- ========== SECTION STATISTIQUES OPÉRATEURS (DG & DSV) ========== -->
        @if ((auth()->user()->hasRole('dg') || auth()->user()->hasRole('dsv')) && isset($compagnies))
                <div class="row justify-content-center">
    <div class="col-md-12">
        <div class="card">
            <div class="card-header bg-primary text-white">
                <h3 class="card-title">
                    <i class="fas fa-chart-line"></i> Statistiques des opérateurs
                </h3>
                <div class="card-tools">
                    <button type="button" class="btn btn-light btn-sm" data-toggle="modal" data-target="#infoModal">
                        <i class="fas fa-info-circle"></i> Info
                    </button>
                </div>
            </div>
            <div class="card-body">
                <!-- Résumé en haut -->
                <div class="row mb-4">
                    <div class="col-md-3 col-sm-6">
                        <div class="info-box bg-gradient-info">
                            <span class="info-box-icon"><i class="fas fa-building"></i></span>
                            <div class="info-box-content">
                                <span class="info-box-text">Opérateurs</span>
                                <span class="info-box-number">{{ $compagnies->count() }}</span>
                            </div>
                        </div>
                    </div>
                    <div class="col-md-3 col-sm-6">
                        <div class="info-box bg-gradient-success">
                            <span class="info-box-icon"><i class="fas fa-hand-holding-usd"></i></span>
                            <div class="info-box-content">
                                <span class="info-box-text">Total Recettes</span>
                                <span class="info-box-number">{{ number_format($compagnies->sum('total_recettes'), 0, ',', ' ') }} MRU</span>
                            </div>
                        </div>
                    </div>
                    <div class="col-md-3 col-sm-6">
                        <div class="info-box bg-gradient-warning">
                            <span class="info-box-icon"><i class="fas fa-exclamation-triangle"></i></span>
                            <div class="info-box-content">
                                <span class="info-box-text">Plafonds dépassés</span>
                                <span class="info-box-number">{{ $compagnies->where('depasse_plafond', 1)->count() }}</span>
                            </div>
                        </div>
                    </div>
                    <div class="col-md-3 col-sm-6">
                        <div class="info-box bg-gradient-danger">
                            <span class="info-box-icon"><i class="fas fa-percentage"></i></span>
                            <div class="info-box-content">
                                <span class="info-box-text">Moyenne dépassement</span>
                                <span class="info-box-number">{{ number_format($compagnies->where('depasse_plafond', 1)->avg('pourcentage_plafond') ?? 0, 1) }}%</span>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Tableau principal -->
                <div class="table-responsive">
                    <table class="table table-striped table-hover" id="compagniesTable">
                        <thead>
                            <tr>
                                <th>#</th>
                                <th>Nom Entreprise</th>
                                <th>Panier (MRU)</th>
                                <th>Plafond (MRU)</th>
                                <th>Total Recettes (MRU)</th>
                                <th>Pourcentage</th>
                                <th>Statut</th>
                                <th>Actions</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach ($compagnies as $index => $compagny)
                                @php
                                    $pourcentage = round($compagny->pourcentage_plafond, 1);
                                    $depassePlafond = $compagny->depasse_plafond == 1;
                                    $progressBarColor = $depassePlafond ? 'bg-danger' : ($pourcentage > 80 ? 'bg-warning' : 'bg-success');
                                    $statusClass = $depassePlafond ? 'badge-danger' : ($pourcentage > 80 ? 'badge-warning' : 'badge-success');
                                    $statusText = $depassePlafond ? 'Dépassé' : ($pourcentage > 80 ? 'Risque' : 'Normal');
                                @endphp
                                <tr class="{{ $depassePlafond ? 'table-danger' : '' }}">
                                    <td>{{ $index + 1 }}</td>
                                    <td>
                                        <strong>{{ $compagny->nom_entreprise }}</strong>
                                        @if($depassePlafond)
                                            <span class="float-right">
                                                <i class="fas fa-exclamation-circle text-danger" 
                                                   title="Plafond dépassé"></i>
                                            </span>
                                        @endif
                                    </td>
                                    <td>{{ number_format($compagny->panier, 0, ',', ' ') }}</td>
                                    <td>{{ number_format($compagny->plafond, 0, ',', ' ') }}</td>
                                    <td>
                                        <strong>{{ number_format($compagny->total_recettes, 0, ',', ' ') }}</strong>
                                        @if($depassePlafond)
                                            <br>
                                            <small class="text-danger">
                                                <i class="fas fa-arrow-up"></i>
                                                +{{ number_format($compagny->total_recettes - $compagny->plafond, 0, ',', ' ') }}
                                            </small>
                                        @endif
                                    </td>
                                    <td>
                                        <div class="progress-group">
                                            <span class="progress-text">{{ $pourcentage }}%</span>
                                            <div class="progress">
                                                <div class="progress-bar {{ $progressBarColor }}" 
                                                     style="width: {{ min($pourcentage, 100) }}%"
                                                     role="progressbar" 
                                                     aria-valuenow="{{ $pourcentage }}" 
                                                     aria-valuemin="0" 
                                                     aria-valuemax="100">
                                                </div>
                                            </div>
                                        </div>
                                    </td>
                                    <td>
                                        <span class="badge {{ $statusClass }} badge-pill">
                                            {{ $statusText }}
                                        </span>
                                    </td>
                                    <td>
                                        <div class="btn-group btn-group-sm">
                                            <button type="button" class="btn btn-info" 
                                                    onclick="showCompagnyDetails({{ $compagny->compagnie_id }})"
                                                    title="Voir détails">
                                                <i class="fas fa-eye"></i>
                                            </button>
                                            @if($depassePlafond)
                                                <button type="button" class="btn btn-warning" 
                                                        onclick="sendReminder({{ $compagny->compagnie_id }})"
                                                        title="Envoyer rappel">
                                                    <i class="fas fa-bell"></i>
                                                </button>
                                            @endif
                                        </div>
                                    </td>
                                </tr>
                            @endforeach
                        </tbody>
                        <tfoot>
                            <tr class="bg-light">
                                <td colspan="3"><strong>TOTAUX</strong></td>
                                <td><strong>{{ number_format($compagnies->sum('plafond'), 0, ',', ' ') }}</strong></td>
                                <td><strong>{{ number_format($compagnies->sum('total_recettes'), 0, ',', ' ') }}</strong></td>
                                <td colspan="3"></td>
                            </tr>
                        </tfoot>
                    </table>
                </div>
            </div>
        </div>
    </div>
</div>
        @endif

@if (auth()->user()->hasRole('dsv') || auth()->user()->hasRole('dg') || auth()->user()->hasRole('dta'))
    <div class="row justify-content-center mt-4">
        <div class="col-md-12">
            <div class="card">
                <div class="card-header bg-info text-white">
                    <h3 class="card-title">
                        <i class="fas fa-chart-bar"></i> Statistiques globales des demandes
                    </h3>
                </div>
                <div class="card-body">
                    <!-- Affichage du nombre de demandeurs -->
                    <div class="alert alert-info">
                        @lang('trans.total_applicants') : <strong id="nombreDemandeurs">0</strong>
                    </div>

                    <!-- Tabs pour les différents types de graphiques -->
                    <ul class="nav nav-tabs" id="statisticsTabs" role="tablist">
                        <li class="nav-item">
                            <a class="nav-link active" id="daily-tab" data-toggle="tab" href="#daily" role="tab">
                                <i class="fas fa-calendar-day"></i> Par Jour
                            </a>
                        </li>
                        <li class="nav-item">
                            <a class="nav-link" id="monthly-tab" data-toggle="tab" href="#monthly" role="tab">
                                <i class="fas fa-calendar-alt"></i> Par Mois
                            </a>
                        </li>
                        <li class="nav-item">
                            <a class="nav-link" id="yearly-tab" data-toggle="tab" href="#yearly" role="tab">
                                <i class="fas fa-calendar"></i> Par Année
                            </a>
                        </li>
                        @if(auth()->user()->hasRole('dta') || auth()->user()->hasRole('dg'))
                        <li class="nav-item">
                            <a class="nav-link" id="type-tab" data-toggle="tab" href="#type" role="tab">
                                <i class="fas fa-plane"></i> Par Type (DTA)
                            </a>
                        </li>
                        @endif
                        @if(auth()->user()->hasRole('dsv') || auth()->user()->hasRole('dg'))
                        <li class="nav-item">
                            <a class="nav-link" id="licence-tab" data-toggle="tab" href="#licence" role="tab">
                                <i class="fas fa-id-card"></i> Licences (DSV)
                            </a>
                        </li>
                        @endif
                    </ul>

                    <!-- Contenu des tabs -->
                    <div class="tab-content mt-3" id="statisticsTabsContent">
                        <!-- Tab Par Jour -->
                        <div class="tab-pane fade show active" id="daily" role="tabpanel">
                            <canvas id="chartJour" width="400" height="200"></canvas>
                            <div class="mt-3">
                                <small class="text-muted">
                                    <i class="fas fa-info-circle"></i> Statistiques des 7 derniers jours
                                </small>
                            </div>
                        </div>

                        <!-- Tab Par Mois -->
                        <div class="tab-pane fade" id="monthly" role="tabpanel">
                            <canvas id="chartMois" width="400" height="200"></canvas>
                            <div class="mt-3">
                                <small class="text-muted">
                                    <i class="fas fa-info-circle"></i> Statistiques de l'année en cours
                                </small>
                            </div>
                        </div>

                        <!-- Tab Par Année -->
                        <div class="tab-pane fade" id="yearly" role="tabpanel">
                            <canvas id="chartAnnee" width="400" height="200"></canvas>
                            <div class="mt-3">
                                <small class="text-muted">
                                    <i class="fas fa-info-circle"></i> Statistiques sur 5 ans
                                </small>
                            </div>
                        </div>

                        <!-- Tab Par Type (DTA & DG) -->
                        @if(auth()->user()->hasRole('dta') || auth()->user()->hasRole('dg'))
                        <div class="tab-pane fade" id="type" role="tabpanel">
                            <div class="row">
                                <div class="col-md-6">
                                    <canvas id="chartTypeAutorisation" height="250"></canvas>
                                </div>
                                <div class="col-md-6">
                                    <div class="table-responsive">
                                        <table class="table table-sm table-hover" id="typeTable">
                                            <thead>
                                                <tr>
                                                    <th>Type d'autorisation</th>
                                                    <th>Total</th>
                                                    <th>Valides</th>
                                                    <th>Taux</th>
                                                </tr>
                                            </thead>
                                            <tbody id="typeTableBody">
                                                <!-- Données chargées via JS -->
                                            </tbody>
                                        </table>
                                    </div>
                                </div>
                            </div>
                            <div class="mt-3">
                                <small class="text-muted">
                                    <i class="fas fa-info-circle"></i> Répartition des autorisations par type
                                </small>
                            </div>
                        </div>
                        @endif

                        <!-- Tab Licences (DSV & DG) -->
                        @if(auth()->user()->hasRole('dsv') || auth()->user()->hasRole('dg'))
                        <div class="tab-pane fade" id="licence" role="tabpanel">
                            <div class="row">
                                <div class="col-md-6">
                                    <canvas id="chartLicenceStatus" height="250"></canvas>
                                </div>
                                <div class="col-md-6">
                                    <div class="table-responsive">
                                        <table class="table table-sm table-hover" id="licenceTable">
                                            <thead>
                                                <tr>
                                                    <th>Catégorie</th>
                                                    <th>Total</th>
                                                    <th>Valides</th>
                                                    <th>Expirant</th>
                                                </tr>
                                            </thead>
                                            <tbody id="licenceTableBody">
                                                <!-- Données chargées via JS -->
                                            </tbody>
                                        </table>
                                    </div>
                                </div>
                            </div>
                            <div class="mt-3">
                                <small class="text-muted">
                                    <i class="fas fa-info-circle"></i> Statistiques des licences par catégorie
                                </small>
                            </div>
                        </div>
                        @endif
                    </div>

                    <!-- Légende -->
                    <div class="row mt-4">
                        <div class="col-12">
                            <div class="alert alert-light">
                                <h6><i class="fas fa-key"></i> Légende :</h6>
                                <div class="row">

                                        @if (auth()->user()->hasRole('dta') || auth()->user()->hasRole('dg'))
                                            <div class="col-md-3">
                                                <span class="badge badge-success mr-2">■</span> Demandes/Autorisations traitées/valides
                                            </div>
                                            <div class="col-md-3">
                                                <span class="badge badge-danger mr-2">■</span> Demandes/Autorisations non traitées/expirées
                                            </div>
                                            <div class="col-md-3">
                                                <span class="badge badge-warning mr-2">■</span> Demandes/Autorisations en cours/en attente
                                            </div>
                                        @endif
                                        @if (auth()->user()->hasRole('dsv') || auth()->user()->hasRole('dg'))
                                            <div class="col-md-3">
                                                <span class="badge badge-info mr-2">■</span> Licences expirant bientôt
                                            </div>
                                        @endif
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
@endif

    </div>

    <!-- Modal Info -->
<div class="modal fade" id="infoModal" tabindex="-1" role="dialog">
    <div class="modal-dialog" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title">
                    <i class="fas fa-info-circle text-primary"></i> Informations
                </h5>
                <button type="button" class="close" data-dismiss="modal">
                    <span>&times;</span>
                </button>
            </div>
            <div class="modal-body">
                <div class="alert alert-info">
                    <h6><i class="fas fa-lightbulb"></i> Légende des statuts:</h6>
                    <ul>
                        <li><span class="badge badge-success">Normal</span> : Plafond respecté</li>
                        <li><span class="badge badge-warning">Risque</span> : > 80% du plafond</li>
                        <li><span class="badge badge-danger">Dépassé</span> : Plafond dépassé</li>
                    </ul>
                </div>
                
            </div>
        </div>
    </div>
</div>
<!-- Modal pour les détails -->
<div class="modal fade" id="detailsModal" tabindex="-1" role="dialog">
    <div class="modal-dialog modal-lg" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title">Détails de l'opérateur</h5>
                <button type="button" class="close" data-dismiss="modal">
                    <span>&times;</span>
                </button>
            </div>
            <div class="modal-body" id="detailsModalContent">
                <!-- Contenu chargé via AJAX -->
            </div>
        </div>
    </div>
</div>
@endsection

@push('script')
    <script src="https://cdn.jsdelivr.net/npm/chart.js@3.0.0/dist/chart.min.js"></script>
    <script src="{{ asset('assets/admin/plugins/datatables/jquery.dataTables.min.js') }}"></script>
    <script src="{{ asset('assets/admin/plugins/datatables-bs4/js/dataTables.bootstrap4.min.js') }}"></script>
    <script src="{{ asset('assets/admin/plugins/datatables-responsive/js/dataTables.responsive.min.js') }}"></script>
    <script src="{{ asset('assets/admin/plugins/datatables-responsive/js/responsive.bootstrap4.min.js') }}"></script>
    <script src="{{ asset('assets/admin/plugins/toastr/toastr.min.js') }}"></script>
@endpush

@push('custom')

<script>
    $(document).ready(function() {
        // Initialiser DataTables si nécessaire
        if ($.fn.DataTable) {
            $('.table').DataTable();
        }
    });

    // Fonctions pour les détails des opérateurs
    function showCompagnyDetails(compagnieId) {
        $.ajax({
            url: "{{ route('compagny.details') }}",
            method: 'GET',
            data: { compagnie_id: compagnieId },
            success: function(response) {
                $('#detailsModalContent').html(response);
                $('#detailsModal').modal('show');
            }
        });
    }

    function sendReminder(compagnieId) {
        if (confirm("Envoyer un rappel WhatsApp à cet opérateur ?")) {
            $.ajax({
                url: "{{ route('compagny.send.reminder') }}",
                method: 'POST',
                data: { 
                    compagnie_id: compagnieId,
                    _token: '{{ csrf_token() }}'
                },
                success: function(response) {
                    toastr.success('Rappel envoyé avec succès !');
                },
                error: function() {
                    toastr.error('Erreur lors de l\'envoi du rappel');
                }
            });
        }
    }
</script>
<script>
    $(document).ready(function() {
        // Initialiser les tabs
        $('#statisticsTabs a').on('click', function (e) {
            e.preventDefault();
            $(this).tab('show');
        });

        // Charger les données statistiques
        loadStatisticsData();
        
        // Actualiser les données toutes les 5 minutes
        setInterval(loadStatisticsData, 300000);
    });

    function loadStatisticsData() {
        showLoading();
        
        fetch("{{ route('dir.data') }}")
            .then(response => {
                if (!response.ok) {
                    throw new Error('Network response was not ok');
                }
                return response.json();
            })
            .then(data => {
                hideLoading();
                
                // Vérifier si les données sont valides
                if (!data || typeof data !== 'object') {
                    throw new Error('Données invalides reçues du serveur');
                }
                
                // Mettre à jour le nombre de demandeurs
                if (document.getElementById('nombreDemandeurs')) {
                    document.getElementById('nombreDemandeurs').innerText = 
                        formatNumber(data.nombreDemandeurs || 0);
                }

                // Graphique des demandes par jour
                if (document.getElementById('chartJour') && data.demandesParJour) {
                    renderChart('chartJour', 
                        data.demandesParJour.map(d => d.date || ''),
                        data.demandesParJour.map(d => parseInt(d.traitees) || 0),
                        data.demandesParJour.map(d => parseInt(d.non_traitees) || 0),
                        'Demandes par jour'
                    );
                }

                // Graphique des demandes par mois
                if (document.getElementById('chartMois') && data.demandesParMois) {
                    renderChart('chartMois', 
                        data.demandesParMois.map(d => d.mois || ''),
                        data.demandesParMois.map(d => parseInt(d.traitees) || 0),
                        data.demandesParMois.map(d => parseInt(d.non_traitees) || 0),
                        'Demandes par mois'
                    );
                }

                // Graphique des demandes par année
                if (document.getElementById('chartAnnee') && data.demandesParAnnee) {
                    renderChart('chartAnnee', 
                        data.demandesParAnnee.map(d => d.annee || ''),
                        data.demandesParAnnee.map(d => parseInt(d.traitees) || 0),
                        data.demandesParAnnee.map(d => parseInt(d.non_traitees) || 0),
                        'Demandes par année'
                    );
                }

                // Graphique par type d'autorisation (DTA & DG)
                if (data.stats_par_type && document.getElementById('chartTypeAutorisation')) {
                    renderTypeChart(data.stats_par_type);
                    updateTypeTable(data.stats_par_type);
                }

                // Graphique des licences (DSV & DG)
                if (data.licences_par_categorie && document.getElementById('chartLicenceStatus')) {
                    renderLicenceChart(data.licences_par_categorie);
                    updateLicenceTable(data.licences_par_categorie);
                }
            })
            .catch(error => {
                hideLoading();
                console.error('Erreur lors du chargement des statistiques:', error);
                showErrorMessage('Impossible de charger les statistiques: ' + error.message);
            });
    }

    function formatNumber(num) {
        // Formater un nombre sans problème de fractionDigits
        if (isNaN(num) || num === null || num === undefined) {
            return '0';
        }
        
        const n = parseInt(num);
        if (isNaN(n)) {
            return '0';
        }
        
        // Formater avec séparateur de milliers
        return n.toString().replace(/\B(?=(\d{3})+(?!\d))/g, ' ');
    }

    function renderChart(canvasId, labels, dataTraitees, dataNonTraitees, title) {
        const ctx = document.getElementById(canvasId);
        if (!ctx) return;
        
        // S'assurer que les données sont des nombres
        const safeDataTraitees = dataTraitees.map(d => parseInt(d) || 0);
        const safeDataNonTraitees = dataNonTraitees.map(d => parseInt(d) || 0);
        
        // Détruire le graphique existant s'il y en a un
        if (ctx.chart && typeof ctx.chart.destroy === 'function') {
            ctx.chart.destroy();
        }
        
        try {
            ctx.chart = new Chart(ctx, {
                type: 'bar',
                data: {
                    labels: labels,
                    datasets: [{
                        label: 'Traités/Validés',
                        backgroundColor: '#28a745',
                        data: safeDataTraitees
                    },
                    {
                        label: 'Non traités/En attente',
                        backgroundColor: '#dc3545',
                        data: safeDataNonTraitees
                    }]
                },
                options: {
                    responsive: true,
                    plugins: {
                        title: {
                            display: true,
                            text: title,
                            font: {
                                size: 16
                            }
                        },
                        legend: {
                            display: true,
                            position: 'top'
                        },
                        tooltip: {
                            callbacks: {
                                label: function(context) {
                                    return `${context.dataset.label}: ${formatNumber(context.raw)}`;
                                }
                            }
                        }
                    },
                    scales: {
                        x: {
                            title: {
                                display: true,
                                text: 'Période'
                            }
                        },
                        y: {
                            beginAtZero: true,
                            title: {
                                display: true,
                                text: 'Nombre'
                            },
                            ticks: {
                                callback: function(value) {
                                    return formatNumber(value);
                                }
                            }
                        }
                    }
                }
            });
        } catch (error) {
            console.error('Erreur création graphique:', error);
            showErrorMessage(`Erreur création graphique ${canvasId}: ${error.message}`);
        }
    }

    function renderTypeChart(stats) {
        const ctx = document.getElementById('chartTypeAutorisation');
        if (!ctx) return;
        
        if (ctx.chart && typeof ctx.chart.destroy === 'function') {
            ctx.chart.destroy();
        }
        
        const labels = stats.map(s => s.type || 'Non spécifié');
        const dataTotal = stats.map(s => parseInt(s.total) || 0);
        const dataValides = stats.map(s => parseInt(s.valides) || 0);
        const dataEnCours = stats.map(s => parseInt(s.en_cours) || 0);
        
        try {
            ctx.chart = new Chart(ctx, {
                type: 'bar',
                data: {
                    labels: labels,
                    datasets: [{
                        label: 'Total',
                        backgroundColor: '#17a2b8',
                        data: dataTotal
                    },
                    {
                        label: 'Validées',
                        backgroundColor: '#28a745',
                        data: dataValides
                    },
                    {
                        label: 'En cours',
                        backgroundColor: '#ffc107',
                        data: dataEnCours
                    }]
                },
                options: {
                    responsive: true,
                    plugins: {
                        title: {
                            display: true,
                            text: 'Autorisations par type',
                            font: {
                                size: 16
                            }
                        },
                        legend: {
                            display: true,
                            position: 'top'
                        },
                        tooltip: {
                            callbacks: {
                                label: function(context) {
                                    return `${context.dataset.label}: ${formatNumber(context.raw)}`;
                                }
                            }
                        }
                    },
                    scales: {
                        x: {
                            title: {
                                display: true,
                                text: 'Type d\'autorisation'
                            }
                        },
                        y: {
                            beginAtZero: true,
                            title: {
                                display: true,
                                text: 'Nombre d\'autorisations'
                            },
                            ticks: {
                                callback: function(value) {
                                    return formatNumber(value);
                                }
                            }
                        }
                    }
                }
            });
        } catch (error) {
            console.error('Erreur création graphique type:', error);
            showErrorMessage('Erreur création graphique par type');
        }
    }

    function updateTypeTable(stats) {
        const tbody = document.getElementById('typeTableBody');
        if (!tbody) return;
        
        let html = '';
        stats.forEach(stat => {
            const total = parseInt(stat.total) || 0;
            const valides = parseInt(stat.valides) || 0;
            const taux = total > 0 ? Math.round((valides / total) * 100) : 0;
            const colorClass = taux >= 80 ? 'success' : taux >= 50 ? 'warning' : 'danger';
            
            html += `
                <tr>
                    <td><strong>${stat.type || 'Non spécifié'}</strong></td>
                    <td>${formatNumber(total)}</td>
                    <td>
                        <span class="badge badge-${valides > 0 ? 'success' : 'secondary'}">
                            ${formatNumber(valides)}
                        </span>
                    </td>
                    <td>
                        <div class="progress" style="height: 20px;">
                            <div class="progress-bar bg-${colorClass}" 
                                 style="width: ${taux}%"
                                 role="progressbar">
                                ${taux}%
                            </div>
                        </div>
                    </td>
                </tr>
            `;
        });
        
        tbody.innerHTML = html;
    }

    function renderLicenceChart(stats) {
        const ctx = document.getElementById('chartLicenceStatus');
        if (!ctx) return;
        
        if (ctx.chart && typeof ctx.chart.destroy === 'function') {
            ctx.chart.destroy();
        }
        
        const labels = stats.map(s => s.categorie || 'Non spécifié');
        const dataValides = stats.map(s => parseInt(s.valides) || 0);
        const dataExpirant = stats.map(s => parseInt(s.expirant_soon) || 0);
        const dataExpirees = stats.map(s => parseInt(s.expirees) || 0);
        
        try {
            ctx.chart = new Chart(ctx, {
                type: 'bar',
                data: {
                    labels: labels,
                    datasets: [{
                        label: 'Valides',
                        backgroundColor: '#28a745',
                        data: dataValides
                    },
                    {
                        label: 'Expirant bientôt',
                        backgroundColor: '#ffc107',
                        data: dataExpirant
                    },
                    {
                        label: 'Expirées/Bloquées',
                        backgroundColor: '#dc3545',
                        data: dataExpirees
                    }]
                },
                options: {
                    responsive: true,
                    plugins: {
                        title: {
                            display: true,
                            text: 'Statut des licences par catégorie',
                            font: {
                                size: 16
                            }
                        },
                        legend: {
                            display: true,
                            position: 'top'
                        },
                        tooltip: {
                            callbacks: {
                                label: function(context) {
                                    return `${context.dataset.label}: ${formatNumber(context.raw)}`;
                                }
                            }
                        }
                    },
                    scales: {
                        x: {
                            title: {
                                display: true,
                                text: 'Catégorie de licence'
                            }
                        },
                        y: {
                            beginAtZero: true,
                            title: {
                                display: true,
                                text: 'Nombre de licences'
                            },
                            ticks: {
                                callback: function(value) {
                                    return formatNumber(value);
                                }
                            }
                        }
                    }
                }
            });
        } catch (error) {
            console.error('Erreur création graphique licences:', error);
            showErrorMessage('Erreur création graphique licences');
        }
    }

    function updateLicenceTable(stats) {
        const tbody = document.getElementById('licenceTableBody');
        if (!tbody) return;
        
        let html = '';
        stats.forEach(stat => {
            const total = parseInt(stat.total) || 0;
            const valides = parseInt(stat.valides) || 0;
            const expirant = parseInt(stat.expirant_soon) || 0;
            const tauxValidite = total > 0 ? Math.round((valides / total) * 100) : 0;
            
            html += `
                <tr>
                    <td><strong>${stat.categorie || 'Non spécifié'}</strong></td>
                    <td>${formatNumber(total)}</td>
                    <td>
                        <span class="badge badge-${valides > 0 ? 'success' : 'secondary'}">
                            ${formatNumber(valides)}
                        </span>
                    </td>
                    <td>
                        <span class="badge badge-${expirant > 0 ? 'warning' : 'secondary'}">
                            ${formatNumber(expirant)}
                        </span>
                    </td>
                </tr>
            `;
        });
        
        tbody.innerHTML = html;
    }

    function showLoading() {
        // Afficher un indicateur de chargement
        const loadingDiv = document.getElementById('statisticsLoading');
        if (!loadingDiv) {
            const div = document.createElement('div');
            div.id = 'statisticsLoading';
            div.className = 'text-center py-3';
            div.innerHTML = `
                <div class="spinner-border text-primary" role="status">
                    <span class="sr-only">Chargement...</span>
                </div>
                <p class="mt-2 text-muted">Chargement des statistiques...</p>
            `;
            const cardBody = document.querySelector('.card-body');
            if (cardBody) {
                cardBody.insertBefore(div, cardBody.firstChild);
            }
        }
    }

    function hideLoading() {
        const loadingDiv = document.getElementById('statisticsLoading');
        if (loadingDiv) {
            loadingDiv.remove();
        }
    }

    function showErrorMessage(message) {
        const errorDiv = document.getElementById('statisticsError');
        if (!errorDiv) {
            const div = document.createElement('div');
            div.id = 'statisticsError';
            div.className = 'alert alert-danger alert-dismissible fade show';
            div.innerHTML = `
                <i class="fas fa-exclamation-triangle"></i> ${message}
                <button type="button" class="close" data-dismiss="alert">
                    <span>&times;</span>
                </button>
            `;
            const cardBody = document.querySelector('.card-body');
            if (cardBody) {
                cardBody.insertBefore(div, cardBody.firstChild);
            }
        } else {
            errorDiv.querySelector('i').nextSibling.textContent = ' ' + message;
        }
        
        // Auto-remove après 10 secondes
        setTimeout(() => {
            if (errorDiv) {
                errorDiv.remove();
            }
        }, 10000);
    }
</script>
@endpush