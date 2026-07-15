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

                    <!-- BOUTON TOUT VALIDER - NOUVEAU -->
                    @if (auth()->user()->hasRole('dta') && !$demandeAutorisation->isFullyValidated())
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
                           


                            <div class="row mt-4" id="avionsTableContainer">
                                <div class="col-lg-12">
                                    <div class="table-responsive">
                                    <table class="table table-striped table-bordered" id="avionsTable">
                                        <thead>
                                            <tr>
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
                                                        onclick="openDecisionModal(
                'avions',
                '{{ $avionItem->id }}',
                '{{ $demandeAutorisation->id }}',
                'approve'
            )">
                                                        @lang('trans.approve')
                                                    </button>

                                                    <button type="button" class="btn btn-danger btn-sm"
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
                            


                            <div class="row mt-4" id="volsTableContainer">
                                <div class="col-lg-12">
                                    <div class="table-responsive">
                                    <table class="table table-striped table-bordered" id="volsTable">
                                        <thead>
                                            <tr>
                                                <th>@lang('trans.flight_number')</th>

                                                <th>@lang('trans.start_aeroport')</th>
                                                <th>Piste depart</th>
                                                <th>@lang('trans.end_aeroport')</th>
                                                <th>Piste arrivee</th>
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
                                            $routeString = $volItem->aeroportDepart->codeICAO ?? 'N/A';
                                            if ($escales->isNotEmpty()) {
                                            foreach ($escales as $escale) {
                                            $routeString .= ' → ' . $escale->aeroport->codeICAO;
                                            }
                                            }
                                            $routeString .= ' → ' . ($volItem->aeroportArrivee->codeICAO ?? 'N/A');
                                            @endphp
                                            <tr id="vol-{{ $volItem->id }}">
                                                <td>{{ $volItem->numero_vol }}</td>

                                                <td>{{ $volItem->aeroportDepart->codeICAO ?? 'N/A' }}</td>
                                                <td>{{ $volItem->numero_piste_depart ?? 'N/A' }}</td>
                                                <td>{{ $volItem->aeroportArrivee->codeICAO ?? 'N/A' }}</td>
                                                <td>{{ $volItem->numero_piste_arrivee ?? 'N/A' }}</td>
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
                                                        onclick="openDecisionModal('vols', '{{ $volItem->id }}', '{{ $demandeAutorisation->id }}', 'approve')">
                                                        @lang('trans.approve')
                                                    </button>

                                                    <button class="btn btn-danger btn-sm"
                                                        onclick="openDecisionModal('vols', '{{ $volItem->id }}', '{{ $demandeAutorisation->id }}', 'reject')">
                                                        @lang('trans.reject')
                                                    </button>

                                                    @elseif ($volItem->valider == 1)
                                                    <span class="badge bg-success">@lang('trans.approved')</span>

                                                    <button class="btn btn-danger btn-sm ms-2"
                                                        onclick="openDecisionModal('vols', '{{ $volItem->id }}', '{{ $demandeAutorisation->id }}', 'reject')">
                                                        @lang('trans.reject')
                                                    </button>

                                                    @else
                                                    <span class="badge bg-danger">@lang('trans.rejected')</span>

                                                    <button class="btn btn-success btn-sm ms-2"
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
                            


                            <div class="row mt-4">
                                <div class="col-lg-12">
                                    <div class="table-responsive">
                                    <table class="table table-striped table-bordered" id="crewTable">
                                        <thead>
                                            <tr>
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
                                                        onclick="openDecisionModal('equipe_vols', '{{ $membre->id }}', '{{ $demandeAutorisation->id }}', 'approve')">
                                                        @lang('trans.approve')
                                                    </button>

                                                    <button class="btn btn-danger btn-sm"
                                                        onclick="openDecisionModal('equipe_vols', '{{ $membre->id }}', '{{ $demandeAutorisation->id }}', 'reject')">
                                                        @lang('trans.reject')
                                                    </button>

                                                    @elseif ($membre->valider == 1)
                                                    <span class="badge bg-success">@lang('trans.approved')</span>

                                                    <button class="btn btn-danger btn-sm ms-2"
                                                        onclick="openDecisionModal('equipe_vols', '{{ $membre->id }}', '{{ $demandeAutorisation->id }}', 'reject')">
                                                        @lang('trans.reject')
                                                    </button>

                                                    @else
                                                    <span class="badge bg-danger">@lang('trans.rejected')</span>

                                                    <button class="btn btn-success btn-sm ms-2"
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
                            


                            <div class="row mt-4">
                                <div class="col-lg-12">
                                    <div class="table-responsive">
                                    <table class="table table-striped table-bordered" id="fretTable">
                                        <thead>
                                            <tr>
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
                                                        onclick="openDecisionModal('fret_vols', '{{ $fret->id }}', '{{ $demandeAutorisation->id }}', 'approve')">
                                                        @lang('trans.approve')
                                                    </button>

                                                    <button class="btn btn-danger btn-sm"
                                                        onclick="openDecisionModal('fret_vols', '{{ $fret->id }}', '{{ $demandeAutorisation->id }}', 'reject')">
                                                        @lang('trans.reject')
                                                    </button>

                                                    @elseif ($fret->valider == 1)
                                                    <span class="badge bg-success">@lang('trans.approved')</span>

                                                    <button class="btn btn-danger btn-sm ms-2"
                                                        onclick="openDecisionModal('fret_vols', '{{ $fret->id }}', '{{ $demandeAutorisation->id }}', 'reject')">
                                                        @lang('trans.reject')
                                                    </button>

                                                    @else
                                                    <span class="badge bg-danger">@lang('trans.rejected')</span>

                                                    <button class="btn btn-success btn-sm ms-2"
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
                            <div class="row mt-4">
                                <div class="col-lg-12">
                                    <table class="table table-striped table-bordered" id="deceasedPersonsTable">
                                        <thead>
                                            <tr>
                                                <th>@lang('trans.full_name')</th>
                                                <th>@lang('trans.passport_number')</th>
                                                <th>@lang('trans.proof')</th>
                                                <th>@lang('trans.actions')</th>
                                            </tr>
                                        </thead>
                                        <tbody>
                                            @foreach ($personnesDeces as $personne)
                                                <tr id="personne-{{ $personne->id }}">
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
                                                        onclick="openDecisionModal('personne_deces', '{{ $personne->id }}', '{{ $demandeAutorisation->id }}', 'approve')">
                                                        @lang('trans.approve')
                                                    </button>

                                                    <button class="btn btn-danger btn-sm"
                                                        onclick="openDecisionModal('personne_deces', '{{ $personne->id }}', '{{ $demandeAutorisation->id }}', 'reject')">
                                                        @lang('trans.reject')
                                                    </button>

                                                    @elseif ($personne->valider == 1)
                                                    <span class="badge bg-success">@lang('trans.approved')</span>

                                                    <button class="btn btn-danger btn-sm ms-2"
                                                        onclick="openDecisionModal('personne_deces', '{{ $personne->id }}', '{{ $demandeAutorisation->id }}', 'reject')">
                                                        @lang('trans.reject')
                                                    </button>

                                                    @else
                                                    <span class="badge bg-danger">@lang('trans.rejected')</span>

                                                    <button class="btn btn-success btn-sm ms-2"
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
                            <div class="row mt-4">
                                <div class="col-lg-12">
                                    <div class="table-responsive">
                                    <table class="table table-striped table-bordered" id="mdnTable">
                                        <thead>
                                            <tr>
                                                <th>@lang('trans.authorization_date')</th>
                                                <th>@lang('trans.mdn_number')</th>
                                                <th>@lang('trans.nationality')</th>
                                                <th>@lang('trans.actions')</th>
                                            </tr>
                                        </thead>
                                        <tbody>
                                            @foreach ($mdns as $mdn)
                                                <tr id="mdn-{{ $mdn->id }}">
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
                                                        onclick="openDecisionModal('mdns', '{{ $mdn->id }}', '{{ $demandeAutorisation->id }}', 'approve')">
                                                        @lang('trans.approve')
                                                    </button>

                                                    <button class="btn btn-danger btn-sm"
                                                        onclick="openDecisionModal('mdns', '{{ $mdn->id }}', '{{ $demandeAutorisation->id }}', 'reject')">
                                                        @lang('trans.reject')
                                                    </button>

                                                    @elseif ($mdn->valider == 1)
                                                    <span class="badge bg-success">@lang('trans.approved')</span>

                                                    <button class="btn btn-danger btn-sm ms-2"
                                                        onclick="openDecisionModal('mdns', '{{ $mdn->id }}', '{{ $demandeAutorisation->id }}', 'reject')">
                                                        @lang('trans.reject')
                                                    </button>

                                                    @else
                                                    <span class="badge bg-danger">@lang('trans.rejected')</span>

                                                    <button class="btn btn-success btn-sm ms-2"
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
                            


                            <div class="row mt-4">
                                <div class="col-lg-12">
                                    <div class="table-responsive">
                                    <table class="table table-striped table-bordered">
                                        <thead>
                                            <tr>
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
                                                        onclick="openDecisionModal('receiving_parties', '{{ $party->id }}', '{{ $demandeAutorisation->id }}', 'approve')">
                                                        @lang('trans.approve')
                                                    </button>

                                                    <button class="btn btn-danger btn-sm"
                                                        onclick="openDecisionModal('receiving_parties', '{{ $party->id }}', '{{ $demandeAutorisation->id }}', 'reject')">
                                                        @lang('trans.reject')
                                                    </button>

                                                    @elseif ($party->valider == 1)
                                                    <span class="badge bg-success">@lang('trans.approved')</span>

                                                    <button class="btn btn-danger btn-sm ms-2"
                                                        onclick="openDecisionModal('receiving_parties', '{{ $party->id }}', '{{ $demandeAutorisation->id }}', 'reject')">
                                                        @lang('trans.reject')
                                                    </button>

                                                    @else
                                                    <span class="badge bg-danger">@lang('trans.rejected')</span>

                                                    <button class="btn btn-success btn-sm ms-2"
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
                           


                            <div class="row mt-4">
                                <div class="col-12">
                                    <div class="table-responsive">
                                    <table class="table table-striped" id="documentsTable">
                                        <thead>
                                            <tr>
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
                                                <td>
                                                    {{ LaravelLocalization::getCurrentLocale() == 'fr' ? optional($document->typeDocument)->nom_fr : optional($document->typeDocument)->nom_en }}
                                                </td>
                                                <td>
                                                    <a href="{{ asset('/uploads/' . $document->url) }}" target="_blank"
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
                                                        onclick="openDecisionModal('document_autorisations', '{{ $document->id }}', '{{ $demandeAutorisation->id }}', 'approve')">
                                                        @lang('trans.approve')
                                                    </button>

                                                    <button class="btn btn-danger btn-sm"
                                                        onclick="openDecisionModal('document_autorisations', '{{ $document->id }}', '{{ $demandeAutorisation->id }}', 'reject')">
                                                        @lang('trans.reject')
                                                    </button>

                                                    @elseif ($document->valider == 1)
                                                    <span class="badge bg-success">@lang('trans.approved')</span>

                                                    <button class="btn btn-danger btn-sm ms-2"
                                                        onclick="openDecisionModal('document_autorisations', '{{ $document->id }}', '{{ $demandeAutorisation->id }}', 'reject')">
                                                        @lang('trans.reject')
                                                    </button>

                                                    @else
                                                    <span class="badge bg-danger">@lang('trans.rejected')</span>

                                                    <button class="btn btn-success btn-sm ms-2"
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
        // Fonction pour ouvrir la modale et définir les valeurs du formulaire
        function openDecisionModal(table, id, demande, action) {
            // Set modal values
            document.getElementById('modalTable').value = table;
            document.getElementById('modalId').value = id;
            document.getElementById('modalDemandeId').value = demande;
            document.getElementById('modalActionType').value = action;

            // Clear previous reason
            document.getElementById('modalMotif').value = '';

            // Configure modal based on action type
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

            // Set submit handler
            document.getElementById('modalSubmitBtn').onclick = function() {
                submitDecisionForm(action);
            };

            // Show modal
            new bootstrap.Modal(document.getElementById('decisionModal')).show();
        }

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



                // Submit via AJAX
                fetch(form.action, {
                        method: 'POST',
                        body: new FormData(form),
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
