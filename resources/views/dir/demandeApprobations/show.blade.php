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
    <link rel="stylesheet"
        href="https://cdnjs.cloudflare.com/ajax/libs/bootstrap-datepicker/1.9.0/css/bootstrap-datepicker.min.css">
    <link rel="stylesheet"
        href="https://cdnjs.cloudflare.com/ajax/libs/bootstrap-timepicker/0.5.2/css/bootstrap-timepicker.min.css">
    <link href="https://cdnjs.cloudflare.com/ajax/libs/toastr.js/latest/toastr.min.css" rel="stylesheet">
    <link rel="stylesheet" href="{{ asset('assets/admin/plugins/daterangepicker/daterangepicker.css') }}">
    <link rel="stylesheet" href="{{ asset('assets/admin/plugins/select2/css/select2.min.css') }}">
    <link rel="stylesheet" href="{{ asset('assets/admin/plugins/select2-bootstrap4-theme/select2-bootstrap4.min.css') }}">
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
    </style>
@endpush

@section('content')



    @if (auth()->user()->hasRole('dta'))
        <div class="container-fluid">
            <div class="row justify-content-center">
                <div class="col-md-12">
                    <div class="d-flex justify-content-between align-items-center mb-4">
                        <h4 class="text-center">
                            {{ $demandeApprobation->reference }} - {{ $demandeApprobation->saison }} -
                            {{ $demandeApprobation->date_debut }} -
                            {{ $demandeApprobation->date_fin }}</h4>
                        {{-- <div>
                        @if ($demandeApprobation->service_valider)
                            <span class="validation-badge validated">Validé</span>
                        @else
                            <span class="validation-badge not-validated">Non validé</span>
                        @endif
                    </div> --}}
                    </div>

                    <!-- Validation Modal -->
                    <div class="modal fade" id="validationModal" tabindex="-1" role="dialog"
                        aria-labelledby="validationModalLabel" aria-hidden="true">
                        <div class="modal-dialog" role="document">
                            <div class="modal-content">
                                <div class="modal-header">
                                    <h5 class="modal-title" id="validationModalLabel">Validation</h5>
                                    <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                                        <span aria-hidden="true">&times;</span>
                                    </button>
                                </div>
                                <form id="validationForm" method="POST">
                                    @csrf
                                    <div class="modal-body">
                                        <input type="hidden" name="item_id" id="validationItemId">
                                        <input type="hidden" name="demande_id" id="validationDemandeId">
                                        <input type="hidden" name="item_type" id="validationItemType">

                                        <div class="form-group">
                                            <label for="validationStatus">Statut</label>
                                            <select class="form-control" name="valider" id="validationStatus">
                                                <option value="1">Valider</option>
                                                <option value="0">Rejeter</option>
                                            </select>
                                        </div>

                                        <div class="form-group" id="commentGroup" style="display: none;">
                                            <label for="validationMotif">Commentaires</label>
                                            <textarea class="form-control" name="motif" id="validationMotif" rows="3"
                                                placeholder="Entrez les commentaires de validation..."></textarea>
                                        </div>
                                    </div>
                                    <div class="modal-footer">
                                        <button type="button" class="btn btn-secondary"
                                            data-dismiss="modal">Fermer</button>
                                        <button type="submit" class="btn btn-primary">Enregistrer</button>
                                    </div>
                                </form>
                            </div>
                        </div>
                    </div>

                    <div class="card card-primary">
                        <div class="card-header bg-primary text-white d-flex justify-content-between align-items-center">
                            <h3 class="card-title">Avions</h3>
                            {{--  @if (!$demandeApprobation->valider)
                            <button class="btn btn-sm btn-light"
                                onclick="validateItem('demande', {{ $demandeApprobation->id }})">
                                Valider cette section
                            </button>
                        @endif --}}
                        </div>
                        <div class="card-body">
                            @if ($demandeApprobation->motif)
                                <div class="alert alert-warning">
                                    <strong>Commentaires de validation:</strong> {{ $demandeApprobation->motif }}
                                </div>
                            @endif

                            @if (isset($avions) && $avions->isNotEmpty())
                                <div class="row mt-4" id="avionsTableContainer">
                                    <div class="col-lg-12">
                                        <table class="table table-striped table-bordered" id="avionsTable">
                                            <thead>
                                                <tr>
                                                    <th>Immatriculation</th>
                                                    <th>Type</th>
                                                    <th>Statut</th>
                                                    <th>Actions</th>
                                                </tr>
                                            </thead>
                                            <tbody>
                                                @foreach ($avions as $avionItem)
                                                    <tr id="avion-{{ $avionItem->id }}">
                                                        <td>{{ $avionItem->immatriculation }}</td>
                                                        <td>{{ $avionItem->type->code ?? 'N/A' }}</td>
                                                        <td>
                                                            @if ($avionItem->valider)
                                                                <span class="validation-badge validated">Validé</span>
                                                            @else
                                                                <span class="validation-badge not-validated">Non
                                                                    validé</span>
                                                            @endif
                                                            @if ($avionItem->motif)
                                                                <div class="validation-comments">{{ $avionItem->motif }}
                                                                </div>
                                                            @endif
                                                        </td>
                                                        <td>
                                                            @if ($avionItem->valider)
                                                                <button class="btn btn-sm btn-info"
                                                                    onclick="validateItem('avion', {{ $avionItem->id }}, {{ $demandeApprobation->id }})">
                                                                    @lang('trans.rectify')
                                                                </button>
                                                            @endif
                                                        </td>
                                                    </tr>
                                                @endforeach
                                            </tbody>
                                        </table>
                                    </div>
                                </div>
                            @else
                                <div class="alert alert-info" id="noAvionsAlert">
                                    Aucun avion enregistré.
                                </div>
                            @endif
                        </div>
                    </div>

                    <div class="card card-primary">
                        <div class="card-header bg-primary text-white d-flex justify-content-between align-items-center">
                            <h3 class="card-title">Programme des vols</h3>
                            {{--  @if (!$demandeApprobation->valider)
                            <button class="btn btn-sm btn-light"
                                onclick="validateItem('vols', {{ $demandeApprobation->id }})">
                                Valider cette section
                            </button>
                        @endif --}}
                        </div>
                        <div class="card-body">
                            @if (isset($vols) && $vols->isNotEmpty())
                                <div class="row mt-4" id="volsTableContainer">
                                    <div class="col-lg-12">
                                        <table class="table table-striped table-bordered" id="volsTable">
                                            <thead>
                                                <tr>
                                                    <th>@lang('trans.flight_number')</th>
                                                    <th>@lang('trans.operating_days')</th>
                                                    <th>@lang('trans.origin')</th>
                                                    <th>@lang('trans.destination')</th>
                                                    <th>@lang('trans.departure_time')</th>
                                                    <th>@lang('trans.arrival_time')</th>
                                                    <th>@lang('trans.period')</th>
                                                    <th>Statut</th>
                                                    <th>@lang('trans.actions')</th>
                                                </tr>
                                            </thead>
                                            <tbody>
                                                @foreach ($vols as $volItem)
                                                    <tr id="vol-{{ $volItem->id }}">
                                                        <td>{{ $volItem->numero_vol }}</td>
                                                        <td>{{ $volItem->jours_operation_display }}</td>
                                                        <td>{{ $volItem->aeroportDepart->codeICAO }}</td>
                                                        <td>{{ $volItem->aeroportArrivee->codeICAO }}</td>
                                                        <td>{{ date('H:i', strtotime($volItem->heure_depart)) }}</td>
                                                        <td>{{ date('H:i', strtotime($volItem->heure_arrivee)) }}</td>
                                                        <td>{{ $volItem->period_formatted }}</td>
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
                                                            @if ($volItem->valider)
                                                                <button class="btn btn-sm btn-info"
                                                                    onclick="validateItem('vol', {{ $volItem->id }},{{ $demandeApprobation->id }})">
                                                                    @lang('trans.rectify')
                                                                </button>
                                                            @endif
                                                        </td>
                                                    </tr>
                                                @endforeach
                                            </tbody>
                                        </table>
                                    </div>
                                </div>
                            @else
                                <div class="alert alert-info" id="noVolsAlert">
                                    Aucun vol enregistré.
                                </div>
                            @endif
                        </div>
                    </div>

                    <!-- Itinerary Section -->
                    <div class="card card-primary">
                        <div class="card-header bg-primary text-white d-flex justify-content-between align-items-center">
                            <h3 class="card-title">@lang('trans.itinerary')</h3>
                            {{--  @if (!$demandeApprobation->valider)
                            <button class="btn btn-sm btn-light"
                                onclick="validateItem('itineraires', {{ $demandeApprobation->id }})">
                                Valider cette section
                            </button>
                        @endif --}}
                        </div>
                        <div class="card-body">
                            @if (isset($itineraires) && $itineraires->isNotEmpty())
                                <div class="row mt-4">
                                    <div class="col-lg-12">
                                        @php
                                            $groupedItineraires = $itineraires->groupBy('vol_id');
                                        @endphp

                                        @foreach ($groupedItineraires as $volId => $volItineraires)
                                            @php
                                                $vol = $volItineraires->first()->volApprobation;
                                                $output = [];
                                                foreach ($volItineraires as $itineraire) {
                                                    $aeroportCode = $itineraire->aeroport->codeICAO;
                                                    $heureArrivee = date('Hi', strtotime($itineraire->heure_arrivee));
                                                    $heureDepart = date('Hi', strtotime($itineraire->heure_depart));
                                                    if (empty($output)) {
                                                        $output[] = "{$heureArrivee} {$aeroportCode} {$heureDepart}";
                                                    } else {
                                                        $output[] = " - {$heureArrivee} {$aeroportCode} {$heureDepart}";
                                                    }
                                                }
                                                $routeString = implode(' ', $output);
                                            @endphp

                                            <div class="card mb-3">
                                                <div
                                                    class="card-header bg-light d-flex justify-content-between align-items-center">
                                                    <h4 class="card-title mb-0">
                                                        Vol {{ $vol->numero_vol }}
                                                        ({{ $vol->period_formatted }} -
                                                        {{ $vol->jours_operation_display }})
                                                    </h4>

                                                </div>
                                                <div class="card-body">
                                                    @if ($vol->motif)
                                                        <div class="alert alert-warning">
                                                            <strong>Commentaires de validation:</strong>
                                                            {{ $vol->motif }}
                                                        </div>
                                                    @endif

                                                    <div class="mb-3">
                                                        <strong>Route :</strong> {{ $routeString }}
                                                    </div>

                                                    <table class="table table-striped table-bordered">
                                                        <thead>
                                                            <tr>
                                                                <th>@lang('trans.aeroport')</th>
                                                                <th>@lang('trans.arrival_date')</th>
                                                                <th>@lang('trans.departure_date')</th>
                                                                <th>Statut</th>
                                                                <th>@lang('trans.actions')</th>
                                                            </tr>
                                                        </thead>
                                                        <tbody>
                                                            @foreach ($volItineraires as $itineraire)
                                                                <tr id="itineraire-{{ $itineraire->id }}">
                                                                    <td>{{ $itineraire->aeroport->codeICAO }} -
                                                                        {{ $itineraire->aeroport->nom }}</td>
                                                                    <td>{{ $itineraire->heure_arrivee }}</td>
                                                                    <td>{{ $itineraire->heure_depart }}</td>
                                                                    <td>
                                                                        @if ($itineraire->valider)
                                                                            <span
                                                                                class="validation-badge validated">Validé</span>
                                                                        @else
                                                                            <span
                                                                                class="validation-badge not-validated">Non
                                                                                validé</span>
                                                                        @endif
                                                                        @if ($itineraire->motif)
                                                                            <div class="validation-comments">
                                                                                {{ $itineraire->motif }}</div>
                                                                        @endif
                                                                    </td>
                                                                    <td>
                                                                        @if ($itineraire->valider)
                                                                            <button class="btn btn-sm btn-info"
                                                                                onclick="validateItem('itineraire', {{ $itineraire->id }},{{ $demandeApprobation->id }})">
                                                                                @lang('trans.rectify')
                                                                            </button>
                                                                        @endif
                                                                    </td>
                                                                </tr>
                                                            @endforeach
                                                        </tbody>
                                                    </table>
                                                </div>
                                            </div>
                                        @endforeach
                                    </div>
                                </div>
                            @endif
                        </div>
                    </div>

                    <!-- Documents Section -->
                    <div class="card card-primary">
                        <div class="card-header bg-primary text-white d-flex justify-content-between align-items-center">
                            <h3 class="card-title">Documents</h3>
                            {{--  @if (!$demandeApprobation->valider)
                            <button class="btn btn-sm btn-light"
                                onclick="validateItem('documents', {{ $demandeApprobation->id }})">
                                Valider cette section
                            </button>
                        @endif --}}
                        </div>
                        <div class="card-body">
                            @if ($demandeApprobation->documents)
                                <div class="row mt-4">
                                    <div class="col-12">
                                        <table class="table table-striped" id="documentsTable">
                                            <thead>
                                                <tr>
                                                    <th>Type</th>
                                                    <th>Document</th>
                                                    <th>Statut</th>
                                                    <th>Actions</th>
                                                </tr>
                                            </thead>
                                            <tbody>
                                                @foreach ($demandeApprobation->documents as $document)
                                                    <tr id="document-{{ $document->id }}">
                                                        <td>
                                                            {{ optional($document->typeDocument)->nom_fr }}
                                                        </td>
                                                        <td>
                                                            <a href="{{ asset('/uploads/' . $document->url) }}"
                                                                target="_blank" class="btn btn-sm btn-primary">
                                                                <i class="fas fa-eye"></i>
                                                            </a>
                                                        </td>
                                                        <td>
                                                            @if ($document->valider)
                                                                <span class="validation-badge validated">Validé</span>
                                                            @else
                                                                <span class="validation-badge not-validated">Non
                                                                    validé</span>
                                                            @endif
                                                            @if ($document->motif)
                                                                <div class="validation-comments">{{ $document->motif }}
                                                                </div>
                                                            @endif
                                                        </td>
                                                        <td>
                                                            @if ($document->valider)
                                                                <button class="btn btn-sm btn-info"
                                                                    onclick="validateItem('document', {{ $document->id }}, {{ $demandeApprobation->id }})">
                                                                    @lang('trans.rectify')
                                                                </button>
                                                            @endif
                                                        </td>
                                                    </tr>
                                                @endforeach
                                            </tbody>
                                        </table>
                                    </div>
                                </div>
                            @else
                                <div class="alert alert-info mt-3">
                                    Aucun document n'a été uploadé pour cette demande.
                                </div>
                            @endif
                        </div>
                    </div>

                    <!-- Global Validation Section -->
                    @if (!$demandeApprobation?->isFullyValidated() ?? false)
                        <div class="card card-primary">
                            <div class="card-header bg-primary text-white">
                                <h3 class="card-title">Validation Globale</h3>
                            </div>
                            <div class="card-body">
                                <button class="btn btn-success btn-lg btn-block"
                                    onclick="validateItem('demande', {{ $demandeApprobation->id }},{{ $demandeApprobation->id }}, true)">
                                    <i class="fas fa-check-circle"></i> Valider toute la demande
                                </button>
                            </div>
                        </div>
                    @endif
                </div>
            </div>
        </div>
    @else
        <div class="container-fluid">
            <div class="row justify-content-center">
                <div class="col-md-12">
                    <h4 class="text-center">
                        {{ $demandeApprobation->reference }} - {{ $demandeApprobation->saison }} -
                        {{ $demandeApprobation->date_debut }} -
                        {{ $demandeApprobation->date_fin }}</h4>


                    <div class="card card-primary">
                        <div class="card-header bg-primary text-white">
                            <h3 class="card-title">Avions</h3>



                        </div>
                        <div class="card-body">


                            <!-- Tableau des avions existants -->
                            @if (isset($avions) && $avions->isNotEmpty())
                                <div class="row mt-4" id="avionsTableContainer">
                                    <div class="col-lg-12">
                                        <table class="table table-striped table-bordered" id="avionsTable">
                                            <thead>
                                                <tr>
                                                    <th>Immatriculation</th>
                                                    <th>Type</th>


                                                </tr>
                                            </thead>
                                            <tbody>
                                                @foreach ($avions as $avionItem)
                                                    <tr id="avion-{{ $avionItem->id }}">
                                                        <td>{{ $avionItem->immatriculation }}</td>
                                                        <td>{{ $avionItem->type->code ?? 'N/A' }}</td>

                                                    </tr>
                                                @endforeach
                                            </tbody>
                                        </table>
                                    </div>
                                </div>
                            @else
                                <div class="alert alert-info" id="noAvionsAlert">
                                    Aucun avion enregistré.
                                </div>
                            @endif
                        </div>
                    </div>
                    <div class="card card-primary">
                        <div class="card-header bg-primary text-white">
                            <h3 class="card-title">Programme des vols</h3>



                        </div>
                        <div class="card-body">
                            <!-- Tableau des vols existants -->
                            @if (isset($vols) && $vols->isNotEmpty())
                                <div class="row mt-4" id="volsTableContainer">
                                    <div class="col-lg-12">
                                        <table class="table table-striped table-bordered" id="volsTable">
                                            <thead>
                                                <tr>
                                                    <th>@lang('trans.flight_number')</th>
                                                    <th>@lang('trans.operating_days')</th>
                                                    <th>@lang('trans.origin')</th>
                                                    <th>@lang('trans.destination')</th>
                                                    <th>@lang('trans.departure_time')</th>
                                                    <th>@lang('trans.arrival_time')</th>
                                                    <th>@lang('trans.period')</th>

                                                </tr>
                                            </thead>
                                            <tbody>
                                                @foreach ($vols as $volItem)
                                                    <tr id="vol-{{ $volItem->id }}">
                                                        <td>{{ $volItem->numero_vol }}</td>
                                                        <td>{{ $volItem->jours_operation_display }}</td>
                                                        <td>{{ $volItem->aeroportDepart->codeICAO }}</td>
                                                        <td>{{ $volItem->aeroportArrivee->codeICAO }}</td>
                                                        <td>{{ date('H:i', strtotime($volItem->heure_depart)) }}</td>
                                                        <td>{{ date('H:i', strtotime($volItem->heure_arrivee)) }}</td>
                                                        <td>{{ $volItem->period_formatted }}</td>

                                                    </tr>
                                                @endforeach
                                            </tbody>
                                        </table>
                                    </div>
                                </div>
                            @else
                                <div class="alert alert-info" id="noVolsAlert">
                                    Aucun vol enregistré.
                                </div>
                            @endif
                        </div>
                    </div>

                    <!-- Itinerary Section -->
                    <div class="card card-primary">
                        <div class="card-header bg-primary text-white">
                            <h3 class="card-title">@lang('trans.itinerary')</h3>
                        </div>
                        <div class="card-body">


                            @if (isset($itineraires) && $itineraires->isNotEmpty())
                                <div class="row mt-4">
                                    <div class="col-lg-12">
                                        @php
                                            // Regrouper les itinéraires par vol
                                            $groupedItineraires = $itineraires->groupBy('vol_id');
                                        @endphp

                                        @foreach ($groupedItineraires as $volId => $volItineraires)
                                            @php
                                                $vol = $volItineraires->first()->volApprobation;
                                                $output = [];
                                                foreach ($volItineraires as $itineraire) {
                                                    $aeroportCode = $itineraire->aeroport->codeICAO;
                                                    $heureArrivee = date('Hi', strtotime($itineraire->heure_arrivee));
                                                    $heureDepart = date('Hi', strtotime($itineraire->heure_depart));
                                                    if (empty($output)) {
                                                        $output[] = "{$heureArrivee} {$aeroportCode} {$heureDepart}";
                                                    } else {
                                                        $output[] = " - {$heureArrivee} {$aeroportCode} {$heureDepart}";
                                                    }
                                                }
                                                $routeString = implode(' ', $output);
                                            @endphp

                                            <div class="card mb-3">
                                                <div class="card-header bg-light">
                                                    <h4 class="card-title">
                                                        Vol {{ $vol->numero_vol }}
                                                        ({{ $vol->period_formatted }} -
                                                        {{ $vol->jours_operation_display }})
                                                    </h4>
                                                </div>
                                                <div class="card-body">
                                                    <div class="mb-3">
                                                        <strong>Route :</strong> {{ $routeString }}
                                                    </div>

                                                    <table class="table table-striped table-bordered">
                                                        <thead>
                                                            <tr>
                                                                <th>@lang('trans.aeroport')</th>
                                                                <th>@lang('trans.arrival_date')</th>
                                                                <th>@lang('trans.departure_date')</th>

                                                            </tr>
                                                        </thead>
                                                        <tbody>
                                                            @foreach ($volItineraires as $itineraire)
                                                                <tr id="itineraire-{{ $itineraire->id }}">
                                                                    <td>{{ $itineraire->aeroport->codeICAO }} -
                                                                        {{ $itineraire->aeroport->nom }}</td>
                                                                    <td>{{ $itineraire->heure_arrivee }}</td>
                                                                    <td>{{ $itineraire->heure_depart }}</td>

                                                                </tr>
                                                            @endforeach
                                                        </tbody>
                                                    </table>
                                                </div>
                                            </div>
                                        @endforeach
                                    </div>
                                </div>
                            @endif
                        </div>
                    </div>

                    <!-- Documents Section -->

                    <div class="card card-primary">
                        <div class="card-header bg-primary text-white">
                            <h3 class="card-title">Documents</h3>
                        </div>
                        <div class="card-body">

                            @if ($demandeApprobation->documents)
                                <div class="row mt-4">
                                    <div class="col-12">
                                        <table class="table table-striped" id="documentsTable">
                                            <thead>
                                                <tr>
                                                    <th>Type</th>
                                                    <th>Document</th>

                                                </tr>
                                            </thead>
                                            <tbody>
                                                @foreach ($demandeApprobation->documents as $document)
                                                    <tr id="document-{{ $document->id }}">
                                                        <td>
                                                            {{ optional($document->typeDocument)->nom_fr }}
                                                        </td>
                                                        <td>
                                                            <a href="{{ asset('/uploads/' . $document->url) }}"
                                                                target="_blank" class="btn btn-sm btn-primary">
                                                                <i class="fas fa-eye"></i>
                                                            </a>
                                                        </td>

                                                    </tr>
                                                @endforeach
                                            </tbody>
                                        </table>
                                    </div>
                                </div>
                            @else
                                <div class="alert alert-info mt-3">
                                    Aucun document n'a été uploadé pour cette demande.
                                </div>
                            @endif
                        </div>
                    </div>

                </div>
            </div>
        </div>
    @endif
