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
    @lang('trans.dashboard_dir', ['role' => strtoupper(auth()->user()->getRoleNames()->first() ?? '')]) </a>
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
    @lang('trans.dashboard_dir', ['role' => strtoupper(auth()->user()->getRoleNames()->first() ?? '')]) </a>
@endif
@endsection
@section('contentheaderactive')
@lang('trans.dashboard_dir', ['role' => strtoupper(auth()->user()->getRoleNames()->first() ?? '')])
@endsection

@push('css')
<link rel="stylesheet" href="{{ asset('assets/admin/plugins/datatables-bs4/css/dataTables.bootstrap4.min.css') }}">
<link rel="stylesheet" href="{{ asset('assets/admin/plugins/datatables-responsive/css/responsive.bootstrap4.min.css') }}">
<link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/summernote/0.8.18/summernote-bs4.min.css">
<link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/toastr.js/latest/toastr.min.css">
<style>
    .validation-badge {
        font-size: 0.8rem;
        padding: 3px 8px;
        border-radius: 10px;
    }

    .validated {
        background-color: #28a745;
        color: white;
    }

    .not-validated {
        background-color: #dc3545;
        color: white;
    }

    .pending {
        background-color: #ffc107;
        color: black;
    }

    .validation-comments {
        font-size: 0.9rem;
        color: #6c757d;
        margin-top: 5px;
    }


    @keyframes pulse {
        0% {
            transform: scale(1);
        }

        50% {
            transform: scale(1.1);
        }

        100% {
            transform: scale(1);
        }
    }
</style>
@endpush

@section('content')
<div class="container-fluid">
    <div class="card border-primary"> <!-- Ajoutez cette div avec une bordure -->
        <div class="card-header bg-primary text-white">
            <h4 class="card-title mb-0">Fiche de Demande d'Autorisation</h4>
        </div>
        <div class="card-body">

            <div class="row justify-content-center">
                <div class="col-md-12">
                   
            <h4 class="text-center">
            {{ $demandeAutorisation->type->libelle }} - 
            @if($demandeAutorisation->type_demande_autorisation_id == 3)
                {{ $demandeAutorisation->type_vol_names }}
            @else
                {{ $demandeAutorisation->typeVol->nom ?? 'N/A' }}
            @endif
            - {{ $demandeAutorisation->date_debut }} -
            {{ $demandeAutorisation->date_fin }} - {{ $demandeAutorisation->user->demandeur->np ?? 'N/A' }}
            - {{ strtoupper($demandeAutorisation->objet) ?? 'N/A' }}
            @if (!empty($demandeAutorisation->sous_validite))
                - {{ '+' . $demandeAutorisation->sous_validite }} H
            @endif
        </h4>
                    <h6 class="text-center">
                        @if (!empty($demandeAutorisation->points) && !$demandeAutorisation->isValidatedByAll() && auth()->user()->hasRole(['dsv', 'dsna', 'dsad']))
                        {!! $demandeAutorisation->points !!}
                        @endif
                    </h6>

                    @php
                        $hasInvalidItems = $demandeAutorisation->hasInvalidComponents();
                        $autorisation = $demandeAutorisation->autorisation($demandeAutorisation->id);
                        $hasAutorisation = !empty($autorisation);
                        // Verrouille toutes les actions (checkboxes, boutons individuels, actions groupées)
                        // dès qu'une ligne est rejetée OU qu'une autorisation a déjà été délivrée pour cette demande.
                        $hasRejectedItem = $hasInvalidItems || $hasAutorisation;
                    @endphp

                    @include('dir.demandeAutorisations.partials.rejected-by', ['demande' => $demandeAutorisation])
                    {{-- Filet de sécurité : affiche le motif même si le flag dta_rejeter/dg_rejeter
                         n'est pas synchronisé avec le motif enregistré. --}}
                    @include('dir.demandeAutorisations.partials.rejection-reasons-list', ['demande' => $demandeAutorisation])
                    @include('dir.demandeAutorisations.partials.invalid-components', ['demande' => $demandeAutorisation])

                    @unless($hasRejectedItem)
                        <!-- Barre d'action groupée multi-sections (avions, vols, équipage, ...) -->
                        <div class="alert alert-secondary d-flex justify-content-between align-items-center" id="globalBulkBar" style="display:none !important;">
                            <span><i class="fas fa-layer-group"></i> <span id="globalBulkCount">0</span> @lang('trans.items_selected_across_sections')</span>
                            <span>
                                <button type="button" class="btn btn-success btn-sm" id="globalApproveBtn" disabled>
                                    <i class="fas fa-check-double"></i> @lang('trans.approve_selected')
                                </button>
                                <button type="button" class="btn btn-danger btn-sm" id="globalRejectBtn" disabled>
                                    <i class="fas fa-times"></i> @lang('trans.reject_selected')
                                </button>
                            </span>
                        </div>
                    @endunless

                    <!-- BOUTON TOUT VALIDER - NOUVEAU -->
                    @if ($hasAutorisation && auth()->user()->hasRole('dta'))
                    <div class="row mb-4">
                        <div class="col-md-12 text-center">
                            <div class="alert alert-info d-inline-block">
                                <i class="fas fa-lock"></i> @lang('trans.autorisation_already_issued')
                            </div>
                        </div>
                    </div>
                    @elseif ($hasInvalidItems && auth()->user()->hasRole('dta'))
                    <div class="row mb-4">
                        <div class="col-md-12 text-center">
                            <div class="alert alert-warning d-inline-block">
                                <i class="fas fa-exclamation-triangle"></i> @lang('trans.validation_blocked_by_rejection')
                            </div>
                        </div>
                    </div>
                    @elseif (auth()->user()->hasRole('dta') && !$demandeAutorisation->isFullyValidated())
                    <div class="row mb-4">
                        <div class="col-md-12 text-center">

                            <button type="submit" class="btn btn-success btn-lg" onclick="validateAllItems()">
                                <i class="fas fa-check-double"></i> Tout Valider
                            </button>
                        </div>
                    </div>



                    @endif
                    <!-- Modal pour Tout Valider -->
                    <div class="modal fade" id="validateAllModal" tabindex="-1" role="dialog"
                        aria-labelledby="validateAllModalLabel" aria-hidden="true">
                        <div class="modal-dialog" role="document">
                            <div class="modal-content">
                                <div class="modal-header">
                                    <h5 class="modal-title" id="validateAllModalLabel">Validation Globale</h5>
                                    <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                                        <span aria-hidden="true">&times;</span>
                                    </button>
                                </div>
                                <form id="validateAllForm" method="POST">
                                    @csrf
                                    <div class="modal-body">
                                        <input type="hidden" name="demande_id" value="{{ $demandeAutorisation->id }}">

                                        <div class="alert alert-info">
                                            <i class="fas fa-info-circle"></i>
                                            Vous êtes sur le point de valider tous les éléments de cette demande.
                                            Voulez-vous continuer ?
                                        </div>

                                        <!-- Liste des éléments à valider -->
                                        <div class="mt-3">
                                            <h6>Éléments à valider :</h6>
                                            <ul class="list-group">
                                                @if(isset($avions) && $avions->isNotEmpty())
                                                <li class="list-group-item d-flex justify-content-between align-items-center">
                                                    Avions
                                                    <span class="badge badge-primary badge-pill">{{ $avions->count() }}</span>
                                                </li>
                                                @endif

                                                @if(isset($vols) && $vols->isNotEmpty())
                                                <li class="list-group-item d-flex justify-content-between align-items-center">
                                                    Vols
                                                    <span class="badge badge-primary badge-pill">{{ $vols->count() }}</span>
                                                </li>
                                                @endif

                                                @if($equipe_vols->isNotEmpty())
                                                <li class="list-group-item d-flex justify-content-between align-items-center">
                                                    Équipage
                                                    <span class="badge badge-primary badge-pill">{{ $equipe_vols->count() }}</span>
                                                </li>
                                                @endif

                                                @if($fretVols->isNotEmpty())
                                                <li class="list-group-item d-flex justify-content-between align-items-center">
                                                    Fret
                                                    <span class="badge badge-primary badge-pill">{{ $fretVols->count() }}</span>
                                                </li>
                                                @endif

                                                @if($receivingParties->isNotEmpty())
                                                <li class="list-group-item d-flex justify-content-between align-items-center">
                                                    Receiving Parties
                                                    <span class="badge badge-primary badge-pill">{{ $receivingParties->count() }}</span>
                                                </li>
                                                @endif

                                                @if($demandeAutorisation->hasDocuments())
                                                <li class="list-group-item d-flex justify-content-between align-items-center">
                                                    Documents
                                                    <span class="badge badge-primary badge-pill">{{ $demandeAutorisation->documents->count() }}</span>
                                                </li>
                                                @endif
                                            </ul>
                                        </div>
                                    </div>
                                    <div class="modal-footer">
                                        <button type="button" class="btn btn-secondary" data-dismiss="modal">Annuler</button>
                                        <button type="submit" class="btn btn-success">
                                            <i class="fas fa-check-double"></i> Tout Valider
                                        </button>
                                    </div>
                                </form>
                            </div>
                        </div>
                    </div>

                    <!-- Avion Section -->
                    <div class="card card-primary">
                        <div class="card-header bg-primary text-white d-flex justify-content-between align-items-center">
                            <h3 class="card-title">Information sur l'avion</h3>

                        </div>
                        @if (isset($avions) && $avions->isNotEmpty())
                        <div class="card-body">
                           


                            @if (auth()->user()->hasRole('dta') && !$hasRejectedItem)
                            <div class="mb-2">
                                <button type="button" class="btn btn-success btn-sm bulk-approve-btn" data-type="avions" data-demande-id="{{ $demandeAutorisation->id }}" disabled>
                                    <i class="fas fa-check-double"></i> @lang('trans.approve_selected')
                                </button>
                                <button type="button" class="btn btn-danger btn-sm bulk-reject-btn" data-type="avions" data-demande-id="{{ $demandeAutorisation->id }}" disabled>
                                    <i class="fas fa-times"></i> @lang('trans.reject_selected')
                                </button>
                            </div>
                            @endif
                            <div class="row mt-4" id="avionsTableContainer">
                                <div class="col-lg-12">
                                    <div class="table-responsive">
                                    <table class="table table-striped table-bordered" id="avionsTable">
                                        <thead>
                                            <tr>
                                                @if (auth()->user()->hasRole('dta'))
                                                <th><input type="checkbox" class="select-all-checkbox" data-type="avions" {{ $hasRejectedItem ? 'disabled' : '' }}></th>
                                                @endif
                                                <th>Immatriculation</th>
                                                <th>Type</th>
                                                <th>Opérateur</th>
                                                @if (auth()->user()->hasRole('dta'))
                                                <th>Statut</th>
                                                <th>Actions</th>
                                                @endif
                                            </tr>
                                        </thead>
                                        <tbody>
                                            @foreach ($avions as $avionItem)
                                            <tr id="avion-{{ $avionItem->id }}">
                                                @if (auth()->user()->hasRole('dta'))
                                                <td><input type="checkbox" class="item-checkbox" data-type="avions" value="{{ $avionItem->id }}" {{ $hasRejectedItem ? 'disabled' : '' }}></td>
                                                @endif
                                                <td>{{ $avionItem->immatriculation }}</td>
                                                <td>{{ $avionItem->type->code ?? 'N/A' }}</td>
                                                <td>{{ $avionItem->compagnie->nom_entreprise ?? 'N/A' }}</td>
                                                @if (auth()->user()->hasRole('dta'))
                                                <td>
                                                    @if ($avionItem->valider)
                                                    <span class="validation-badge validated">Validé</span>
                                                    @else
                                                    <span class="validation-badge not-validated">Non
                                                        validé</span>
                                                    @endif
                                                    @if ($avionItem->motif)
                                                    <div class="validation-comments">
                                                        {{ $avionItem->motif }}
                                                    </div>
                                                    @endif
                                                </td>
                                                <td>
                                                    @if (is_null($avionItem->valider))
                                                    {{-- Etat NULL : pas encore traité --}}
                                                    <button type="button" class="btn btn-success btn-sm me-1"
                                                        {{ $hasRejectedItem ? 'disabled' : '' }}
                                                        onclick="openDecisionModal(
                'avions',
                '{{ $avionItem->id }}',
                '{{ $demandeAutorisation->id }}',
                'approve'
            )">
                                                        @lang('trans.approve')
                                                    </button>

                                                    <button type="button" class="btn btn-danger btn-sm"
                                                        {{ $hasRejectedItem ? 'disabled' : '' }}
                                                        onclick="openDecisionModal(
                'avions',
                '{{ $avionItem->id }}',
                '{{ $demandeAutorisation->id }}',
                'reject'
            )">
                                                        @lang('trans.reject')
                                                    </button>

                                                    @elseif ($avionItem->valider == 1)
                                                    {{-- Etat validé --}}
                                                    <span class="badge bg-success">@lang('trans.approved')</span>

                                                    <button type="button" class="btn btn-danger btn-sm ms-2"
                                                        {{ $hasRejectedItem ? 'disabled' : '' }}
                                                        onclick="openDecisionModal(
                'avions',
                '{{ $avionItem->id }}',
                '{{ $demandeAutorisation->id }}',
                'reject'
            )">
                                                        @lang('trans.reject')
                                                    </button>

                                                    @else
                                                    {{-- Etat rejeté --}}
                                                    <span class="badge bg-danger">@lang('trans.rejected')</span>

                                                    <button type="button" class="btn btn-success btn-sm ms-2"
                                                        {{ $hasRejectedItem ? 'disabled' : '' }}
                                                        onclick="openDecisionModal(
                'avions',
                '{{ $avionItem->id }}',
                '{{ $demandeAutorisation->id }}',
                'approve'
            )">
                                                        @lang('trans.approve')
                                                    </button>
                                                    @endif
                                                </td>

                                                @endif
                                            </tr>
                                            @endforeach
                                        </tbody>
                                    </table>
                                    </div>
                                </div>
                            </div>


                        </div>
                    </div>
                    @endif
                    <!-- Vol Section -->
                    @if (isset($vols) && $vols->isNotEmpty())
                    <div class="card card-primary">
                        <div class="card-header bg-primary text-white d-flex justify-content-between align-items-center">
                            <h3 class="card-title">Information sur le vol</h3>

                        </div>
                        <div class="card-body">
                            


                            @if (auth()->user()->hasRole('dta') && !$hasRejectedItem)
                            <div class="mb-2">
                                <button type="button" class="btn btn-success btn-sm bulk-approve-btn" data-type="vols" data-demande-id="{{ $demandeAutorisation->id }}" disabled>
                                    <i class="fas fa-check-double"></i> @lang('trans.approve_selected')
                                </button>
                                <button type="button" class="btn btn-danger btn-sm bulk-reject-btn" data-type="vols" data-demande-id="{{ $demandeAutorisation->id }}" disabled>
                                    <i class="fas fa-times"></i> @lang('trans.reject_selected')
                                </button>
                            </div>
                            @endif
                            <div class="row mt-4" id="volsTableContainer">
                                <div class="col-lg-12">
                                    <div class="table-responsive">
                                    <table class="table table-striped table-bordered" id="volsTable">
                                        <thead>
                                            <tr>
                                                @if (auth()->user()->hasRole('dta'))
                                                <th><input type="checkbox" class="select-all-checkbox" data-type="vols" {{ $hasRejectedItem ? 'disabled' : '' }}></th>
                                                @endif
                                                <th>@lang('trans.flight_number')</th>

                                                <th>@lang('trans.start_aeroport')</th>
                                                <th>@lang('trans.end_aeroport')</th>
                                                <th>@lang('trans.departure_time')</th>
                                                <th>@lang('trans.arrival_time')</th>
                                                <th>@lang('trans.nb_passagers')</th>
                                                <th>Itinéraire</th>

                                                @if (auth()->user()->hasRole('dta'))
                                                <th>@lang('trans.status')</th>
                                                <th>@lang('trans.actions')</th>
                                                @endif
                                            </tr>
                                        </thead>
                                        <tbody>
                                            @foreach ($vols as $volItem)
                                            @php
                                            // Récupérer les escales pour ce vol
                                            $escales = $volItem->escales()->orderBy('ordre')->get();
                                            $routeString = optional($volItem->aeroportDepart)->codeICAO ?? $volItem->nom_piste_depart ?? 'N/A';
                                            if ($escales->isNotEmpty()) {
                                            foreach ($escales as $escale) {
                                            $routeString .= ' → ' . $escale->aeroport->codeICAO;
                                            }
                                            }
                                            $routeString .= ' → ' . (optional($volItem->aeroportArrivee)->codeICAO ?? $volItem->nom_piste_arrivee ?? 'N/A');
                                            @endphp
                                            <tr id="vol-{{ $volItem->id }}">
                                                @if (auth()->user()->hasRole('dta'))
                                                <td><input type="checkbox" class="item-checkbox" data-type="vols" value="{{ $volItem->id }}" {{ $hasRejectedItem ? 'disabled' : '' }}></td>
                                                @endif
                                                <td>{{ $volItem->numero_vol }}</td>

                                                <td>{{ optional($volItem->aeroportDepart)->codeICAO ?? $volItem->nom_piste_depart ?? 'N/A' }}</td>
                                                <td>{{ optional($volItem->aeroportArrivee)->codeICAO ?? $volItem->nom_piste_arrivee ?? 'N/A' }}</td>
                                                <td>{{ date('H:i', strtotime($volItem->date_depart)) }}</td>
                                                <td>{{ date('H:i', strtotime($volItem->date_arrivee)) }}</td>
                                                <td>{{ $volItem->nbr_passagers }}</td>
                                                <td>
                                                    <small class="text-muted">{{ $routeString }}</small><br>
                                                    <small>
                                                        @if ($escales->isNotEmpty())
                                                        @foreach ($escales as $escale)
                                                        {{ date('H:i', strtotime($escale->date_arrivee)) }}
                                                        {{ $escale->aeroport->codeICAO }}
                                                        {{ date('H:i', strtotime($escale->date_depart)) }}@if(!$loop->last) → @endif
                                                        @endforeach
                                                        @else
                                                        Aucune aéroport intermédiaire
                                                        @endif
                                                    </small>
                                                </td>
                                                @if (auth()->user()->hasRole('dta'))
                                                <td>
                                                    @if ($volItem->valider)
                                                    <span class="validation-badge validated">Validé</span>
                                                    @else
                                                    <span class="validation-badge not-validated">Non
                                                        validé</span>
                                                    @endif
                                                    @if ($volItem->motif)
                                                    <div class="validation-comments">{{ $volItem->motif }}
                                                    </div>
                                                    @endif
                                                </td>
                                                <td>
                                                    @if (is_null($volItem->valider))
                                                    <button class="btn btn-success btn-sm me-1"
                                                        {{ $hasRejectedItem ? 'disabled' : '' }}
                                                        onclick="openDecisionModal('vols', '{{ $volItem->id }}', '{{ $demandeAutorisation->id }}', 'approve')">
                                                        @lang('trans.approve')
                                                    </button>

                                                    <button class="btn btn-danger btn-sm"
                                                        {{ $hasRejectedItem ? 'disabled' : '' }}
                                                        onclick="openDecisionModal('vols', '{{ $volItem->id }}', '{{ $demandeAutorisation->id }}', 'reject')">
                                                        @lang('trans.reject')
                                                    </button>

                                                    @elseif ($volItem->valider == 1)
                                                    <span class="badge bg-success">@lang('trans.approved')</span>

                                                    <button class="btn btn-danger btn-sm ms-2"
                                                        {{ $hasRejectedItem ? 'disabled' : '' }}
                                                        onclick="openDecisionModal('vols', '{{ $volItem->id }}', '{{ $demandeAutorisation->id }}', 'reject')">
                                                        @lang('trans.reject')
                                                    </button>

                                                    @else
                                                    <span class="badge bg-danger">@lang('trans.rejected')</span>

                                                    <button class="btn btn-success btn-sm ms-2"
                                                        {{ $hasRejectedItem ? 'disabled' : '' }}
                                                        onclick="openDecisionModal('vols', '{{ $volItem->id }}', '{{ $demandeAutorisation->id }}', 'approve')">
                                                        @lang('trans.approve')
                                                    </button>
                                                    @endif
                                                </td>

                                                @endif
                                            </tr>
                                            @endforeach
                                        </tbody>
                                    </table>
                                    </div>
                                </div>
                            </div>


                        </div>
                    </div>
                    @endif
                    <!-- Flight Crew Section -->
                    @if ($equipe_vols->isNotEmpty())
                    <div class="card card-primary">
                        <div class="card-header bg-primary text-white d-flex justify-content-between align-items-center">
                            <h3 class="card-title">@lang('trans.flight_crew')</h3>

                        </div>
                        <div class="card-body">
                            


                            @if (auth()->user()->hasRole('dta') && !$hasRejectedItem)
                            <div class="mb-2">
                                <button type="button" class="btn btn-success btn-sm bulk-approve-btn" data-type="equipe_vols" data-demande-id="{{ $demandeAutorisation->id }}" disabled>
                                    <i class="fas fa-check-double"></i> @lang('trans.approve_selected')
                                </button>
                                <button type="button" class="btn btn-danger btn-sm bulk-reject-btn" data-type="equipe_vols" data-demande-id="{{ $demandeAutorisation->id }}" disabled>
                                    <i class="fas fa-times"></i> @lang('trans.reject_selected')
                                </button>
                            </div>
                            @endif
                            <div class="row mt-4">
                                <div class="col-lg-12">
                                    <div class="table-responsive">
                                    <table class="table table-striped table-bordered" id="crewTable">
                                        <thead>
                                            <tr>
                                                @if (auth()->user()->hasRole('dta'))
                                                <th><input type="checkbox" class="select-all-checkbox" data-type="equipe_vols" {{ $hasRejectedItem ? 'disabled' : '' }}></th>
                                                @endif
                                                <th>@lang('trans.role')</th>
                                                <th>@lang('trans.license')</th>
                                                <th>@lang('trans.proof')</th>
                                                @if (auth()->user()->hasRole('dta'))
                                                <th>Statut</th>
                                                <th>Actions</th>
                                                @endif
                                            </tr>
                                        </thead>
                                        <tbody>
                                            @foreach ($equipe_vols as $membre)
                                            <tr id="membre-{{ $membre->id }}">
                                                @if (auth()->user()->hasRole('dta'))
                                                <td><input type="checkbox" class="item-checkbox" data-type="equipe_vols" value="{{ $membre->id }}" {{ $hasRejectedItem ? 'disabled' : '' }}></td>
                                                @endif
                                                <td>{{ strtoupper($membre->fonction) }}</td>
                                                <td>
                                                    @if ($membre->licence_numero)
                                                    {{ $membre->licence_numero }}
                                                    ({{ $membre->licence_expiration ? $membre->licence_expiration->format('d/m/Y') : 'N/A' }})
                                                    @else
                                                    N/A
                                                    @endif
                                                </td>
                                                <td>
                                                    @if ($membre->justificatif)
                                                    <a href="{{ asset('/uploads/' . $membre->justificatif) }}"
                                                        target="_blank" class="btn btn-sm btn-primary">
                                                        <i class="fas fa-eye"></i>
                                                    </a>
                                                    @else
                                                    N/A
                                                    @endif
                                                </td>
                                                @if (auth()->user()->hasRole('dta'))
                                                <td>
                                                    @if ($membre->valider)
                                                    <span class="validation-badge validated">Validé</span>
                                                    @else
                                                    <span class="validation-badge not-validated">Non
                                                        validé</span>
                                                    @endif
                                                    @if ($membre->motif)
                                                    <div class="validation-comments">{{ $membre->motif }}
                                                    </div>
                                                    @endif
                                                </td>
                                                <td>
                                                    @if (is_null($membre->valider))
                                                    <button class="btn btn-success btn-sm me-1"
                                                        {{ $hasRejectedItem ? 'disabled' : '' }}
                                                        onclick="openDecisionModal('equipe_vols', '{{ $membre->id }}', '{{ $demandeAutorisation->id }}', 'approve')">
                                                        @lang('trans.approve')
                                                    </button>

                                                    <button class="btn btn-danger btn-sm"
                                                        {{ $hasRejectedItem ? 'disabled' : '' }}
                                                        onclick="openDecisionModal('equipe_vols', '{{ $membre->id }}', '{{ $demandeAutorisation->id }}', 'reject')">
                                                        @lang('trans.reject')
                                                    </button>

                                                    @elseif ($membre->valider == 1)
                                                    <span class="badge bg-success">@lang('trans.approved')</span>

                                                    <button class="btn btn-danger btn-sm ms-2"
                                                        {{ $hasRejectedItem ? 'disabled' : '' }}
                                                        onclick="openDecisionModal('equipe_vols', '{{ $membre->id }}', '{{ $demandeAutorisation->id }}', 'reject')">
                                                        @lang('trans.reject')
                                                    </button>

                                                    @else
                                                    <span class="badge bg-danger">@lang('trans.rejected')</span>

                                                    <button class="btn btn-success btn-sm ms-2"
                                                        {{ $hasRejectedItem ? 'disabled' : '' }}
                                                        onclick="openDecisionModal('equipe_vols', '{{ $membre->id }}', '{{ $demandeAutorisation->id }}', 'approve')">
                                                        @lang('trans.approve')
                                                    </button>
                                                    @endif
                                                </td>

                                                @endif
                                            </tr>
                                            @endforeach
                                        </tbody>
                                    </table>
                                    </div>
                                </div>
                            </div>

                        </div>

                    </div>
                    @endif
                    @if ($fretVols->isNotEmpty())
                    <!-- Freight Section -->
                    <div class="card card-primary">
                        <div class="card-header bg-primary text-white d-flex justify-content-between align-items-center">
                            <h3 class="card-title">@lang('trans.freight')</h3>

                        </div>
                        <div class="card-body">
                            


                            @if (auth()->user()->hasRole('dta') && !$hasRejectedItem)
                            <div class="mb-2">
                                <button type="button" class="btn btn-success btn-sm bulk-approve-btn" data-type="fret_vols" data-demande-id="{{ $demandeAutorisation->id }}" disabled>
                                    <i class="fas fa-check-double"></i> @lang('trans.approve_selected')
                                </button>
                                <button type="button" class="btn btn-danger btn-sm bulk-reject-btn" data-type="fret_vols" data-demande-id="{{ $demandeAutorisation->id }}" disabled>
                                    <i class="fas fa-times"></i> @lang('trans.reject_selected')
                                </button>
                            </div>
                            @endif
                            <div class="row mt-4">
                                <div class="col-lg-12">
                                    <div class="table-responsive">
                                    <table class="table table-striped table-bordered" id="fretTable">
                                        <thead>
                                            <tr>
                                                @if (auth()->user()->hasRole('dta'))
                                                <th><input type="checkbox" class="select-all-checkbox" data-type="fret_vols" {{ $hasRejectedItem ? 'disabled' : '' }}></th>
                                                @endif
                                                <th>@lang('trans.nature')</th>
                                                <th>@lang('trans.weight_kg')</th>
                                                <th>@lang('trans.description')</th>
                                                @if (auth()->user()->hasRole('dta'))
                                                <th>Statut</th>
                                                <th>Actions</th>
                                                @endif
                                            </tr>
                                        </thead>
                                        <tbody>
                                            @foreach ($fretVols as $fret)
                                            <tr id="fret-{{ $fret->id }}">
                                                @if (auth()->user()->hasRole('dta'))
                                                <td><input type="checkbox" class="item-checkbox" data-type="fret_vols" value="{{ $fret->id }}" {{ $hasRejectedItem ? 'disabled' : '' }}></td>
                                                @endif
                                                <td>{{ strtoupper($fret->nature) }}</td>
                                                <td>{{ $fret->poids }} kg</td>
                                                <td>{{ $fret->instructions_speciales }}</td>
                                                @if (auth()->user()->hasRole('dta'))
                                                <td>
                                                    @if ($fret->valider)
                                                    <span class="validation-badge validated">Validé</span>
                                                    @else
                                                    <span class="validation-badge not-validated">Non
                                                        validé</span>
                                                    @endif
                                                    @if ($fret->motif)
                                                    <div class="validation-comments">{{ $fret->motif }}
                                                    </div>
                                                    @endif
                                                </td>
                                                <td>
                                                    @if (is_null($fret->valider))
                                                    <button class="btn btn-success btn-sm me-1"
                                                        {{ $hasRejectedItem ? 'disabled' : '' }}
                                                        onclick="openDecisionModal('fret_vols', '{{ $fret->id }}', '{{ $demandeAutorisation->id }}', 'approve')">
                                                        @lang('trans.approve')
                                                    </button>

                                                    <button class="btn btn-danger btn-sm"
                                                        {{ $hasRejectedItem ? 'disabled' : '' }}
                                                        onclick="openDecisionModal('fret_vols', '{{ $fret->id }}', '{{ $demandeAutorisation->id }}', 'reject')">
                                                        @lang('trans.reject')
                                                    </button>

                                                    @elseif ($fret->valider == 1)
                                                    <span class="badge bg-success">@lang('trans.approved')</span>

                                                    <button class="btn btn-danger btn-sm ms-2"
                                                        {{ $hasRejectedItem ? 'disabled' : '' }}
                                                        onclick="openDecisionModal('fret_vols', '{{ $fret->id }}', '{{ $demandeAutorisation->id }}', 'reject')">
                                                        @lang('trans.reject')
                                                    </button>

                                                    @else
                                                    <span class="badge bg-danger">@lang('trans.rejected')</span>

                                                    <button class="btn btn-success btn-sm ms-2"
                                                        {{ $hasRejectedItem ? 'disabled' : '' }}
                                                        onclick="openDecisionModal('fret_vols', '{{ $fret->id }}', '{{ $demandeAutorisation->id }}', 'approve')">
                                                        @lang('trans.approve')
                                                    </button>
                                                    @endif
                                                </td>

                                                @endif
                                            </tr>
                                            @endforeach
                                        </tbody>
                                    </table>
                                    </div>
                                </div>
                            </div>

                        </div>
                    </div>
                    @endif
                        @if ($personnesDeces->isNotEmpty())
                                        <div class="card card-primary">
                    <div class="card-header bg-primary text-white">
                        <h3 class="card-title">@lang('trans.deceased_persons')</h3>
                    </div>
                    <div class="card-body">
                            @if (!$hasRejectedItem)
                            <div class="mb-2">
                                <button type="button" class="btn btn-success btn-sm bulk-approve-btn" data-type="personne_deces" data-demande-id="{{ $demandeAutorisation->id }}" disabled>
                                    <i class="fas fa-check-double"></i> @lang('trans.approve_selected')
                                </button>
                                <button type="button" class="btn btn-danger btn-sm bulk-reject-btn" data-type="personne_deces" data-demande-id="{{ $demandeAutorisation->id }}" disabled>
                                    <i class="fas fa-times"></i> @lang('trans.reject_selected')
                                </button>
                            </div>
                            @endif
                            <div class="row mt-4">
                                <div class="col-lg-12">
                                    <table class="table table-striped table-bordered" id="deceasedPersonsTable">
                                        <thead>
                                            <tr>
                                                <th><input type="checkbox" class="select-all-checkbox" data-type="personne_deces" {{ $hasRejectedItem ? 'disabled' : '' }}></th>
                                                <th>@lang('trans.full_name')</th>
                                                <th>@lang('trans.passport_number')</th>
                                                <th>@lang('trans.proof')</th>
                                                <th>@lang('trans.actions')</th>
                                            </tr>
                                        </thead>
                                        <tbody>
                                            @foreach ($personnesDeces as $personne)
                                                <tr id="personne-{{ $personne->id }}">
                                                    <td><input type="checkbox" class="item-checkbox" data-type="personne_deces" value="{{ $personne->id }}" {{ $hasRejectedItem ? 'disabled' : '' }}></td>
                                                    <td>{{ $personne->nom_prenom }}</td>
                                                    <td>{{ $personne->numero_passport ?? 'N/A' }}</td>
                                                    <td>
                                                        @if ($personne->justificatif)
                                                            <a href="{{ asset('/uploads/' . $personne->justificatif) }}"
                                                                target="_blank" class="btn btn-sm btn-primary">
                                                                <i class="fas fa-eye"></i>
                                                            </a>
                                                        @else
                                                            N/A
                                                        @endif
                                                    </td>