@endsection

@push('script')
    <script src="https://cdnjs.cloudflare.com/ajax/libs/bootstrap-datepicker/1.9.0/js/bootstrap-datepicker.min.js"></script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/bootstrap-datepicker/1.9.0/locales/bootstrap-datepicker.fr.min.js">
    </script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/bootstrap-timepicker/0.5.2/js/bootstrap-timepicker.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/toastr.js/latest/toastr.min.js"></script>
    <script src="{{ asset('assets/admin/plugins/moment/moment.min.js') }}"></script>
    <script src="{{ asset('assets/admin/plugins/daterangepicker/daterangepicker.js') }}"></script>
    <script src="{{ asset('assets/admin/plugins/select2/js/select2.full.min.js') }}"></script>
    <script>
        $(document).ready(function() {
            // Show/hide comments based on selection
            $('#validationStatus').change(function() {
                if ($(this).val() == '0') { // If Reject is selected
                    $('#commentGroup').show();
                } else {
                    $('#commentGroup').hide();
                    $('#validationMotif').val(''); // Clear comments when not rejecting
                }
            });

            // Trigger change event on modal show to set initial state
            $('#validationModal').on('show.bs.modal', function() {
                $('#validationStatus').trigger('change');
            });
        });
    </script>
    <script>
        function validateItem(type, id, demandeId, isGlobal = false) {
            // Set the form action based on the type
            let actionUrl;
            switch (type) {
                case 'avion':
                    actionUrl = "{{ route('validate.avion') }}";
                    break;
                case 'vol':
                    actionUrl = "{{ route('validate.vol') }}";
                    break;
                case 'itineraire':
                    actionUrl = "{{ route('validate.itineraire') }}";
                    break;
                case 'document':
                    actionUrl = "{{ route('validate.document') }}";
                    break;
                case 'demande':
                    actionUrl = "{{ route('validate.demande') }}";
                    break;
                default:
                    actionUrl = "#";
            }

            // Set the form values
            $('#validationForm').attr('action', actionUrl);
            $('#validationItemId').val(id);
            $('#validationDemandeId').val(demandeId);
            $('#validationItemType').val(type);

            // If this is a global validation, change the modal title
            if (isGlobal) {
                $('#validationModalLabel').text('Validation Globale de la Demande');
            } else {
                $('#validationModalLabel').text('Validation - ' + type.charAt(0).toUpperCase() + type.slice(1));
            }

            // Show the modal
            $('#validationModal').modal('show');
        }

        // Handle form submission
        $('#validationForm').on('submit', function(e) {
            e.preventDefault();

            $.ajax({
                url: $(this).attr('action'),
                method: 'POST',
                data: $(this).serialize(),
                success: function(response) {
                    if (response.success) {
                        toastr.success(response.message);
                        $('#validationModal').modal('hide');

                        // Reload the page to see the changes
                        setTimeout(() => {
                            location.reload();
                        }, 1500);
                    } else {
                        toastr.error(response.message);
                    }
                },
                error: function(xhr) {
                    toastr.error('Une erreur est survenue lors de la validation.');
                }
            });
        });
    </script>
@endpush