<td>
                                                    @if (is_null($personne->valider))
                                                    <button class="btn btn-success btn-sm me-1"
                                                        {{ $hasRejectedItem ? 'disabled' : '' }}
                                                        onclick="openDecisionModal('personne_deces', '{{ $personne->id }}', '{{ $demandeAutorisation->id }}', 'approve')">
                                                        @lang('trans.approve')
                                                    </button>

                                                    <button class="btn btn-danger btn-sm"
                                                        {{ $hasRejectedItem ? 'disabled' : '' }}
                                                        onclick="openDecisionModal('personne_deces', '{{ $personne->id }}', '{{ $demandeAutorisation->id }}', 'reject')">
                                                        @lang('trans.reject')
                                                    </button>

                                                    @elseif ($personne->valider == 1)
                                                    <span class="badge bg-success">@lang('trans.approved')</span>

                                                    <button class="btn btn-danger btn-sm ms-2"
                                                        {{ $hasRejectedItem ? 'disabled' : '' }}
                                                        onclick="openDecisionModal('personne_deces', '{{ $personne->id }}', '{{ $demandeAutorisation->id }}', 'reject')">
                                                        @lang('trans.reject')
                                                    </button>

                                                    @else
                                                    <span class="badge bg-danger">@lang('trans.rejected')</span>

                                                    <button class="btn btn-success btn-sm ms-2"
                                                        {{ $hasRejectedItem ? 'disabled' : '' }}
                                                        onclick="openDecisionModal('personne_deces', '{{ $personne->id }}', '{{ $demandeAutorisation->id }}', 'approve')">
                                                        @lang('trans.approve')
                                                    </button>
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
                        @endif
                                    
                        {{-- MDN List --}}
                        @if(isset($mdns) && $mdns->isNotEmpty())
                            @if (!$hasRejectedItem)
                            <div class="mb-2">
                                <button type="button" class="btn btn-success btn-sm bulk-approve-btn" data-type="mdns" data-demande-id="{{ $demandeAutorisation->id }}" disabled>
                                    <i class="fas fa-check-double"></i> @lang('trans.approve_selected')
                                </button>
                                <button type="button" class="btn btn-danger btn-sm bulk-reject-btn" data-type="mdns" data-demande-id="{{ $demandeAutorisation->id }}" disabled>
                                    <i class="fas fa-times"></i> @lang('trans.reject_selected')
                                </button>
                            </div>
                            @endif
                            <div class="row mt-4">
                                <div class="col-lg-12">
                                    <div class="table-responsive">
                                    <table class="table table-striped table-bordered" id="mdnTable">
                                        <thead>
                                            <tr>
                                                <th><input type="checkbox" class="select-all-checkbox" data-type="mdns" {{ $hasRejectedItem ? 'disabled' : '' }}></th>
                                                <th>@lang('trans.authorization_date')</th>
                                                <th>@lang('trans.mdn_number')</th>
                                                <th>@lang('trans.nationality')</th>
                                                <th>@lang('trans.actions')</th>
                                            </tr>
                                        </thead>
                                        <tbody>
                                            @foreach ($mdns as $mdn)
                                                <tr id="mdn-{{ $mdn->id }}">
                                                    <td><input type="checkbox" class="item-checkbox" data-type="mdns" value="{{ $mdn->id }}" {{ $hasRejectedItem ? 'disabled' : '' }}></td>
                                                    <td>{{ $mdn->formatted_date_autorisation }}</td>
                                                    <td>{{ $mdn->numero_mdn }}</td>
                                                    <td>
                                                        <span class="badge badge-info">
                                                            {{ $mdn->pays->nom ?? 'NR' }}
                                                        </span>
                                                    </td>
                                                    <td>
                                                    @if (is_null($mdn->valider))
                                                    <button class="btn btn-success btn-sm me-1"
                                                        {{ $hasRejectedItem ? 'disabled' : '' }}
                                                        onclick="openDecisionModal('mdns', '{{ $mdn->id }}', '{{ $demandeAutorisation->id }}', 'approve')">
                                                        @lang('trans.approve')
                                                    </button>

                                                    <button class="btn btn-danger btn-sm"
                                                        {{ $hasRejectedItem ? 'disabled' : '' }}
                                                        onclick="openDecisionModal('mdns', '{{ $mdn->id }}', '{{ $demandeAutorisation->id }}', 'reject')">
                                                        @lang('trans.reject')
                                                    </button>

                                                    @elseif ($mdn->valider == 1)
                                                    <span class="badge bg-success">@lang('trans.approved')</span>

                                                    <button class="btn btn-danger btn-sm ms-2"
                                                        {{ $hasRejectedItem ? 'disabled' : '' }}
                                                        onclick="openDecisionModal('mdns', '{{ $mdn->id }}', '{{ $demandeAutorisation->id }}', 'reject')">
                                                        @lang('trans.reject')
                                                    </button>

                                                    @else
                                                    <span class="badge bg-danger">@lang('trans.rejected')</span>

                                                    <button class="btn btn-success btn-sm ms-2"
                                                        {{ $hasRejectedItem ? 'disabled' : '' }}
                                                        onclick="openDecisionModal('mdns', '{{ $mdn->id }}', '{{ $demandeAutorisation->id }}', 'approve')">
                                                        @lang('trans.approve')
                                                    </button>
                                                    @endif
                                                </td>
                                                </tr>
                                                
                                            @endforeach
                                        </tbody>
                                    </table>
                                    </div>
                                </div>
                            </div>
                        @endif
                    <!-- Receiving Party Section -->
                    @if ($receivingParties->isNotEmpty())
                    <div class="card card-primary">
                        <div class="card-header bg-primary text-white d-flex justify-content-between align-items-center">
                            <h3 class="card-title">Renseignements sur le Receiving-party</h3>

                        </div>
                        <div class="card-body">
                            


                            @if (auth()->user()->hasRole('dta') && !$hasRejectedItem)
                            <div class="mb-2">
                                <button type="button" class="btn btn-success btn-sm bulk-approve-btn" data-type="receiving_parties" data-demande-id="{{ $demandeAutorisation->id }}" disabled>
                                    <i class="fas fa-check-double"></i> @lang('trans.approve_selected')
                                </button>
                                <button type="button" class="btn btn-danger btn-sm bulk-reject-btn" data-type="receiving_parties" data-demande-id="{{ $demandeAutorisation->id }}" disabled>
                                    <i class="fas fa-times"></i> @lang('trans.reject_selected')
                                </button>
                            </div>
                            @endif
                            <div class="row mt-4">
                                <div class="col-lg-12">
                                    <div class="table-responsive">
                                    <table class="table table-striped table-bordered">
                                        <thead>
                                            <tr>
                                                @if (auth()->user()->hasRole('dta'))
                                                <th><input type="checkbox" class="select-all-checkbox" data-type="receiving_parties" {{ $hasRejectedItem ? 'disabled' : '' }}></th>
                                                @endif
                                                <th>Contact</th>
                                                <th>Téléphone</th>
                                                <th>Email</th>
                                                <th>Fonction</th>
                                                <th>Piece d'identité</th>
                                                @if (auth()->user()->hasRole('dta'))
                                                <th>Statut</th>
                                                <th>Actions</th>
                                                @endif
                                            </tr>
                                        </thead>
                                        <tbody>
                                            @foreach ($receivingParties as $party)
                                            <tr id="party-{{ $party->id }}">
                                                @if (auth()->user()->hasRole('dta'))
                                                <td><input type="checkbox" class="item-checkbox" data-type="receiving_parties" value="{{ $party->id }}" {{ $hasRejectedItem ? 'disabled' : '' }}></td>
                                                @endif
                                                <td>{{ $party->nom_contact }}</td>
                                                <td>{{ $party->telephone_contact }}</td>
                                                <td>{{ $party->email_contact }}</td>
                                                <td>{{ $party->fonction_contact }}</td>
                                                <td>
                                                    <a href="{{ asset('/uploads/' . $party->piece_identite_path) }}"
                                                        target="_blank" class="btn btn-sm btn-primary">
                                                        <i class="fas fa-eye"></i>
                                                    </a>
                                                </td>
                                                @if (auth()->user()->hasRole('dta'))
                                                <td>
                                                    @if ($party->valider)
                                                    <span class="validation-badge validated">Validé</span>
                                                    @else
                                                    <span class="validation-badge not-validated">Non
                                                        validé</span>
                                                    @endif
                                                    @if ($party->motif)
                                                    <div class="validation-comments">{{ $party->motif }}
                                                    </div>
                                                    @endif
                                                </td>
                                                <td>
                                                    @if (is_null($party->valider))
                                                    <button class="btn btn-success btn-sm me-1"
                                                        {{ $hasRejectedItem ? 'disabled' : '' }}
                                                        onclick="openDecisionModal('receiving_parties', '{{ $party->id }}', '{{ $demandeAutorisation->id }}', 'approve')">
                                                        @lang('trans.approve')
                                                    </button>

                                                    <button class="btn btn-danger btn-sm"
                                                        {{ $hasRejectedItem ? 'disabled' : '' }}
                                                        onclick="openDecisionModal('receiving_parties', '{{ $party->id }}', '{{ $demandeAutorisation->id }}', 'reject')">
                                                        @lang('trans.reject')
                                                    </button>

                                                    @elseif ($party->valider == 1)
                                                    <span class="badge bg-success">@lang('trans.approved')</span>

                                                    <button class="btn btn-danger btn-sm ms-2"
                                                        {{ $hasRejectedItem ? 'disabled' : '' }}
                                                        onclick="openDecisionModal('receiving_parties', '{{ $party->id }}', '{{ $demandeAutorisation->id }}', 'reject')">
                                                        @lang('trans.reject')
                                                    </button>

                                                    @else
                                                    <span class="badge bg-danger">@lang('trans.rejected')</span>

                                                    <button class="btn btn-success btn-sm ms-2"
                                                        {{ $hasRejectedItem ? 'disabled' : '' }}
                                                        onclick="openDecisionModal('receiving_parties', '{{ $party->id }}', '{{ $demandeAutorisation->id }}', 'approve')">
                                                        @lang('trans.approve')
                                                    </button>
                                                    @endif
                                                </td>

                                                @endif
                                            </tr>
                                            @endforeach
                                        </tbody>
                                    </table>
                                    </div>
                                </div>
                            </div>

                        </div>
                    </div>
                    @endif
                    <!-- Documents Section -->
                    @if ($demandeAutorisation->hasDocuments())
                    <div class="card card-primary">
                        <div class="card-header bg-primary text-white d-flex justify-content-between align-items-center">
                            <h3 class="card-title">Documents</h3>

                        </div>
                        <div class="card-body">
                           


                            @if (auth()->user()->hasRole('dta') && !$hasRejectedItem)
                            <div class="mb-2">
                                <button type="button" class="btn btn-success btn-sm bulk-approve-btn" data-type="document_autorisations" data-demande-id="{{ $demandeAutorisation->id }}" disabled>
                                    <i class="fas fa-check-double"></i> @lang('trans.approve_selected')
                                </button>
                                <button type="button" class="btn btn-danger btn-sm bulk-reject-btn" data-type="document_autorisations" data-demande-id="{{ $demandeAutorisation->id }}" disabled>
                                    <i class="fas fa-times"></i> @lang('trans.reject_selected')
                                </button>
                            </div>
                            @endif
                            <div class="row mt-4">
                                <div class="col-12">
                                    <div class="table-responsive">
                                    <table class="table table-striped" id="documentsTable">
                                        <thead>
                                            <tr>
                                                @if (auth()->user()->hasRole('dta'))
                                                <th><input type="checkbox" class="select-all-checkbox" data-type="document_autorisations" {{ $hasRejectedItem ? 'disabled' : '' }}></th>
                                                @endif
                                                <th>Type</th>
                                                <th>Document</th>
                                                @if (auth()->user()->hasRole('dta'))
                                                <th>Statut</th>
                                                <th>Actions</th>
                                                @endif
                                            </tr>
                                        </thead>
                                        <tbody>
                                            @foreach ($demandeAutorisation->documents as $document)
                                            <tr id="document-{{ $document->id }}">
                                                @if (auth()->user()->hasRole('dta'))
                                                <td><input type="checkbox" class="item-checkbox" data-type="document_autorisations" value="{{ $document->id }}" {{ $hasRejectedItem ? 'disabled' : '' }}></td>
                                                @endif
                                                <td>
                                                    {{ LaravelLocalization::getCurrentLocale() == 'fr' ? optional($document->typeDocument)->nom_fr : optional($document->typeDocument)->nom_en }}
                                                </td>
                                                <td>
                                                    <a href="{{ $document->file_url }}" target="_blank"
                                                        class="btn btn-sm btn-primary">
                                                        <i class="fas fa-eye"></i>
                                                    </a>
                                                </td>
                                                @if (auth()->user()->hasRole('dta'))
                                                <td>
                                                    @if ($document->valider)
                                                    <span class="validation-badge validated">Validé</span>
                                                    @else
                                                    <span class="validation-badge not-validated">Non
                                                        validé</span>
                                                    @endif
                                                    @if ($document->motif)
                                                    <div class="validation-comments">
                                                        {{ $document->motif }}
                                                    </div>
                                                    @endif
                                                </td>
                                                <td>
                                                    @if (is_null($document->valider))
                                                    <button class="btn btn-success btn-sm me-1"
                                                        {{ $hasRejectedItem ? 'disabled' : '' }}
                                                        onclick="openDecisionModal('document_autorisations', '{{ $document->id }}', '{{ $demandeAutorisation->id }}', 'approve')">
                                                        @lang('trans.approve')
                                                    </button>

                                                    <button class="btn btn-danger btn-sm"
                                                        {{ $hasRejectedItem ? 'disabled' : '' }}
                                                        onclick="openDecisionModal('document_autorisations', '{{ $document->id }}', '{{ $demandeAutorisation->id }}', 'reject')">
                                                        @lang('trans.reject')
                                                    </button>

                                                    @elseif ($document->valider == 1)
                                                    <span class="badge bg-success">@lang('trans.approved')</span>

                                                    <button class="btn btn-danger btn-sm ms-2"
                                                        {{ $hasRejectedItem ? 'disabled' : '' }}
                                                        onclick="openDecisionModal('document_autorisations', '{{ $document->id }}', '{{ $demandeAutorisation->id }}', 'reject')">
                                                        @lang('trans.reject')
                                                    </button>

                                                    @else
                                                    <span class="badge bg-danger">@lang('trans.rejected')</span>

                                                    <button class="btn btn-success btn-sm ms-2"
                                                        {{ $hasRejectedItem ? 'disabled' : '' }}
                                                        onclick="openDecisionModal('document_autorisations', '{{ $document->id }}', '{{ $demandeAutorisation->id }}', 'approve')">
                                                        @lang('trans.approve')
                                                    </button>
                                                    @endif
                                                </td>

                                                @endif
                                            </tr>
                                            @endforeach
                                        </tbody>
                                    </table>
                                    </div>
                                </div>
                            </div>

                        </div>
                    </div>
                    @endif
                </div>
            </div>
        </div>
    </div>
</div>

<!-- Single Decision Modal -->
<div class="modal fade" id="decisionModal" tabindex="-1" aria-labelledby="decisionModalLabel" aria-hidden="true">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="decisionModalLabel"></h5>
                <button type="button" class="btn-close" data-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body">
                <form id="decisionForm" method="POST" action="{{ route('vr.handle_approval') }}">
                    @csrf
                    <input type="hidden" name="action_type" id="modalActionType">
                    <input type="hidden" name="table" id="modalTable">
                    <input type="hidden" name="id" id="modalId">
                    <input type="hidden" name="demande_id" id="modalDemandeId">

                    <div class="form-group" id="reasonFieldGroup">
                        <label for="modalMotif">@lang('trans.rejection_reason_indication')</label>
                        <textarea name="motif" id="modalMotif" class="form-control" rows="3"></textarea>
                    </div>
                </form>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-dismiss="modal">@lang('trans.close')</button>
                <button type="button" class="btn btn-primary" id="modalSubmitBtn"></button>
            </div>
        </div>
    </div>
</div>
@endsection

@push('script')
<script src="https://cdnjs.cloudflare.com/ajax/libs/summernote/0.8.18/summernote-bs4.min.js"></script>
<script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
<script src="https://cdnjs.cloudflare.com/ajax/libs/toastr.js/latest/toastr.min.js"></script>
<script>
        // Configuration commune du modal (titre, couleurs, bouton) selon l'action
        function configureDecisionModal(action) {
            document.getElementById('modalActionType').value = action;
            document.getElementById('modalMotif').value = '';

            if (action === 'approve') {
                document.getElementById('decisionModalLabel').textContent = '@lang('trans.confirm_approval')';
                document.getElementById('modalSubmitBtn').textContent = '@lang('trans.approve')';
                document.getElementById('modalSubmitBtn').className = 'btn btn-success';
                document.getElementById('reasonFieldGroup').style.display = 'none';
            } else {
                document.getElementById('decisionModalLabel').textContent = '@lang('trans.confirm_rejection')';
                document.getElementById('modalSubmitBtn').textContent = '@lang('trans.reject')';
                document.getElementById('modalSubmitBtn').className = 'btn btn-danger';
                document.getElementById('reasonFieldGroup').style.display = 'block';
            }

            document.getElementById('modalSubmitBtn').onclick = function() {
                submitDecisionForm(action);
            };
        }

        // Fonction pour ouvrir la modale (un seul élément)
        function openDecisionModal(table, id, demande, action) {
            document.getElementById('modalTable').value = table;
            document.getElementById('modalId').value = id;
            document.getElementById('modalDemandeId').value = demande;
            window.currentBulkIds = null;
            window.currentBulkGroups = null;

            configureDecisionModal(action);
            new bootstrap.Modal(document.getElementById('decisionModal')).show();
        }

        // Fonction pour ouvrir la modale (plusieurs éléments d'une même section)
        window.openBulkDecisionModal = function(table, ids, demande, action) {
            if (!ids || ids.length === 0) {
                alert('@lang('trans.select_items_first')');
                return;
            }

            document.getElementById('modalTable').value = table;
            document.getElementById('modalId').value = '';
            document.getElementById('modalDemandeId').value = demande;
            window.currentBulkIds = ids;
            window.currentBulkGroups = null;

            configureDecisionModal(action);
            new bootstrap.Modal(document.getElementById('decisionModal')).show();
        };

        // Fonction pour ouvrir la modale (éléments sélectionnés dans plusieurs sections à la fois)
        window.openGlobalBulkDecisionModal = function(demande, action) {
            const groups = getAllCheckedGroups();
            if (groups.length === 0) {
                alert('@lang('trans.select_items_first')');
                return;
            }

            document.getElementById('modalTable').value = '';
            document.getElementById('modalId').value = '';
            document.getElementById('modalDemandeId').value = demande;
            window.currentBulkIds = null;
            window.currentBulkGroups = groups;

            configureDecisionModal(action);
            new bootstrap.Modal(document.getElementById('decisionModal')).show();
        };

        function submitDecisionForm(action) {
            const form = document.getElementById('decisionForm');
            const motif = document.getElementById('modalMotif').value;

            if (action === 'reject' && !motif.trim()) {
                alert('@lang('trans.rejection_reason_required')');
                return;
            }

            if (confirm(action === 'approve' ?
                    '@lang('trans.confirm_approval_question')' :
                    '@lang('trans.confirm_rejection_question')')) {

                const formData = new FormData(form);
                if (window.currentBulkGroups && window.currentBulkGroups.length) {
                    formData.delete('id');
                    formData.delete('table');
                    formData.append('items', JSON.stringify(window.currentBulkGroups));
                } else if (window.currentBulkIds && window.currentBulkIds.length) {
                    formData.delete('id');
                    window.currentBulkIds.forEach(id => formData.append('ids[]', id));
                }

                // Submit via AJAX
                fetch(form.action, {
                        method: 'POST',
                        body: formData,
                        headers: {
                            'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content
                        }
                    })
                    .then(response => response.json())
                    .then(data => {
                        if (data.success) {
                            window.location.reload();
                        } else {
                            alert(data.message || '@lang('trans.error_occurred')');
                        }
                    })
                    .catch(error => {
                        console.error('Error:', error);
                        alert('@lang('trans.request_failed')');
                    });
            }
        }

        // Sélection multiple des lignes par section (avions, vols, équipage, ...)
        function getCheckedIds(type) {
            return $('.item-checkbox[data-type="' + type + '"]:checked')
                .map(function() { return this.value; })
                .get();
        }

        // Sélection multiple tous types confondus (avions + vols + ... en même temps)
        function getAllCheckedGroups() {
            const groups = {};
            $('.item-checkbox:checked').each(function() {
                const type = $(this).data('type');
                if (!groups[type]) {
                    groups[type] = [];
                }
                groups[type].push(this.value);
            });
            return Object.keys(groups).map(table => ({ table: table, ids: groups[table] }));
        }

        function refreshBulkButtons(type) {
            const hasSelection = getCheckedIds(type).length > 0;
            $('.bulk-approve-btn[data-type="' + type + '"], .bulk-reject-btn[data-type="' + type + '"]')
                .prop('disabled', !hasSelection);
        }

        function refreshGlobalBulkBar() {
            const total = $('.item-checkbox:checked').length;
            $('#globalBulkCount').text(total);
            $('#globalApproveBtn, #globalRejectBtn').prop('disabled', total === 0);
            $('#globalBulkBar').toggle(total > 0);
        }

        $(document).on('change', '.select-all-checkbox', function() {
            const type = $(this).data('type');
            const checked = $(this).prop('checked');
            $('.item-checkbox[data-type="' + type + '"]').prop('checked', checked);
            refreshBulkButtons(type);
            refreshGlobalBulkBar();
        });

        $(document).on('change', '.item-checkbox', function() {
            const type = $(this).data('type');
            const allChecked = $('.item-checkbox[data-type="' + type + '"]').length ===
                $('.item-checkbox[data-type="' + type + '"]:checked').length;
            $('.select-all-checkbox[data-type="' + type + '"]').prop('checked', allChecked);
            refreshBulkButtons(type);
            refreshGlobalBulkBar();
        });

        $('#globalApproveBtn').on('click', function() {
            window.openGlobalBulkDecisionModal('{{ $demandeAutorisation->id }}', 'approve');
        });

        $('#globalRejectBtn').on('click', function() {
            window.openGlobalBulkDecisionModal('{{ $demandeAutorisation->id }}', 'reject');
        });

        $(document).on('click', '.bulk-approve-btn', function() {
            const type = $(this).data('type');
            const demandeId = $(this).data('demande-id');
            window.openBulkDecisionModal(type, getCheckedIds(type), demandeId, 'approve');
        });

        $(document).on('click', '.bulk-reject-btn', function() {
            const type = $(this).data('type');
            const demandeId = $(this).data('demande-id');
            window.openBulkDecisionModal(type, getCheckedIds(type), demandeId, 'reject');
        });
    </script>
<script>
    $(document).ready(function() {
        // Gestion de la soumission du formulaire Tout Valider
        $('#validateAllForm').on('submit', function(e) {
            e.preventDefault();

            $.ajax({
                url: "{{ route('validate.all.items') }}",
                method: 'POST',
                data: $(this).serialize(),
                beforeSend: function() {
                    // Afficher un indicateur de chargement
                    $('#validateAllModal').modal('hide');
                    Swal.fire({
                        title: 'Validation en cours',
                        html: 'Veuillez patienter pendant la validation de tous les éléments...',
                        allowOutsideClick: false,
                        didOpen: () => {
                            Swal.showLoading();
                        }
                    });
                },
                success: function(response) {
                    if (response.success) {
                        Swal.fire({
                            icon: 'success',
                            title: 'Succès!',
                            text: response.message,
                            confirmButtonText: 'OK'
                        }).then(() => {
                            location.reload();
                        });
                    } else {
                        Swal.fire({
                            icon: 'error',
                            title: 'Erreur!',
                            text: response.message || 'Une erreur est survenue',
                            confirmButtonText: 'OK'
                        });
                    }
                },
                error: function(xhr) {
                    Swal.fire({
                        icon: 'error',
                        title: 'Erreur!',
                        text: 'Une erreur est survenue lors de la validation globale',
                        confirmButtonText: 'OK'
                    });
                }
            });
        });
    });

    // Nouvelle fonction pour Tout Valider
    function validateAllItems() {
        // Ouvrir le modal de validation globale
        $('#validateAllModal').modal('show');
    }

</script>
@endpush
