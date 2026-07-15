@extends('user.layouts.app')
@section('title')
    @lang('trans.dashboard')
@endsection
@section('contentheader')
    @lang('trans.dashboard')
@endsection
@section('contentheaderlink')
    <a href="{{ route('user') }}">
        @lang('trans.dashboard')</a>
@endsection
@section('contentheaderactive')
    @lang('trans.dashboard')
@endsection

@push('css')
    <link rel="stylesheet" href="{{ asset('assets/admin/plugins/datatables-bs4/css/dataTables.bootstrap4.min.css') }}">
    <link rel="stylesheet" href="{{ asset('assets/admin/plugins/datatables-responsive/css/responsive.bootstrap4.min.css') }}">
    <link rel="stylesheet"
        href="https://cdnjs.cloudflare.com/ajax/libs/bootstrap-datepicker/1.9.0/css/bootstrap-datepicker.min.css">
    <link rel="stylesheet"
        href="https://cdnjs.cloudflare.com/ajax/libs/bootstrap-timepicker/0.5.2/css/bootstrap-timepicker.min.css">
    <link href="https://cdnjs.cloudflare.com/ajax/libs/toastr.js/latest/toastr.min.css" rel="stylesheet">
    <link rel="stylesheet" href="{{ asset('assets/admin/plugins/select2/css/select2.min.css') }}">
    <link rel="stylesheet" href="{{ asset('assets/admin/plugins/select2-bootstrap4-theme/select2-bootstrap4.min.css') }}">
    <style>
        .select2-container--bootstrap4 .select2-selection--multiple .select2-selection__choice {
            background-color: #28a745;
            color: white;
            border-color: #28a745;
            padding: 5px 10px;
        }

        .select2-container--bootstrap4 .select2-selection--multiple .select2-selection__choice__remove {
            color: white;
            margin-right: 5px;
            border-right: 1px solid rgba(255, 255, 255, 0.3);
        }

        .select2-container--bootstrap4 .select2-selection--multiple .select2-selection__choice__remove:hover {
            color: #ffc107;
            background: transparent;
        }

        .select2-container--bootstrap4 .select2-selection--multiple {
            min-height: 100px;
            border: 1px solid #ced4da;
        }

        .select2-container--bootstrap4 .select2-selection--multiple .select2-search__field {
            margin-top: 8px;
        }

        .preview-badge {
            font-size: 0.9rem;
            padding: 5px 10px;
            margin: 2px;
            border-radius: 3px;
            background-color: #17a2b8;
            color: white;
            display: inline-block;
        }

        #volForm>.row:nth-of-type(2),
        #volForm>.row:nth-of-type(3) {
            padding: 15px;
            margin-right: 0;
            margin-left: 0;
            margin-bottom: 15px;
            border: 1px solid #e9ecef;
            border-left: 4px solid #007bff;
            border-radius: 4px;
            background: #fbfcfe;
        }

        #volForm>.row:nth-of-type(3) {
            border-left-color: #28a745;
        }

        #aeroport_depart_id+.select2-container,
        #aeroport_arrivee_id+.select2-container {
            width: 100% !important;
            min-width: 100%;
        }
    </style>
@endpush

@section('content')
    <div class="container-fluid">
        <div class="row justify-content-center">
            <div class="col-md-12">
                <!-- Au début de la vue, dans le titre -->
                <h4 class="text-center">
                    {{ $demandeAutorisation->type->libelle }} -
                    @if ($demandeAutorisation->type_demande_autorisation_id == 3)
                        {{ $demandeAutorisation->type_vol_names }}
                    @else
                        {{ $demandeAutorisation->typeVol->nom ?? 'N/A' }}
                    @endif
                    - {{ $demandeAutorisation->date_debut }} -
                    {{ $demandeAutorisation->date_fin }} - {{ Auth::user()->demandeur->np }}
                    - {{ strtoupper($demandeAutorisation->objet) ?? 'N/A' }}
                    @if (!empty($demandeAutorisation->sous_validite))
                        - {{ '+' . $demandeAutorisation->sous_validite }} H
                    @endif
                </h4>

                <div class="card card-primary">
                    <div class="card-header bg-primary text-white">
                        <h3 class="card-title">Information sur l'avion</h3>
                        <button type="button" class="btn btn-sm btn-light float-right" id="showAvionFormBtn">
                            <i class="fas fa-plus"></i> Ajouter des avions
                        </button>
                    </div>
                    <div class="card-body">
                        <!-- Formulaire avec Select2 Tags -->
                        <form method="POST" id="avionForm" style="display: none;">
                            @csrf
                            <input type="hidden" name="avion_id" id="avion_id" value="">
                            <input type="hidden" name="demande_autorisation_id" id="demande_autorisation_id"
                                value="{{ $demandeAutorisation->id }}">

                            <div class="alert alert-info">
                                <i class="fas fa-info-circle"></i>
                                Saisissez les immatriculations une par une et appuyez sur <kbd>Entrée</kbd> ou <kbd>,</kbd>
                                pour les ajouter.
                            </div>

                            <div class="row">
                                <!-- Immatriculations multiples avec Select2 Tags -->
                                <div class="col-md-12 mb-3">
                                    <div class="form-group">
                                        <label for="immatriculations" class="form-label">
                                            Immatriculations <span class="text-danger">*</span>
                                        </label>
                                        <!-- Champs caché pour Select2 -->
                                        <select class="form-control" id="immatriculations_select" name="immatriculations[]"
                                            multiple="multiple" style="width: 100%; height: 100px;">
                                            <!-- Les options seront ajoutées dynamiquement -->
                                        </select>
                                        <div class="invalid-feedback" id="immatriculations_error"></div>
                                        <small class="text-muted">Tapez une immatriculation et appuyez sur Entrée pour
                                            l'ajouter</small>
                                    </div>
                                </div>
                            </div>

                            <div class="row">
                                <!-- Type d'avion -->
                                <div class="col-md-6 mb-3">
                                    <div class="form-group">
                                        <div class="d-flex justify-content-between align-items-center mb-1">
                                            <label for="type_avion_id" class="form-label">Type d'avion <span
                                                    class="text-danger">*</span></label>
                                            <button type="button" class="btn btn-sm btn-success" id="addTypeAvionBtn">
                                                <i class="fas fa-plus"></i> Ajouter
                                            </button>
                                        </div>
                                        <select class="form-control select2-single" id="type_avion_id" name="type_avion_id"
                                            required>
                                            <option value="">Sélectionnez un type</option>
                                            @foreach ($type_avions as $type)
                                                <option value="{{ $type->id }}" data-code="{{ $type->code }}"
                                                    data-capacite="{{ $type->capacite }}">
                                                    {{ $type->code }} ({{ $type->capacite }} places)
                                                </option>
                                            @endforeach
                                        </select>
                                        <div class="invalid-feedback" id="type_avion_id_error"></div>
                                    </div>
                                </div>

                                <!-- Opérateur -->
                                <div class="col-md-6 mb-3">
                                    <div class="form-group">
                                        <div class="d-flex justify-content-between align-items-center mb-1">
                                            <label for="compagnie_aerienne_id" class="form-label">Opérateur <span
                                                    class="text-danger">*</span></label>
                                            <button type="button" class="btn btn-sm btn-success" id="addCompanyBtn">
                                                <i class="fas fa-plus"></i> Ajouter
                                            </button>
                                        </div>

                                        <select class="form-control select2-single" id="compagnie_aerienne_id"
                                            name="compagnie_aerienne_id" required>
                                            <option value="">Sélectionnez un Opérateur</option>
                                            @foreach ($compagnies as $compagnie)
                                                <option value="{{ $compagnie->id }}" data-code="{{ $compagnie->code }}">
                                                    @if (!empty($compagnie->code))
                                                        {{ $compagnie->code }} {{ $compagnie->nom_entreprise }}
                                                    @else
                                                        {{ $compagnie->nom_entreprise }}
                                                    @endif
                                                </option>
                                            @endforeach
                                        </select>
                                        <div class="invalid-feedback" id="compagnie_aerienne_id_error"></div>
                                    </div>
                                </div>
                            </div>

                            <!-- Prévisualisation -->
                            <div class="row" id="previewSection" style="display: none;">
                                <div class="col-md-12">
                                    <div class="alert alert-success">
                                        <h6><i class="fas fa-plane"></i> Aperçu des avions à ajouter :</h6>
                                        <div class="row">
                                            <div class="col-md-6">
                                                <p><strong>Type:</strong> <span id="selectedTypeDisplay">-</span></p>
                                                <p><strong>Opérateur:</strong> <span id="selectedOperatorDisplay">-</span>
                                                </p>
                                            </div>
                                            <div class="col-md-6">
                                                <div id="immatriculationsPreview" class="mt-2"></div>
                                                <p class="mb-0"><strong>Total:</strong> <span id="totalCount">0</span>
                                                    avion(s)</p>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>

                            <div class="row">
                                <div class="col-md-12">
                                    <button type="submit" class="btn btn-success float-right" id="submitAvionBtn">
                                        <i class="fas fa-save"></i> <span id="formActionText">Ajouter les avions</span>
                                    </button>
                                    <button type="button" class="btn btn-secondary float-right mr-2"
                                        id="cancelAvionFormBtn">
                                        <i class="fas fa-times"></i> Annuler
                                    </button>
                                </div>
                            </div>
                        </form>

                        <!-- Tableau des avions existants -->
                        @if (isset($avions) && $avions->isNotEmpty())
                            <div class="table-responsive">
                                <div class="row mt-4" id="avionsTableContainer">
                                    <div class="col-lg-12">
                                        <div class="table-responsive">
                                            <table class="table table-striped table-bordered" id="avionsTable">
                                                <thead>
                                                    <tr>
                                                        <th>Immatriculation</th>
                                                        <th>Type</th>
                                                    <th>Opérateur</th>
                                                        <th>Actions</th>
                                                    </tr>
                                                </thead>
                                                <tbody>
                                                    @foreach ($avions as $avionItem)
                                                        <tr id="avion-{{ $avionItem->id }}">
                                                            <td>{{ $avionItem->immatriculation }}</td>
                                                            <td>{{ $avionItem->type->code ?? 'N/A' }}</td>
                                                            <td>{{ $avionItem->compagnie->nom_entreprise ?? 'N/A' }}</td>
                                                            <td>
                                                                <div class="btn-group" role="group">
                                                                    <button class="btn btn-warning btn-sm edit-avion"
                                                                        data-id="{{ $avionItem->id }}"
                                                                        data-immatriculation="{{ $avionItem->immatriculation }}"
                                                                        data-type_avion_id="{{ $avionItem->type_avion_id }}"
                                                                        data-compagnie_aerienne_id="{{ $avionItem->compagnie_aerienne_id }}">
                                                                        <i class="fas fa-edit"></i> Modifier
                                                                    </button>
                                                                    <button class="btn btn-danger btn-sm delete-avion"
                                                                        data-id="{{ $avionItem->id }}">
                                                                        <i class="fas fa-trash"></i> Supprimer
                                                                    </button>
                                                                </div>
                                                            </td>
                                                        </tr>
                                                    @endforeach
                                                </tbody>
                                            </table>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        @else
                            <div class="alert alert-info" id="noAvionsAlert">
                                Aucun avion enregistré.
                            </div>
                        @endif
                    </div>
                </div>

                <!-- Information sur le vol -->
                <div class="card card-primary">
                    <div class="card-header bg-primary text-white">
                        <h3 class="card-title">Information sur le vol</h3>
                        <button type="button" class="btn btn-sm btn-light float-right" id="showVolFormBtn">
                            <i class="fas fa-plus"></i> Ajouter un vol
                        </button>
                    </div>
                    <div class="card-body">
                        <!-- Formulaire (caché par défaut) -->
                        <form method="POST" id="volForm" style="display: none;">
                            @csrf
                            <input type="hidden" name="vol_id" id="vol_id" value="">
                            <input type="hidden" name="demande_autorisation_id" id="demande_autorisation_id"
                                value="{{ $demandeAutorisation->id }}">

                            <div class="row">
                                <!-- Numéro de vol -->
                                @if (!in_array($demandeAutorisation->type->id, [3]))
                                    <div class="col-md-6 mb-3">
                                        <div class="form-group">
                                            <label for="numero_vol" class="form-label">@lang('trans.flight_number')</label>
                                            <input type="text" class="form-control" id="numero_vol"
                                                name="numero_vol">
                                            <div class="invalid-feedback" id="numero_vol_error"></div>
                                        </div>
                                    </div>
                                @endif
                                @if (in_array($demandeAutorisation->type->id, [2, 3, 4, 5, 7]))
                                    <!-- Nombre de passagers -->
                                    <div class="col-md-6 mb-3">
                                        <div class="form-group">
                                            <label for="nbr_passagers" id = "nbr_passagers_label"
                                                class="form-label">Nombre de passagers</label>
                                            <input type="number" class="form-control" id="nbr_passagers"
                                                name="nbr_passagers" min="0">
                                            <div class="invalid-feedback" id="nbr_passagers_error"></div>
                                        </div>
                                    </div>
                                @endif
                            </div>

                            <div class="row">
                                <!-- Aéroport de départ -->
                                <div class="col-lg-6 col-md-5 mb-3">
                                    <div class="form-group">
                                        <div class="d-flex justify-content-between align-items-center mb-1">
                                            <label for="aeroport_depart_id" class="form-label">@lang('trans.start_aeroport') <span
                                                    class="text-danger">*</span></label>
                                            <button type="button" class="btn btn-sm btn-success" id="addAeroportBtn">
                                                <i class="fas fa-plus"></i> Ajouter
                                            </button>
                                        </div>
                                        <select class="form-control select2_aeroports" id="aeroport_depart_id"
                                            name="aeroport_depart_id" required>
                                            @foreach ($aeroports as $aeroport)
                                                <option value="{{ $aeroport->id }}">{{ $aeroport->codeICAO }}
                                                </option>
                                            @endforeach
                                        </select>
                                        <div class="invalid-feedback" id="aeroport_depart_id_error"></div>
                                    </div>
                                </div>

                                <div class="col-lg-3 col-md-3 mb-3">
                                    <div class="form-group">
                                        <label for="numero_piste_depart" class="form-label">Piste depart <span
                                                class="text-danger">*</span></label>
                                        <input type="text" class="form-control runway-number" id="numero_piste_depart"
                                            name="numero_piste_depart" maxlength="3"
                                            pattern="^(0[1-9]|[12][0-9]|3[0-6])[LCR]?$" placeholder="09L" required>
                                        <div class="invalid-feedback" id="numero_piste_depart_error"></div>
                                    </div>
                                </div>

                                <!-- Heure de départ -->
                                <div class="col-lg-3 col-md-3 mb-3">
                                    <div class="form-group">
                                        <label for="date_depart" class="form-label">Heure départ <span
                                                class="text-danger">*</span></label>
                                        <input type="time" class="form-control" id="date_depart" name="date_depart"
                                            required>
                                        <div class="invalid-feedback" id="date_depart_error"></div>
                                    </div>
                                </div>
                            </div>

                            <div class="row">
                                <!-- Aéroport d'arrivée -->
                                <div class="col-lg-6 col-md-5 mb-3">
                                    <div class="form-group">
                                        <label for="aeroport_arrivee_id">@lang('trans.end_aeroport')<span
                                                class="text-danger">*</span></label>
                                        <select class="form-control select2_aeroports" id="aeroport_arrivee_id"
                                            name="aeroport_arrivee_id" required>
                                            @foreach ($aeroports as $aeroport)
                                                <option value="{{ $aeroport->id }}">{{ $aeroport->codeICAO }}
                                                </option>
                                            @endforeach
                                        </select>
                                        <div class="invalid-feedback" id="aeroport_arrivee_id_error"></div>
                                    </div>
                                </div>

                                <div class="col-lg-3 col-md-3 mb-3">
                                    <div class="form-group">
                                        <label for="numero_piste_arrivee" class="form-label">Piste arrivee <span
                                                class="text-danger">*</span></label>
                                        <input type="text" class="form-control runway-number"
                                            id="numero_piste_arrivee" name="numero_piste_arrivee" maxlength="3"
                                            pattern="^(0[1-9]|[12][0-9]|3[0-6])[LCR]?$" placeholder="27R" required>
                                        <div class="invalid-feedback" id="numero_piste_arrivee_error"></div>
                                    </div>
                                </div>

                                <!-- Heure d'arrivée -->
                                <div class="col-lg-3 col-md-4 mb-3">
                                    <div class="form-group">
                                        <label for="date_arrivee" class="form-label">Heure arrivée <span
                                                class="text-danger">*</span></label>
                                        <input type="time" class="form-control" id="date_arrivee" name="date_arrivee"
                                            required>
                                        <div class="invalid-feedback" id="date_arrivee_error"></div>
                                    </div>
                                </div>
                            </div>

                            <!-- Section pour les escales intermédiaires -->
                            <div class="row">
                                <div class="col-md-12">
                                    <div class="card card-secondary">
                                        <div class="card-header">
                                            <h3 class="card-title">Aéroport(s) intermédiaire(s)</h3>
                                            <button type="button" class="btn btn-sm btn-success float-right"
                                                id="addEscaleBtn">
                                                <i class="fas fa-plus"></i> Ajouter une aéroport intermédiaire
                                            </button>
                                        </div>
                                        <div class="card-body" id="escalesContainer">
                                            <!-- Les escales seront ajoutées dynamiquement ici -->
                                            <div class="alert alert-info" id="noEscalesAlert">
                                                Aucune aéroport intermédiaire ajoutée.
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>



                            <div class="row">
                                <div class="col-md-12">
                                    <button type="submit" class="btn btn-success float-right" id="submitVolBtn">
                                        <i class="fas fa-save"></i> <span id="volFormAction">Ajouter</span>
                                    </button>
                                    <button type="button" class="btn btn-secondary float-right mr-2"
                                        id="cancelVolFormBtn">
                                        <i class="fas fa-times"></i> Annuler
                                    </button>
                                </div>
                            </div>
                        </form>

                        <!-- Tableau des vols existants -->
                        @if (isset($vols) && $vols->isNotEmpty())
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
                                                    <th>@lang('trans.itinerary') </th>
                                                    <th>@lang('trans.actions')</th>
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
                                                        $routeString .=
                                                            ' → ' . ($volItem->aeroportArrivee->codeICAO ?? 'N/A');
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
                                                                        {{ date('H:i', strtotime($escale->date_depart)) }}
                                                                        @if (!$loop->last)
                                                                            →
                                                                        @endif
                                                                    @endforeach
                                                                @else
                                                                    Aucune aéroport intermédiaire
                                                                @endif
                                                            </small>
                                                        </td>
                                                        <td>
                                                            <button class="btn btn-warning btn-sm edit-vol"
                                                                data-id="{{ $volItem->id }}"
                                                                data-numero_vol="{{ $volItem->numero_vol }}"
                                                                data-numero_piste_depart="{{ $volItem->numero_piste_depart }}"
                                                                data-numero_piste_arrivee="{{ $volItem->numero_piste_arrivee }}"
                                                                data-aeroport_depart_id="{{ $volItem->aeroport_depart_id }}"
                                                                data-aeroport_arrivee_id="{{ $volItem->aeroport_arrivee_id }}"
                                                                data-date_depart="{{ $volItem->date_depart }}"
                                                                data-date_arrivee="{{ $volItem->date_arrivee }}"
                                                                data-nbr_passagers="{{ $volItem->nbr_passagers }}"
                                                                data-objet="{{ $volItem->objet_vol }}"
                                                                data-escales="{{ json_encode($volItem->escales) }}">
                                                                <i class="fas fa-edit"></i> @lang('trans.edit')
                                                            </button>
                                                            <button class="btn btn-danger btn-sm delete-vol"
                                                                data-id="{{ $volItem->id }}">
                                                                <i class="fas fa-trash"></i> @lang('trans.delete')
                                                            </button>
                                                        </td>
                                                    </tr>
                                                @endforeach
                                            </tbody>
                                        </table>
                                    </div>
                                </div>
                            </div>
                        @else
                            <div class="alert alert-info" id="noVolsAlert">
                                Aucun vol enregistré.
                            </div>
                        @endif
                    </div>
                </div>
                <!-- Flight Crew Section -->
                @if (!in_array($demandeAutorisation->first_type_vol_id, [12, 13]))
                    <div class="card card-primary">
                        <div class="card-header bg-primary text-white">
                            <h3 class="card-title">@lang('trans.flight_crew')</h3>
                        </div>
                        <div class="card-body">
                            <form method="POST" id="crewForm" action="{{ url('/user/equipes') }}"
                                enctype="multipart/form-data">
                                @csrf
                                <input type="hidden" value="{{ $demandeAutorisation->id }}"
                                    id="demande_autorisation_id" name="demande_autorisation_id">
                                {{--
                                    <div class="row">
                                        <div class="col-md-3">
                                            <div class="form-group">
                                                <label for="nom"><span class="text-danger">*</span>@lang('trans.last_name')</label>
                                                <input type="text" class="form-control" id="nom" name="nom"
                                                    required>
                                            </div>
                                        </div>
                                        <div class="col-md-3">
                                            <div class="form-group">
                                                <label for="prenom"><span class="text-danger">*</span>@lang('trans.first_name')</label>
                                                <input type="text" class="form-control" id="prenom" name="prenom"
                                                    required>
                                            </div>
                                        </div>
                                        <div class="col-md-2">
                                            <div class="form-group">
                                                <label for="age"><span class="text-danger">*</span>@lang('trans.age')</label>
                                                <input type="number" min="18" class="form-control"
                                                    id="age" name="age" required>
                                            </div>
                                        </div>
                                        <div class="col-md-2">
                                            <div class="form-group">
                                                <label for="fonction"><span class="text-danger">*</span>@lang('trans.role')</label>
                                                <select class="form-control" id="fonction" name="fonction" required>
                                                    <option value="pilot">@lang('trans.pilot')</option>
                                                    <option value="copilot">@lang('trans.copilot')</option>
                                                    <option value="mechanic">@lang('trans.mechanic')</option>
                                                    <option value="steward">@lang('trans.steward')</option>
                                                    <option value="hostess">@lang('trans.hostess')</option>
                                                </select>
                                            </div>
                                        </div>
                                        <div class="col-md-2">
                                            <div class="form-group">
                                                <label for="email"><span class="text-danger">*</span>@lang('trans.email')</label>
                                                <input type="email" class="form-control" id="email" name="email"
                                                    required>
                                            </div>
                                        </div>

                                    </div>
                                --}}
                                <div class="row">
                                    <div class="col-md-3">
                                        <div class="form-group">
                                            <label for="fonction">@lang('trans.role')</label>
                                            <select class="form-control" id="fonction" name="fonction" required>
                                                <option value="pilot">@lang('trans.pilot')</option>
                                                <option value="copilot">@lang('trans.copilot')</option>
                                                <option value="mechanic">@lang('trans.mechanic')</option>
                                                <option value="steward">@lang('trans.steward')</option>
                                                <option value="hostess">@lang('trans.hostess')</option>
                                            </select>
                                        </div>
                                    </div>
                                    <div class="col-md-3">
                                        <div class="form-group">
                                            <label for="licence_numero">@lang('trans.license_number')</label>
                                            <input type="text" class="form-control" id="licence_numero"
                                                name="licence_numero">
                                        </div>
                                    </div>
                                    <div class="col-md-3">
                                        <div class="form-group">
                                            <label for="licence_expiration">@lang('trans.license_expiry')</label>
                                            <input type="date" class="form-control" id="licence_expiration"
                                                name="licence_expiration">
                                        </div>
                                    </div>
                                    <!--justificatif-->
                                    <div class="col-md-3">
                                        <div class="form-group">
                                            <label for="justificatif">@lang('trans.proof')</label>
                                            <input type="file" class="form-control" id="justificatif"
                                                name="justificatif">
                                        </div>
                                    </div>
                                </div>


                                <div class="row">
                                    <div class="col-lg-12">
                                        <button id="submitCrew" type="submit" class="btn btn-success float-right">
                                            <i class="fas fa-plus"></i> @lang('trans.add')
                                        </button>
                                    </div>
                                </div>
                            </form>

                            @if ($equipe_vols->isNotEmpty())
                                <div class="row mt-4">
                                    <div class="col-lg-12">
                                        <div class="table-responsive">
                                            <table class="table table-striped table-bordered" id="crewTable">
                                                <thead>
                                                    <tr>
                                                        {{-- <th>@lang('trans.name')</th> --}}
                                                        <th>@lang('trans.role')</th>
                                                        {{-- <th>@lang('trans.age')</th> --}}
                                                        {{-- <th>@lang('trans.email')</th> --}}
                                                        <th>@lang('trans.license')</th>
                                                        <th>@lang('trans.proof')</th>
                                                        <th>@lang('trans.actions')</th>
                                                    </tr>
                                                </thead>
                                                <tbody>
                                                    @foreach ($equipe_vols as $membre)
                                                        <tr id="membre-{{ $membre->id }}">
                                                            {{-- <td>{{ $membre->prenom }} {{ $membre->nom }}</td>
                                                        <td>{{ strtoupper($membre->fonction) }}</td>
                                                        <td>{{ $membre->age }}</td>
                                                        <td>{{ $membre->email }}</td> --}}
                                                            <td>{{ strtoupper($membre->fonction) }}</td>
                                                            <td>
                                                                @if ($membre->licence_numero)
                                                                    {{ $membre->licence_numero }}
                                                                    @if (!empty($membre->licence_expiration))
                                                                        ({{ date('d/m/Y', strtotime($membre->licence_expiration)) }})
                                                                    @endif
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
                                                            <td>
                                                                <button class="btn btn-warning btn-sm edit-membre"
                                                                    data-id="{{ $membre->id }}">@lang('trans.update')</button>
                                                                <button class="btn btn-danger btn-sm delete-membre"
                                                                    data-id="{{ $membre->id }}">@lang('trans.delete')</button>
                                                            </td>
                                                        </tr>
                                                        <tr id="edit-form-membre-{{ $membre->id }}"
                                                            style="display: none;">
                                                            <td colspan="6">
                                                                <form id="updateCrewForm-{{ $membre->id }}"
                                                                    method="POST" enctype="multipart/form-data">
                                                                    @method('PUT')
                                                                    @csrf

                                                                    <input type="hidden" name="membre_id" id="membre_id"
                                                                        value="{{ $membre->id }}">

                                                                    <div class="row">
                                                                        <div class="col-md-2">
                                                                            <div class="form-group">
                                                                                <label>@lang('trans.role')</label>
                                                                                <select class="form-control"
                                                                                    name="fonction" required>
                                                                                    @foreach (['pilot', 'copilot', 'mechanic', 'steward', 'hostess'] as $role)
                                                                                        <option
                                                                                            value="{{ $role }}"
                                                                                            {{ $membre->fonction == $role ? 'selected' : '' }}>
                                                                                            @lang('trans.' . $role)
                                                                                        </option>
                                                                                    @endforeach
                                                                                </select>
                                                                            </div>
                                                                        </div>
                                                                    </div>

                                                                    <div class="row">
                                                                        <div class="col-md-4">
                                                                            <div class="form-group">
                                                                                <label>@lang('trans.license_number')</label>
                                                                                <input type="text" class="form-control"
                                                                                    name="licence_numero"
                                                                                    value="{{ $membre->licence_numero }}">
                                                                            </div>
                                                                        </div>
                                                                        <div class="col-md-4">
                                                                            <div class="form-group">
                                                                                <label>@lang('trans.license_expiry')</label>
                                                                                <input type="date" class="form-control"
                                                                                    name="licence_expiration"
                                                                                    value="{{ $membre->licence_expiration ? $membre->licence_expiration->format('Y-m-d') : '' }}">
                                                                            </div>
                                                                        </div>
                                                                        <div class="col-md-4">
                                                                            <div class="form-group">
                                                                                <label
                                                                                    for="justificatif">@lang('trans.proof')</label>
                                                                                <input type="file" class="form-control"
                                                                                    id="justificatif" name="justificatif">
                                                                            </div>
                                                                        </div>
                                                                    </div>

                                                                    <button type="submit"
                                                                        class="btn btn-primary btn-sm update-membre">
                                                                        @lang('trans.update')
                                                                    </button>
                                                                    <button type="button"
                                                                        class="btn btn-secondary btn-sm cancel-edit"
                                                                        data-id="{{ $membre->id }}" data-type="membre">
                                                                        @lang('trans.cancel')
                                                                    </button>
                                                                </form>
                                                            </td>
                                                        </tr>
                                                    @endforeach
                                                </tbody>
                                            </table>
                                        </div>
                                    </div>
                                </div>
                            @endif
                        </div>
                    </div>
                @endif
                @if (in_array($demandeAutorisation->type->id, [5, 6, 7]))
                    <div class="card card-info mt-4">
                        <div class="card-header bg-primary text-white">
                            <h3 class="card-title">@lang('trans.mdn_management')</h3>
                        </div>
                        <div class="card-body">
                            {{-- Add MDN Form --}}
                            <form method="POST" id="mdnForm" action="{{ url('/user/mdns') }}"
                                enctype="multipart/form-data">
                                @csrf
                                <input type="hidden" value="{{ $demandeAutorisation->id }}"
                                    id="demande_autorisation_id" name="demande_autorisation_id">

                                <div class="row">
                                    <div class="col-md-4">
                                        <div class="form-group">
                                            <label for="date_autorisation">@lang('trans.authorization_date') <span
                                                    class="text-danger">*</span></label>
                                            <input type="date" class="form-control" id="date_autorisation"
                                                name="date_autorisation" required>
                                        </div>
                                    </div>
                                    <div class="col-md-4">
                                        <div class="form-group">
                                            <label for="numero_mdn">@lang('trans.mdn_number') <span
                                                    class="text-danger">*</span></label>
                                            <input type="text" class="form-control" id="numero_mdn" name="numero_mdn"
                                                placeholder="" required>
                                        </div>
                                    </div>
                                    <div class="col-md-4">
                                        <div class="form-group">
                                            <label for="nationalite">@lang('trans.nationality') <span
                                                    class="text-danger">*</span></label>
                                            <select class="form-control" id="nationalite" name="pays_id" required>
                                                @foreach ($pays as $pay)
                                                    <option value="{{ $pay->id }}">{{ $pay->nom }}
                                                        ({{ $pay->code }})
                                                    </option>
                                                @endforeach
                                            </select>
                                        </div>
                                    </div>
                                </div>

                                <div class="row">
                                    <div class="col-lg-12">
                                        <button id="submitMdn" type="submit" class="btn btn-success float-right">
                                            <i class="fas fa-plus"></i> @lang('trans.add')
                                        </button>
                                    </div>
                                </div>
                            </form>

                            {{-- MDN List --}}
                            @if (isset($mdns) && $mdns->isNotEmpty())
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
                                                                <button class="btn btn-warning btn-sm edit-mdn"
                                                                    data-id="{{ $mdn->id }}">
                                                                    <i class="fas fa-edit"></i> @lang('trans.update')
                                                                </button>
                                                                <button class="btn btn-danger btn-sm delete-mdn"
                                                                    data-id="{{ $mdn->id }}">
                                                                    <i class="fas fa-trash"></i> @lang('trans.delete')
                                                                </button>
                                                            </td>
                                                        </tr>
                                                        <tr id="edit-form-mdn-{{ $mdn->id }}"
                                                            style="display: none;">
                                                            <td colspan="4">
                                                                <form id="updateMdnForm-{{ $mdn->id }}"
                                                                    method="POST" enctype="multipart/form-data">
                                                                    @method('PUT')
                                                                    @csrf

                                                                    <input type="hidden" name="mdn_id"
                                                                        value="{{ $mdn->id }}">

                                                                    <div class="row">
                                                                        <div class="col-md-4">
                                                                            <div class="form-group">
                                                                                <label>@lang('trans.authorization_date')</label>
                                                                                <input type="date" class="form-control"
                                                                                    name="date_autorisation"
                                                                                    value="{{ $mdn->date_autorisation ? $mdn->date_autorisation->format('Y-m-d') : '' }}"
                                                                                    required>
                                                                            </div>
                                                                        </div>
                                                                        <div class="col-md-4">
                                                                            <div class="form-group">
                                                                                <label>@lang('trans.mdn_number')</label>
                                                                                <input type="text" class="form-control"
                                                                                    name="numero_mdn"
                                                                                    value="{{ $mdn->numero_mdn }}"
                                                                                    required>
                                                                            </div>
                                                                        </div>
                                                                        <div class="col-md-4">
                                                                            <div class="form-group">
                                                                                <label>@lang('trans.nationality')</label>
                                                                                <select class="form-control"
                                                                                    id="nationalite" name="pays_id"
                                                                                    required>
                                                                                    @foreach ($pays as $pay)
                                                                                        <option
                                                                                            value="{{ $pay->id }}">
                                                                                            {{ $pay->nom }}
                                                                                            ({{ $pay->code }})
                                                                                        </option>
                                                                                    @endforeach
                                                                                </select>
                                                                            </div>
                                                                        </div>
                                                                    </div>

                                                                    <button type="submit"
                                                                        class="btn btn-primary btn-sm update-mdn">
                                                                        <i class="fas fa-save"></i> @lang('trans.update')
                                                                    </button>
                                                                    <button type="button"
                                                                        class="btn btn-secondary btn-sm cancel-edit-mdn"
                                                                        data-id="{{ $mdn->id }}">
                                                                        <i class="fas fa-times"></i> @lang('trans.cancel')
                                                                    </button>
                                                                </form>
                                                            </td>
                                                        </tr>
                                                    @endforeach
                                                </tbody>
                                            </table>
                                        </div>
                                    </div>
                                </div>
                            @endif
                        </div>
                    </div>
                @endif
                @if ($demandeAutorisation->first_type_vol_id == 1)
                    <!-- Freight Section -->
                    <div class="card card-primary">
                        <div class="card-header bg-primary text-white">
                            <h3 class="card-title">@lang('trans.freight')</h3>
                        </div>
                        <div class="card-body">
                            <form method="POST" id="fretForm" action="{{ url('/user/frets') }}">
                                @csrf
                                <input type="hidden" value="{{ $demandeAutorisation->id }}"
                                    id="demande_autorisation_id" name="demande_autorisation_id">

                                <div class="row">
                                    <div class="col-md-6">
                                        <div class="form-group">
                                            <label for="nature">@lang('trans.nature')<span
                                                    class="text-danger">*</span></label>
                                            <select class="form-control" id="nature" name="nature" required>
                                                <option value="normal">@lang('trans.normal')</option>
                                                <option value="dangerous">@lang('trans.dangerous')</option>
                                                <option value="perishable">@lang('trans.perishable')</option>
                                                <option value="living">@lang('trans.living')</option>
                                            </select>
                                        </div>
                                    </div>
                                    <div class="col-md-6">
                                        <div class="form-group">
                                            <label for="poids">@lang('trans.weight_kg')
                                                <span class="text-danger">*</span>
                                            </label>
                                            <input type="number" step="0.01" min="0" class="form-control"
                                                id="poids" name="poids" required>
                                        </div>
                                    </div>
                                </div>

                                <div class="row">
                                    <div class="col-md-12">
                                        <div class="form-group">
                                            <label for="instructions_speciales">@lang('trans.special_instructions')</label>
                                            <textarea class="form-control" id="instructions_speciales" name="instructions_speciales" rows="2"></textarea>
                                        </div>
                                    </div>
                                </div>

                                <div class="row">
                                    <div class="col-lg-12">
                                        <button id="submitFret" type="submit" class="btn btn-success float-right">
                                            <i class="fas fa-plus"></i> @lang('trans.add')
                                        </button>
                                    </div>
                                </div>
                            </form>

                            @if ($fretVols->isNotEmpty())
                                <div class="row mt-4">
                                    <div class="col-lg-12">
                                        <div class="table-responsive">
                                            <table class="table table-striped table-bordered" id="fretTable">
                                                <thead>
                                                    <tr>
                                                        <th>@lang('trans.nature')</th>
                                                        <th>@lang('trans.weight_kg')</th>
                                                        <th>@lang('trans.description')</th>
                                                        <th>@lang('trans.actions')</th>
                                                    </tr>
                                                </thead>
                                                <tbody>
                                                    @foreach ($fretVols as $fret)
                                                        <tr id="fret-{{ $fret->id }}">
                                                            <td>{{ strtoupper($fret->nature) }}</td>
                                                            <td>{{ $fret->poids }} kg</td>
                                                            <td>{{ $fret->instructions_speciales }}</td>
                                                            <td>
                                                                <button class="btn btn-warning btn-sm edit-fret"
                                                                    data-id="{{ $fret->id }}">@lang('trans.update')</button>
                                                                <button class="btn btn-danger btn-sm delete-fret"
                                                                    data-id="{{ $fret->id }}">@lang('trans.destroy')</button>
                                                            </td>
                                                        </tr>
                                                        <tr id="edit-form-fret-{{ $fret->id }}"
                                                            style="display: none;">
                                                            <td colspan="6">
                                                                <form id="updateFretForm-{{ $fret->id }}">
                                                                    @csrf
                                                                    @method('PUT')
                                                                    <input type="hidden" name="fret_id"
                                                                        value="{{ $fret->id }}">
                                                                    <div class="row">
                                                                        <div class="col-md-6">
                                                                            <div class="form-group">
                                                                                <label>@lang('trans.nature')</label>
                                                                                <select class="form-control"
                                                                                    name="nature" required>
                                                                                    <option value="normal"
                                                                                        {{ $fret->nature == 'normal' ? 'selected' : '' }}>
                                                                                        @lang('trans.normal')</option>
                                                                                    <option value="dangerous"
                                                                                        {{ $fret->nature == 'dangerous' ? 'selected' : '' }}>
                                                                                        @lang('trans.dangerous')</option>
                                                                                    <option value="perishable"
                                                                                        {{ $fret->nature == 'perishable' ? 'selected' : '' }}>
                                                                                        @lang('trans.perishable')</option>
                                                                                    <option value="living"
                                                                                        {{ $fret->nature == 'living' ? 'selected' : '' }}>
                                                                                        @lang('trans.living')</option>
                                                                                </select>
                                                                            </div>
                                                                        </div>
                                                                        <div class="col-md-6">
                                                                            <div class="form-group">
                                                                                <label>@lang('trans.weight_kg')</label>
                                                                                <input type="number" step="0.01"
                                                                                    class="form-control" name="poids"
                                                                                    value="{{ $fret->poids }}" required>
                                                                            </div>
                                                                        </div>

                                                                    </div>

                                                                    <div class="row">
                                                                        <div class="col-md-12">
                                                                            <div class="form-group">
                                                                                <label>@lang('trans.special_instructions')</label>
                                                                                <textarea class="form-control" name="instructions_speciales" rows="2">{{ $fret->instructions_speciales }}</textarea>
                                                                            </div>
                                                                        </div>
                                                                    </div>
                                                                    <button type="submit"
                                                                        class="btn btn-primary btn-sm update-fret"
                                                                        data-id="{{ $fret->id }}">@lang('trans.update')</button>
                                                                    <button type="button"
                                                                        class="btn btn-secondary btn-sm cancel-edit"
                                                                        data-id="{{ $fret->id }}"
                                                                        data-type="fret">@lang('trans.cancel')</button>
                                                                </form>
                                                            </td>
                                                        </tr>
                                                    @endforeach
                                                </tbody>
                                            </table>
                                        </div>
                                    </div>
                                </div>
                            @endif
                        </div>
                    </div>
                @endif
                @if ($demandeAutorisation->type->id === 2)
                    <!-- Receiving Party Section -->
                    <div class="card card-primary">
                        <div class="card-header bg-primary text-white">
                            <h3 class="card-title">Renseignements sur le Receiving-party</h3>
                        </div>
                        <div class="card-body">
                            <form method="POST" id="receivingPartyForm" action="{{ url('/user/receiving-parties/') }}"
                                enctype="multipart/form-data">
                                @csrf
                                <input type="hidden" value="{{ $demandeAutorisation->id }}"
                                    id="demande_autorisation_id" name="demande_autorisation_id">

                                <div class="row">
                                    <div class="col-md-6">
                                        <div class="form-group">
                                            <label for="nomcontact"><i class="fa fa-user mr-2"></i>Nom et Prénoms*</label>
                                            <input id="nomcontact" name="nom_contact" class="form-control" required>
                                        </div>
                                    </div>
                                    <div class="col-md-6">
                                        <div class="form-group">
                                            <label for="telephonecontact"><i
                                                    class="fa fa-phone mr-2"></i>Téléphone/WhatsApp*</label>
                                            <input id="telephonecontact" name="telephone_contact" class="form-control"
                                                required>
                                        </div>
                                    </div>
                                </div>

                                <div class="row">
                                    <div class="col-md-6">
                                        <div class="form-group">
                                            <label for="emailcontact"><i class="fa fa-envelope mr-2"></i>Email</label>
                                            <input id="emailcontact" name="email_contact" type="email"
                                                class="form-control">
                                        </div>
                                    </div>
                                    <div class="col-md-6">
                                        <div class="form-group">
                                            <label for="fonctioncontact"><i
                                                    class="fa fa-certificate mr-2"></i>Fonction</label>
                                            <input id="fonctioncontact" name="fonction_contact" class="form-control">
                                        </div>
                                    </div>
                                </div>

                                <div class="row">
                                    <div class="col-md-12">
                                        <div class="form-group">
                                            <label for="autrerenseignement">Autres renseignements</label>
                                            <textarea class="form-control" id="autrerenseignement" name="autres_renseignements" rows="2"></textarea>
                                        </div>
                                    </div>
                                </div>

                                <div class="row">
                                    <div class="col-md-12">
                                        <div class="form-group">
                                            <label for="pieceidentite"><i class="fa fa-credit-card mr-2"></i>Pièce
                                                d'identité</label>
                                            <input id="pieceidentite" name="piece_identite" type="file"
                                                class="form-control-file">
                                        </div>
                                    </div>
                                </div>

                                <div class="row">
                                    <div class="col-lg-12">
                                        <button type="submit" class="btn btn-success float-right">
                                            <i class="fas fa-plus"></i> Ajouter
                                        </button>
                                    </div>
                                </div>
                            </form>

                            @if ($receivingParties->isNotEmpty())
                                <div class="row mt-4">
                                    <div class="col-lg-12">
                                        <div class="table-responsive">
                                            <table class="table table-striped table-bordered" id="partyTable">
                                                <thead>
                                                    <tr>
                                                        <th>Contact</th>
                                                        <th>Téléphone</th>
                                                        <th>Email</th>
                                                        <th>Fonction</th>
                                                        <th>Pièce d'identité</th>
                                                        <th>Actions</th>
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
                                                                @if ($party->piece_identite_path)
                                                                    <a href="{{ asset('/uploads/' . $party->piece_identite_path) }}"
                                                                        target="_blank" class="btn btn-sm btn-primary">
                                                                        <i class="fas fa-eye"></i>
                                                                    </a>
                                                                @else
                                                                    N/A
                                                                @endif
                                                            </td>
                                                            <td>
                                                                <button class="btn btn-warning btn-sm edit-party"
                                                                    data-id="{{ $party->id }}">
                                                                    <i class="fas fa-edit"></i>
                                                                </button>
                                                                <button class="btn btn-danger btn-sm delete-party"
                                                                    data-id="{{ $party->id }}">
                                                                    <i class="fas fa-trash"></i>
                                                                </button>
                                                            </td>
                                                        </tr>
                                                        <tr id="edit-form-party-{{ $party->id }}"
                                                            style="display: none;">
                                                            <td colspan="6">
                                                                <form id="updatePartyForm-{{ $party->id }}"
                                                                    enctype="multipart/form-data">
                                                                    @csrf

                                                                    <input type="hidden" name="party_id"
                                                                        value="{{ $party->id }}">
                                                                    <div class="row">
                                                                        <div class="col-md-6">
                                                                            <div class="form-group">
                                                                                <label>Nom et Prénoms*</label>
                                                                                <input type="text" class="form-control"
                                                                                    name="nom_contact"
                                                                                    value="{{ $party->nom_contact }}"
                                                                                    required>
                                                                            </div>
                                                                        </div>
                                                                        <div class="col-md-6">
                                                                            <div class="form-group">
                                                                                <label>Téléphone*</label>
                                                                                <input type="text" class="form-control"
                                                                                    name="telephone_contact"
                                                                                    value="{{ $party->telephone_contact }}"
                                                                                    required>
                                                                            </div>
                                                                        </div>
                                                                    </div>
                                                                    <div class="row">
                                                                        <div class="col-md-6">
                                                                            <div class="form-group">
                                                                                <label>Email</label>
                                                                                <input type="email" class="form-control"
                                                                                    name="email_contact"
                                                                                    value="{{ $party->email_contact }}">
                                                                            </div>
                                                                        </div>
                                                                        <div class="col-md-6">
                                                                            <div class="form-group">
                                                                                <label>Fonction</label>
                                                                                <input type="text" class="form-control"
                                                                                    name="fonction_contact"
                                                                                    value="{{ $party->fonction_contact }}">
                                                                            </div>
                                                                        </div>
                                                                    </div>
                                                                    <div class="row">
                                                                        <div class="col-md-12">
                                                                            <div class="form-group">
                                                                                <label>Autres renseignements</label>
                                                                                <textarea class="form-control" name="autres_renseignements" rows="2">{{ $party->autres_renseignements }}</textarea>
                                                                            </div>
                                                                        </div>
                                                                    </div>
                                                                    <div class="row">
                                                                        <div class="col-md-12">
                                                                            <div class="form-group">
                                                                                <label>Pièce d'identité (Laisser vide pour
                                                                                    ne
                                                                                    pas
                                                                                    changer)</label>
                                                                                <input type="file"
                                                                                    class="form-control-file"
                                                                                    name="piece_identite">
                                                                            </div>
                                                                        </div>
                                                                    </div>
                                                                    <button type="submit"
                                                                        class="btn btn-primary btn-sm update-party"
                                                                        data-id="{{ $party->id }}">Mettre à
                                                                        jour</button>
                                                                    <button type="button"
                                                                        class="btn btn-secondary btn-sm cancel-edit"
                                                                        data-id="{{ $party->id }}"
                                                                        data-type="party">Annuler</button>
                                                                </form>
                                                            </td>
                                                        </tr>
                                                    @endforeach
                                                </tbody>
                                            </table>
                                        </div>
                                    </div>
                                </div>
                            @endif
                        </div>
                    </div>
                @endif
                @if ($demandeAutorisation->type->id === 4)
                    <div class="card card-primary">
                        <div class="card-header bg-primary text-white">
                            <h3 class="card-title">@lang('trans.deceased_persons')</h3>
                        </div>
                        <div class="card-body">
                            <form method="POST" id="deceasedPersonForm" action="{{ url('/user/personnes-deces') }}"
                                enctype="multipart/form-data">
                                @csrf
                                <input type="hidden" value="{{ $demandeAutorisation->id }}"
                                    id="demande_autorisation_id" name="demande_autorisation_id">

                                <div class="row">
                                    <div class="col-md-4">
                                        <div class="form-group">
                                            <label for="nom_prenom"><span
                                                    class="text-danger">*</span>@lang('trans.full_name')</label>
                                            <input type="text" class="form-control" id="nom_prenom" name="nom_prenom"
                                                required>
                                        </div>
                                    </div>
                                    <div class="col-md-4">
                                        <div class="form-group">
                                            <label for="numero_passport">@lang('trans.passport_number')</label>
                                            <input type="text" class="form-control" id="numero_passport"
                                                name="numero_passport">
                                        </div>
                                    </div>
                                    <div class="col-md-4">
                                        <div class="form-group">
                                            <label for="justificatif">@lang('trans.proof')</label>
                                            <input type="file" class="form-control" id="justificatif"
                                                name="justificatif">
                                        </div>
                                    </div>
                                </div>

                                <div class="row">
                                    <div class="col-lg-12">
                                        <button id="submitDeceasedPerson" type="submit"
                                            class="btn btn-success float-right">
                                            <i class="fas fa-plus"></i> @lang('trans.add')
                                        </button>
                                    </div>
                                </div>
                            </form>

                            @if ($personnesDeces->isNotEmpty())
                                <div class="row mt-4">
                                    <div class="col-lg-12">
                                        <div class="table-responsive">
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
                                                                <button class="btn btn-warning btn-sm edit-personne"
                                                                    data-id="{{ $personne->id }}">@lang('trans.update')</button>
                                                                <button class="btn btn-danger btn-sm delete-personne"
                                                                    data-id="{{ $personne->id }}">@lang('trans.delete')</button>
                                                            </td>
                                                        </tr>
                                                        <tr id="edit-form-personne-{{ $personne->id }}"
                                                            style="display: none;">
                                                            <td colspan="4">
                                                                <form id="updateDeceasedPersonForm-{{ $personne->id }}"
                                                                    method="POST" enctype="multipart/form-data">
                                                                    @method('PUT')
                                                                    @csrf

                                                                    <input type="hidden" name="personne_id"
                                                                        id="personne_id" value="{{ $personne->id }}">

                                                                    <div class="row">
                                                                        <div class="col-md-4">
                                                                            <div class="form-group">
                                                                                <label>@lang('trans.full_name')</label>
                                                                                <input type="text" class="form-control"
                                                                                    name="nom_prenom"
                                                                                    value="{{ $personne->nom_prenom }}"
                                                                                    required>
                                                                            </div>
                                                                        </div>
                                                                        <div class="col-md-4">
                                                                            <div class="form-group">
                                                                                <label>@lang('trans.passport_number')</label>
                                                                                <input type="text" class="form-control"
                                                                                    name="numero_passport"
                                                                                    value="{{ $personne->numero_passport }}">
                                                                            </div>
                                                                        </div>
                                                                        <div class="col-md-4">
                                                                            <div class="form-group">
                                                                                <label
                                                                                    for="justificatif">@lang('trans.proof')</label>
                                                                                <input type="file" class="form-control"
                                                                                    id="justificatif" name="justificatif">
                                                                                @if ($personne->justificatif)
                                                                                    <small class="form-text text-muted">
                                                                                        @lang('trans.current_file'):
                                                                                        {{ $personne->justificatif }}
                                                                                    </small>
                                                                                @endif
                                                                            </div>
                                                                        </div>
                                                                    </div>

                                                                    <button type="submit"
                                                                        class="btn btn-primary btn-sm update-personne">
                                                                        @lang('trans.update')
                                                                    </button>
                                                                    <button type="button"
                                                                        class="btn btn-secondary btn-sm cancel-edit"
                                                                        data-id="{{ $personne->id }}"
                                                                        data-type="personne">
                                                                        @lang('trans.cancel')
                                                                    </button>
                                                                </form>
                                                            </td>
                                                        </tr>
                                                    @endforeach
                                                </tbody>
                                            </table>
                                        </div>
                                    </div>
                                </div>
                            @endif
                        </div>
                    </div>
                @endif
                <!-- Assistance Section -->
                {{-- <div class="card card-primary">
                    <div class="card-header bg-primary text-white">
                        <h3 class="card-title">Assistance escale et PEA</h3>
                    </div>
                    <div class="card-body">
                        <form method="POST" id="assistanceForm" action="{{ url('/user/assistance') }}"
                            enctype="multipart/form-data">
                            @csrf
                            <input type="hidden" value="{{ $demandeAutorisation->id }}" id="demande_autorisation_id"
                                name="demande_autorisation_id">

                            <div class="row">
                                <div class="col-md-6">
                                    <div class="form-group">
                                        <label for="structure_assistance">Structure d'assistance en escale</label>
                                        <input id="structure_assistance" name="structure_assistance"
                                            value="{{ $assistance->structure_assistance ?? old('structure_assistance') }}"
                                            class="form-control">
                                    </div>
                                </div>
                                <div class="col-md-6">
                                    <div class="form-group">
                                        <label for="etat_pea">Etat de délivrance PEA</label>
                                        <input id="etat_pea" name="etat_pea"
                                            value="{{ $assistance->etat_pea ?? old('etat_pea') }}" class="form-control">
                                    </div>
                                </div>
                            </div>

                            <div class="row">
                                <div class="col-md-12">
                                    <div class="form-group">
                                        <label for="renseignements_divers">Renseignements divers</label>
                                        <textarea class="form-control" id="renseignements_divers" name="renseignements_divers" rows="3">{{ $assistance->renseignements_divers ?? old('renseignements_divers') }}</textarea>
                                    </div>
                                </div>
                            </div>

                            <div class="row">
                                <div class="col-md-12">
                                    <button type="submit" class="btn btn-success float-right">
                                        <i class="fas fa-save"></i> Enregistrer
                                    </button>
                                </div>
                            </div>
                        </form>
                        @if (isset($demandeAutorisation->assistance))
                            <div class="row mt-4">
                                <div class="col-lg-12">
                                    <div class="callout callout-info">
                                        <h5>Dernière mise à jour</h5>
                                        <p>
                                            <strong>Structure d'assistance:</strong>
                                            {{ $vol->assistance->structure_assistance ?? 'Non renseigné' }}<br>
                                            <strong>Etat PEA:</strong>
                                            {{ $vol->assistance->etat_pea ?? 'Non renseigné' }}<br>
                                            <strong>Informations:</strong>
                                            {{ $vol->assistance->renseignements_divers ?? 'Aucune information supplémentaire' }}
                                        </p>
                                    </div>
                                </div>
                            </div>
                        @endif
                    </div>
                </div> --}}
                <!-- Documents Section -->

                <div class="card card-primary">
                    <div class="card-header bg-primary text-white">
                        <h3 class="card-title">Documents</h3>
                    </div>
                    <div class="card-body">
                        <form method="POST" id="documentForm" action="{{ url('/user/documents') }}"
                            enctype="multipart/form-data">
                            @csrf
                            <input type="hidden" name="demande_autorisation_id"
                                value="{{ $demandeAutorisation->id }}">

                            <div class="row justify-content-center">
                                <div class="col-md-8">
                                    <div class="form-group">
                                        <ol>
                                            @foreach ($requiredDocs as $index => $requiredDoc)
                                                @php
                                                    $existingDoc = $demandeAutorisation->documents
                                                        ->where('type_document_id', $requiredDoc->id)
                                                        ->first();
                                                @endphp
                                                <li class="mb-3">
                                                    <input type="hidden" value="{{ $requiredDoc->id }}"
                                                        id="type_document_id_{{ $index }}"
                                                        name="type_document_id[]">

                                                    <strong>{{ LaravelLocalization::getCurrentLocale() == 'fr' ? $requiredDoc->nom_fr : $requiredDoc->nom_en }}</strong>

                                                    @if ($existingDoc)
                                                        <span class="badge badge-success ml-2">Document existant</span>
                                                    @endif

                                                    <div class="input-group">
                                                        <input type="file" class="form-control"
                                                            id="piece_{{ $index }}" name="pieces[]"
                                                            accept="application/pdf">

                                                        {{-- Input caché pour identifier les documents existants --}}
                                                        @if ($existingDoc)
                                                            <input type="hidden" name="existing_document_ids[]"
                                                                value="{{ $existingDoc->id }}">
                                                        @endif
                                                    </div>
                                                    <small class="form-text text-muted">
                                                        Formats acceptés: PDF uniquement (max 10MB)
                                                    </small>
                                                </li>
                                            @endforeach
                                        </ol>
                                    </div>
                                </div>
                            </div>

                            <div class="row">
                                <div class="col-md-12">
                                    <div id="uploadProgress" class="progress mt-3" style="display: none;">
                                        <div class="progress-bar progress-bar-striped progress-bar-animated"
                                            role="progressbar" style="width: 0%">0%</div>
                                    </div>

                                    <button id="uploadBtn" type="submit" class="btn btn-success mt-4 float-right">
                                        <i class="fas fa-upload"></i> <span id="uploadBtnText">Uploader</span>
                                    </button>
                                </div>
                            </div>
                        </form>

                        @if ($demandeAutorisation->hasDocuments())
                            <div class="row mt-4">
                                <div class="col-12">
                                    <div class="table-responsive">
                                        <table class="table table-striped" id="documentsTable">
                                            <thead>
                                                <tr>
                                                    <th>Type</th>
                                                    <th>Document</th>
                                                    <th>Statut</th>
                                                    <th>Dernière modification</th>
                                                    <th>Actions</th>
                                                </tr>
                                            </thead>
                                            <tbody>
                                                @foreach ($demandeAutorisation->documents as $document)
                                                    <tr id="document-{{ $document->id }}">
                                                        <td>
                                                            {{ LaravelLocalization::getCurrentLocale() == 'fr' ? optional($document->typeDocument)->nom_fr : optional($document->typeDocument)->nom_en }}
                                                        </td>
                                                        <td>
                                                            <a href="{{ asset('/uploads/documents/' . $document->url) }}"
                                                                target="_blank" class="btn btn-sm btn-primary">
                                                                <i class="fas fa-eye"></i> Voir
                                                            </a>
                                                        </td>
                                                        <td>
                                                            <span class="badge badge-success">Uploadé</span>
                                                        </td>
                                                        <td>
                                                            {{ $document->updated_at->format('d/m/Y H:i') }}
                                                        </td>
                                                        <td>
                                                            <div class="btn-group">
                                                                <button class="btn btn-sm btn-info replace-document"
                                                                    data-id="{{ $document->id }}"
                                                                    data-type="{{ optional($document->typeDocument)->nom_fr }}">
                                                                    <i class="fas fa-sync"></i> Remplacer
                                                                </button>
                                                                <button class="btn btn-sm btn-danger delete-document"
                                                                    data-id="{{ $document->id }}">
                                                                    <i class="fas fa-trash"></i>
                                                                </button>
                                                            </div>
                                                        </td>
                                                    </tr>
                                                @endforeach
                                            </tbody>
                                        </table>
                                    </div>
                                </div>
                            </div>
                        @else
                            <div class="alert alert-info mt-3">
                                <i class="fas fa-info-circle"></i> Aucun document n'a été uploadé pour cette demande.
                            </div>
                        @endif
                    </div>
                </div>

                <!-- Modal pour remplacer un document -->
                <div class="modal fade" id="replaceDocumentModal" tabindex="-1" role="dialog">
                    <div class="modal-dialog" role="document">
                        <div class="modal-content">
                            <div class="modal-header">
                                <h5 class="modal-title">Remplacer le document</h5>
                                <button type="button" class="close" data-dismiss="modal">
                                    <span>&times;</span>
                                </button>
                            </div>
                            <div class="modal-body">
                                <p>Type de document: <strong id="replace-doc-type"></strong></p>
                                <form id="replaceDocumentForm" enctype="multipart/form-data">
                                    @csrf
                                    @method('PUT')
                                    <div class="form-group">
                                        <label>Nouveau document (PDF uniquement)</label>
                                        <input type="file" class="form-control" id="replace_piece" name="piece"
                                            accept="application/pdf" required>
                                    </div>
                                </form>
                            </div>
                            <div class="modal-footer">
                                <button type="button" class="btn btn-secondary" data-dismiss="modal">Annuler</button>
                                <button type="button" class="btn btn-primary" id="confirmReplace">
                                    <i class="fas fa-sync"></i> Remplacer
                                </button>
                            </div>
                        </div>
                    </div>
                </div>

            </div>
        </div>
    </div>

    <!-- Add Aeroport Modal -->
    <div class="modal fade" id="addAeroportModal" tabindex="-1" role="dialog" aria-labelledby="addAeroportModalLabel"
        aria-hidden="true">
        <div class="modal-dialog modal-lg" role="document">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="addAeroportModalLabel">Add New Aeroport</h5>
                    <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                        <span aria-hidden="true">&times;</span>
                    </button>
                </div>
                <form id="addAeroportForm">
                    @csrf
                    <div class="modal-body">
                        <div class="row">
                            <div class="col-md-6">
                                <div class="form-group">
                                    <label for="nom">Nom Aeroport</label>
                                    <input type="text" class="form-control" id="nom" name="nom" required>
                                </div>
                            </div>
                            <div class="col-md-3">
                                <div class="form-group">
                                    <label for="codeIATA">IATA Code</label>
                                    <input type="text" class="form-control" id="codeIATA" name="codeIATA"
                                        maxlength="3">
                                </div>
                            </div>
                            <div class="col-md-3">
                                <div class="form-group">
                                    <label for="codeICAO">ICAO Code</label>
                                    <input type="text" class="form-control" id="codeICAO" name="codeICAO"
                                        maxlength="4">
                                </div>
                            </div>
                        </div>

                        <div class="row">
                            <div class="col-md-6">
                                <div class="form-group">
                                    <label for="pays_id">Country</label>
                                    <select class="form-control" id="pays_id" name="pays_id" required>
                                        <option value="">Select Country</option>
                                        @foreach ($pays as $pay)
                                            <option value="{{ $pay->id }}">{{ $pay->nom }}
                                                ({{ $pay->code }})
                                            </option>
                                        @endforeach
                                    </select>
                                </div>
                            </div>
                            <div class="col-md-6">
                                <div class="form-group">
                                    <label for="ville">City</label>
                                    <input type="text" class="form-control" id="ville" name="ville">
                                </div>
                            </div>
                        </div>

                        <div class="row">
                            <div class="col-md-6">
                                <div class="form-group">
                                    <label for="latitude">Latitude</label>
                                    <input type="number" step="any" class="form-control" id="latitude"
                                        name="latitude">
                                </div>
                            </div>
                            <div class="col-md-6">
                                <div class="form-group">
                                    <label for="longitude">Longitude</label>
                                    <input type="number" step="any" class="form-control" id="longitude"
                                        name="longitude">
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-secondary" data-dismiss="modal">Cancel</button>
                        <button type="submit" class="btn btn-primary">Save Aeroport</button>
                    </div>
                </form>
            </div>
        </div>
    </div>

    <!-- Modal pour ajouter une compagnie -->
    <div class="modal fade" id="companyModal" tabindex="-1" aria-hidden="true">
        <div class="modal-dialog">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title">Nouveau Opérateur</h5>
                    <button type="button" class="btn-close" data-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body">
                    <form id="companyForm">
                        @csrf
                        <div class="mb-3">
                            <label for="nom" class="form-label">Nom <span class="text-danger">*</span></label>
                            <input type="text" class="form-control" id="nom_entreprise" name="nom_entreprise"
                                required>
                        </div>
                        <div class="mb-3">
                            <label for="code" class="form-label">Code <span class="text-danger">*</span></label>
                            <input type="text" class="form-control" id="code" name="code" required>
                        </div>
                        <div class="form-group">
                            <label for="email">Email</label>
                            <input type="email" class="form-control" id="email" name="email">
                        </div>
                        <div class="form-group">
                            <label for="telephone">Téléphone</label>
                            <input type="text" class="form-control" id="telephone" name="telephone">
                        </div>
                        <div class="form-group">
                            <label for="adresse">Adresse</label>
                            <input type="text" class="form-control" id="adresse" name="adresse">
                        </div>
                    </form>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-dismiss="modal">Fermer</button>
                    <button type="button" class="btn btn-primary" id="saveCompanyBtn">Enregistrer</button>
                </div>
            </div>
        </div>
    </div>
    <!-- Modal pour ajouter un type d'avion -->
    <div class="modal fade" id="typeAvionModal" tabindex="-1" aria-hidden="true">
        <div class="modal-dialog">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title">Nouveau Type d'Avion</h5>
                    <button type="button" class="btn-close" data-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body">
                    <form id="typeAvionForm">
                        @csrf
                        <div class="mb-3">
                            <label for="code" class="form-label">Code <span class="text-danger">*</span></label>
                            <input type="text" class="form-control" id="code_type" name="code" required>
                        </div>
                        <div class="mb-3">
                            <label for="capacite" class="form-label">Capacité (passagers)</label>
                            <input type="number" class="form-control" id="capacite" name="capacite"
                                min="0" value="0">
                        </div>
                        <div class="mb-3">
                            <label for="charge_max" class="form-label">Charge maximale (kg)</label>
                            <input type="number" class="form-control" id="charge_max" name="charge_max"
                                min="0" value="0">
                        </div>
                    </form>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-dismiss="modal">Fermer</button>
                    <button type="button" class="btn btn-primary" id="saveTypeAvionBtn">Enregistrer</button>
                </div>
            </div>
        </div>
    </div>
@endsection

@push('script')
    <script src="https://cdnjs.cloudflare.com/ajax/libs/bootstrap-datepicker/1.9.0/js/bootstrap-datepicker.min.js"></script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/bootstrap-datepicker/1.9.0/locales/bootstrap-datepicker.fr.min.js">
    </script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/bootstrap-timepicker/0.5.2/js/bootstrap-timepicker.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/toastr.js/latest/toastr.min.js"></script>
    <!-- Select2 -->
    <script src="{{ asset('assets/admin/plugins/select2/js/select2.full.min.js') }}"></script>
    <!-- dropzonejs -->
    <!-- Page specific script -->
@endpush
@push('custom')
    <script>
        $(document).ready(function() {
            $('#addAeroportBtn').click(function() {
                $('#addAeroportModal').modal('show');
            });
            // Handle form submission
            $('#addAeroportForm').submit(function(e) {
                e.preventDefault();

                // Get form data
                var formData = $(this).serialize();

                // Show loading state
                var submitButton = $(this).find('button[type="submit"]');
                submitButton.prop('disabled', true).html(
                    '<i class="fas fa-spinner fa-spin"></i> Saving...');

                // AJAX request
                $.ajax({
                    url: '{{ route('user.store_aeroports') }}',
                    type: 'POST',
                    data: formData,
                    success: function(response) {
                        // Hide modal
                        $('#addAeroportModal').modal('hide');

                        // Show success message
                        toastr.success('Aeroport added successfully!');

                        // Reset form
                        $('#addAeroportForm')[0].reset();

                        // Refresh the table or add the new row
                        if (typeof refreshAeroportsTable === 'function') {
                            refreshAeroportsTable();
                        } else {
                            // Fallback: reload the page
                            window.location.reload();
                        }
                    },
                    error: function(xhr) {
                        // Show error message
                        var errorMessage = xhr.responseJSON.message || 'An error occurred';
                        toastr.error(errorMessage);

                        // Highlight error fields
                        if (xhr.responseJSON.errors) {
                            $.each(xhr.responseJSON.errors, function(key, value) {
                                $('#' + key).addClass('is-invalid');
                                $('#' + key).after('<div class="invalid-feedback">' +
                                    value[0] + '</div>');
                            });
                        }
                    },
                    complete: function() {
                        // Reset button state
                        submitButton.prop('disabled', false).html('Save Aeroport');
                    }
                });
            });

            // Clear validation errors when modal is hidden
            $('#addAeroportModal').on('hidden.bs.modal', function() {
                $('#addAeroportForm input, #addAeroportForm select').removeClass('is-invalid');
                $('.invalid-feedback').remove();
            });

            // Auto-uppercase for IATA and ICAO codes
            $('#codeIATA, #codeICAO').keyup(function() {
                $(this).val($(this).val().toUpperCase());
            });
        });

        $(document).ready(function() {
            // Dans votre script, modifiez la fonction updatePassengerField
            function updatePassengerField() {
                // Récupérer le type de vol depuis le sélecteur ou depuis les données PHP
                let typeVol = null;

                // Si nous sommes en édition, essayer de récupérer depuis les données PHP
                @php
                    $typeVolId = null;
                    if ($demandeAutorisation->type_demande_autorisation_id == 3) {
                        $firstTypeVol = $demandeAutorisation->type_vols_list->first();
                        $typeVolId = $firstTypeVol ? $firstTypeVol->id : null;
                    } else {
                        $typeVolId = $demandeAutorisation->typeVol->id ?? null;
                    }
                @endphp

                const initialTypeVol = {!! json_encode($typeVolId) !!};
                const passagerField = $('#nbr_passagers');
                const passagerFieldLabel = $('#nbr_passagers_label');


                // Si nous avons un champ type_vol_id dans le formulaire (pour l'édition)
                if ($('#type_vol_id').length && $('#type_vol_id').val()) {
                    typeVol = $('#type_vol_id').val();
                } else if (initialTypeVol) {
                    typeVol = initialTypeVol;
                }

                if (typeVol && [2, 8, 10, 9, 7].includes(parseInt(typeVol))) {
                    passagerField.show();
                    passagerFieldLabel.show();
                    passagerField.prop('required', true);
                } else {
                    passagerField.hide();
                    passagerFieldLabel.hide();
                    passagerField.prop('required', false);
                    passagerField.val('');
                }
            }

            updatePassengerField();
        });
    </script>
    <script>
        $(document).ready(function() {
            // Afficher le formulaire quand on clique sur "Ajouter un avion"
            $('#showAvionFormBtn').click(function() {
                $('#avionForm').show();
                $('#avionsTableContainer, #noAvionsAlert').hide();
                $('#showAvionFormBtn').hide();
                $('#avion_id').val(''); // Reset l'ID pour une nouvelle création
                $('#formActionText').text('Ajouter');
                $('#avionForm')[0].reset(); // Reset le formulaire
                $('.invalid-feedback').text(''); // Effacer les messages d'erreur
                $('.is-invalid').removeClass('is-invalid'); // Enlever les classes d'erreur
            });

            // Cacher le formulaire quand on clique sur Annuler
            $('#cancelAvionFormBtn').click(function() {
                $('#avionForm').hide();
                $('#avionsTableContainer, #noAvionsAlert').show();
                $('#showAvionFormBtn').show();
            });

            // Quand on clique sur Modifier pour un avion existant
            $(document).on('click', '.edit-avion', function() {
                const avionId = $(this).data('id');
                const immatriculation = $(this).data('immatriculation');
                // Remplir le formulaire avec les données de l'avion
                $('#avion_id').val(avionId);

                // Vider et ajouter l'immatriculation dans le select
                $('#immatriculations_select').empty().trigger('change');
                const newOption = new Option(immatriculation, immatriculation, true, true);
                $('#immatriculations_select').append(newOption).trigger('change');
                $('#type_avion_id').val($(this).data('type_avion_id')).trigger('change');
                $('#compagnie_aerienne_id').val($(this).data('compagnie_aerienne_id')).trigger('change');

                // Afficher le formulaire
                $('#avionForm').show();
                $('#avionsTableContainer, #noAvionsAlert').hide();
                $('#showAvionFormBtn').hide();
                $('#formActionText').text('Mettre à jour');

                // Scroller vers le formulaire
                $('html, body').animate({
                    scrollTop: $('#avionForm').offset().top
                }, 500);
            });
            // Suppression d'un avion
            $(document).on('click', '.delete-avion', function() {
                const avionId = $(this).data('id');

                Swal.fire({
                    title: 'Confirmer la suppression',
                    text: "Êtes-vous sûr de vouloir supprimer cet avion?",
                    icon: 'warning',
                    showCancelButton: true,
                    confirmButtonColor: '#3085d6',
                    cancelButtonColor: '#d33',
                    confirmButtonText: 'Oui, supprimer!'
                }).then((result) => {
                    if (result.isConfirmed) {
                        $.ajax({
                            url: '/user/avions/' + avionId,
                            type: 'DELETE',
                            data: {
                                _token: '{{ csrf_token() }}'
                            },
                            success: function(response) {
                                Swal.fire({
                                    icon: 'success',
                                    title: 'Succès',
                                    text: response.message,
                                }).then(() => {
                                    location.reload();
                                });
                            },
                            error: function(xhr) {
                                Swal.fire({
                                    icon: 'error',
                                    title: 'Erreur',
                                    text: xhr.responseJSON.message ||
                                        'Une erreur est survenue'
                                });
                            }
                        });
                    }
                });
            });
            // Après soumission réussie du formulaire
            $('#avionForm').submit(function(e) {
                e.preventDefault();

                const formData = $(this).serialize();
                const url = $('#avion_id').val() ? '/user/avions/' + $('#avion_id').val() :
                    '/user/avions';
                const method = $('#avion_id').val() ? 'PUT' : 'POST';

                $.ajax({
                    url: url,
                    type: method,
                    data: formData,
                    success: function(response) {
                        // Cacher le formulaire et recharger la liste
                        $('#avionForm').hide();
                        $('#showAvionFormBtn').show();
                        location.reload();
                    },
                    error: function(xhr) {
                        if (xhr.status === 422) {
                            const errors = xhr.responseJSON.errors;
                            $.each(errors, function(key, value) {
                                $(`#${key}`).addClass('is-invalid');
                                $(`#${key}_error`).text(value[0]);
                            });
                        } else {
                            Swal.fire({
                                icon: 'error',
                                title: 'Erreur',
                                text: xhr.responseJSON.message ||
                                    'Une erreur est survenue'
                            });
                        }
                    }
                });
            });
        });
    </script>
    <script>
        // Déclarer les aéroports en JavaScript
        const aeroports = @json(
            $aeroports->map(function ($aeroport) {
                return [
                    'id' => $aeroport->id,
                    'codeICAO' => $aeroport->codeICAO,
                    'nom' => $aeroport->nom,
                ];
            }));
        $(document).ready(function() {

            // Variables globales
            let escaleCounter = 0;
            let currentEscalesData = []; // Pour garder une trace des escales existantes

            // Fonction pour formater la time 
            function formatTime(timeString) {
                return timeString ? timeString.substring(0, 5) : '';
            }

            // Template pour une escale
            function getEscaleTemplate(counter, escaleData = null) {
                const isEditMode = escaleData !== null;
                const aeroportId = isEditMode ? escaleData.aeroport_id : '';
                const dateArrivee = isEditMode ? formatTime(escaleData.date_arrivee) : '';
                const dateDepart = isEditMode ? formatTime(escaleData.date_depart) : '';
                const escaleId = isEditMode ? escaleData.id : '';

                // Construire les options des aéroports
                let options = '<option value="">Sélectionnez...</option>';
                if (typeof aeroports !== 'undefined') {
                    aeroports.forEach(aeroport => {
                        const selected = isEditMode && aeroportId == aeroport.id ? 'selected' : '';
                        options +=
                            `<option value="${aeroport.id}" ${selected}>${aeroport.codeICAO}</option>`;
                    });
                }

                return `
            <div class="row escale-row" id="escale-row-${counter}" data-escale-id="${escaleId}">
                <div class="col-md-4">
                    <div class="form-group">
                        <label for="escale_aeroport_${counter}">Aéroport d'escale</label>
                        <select class="form-control select2 escale-aeroport" 
                                id="escale_aeroport_${counter}" 
                                name="escales[${counter}][aeroport_id]"
                                data-counter="${counter}" required>
                            ${options}
                        </select>
                    </div>
                </div>
                <div class="col-md-3">
                    <div class="form-group">
                        <label for="escale_arrivee_${counter}">Heure arrivée</label>
                        <input type="time" class="form-control escale-arrivee" 
                               id="escale_arrivee_${counter}" 
                               name="escales[${counter}][date_arrivee]"
                               value="${dateArrivee}" required>
                    </div>
                </div>
                <div class="col-md-3">
                    <div class="form-group">
                        <label for="escale_depart_${counter}">Heure départ</label>
                        <input type="time" class="form-control escale-depart" 
                               id="escale_depart_${counter}" 
                               name="escales[${counter}][date_depart]"
                               value="${dateDepart}" required>
                    </div>
                </div>
                <div class="col-md-2">
                    <div class="form-group">
                        <label>&nbsp;</label>
                        <button type="button" class="btn btn-danger btn-block remove-escale" 
                                data-counter="${counter}">
                            <i class="fas fa-trash"></i>
                        </button>
                    </div>
                </div>
                ${isEditMode && escaleId ? `<input type="hidden" name="escales[${counter}][id]" value="${escaleId}">` : ''}
            </div>
            <hr>
        `;
            }

            // Ajouter une escale
            $('#addEscaleBtn').click(function() {
                $('#noEscalesAlert').hide();
                escaleCounter++;
                const template = getEscaleTemplate(escaleCounter);
                $('#escalesContainer').append(template);

                // Initialiser Select2 pour le nouvel élément
                setTimeout(() => {
                    $(`#escale_aeroport_${escaleCounter}`).select2({
                        theme: 'bootstrap4',
                        placeholder: "Sélectionnez un aéroport",
                        allowClear: true
                    });
                }, 50);
            });

            // Supprimer une escale
            $(document).on('click', '.remove-escale', function() {
                const counter = $(this).data('counter');
                $(`#escale-row-${counter}`).next('hr').remove();
                $(`#escale-row-${counter}`).remove();

                // Si plus d'escales, afficher le message
                if ($('.escale-row').length === 0) {
                    $('#noEscalesAlert').show();
                }
            });

            // Fonction pour charger les escales lors de l'édition
            function loadEscalesForEdit(escalesData) {
                $('#escalesContainer').empty();
                escaleCounter = 0;
                currentEscalesData = escalesData || [];

                if (currentEscalesData.length > 0) {
                    $('#noEscalesAlert').hide();
                    currentEscalesData.forEach((escale, index) => {
                        escaleCounter = index + 1;
                        const template = getEscaleTemplate(escaleCounter, {
                            id: escale.id,
                            aeroport_id: escale.aeroport_id,
                            date_arrivee: escale.date_arrivee,
                            date_depart: escale.date_depart
                        });
                        $('#escalesContainer').append(template);

                        // Initialiser Select2
                        setTimeout(() => {
                            $(`#escale_aeroport_${escaleCounter}`).select2({
                                theme: 'bootstrap4',
                                placeholder: "Sélectionnez un aéroport",
                                allowClear: true
                            });
                        }, 50);
                    });
                } else {
                    $('#noEscalesAlert').show();
                }
            }

            // Fonction pour formater les données des escales
            function getEscalesData() {
                const escalesData = [];
                $('.escale-row').each(function(index) {
                    const escaleId = $(this).data('escale-id');
                    const aeroportId = $(this).find('.escale-aeroport').val();
                    const dateArrivee = $(this).find('.escale-arrivee').val();
                    const dateDepart = $(this).find('.escale-depart').val();

                    if (aeroportId && dateArrivee && dateDepart) {
                        escalesData.push({
                            id: escaleId || null,
                            aeroport_id: aeroportId,
                            date_arrivee: dateArrivee,
                            date_depart: dateDepart,
                            ordre: index + 1
                        });
                    }
                });
                return escalesData;
            }

            // Validation des heures des escales
            function validateEscales() {
                let isValid = true;
                let previousTime = $('#date_depart').val();

                if (!previousTime) {
                    toastr.error('Veuillez d\'abord remplir l\'heure de départ');
                    return false;
                }

                // Valider les escales dans l'ordre
                $('.escale-row').each(function() {
                    const arrivee = $(this).find('.escale-arrivee').val();
                    const depart = $(this).find('.escale-depart').val();
                    const aeroport = $(this).find('.escale-aeroport').val();

                    if (!aeroport) {
                        toastr.error('Veuillez sélectionner un aéroport pour toutes les escales');
                        isValid = false;
                        return false;
                    }

                    if (!arrivee || !depart) {
                        toastr.error('Veuillez remplir toutes les heures des escales');
                        isValid = false;
                        return false;
                    }

                    //if (arrivee <= previousTime) {
                    //  toastr.error('L\'heure d\'arrivée de l\'escale doit être après l\'heure précédente');
                    //isValid = false;
                    //return false;
                    //}

                    //if (depart <= arrivee) {
                    //  toastr.error('L\'heure de départ de l\'escale doit être après l\'heure d\'arrivée');
                    //isValid = false;
                    //return false;
                    //}

                    previousTime = depart;
                });

                // Vérifier que l'heure d'arrivée finale est après la dernière escale
                //const finalArrival = $('#date_arrivee').val();
                //if (finalArrival && finalArrival <= previousTime) {
                //  toastr.error('L\'heure d\'arrivée finale doit être après la dernière escale');
                //isValid = false;
                //}

                return isValid;
            }

            // Afficher le formulaire quand on clique sur "Ajouter un vol"
            $('#showVolFormBtn').click(function() {
                $('#volForm').show();
                $('#volsTableContainer, #noVolsAlert').hide();
                $('#showVolFormBtn').hide();
                $('#vol_id').val('');
                $('#volFormAction').text('Ajouter');
                $('#volForm')[0].reset();
                $('.invalid-feedback').text('');
                $('.is-invalid').removeClass('is-invalid');

                // Réinitialiser les escales
                $('#escalesContainer').empty();
                escaleCounter = 0;
                currentEscalesData = [];
                $('#noEscalesAlert').show();

                // Réinitialiser Select2
                $('.select2').val(null).trigger('change');
            });

            // Cacher le formulaire
            $('#cancelVolFormBtn').click(function() {
                $('#volForm').hide();
                $('#volsTableContainer, #noVolsAlert').show();
                $('#showVolFormBtn').show();
            });

            // Quand on clique sur Modifier pour un vol existant
            $(document).on('click', '.edit-vol', function() {
                const volId = $(this).data('id');

                // Remplir le formulaire
                $('#vol_id').val(volId);
                $('#numero_vol').val($(this).data('numero_vol'));
                $('#numero_piste_depart').val($(this).data('numero_piste_depart'));
                $('#numero_piste_arrivee').val($(this).data('numero_piste_arrivee'));
                $('#aeroport_depart_id').val($(this).data('aeroport_depart_id')).trigger('change');
                $('#aeroport_arrivee_id').val($(this).data('aeroport_arrivee_id')).trigger('change');
                $('#date_depart').val(formatTime($(this).data('date_depart')));
                $('#date_arrivee').val(formatTime($(this).data('date_arrivee')));
                $('#nbr_passagers').val($(this).data('nbr_passagers'));
                $('#objet').val($(this).data('objet'));

                // Charger les escales
                let escalesData = [];
                try {
                    const escalesJson = $(this).attr('data-escales');
                    escalesData = escalesJson ? JSON.parse(escalesJson) : [];
                } catch (e) {
                    console.error('Erreur parsing escales:', e);
                    escalesData = [];
                }

                loadEscalesForEdit(escalesData);

                // Afficher le formulaire
                $('#volForm').show();
                $('#volsTableContainer, #noVolsAlert').hide();
                $('#showVolFormBtn').hide();
                $('#volFormAction').text('Mettre à jour');

                // Scroller vers le formulaire
                $('html, body').animate({
                    scrollTop: $('#volForm').offset().top
                }, 500);
            });

            // Après soumission du formulaire
            $(document).on('input', '.runway-number', function() {
                this.value = this.value.toUpperCase();
            });

            $('#volForm').submit(function(e) {
                e.preventDefault();

                console.log('=== DÉBUT SOUMISSION ===');

                // Valider les escales
                if (!validateEscales()) {
                    return false;
                }

                // Récupérer les escales
                const escalesData = getEscalesData();
                console.log('Escales récupérées:', escalesData);

                // Créer FormData
                const formData = new FormData(this);

                // Ajouter le token CSRF
                formData.append('_token', $('meta[name="csrf-token"]').attr('content'));

                // Pour PUT
                const volId = $('#vol_id').val();
                if (volId) {
                    formData.append('_method', 'PUT');
                }

                // SUPPRIMER les anciennes entrées escales du FormData
                // (car elles sont ajoutées automatiquement par les inputs du formulaire)
                const entries = Array.from(formData.entries());
                entries.forEach(([key, value]) => {
                    if (key.startsWith('escales[')) {
                        formData.delete(key);
                    }
                });

                console.log('FormData après suppression des anciennes escales:');
                Array.from(formData.entries()).forEach(([key, value]) => {
                    console.log(`${key}: ${value}`);
                });

                // Ajouter les nouvelles escales
                escalesData.forEach((escale, index) => {
                    if (escale.id) {
                        formData.append(`escales[${index}][id]`, escale.id);
                    }
                    formData.append(`escales[${index}][aeroport_id]`, escale.aeroport_id);
                    formData.append(`escales[${index}][date_arrivee]`, escale.date_arrivee);
                    formData.append(`escales[${index}][date_depart]`, escale.date_depart);
                    formData.append(`escales[${index}][ordre]`, escale.ordre || (index + 1));
                });

                console.log('FormData final avec escales:');
                Array.from(formData.entries()).forEach(([key, value]) => {
                    console.log(`${key}: ${value}`);
                });

                const url = volId ? '/user/vols/' + volId : '/user/vols';

                $.ajax({
                    url: url,
                    type: 'POST',
                    data: formData,
                    processData: false,
                    contentType: false,
                    headers: {
                        'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
                    },
                    beforeSend: function() {
                        $('#submitVolBtn').prop('disabled', true).html(
                            '<i class="fas fa-spinner fa-spin"></i> Enregistrement...');
                    },
                    success: function(response) {
                        console.log('Réponse réussie:', response);
                        toastr.success(response.message || 'Vol enregistré avec succès');
                        setTimeout(() => {
                            location.reload();
                        }, 1500);
                    },
                    error: function(xhr) {
                        console.error('Erreur AJAX:', xhr);
                        console.error('Réponse:', xhr.responseJSON);

                        if (xhr.status === 419) {
                            toastr.error(
                                'Session expirée. Veuillez rafraîchir la page et réessayer.'
                                );
                            setTimeout(() => {
                                location.reload();
                            }, 2000);
                        } else if (xhr.status === 422) {
                            const errors = xhr.responseJSON.errors;
                            console.error('Erreurs de validation:', errors);
                            $.each(errors, function(key, value) {
                                $(`#${key}`).addClass('is-invalid');
                                $(`#${key}_error`).text(value[0]);
                            });
                            toastr.error('Veuillez corriger les erreurs dans le formulaire');
                        } else {
                            toastr.error(xhr.responseJSON?.message ||
                            'Une erreur est survenue');
                        }
                    },
                    complete: function() {
                        $('#submitVolBtn').prop('disabled', false).html(
                            '<i class="fas fa-save"></i> ' + $('#volFormAction').text());
                    }
                });

                console.log('=== FIN SOUMISSION ===');
            });


            // Suppression d'un vol
            $(document).on('click', '.delete-vol', function() {
                const volId = $(this).data('id');

                Swal.fire({
                    title: 'Confirmer la suppression',
                    text: "Êtes-vous sûr de vouloir supprimer ce vol?",
                    icon: 'warning',
                    showCancelButton: true,
                    confirmButtonColor: '#d33',
                    cancelButtonColor: '#3085d6',
                    confirmButtonText: 'Oui, supprimer!',
                    cancelButtonText: 'Annuler'
                }).then((result) => {
                    if (result.isConfirmed) {
                        $.ajax({
                            url: '/user/vols/' + volId,
                            type: 'DELETE',
                            data: {
                                _token: '{{ csrf_token() }}'
                            },
                            success: function(response) {
                                Swal.fire(
                                    'Supprimé!',
                                    'Le vol a été supprimé.',
                                    'success'
                                ).then(() => {
                                    location.reload();
                                });
                            },
                            error: function(xhr) {
                                Swal.fire({
                                    icon: 'error',
                                    title: 'Erreur',
                                    text: xhr.responseJSON.message ||
                                        'Une erreur est survenue lors de la suppression'
                                });
                            }
                        });
                    }
                });
            });



            // Initialiser Select2
            $('.select2').select2();
            $('.select2_aeroports').select2({
                width: '100%',
                matcher: function(params, data) {
                    if (!params.term || params.term.trim() === '') {
                        return data;
                    }
                    const searchTerm = params.term.trim().toUpperCase();
                    const optionText = data.text.toUpperCase();
                    if (optionText.startsWith(searchTerm)) {
                        return data;
                    }
                    return null;
                },
                minimumInputLength: 1
            });
        });
    </script>
    <script>
        $(document).ready(function() {
            // Gestion de l'ajout de type d'avion
            $('#addTypeAvionBtn').click(function() {
                $('#typeAvionModal').modal('show');
            });

            $('#saveTypeAvionBtn').click(function() {
                $.ajax({
                    url: '{{ route('user.store_type_avions') }}', // À adapter selon votre route
                    type: 'POST',
                    data: $('#typeAvionForm').serialize(),
                    success: function(response) {
                        // Ajouter la nouvelle option au select
                        $('#type_avion_id').append($('<option>', {
                            value: response.id,
                            text: response.code,
                            selected: true
                        }));

                        // Réinitialiser Select2 pour afficher la nouvelle valeur
                        $('#type_avion_id').trigger('change');

                        $('#typeAvionModal').modal('hide');
                        $('#typeAvionForm')[0].reset();

                        Swal.fire({
                            icon: 'success',
                            title: 'Succès',
                            text: 'Type d\'avion ajouté avec succès',
                            timer: 2000,
                            showConfirmButton: false
                        });

                        // Rafraîchir la page si nécessaire
                        location.reload();

                    },
                    error: function(xhr) {
                        let errors = xhr.responseJSON.errors;
                        let errorMsg = '';

                        $.each(errors, function(key, value) {
                            errorMsg += value + '<br>';
                        });

                        Swal.fire({
                            icon: 'error',
                            title: 'Erreur',
                            html: errorMsg
                        });
                    }
                });
            });

            // Gestion de l'ajout de compagnie (code existant)
            $('#addCompanyBtn').click(function() {
                $('#companyModal').modal('show');
            });



            $('#saveCompanyBtn').click(function() {
                $.ajax({
                    url: '{{ route('user.store_compagnies') }}',
                    type: 'POST',
                    data: $('#companyForm').serialize(),
                    success: function(response) {
                        // Ajouter la nouvelle option au select
                        $('#compagnie_aerienne_id').append($('<option>', {
                            value: response.id,
                            text: response.nom,
                            selected: true
                        }));

                        // Réinitialiser Select2 pour afficher la nouvelle valeur
                        $('#compagnie_aerienne_id').trigger('change');

                        $('#companyModal').modal('hide');
                        $('#companyForm')[0].reset();

                        Swal.fire({
                            icon: 'success',
                            title: 'Succès',
                            text: 'Opérateur ajoutée avec succès',
                            timer: 2000,
                            showConfirmButton: false
                        });
                        location.reload();

                    },
                    error: function(xhr) {
                        let errors = xhr.responseJSON.errors;
                        let errorMsg = '';

                        $.each(errors, function(key, value) {
                            errorMsg += value + '<br>';
                        });

                        Swal.fire({
                            icon: 'error',
                            title: 'Erreur',
                            html: errorMsg
                        });
                    }
                });
            });
        });
    </script>
    <script>
        $(document).ready(function() {
            // Initialize date and time pickers
            $('.datepicker').datepicker({
                format: 'dd/mm/yyyy',
                autoclose: true,
                todayHighlight: true,
                language: 'fr'
            });

            $('.timepicker').timepicker({
                showMeridian: false,
                minuteStep: 1
            });

            // Toggle edit forms
            function toggleEditForm(id, type) {
                $(`#${type}-${id}`).toggle();
                $(`#edit-form-${type}-${id}`).toggle();
            }

            // Handle form submission for adding deceased person
            $('#deceasedPersonForm').submit(function(e) {
                e.preventDefault();
                let formData = new FormData(this);

                $.ajax({
                    url: $(this).attr('action'),
                    type: 'POST',
                    data: formData,
                    processData: false,
                    contentType: false,
                    success: function(response) {
                        location.reload();
                    },
                    error: function(xhr) {
                        toastr.error(xhr.responseJSON?.message || "Une erreur s'est produite.");
                    }
                });
            });

            // Handle update form submission
            $(document).on('submit', '[id^="updateDeceasedPersonForm-"]', function(e) {
                e.preventDefault();

                var form = $(this);
                var formData = new FormData(this);
                var id = form.find('input[name="personne_id"]').val();

                $.ajax({
                    url: `/user/personnes-deces/${id}`,
                    type: 'POST',
                    data: formData,
                    processData: false,
                    contentType: false,
                    success: function(response) {
                        location.reload();
                    },
                    error: function(xhr) {
                        toastr.error(xhr.responseJSON?.message || "Une erreur s'est produite.");
                    }
                });
            });

            // Handle delete
            $(document).on('click', '.delete-personne', function() {
                const id = $(this).data('id');

                Swal.fire({
                    title: 'Confirmer la suppression',
                    text: "Êtes-vous sûr de vouloir supprimer cette personne?",
                    icon: 'warning',
                    showCancelButton: true,
                    confirmButtonColor: '#3085d6',
                    cancelButtonColor: '#d33',
                    confirmButtonText: 'Oui, supprimer!'
                }).then((result) => {
                    if (result.isConfirmed) {
                        $.ajax({
                            url: `/user/personnes-deces/${id}`,
                            type: 'DELETE',
                            data: {
                                _token: '{{ csrf_token() }}'
                            },
                            success: function() {
                                $(`#personne-${id}, #edit-form-personne-${id}`)
                            .remove();
                                toastr.success('Personne supprimée avec succès');
                            }
                        });
                    }
                });
            });

            // Handle edit button click
            $(document).on('click', '.edit-personne', function() {
                const id = $(this).data('id');
                toggleEditForm(id, 'personne');
            });
            // Cancel edit button
            $(document).on('click', '.cancel-edit', function() {
                const id = $(this).data('id');
                const type = $(this).data('type');
                toggleEditForm(id, type);
            });

            // Show edit form
            $(document).on('click', '.edit-membre, .edit-fret, .edit-party', function() {
                const id = $(this).data('id');
                const type = $(this).closest('tr').attr('id').split('-')[0];
                toggleEditForm(id, type);
            });

            // Handle crew member operations
            $('#crewForm').submit(function(e) {
                e.preventDefault();
                let formData = new FormData(this);

                $.ajax({
                    url: $(this).attr('action'),
                    type: 'POST',
                    data: formData,
                    processData: false,
                    contentType: false,
                    success: function(response) {
                        location.reload();
                    },
                    error: function(xhr) {
                        toastr.error(xhr.responseJSON?.message || "Une erreur s'est produite.");
                    }
                });
            });



            $.ajaxSetup({
                headers: {
                    'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
                }
            });

            // Handle form submission
            $(document).on('submit', '[id^="updateCrewForm-"]', function(e) {
                e.preventDefault();

                var form = $(this);
                var formData = new FormData(this);
                var id = form.find('input[name="membre_id"]').val();

                $.ajax({
                    url: `/user/equipes/${id}`,
                    type: 'POST',
                    data: formData,
                    processData: false,
                    contentType: false,
                    success: function(response) {
                        // Refresh or update the row
                        location.reload();
                    },
                    error: function(xhr) {
                        alert('Error: ' + xhr.responseText);
                    }
                });
            });

            $(document).on('click', '.delete-membre', function() {
                const id = $(this).data('id');

                Swal.fire({
                    title: 'Confirmer la suppression',
                    text: "Êtes-vous sûr de vouloir supprimer ce membre d'équipage?",
                    icon: 'warning',
                    showCancelButton: true,
                    confirmButtonColor: '#3085d6',
                    cancelButtonColor: '#d33',
                    confirmButtonText: 'Oui, supprimer!'
                }).then((result) => {
                    if (result.isConfirmed) {
                        $.ajax({
                            url: `/user/equipes/${id}`,
                            type: 'DELETE',
                            data: {
                                _token: '{{ csrf_token() }}'
                            },
                            success: function() {
                                $(`#membre-${id}, #edit-form-membre-${id}`).remove();
                                toastr.success('Membre supprimé avec succès');
                            }
                        });
                    }
                });
            });

            // Handle freight operations
            $('#fretForm').submit(function(e) {
                e.preventDefault();
                $.ajax({
                    url: $(this).attr('action'),
                    type: 'POST',
                    data: $(this).serialize(),
                    success: function(response) {


                        location.reload();
                    },
                    error: function(xhr) {

                        toastr.error(xhr.responseJSON.message);
                    }
                });
            });

            $(document).on('submit', 'form[id^="updateFretForm-"]', function(e) {
                e.preventDefault();
                const formId = $(this).attr('id');
                const id = formId.split('-')[1];

                $.ajax({
                    url: `/user/frets/${id}`,
                    type: 'PUT',
                    data: $(this).serialize(),
                    success: function(response) {
                        location.reload();
                    },
                    error: function(xhr) {
                        toastr.error(xhr.responseJSON.message);
                    }
                });
            });

            $(document).on('click', '.delete-fret', function() {
                const id = $(this).data('id');

                Swal.fire({
                    title: 'Confirmer la suppression',
                    text: "Êtes-vous sûr de vouloir supprimer ce fret?",
                    icon: 'warning',
                    showCancelButton: true,
                    confirmButtonColor: '#3085d6',
                    cancelButtonColor: '#d33',
                    confirmButtonText: 'Oui, supprimer!'
                }).then((result) => {
                    if (result.isConfirmed) {
                        $.ajax({
                            url: `/user/frets/${id}`,
                            type: 'DELETE',
                            data: {
                                _token: '{{ csrf_token() }}'
                            },
                            success: function() {
                                $(`#fret-${id}, #edit-form-fret-${id}`).remove();
                                toastr.success('Fret supprimé avec succès');
                            }
                        });
                    }
                });
            });



            // Handle receiving party operations
            $('#receivingPartyForm').submit(function(e) {
                e.preventDefault();
                let formData = new FormData(this);

                $.ajax({
                    url: $(this).attr('action'),
                    type: 'POST',
                    data: formData,
                    processData: false,
                    contentType: false,
                    success: function(response) {
                        location.reload();
                    },
                    error: function(xhr) {
                        toastr.error(xhr.responseJSON.message);
                    }
                });
            });

            $(document).on('submit', 'form[id^="updatePartyForm-"]', function(e) {
                e.preventDefault();
                const formId = $(this).attr('id');
                const id = formId.split('-')[1];
                let formData = new FormData(this);

                $.ajax({
                    url: `/user/receiving-parties/${id}`,
                    type: 'POST',
                    data: formData,
                    processData: false,
                    contentType: false,
                    success: function(response) {
                        location.reload();
                    },
                    error: function(xhr) {
                        toastr.error(xhr.responseJSON.message);
                    }
                });
            });

            $(document).on('click', '.delete-party', function() {
                const id = $(this).data('id');

                Swal.fire({
                    title: 'Confirmer la suppression',
                    text: "Êtes-vous sûr de vouloir supprimer ce contact?",
                    icon: 'warning',
                    showCancelButton: true,
                    confirmButtonColor: '#3085d6',
                    cancelButtonColor: '#d33',
                    confirmButtonText: 'Oui, supprimer!'
                }).then((result) => {
                    if (result.isConfirmed) {
                        $.ajax({
                            url: `/user/receiving-parties/${id}`,
                            type: 'DELETE',
                            data: {
                                _token: '{{ csrf_token() }}'
                            },
                            success: function() {
                                $(`#party-${id}, #edit-form-party-${id}`).remove();
                                toastr.success('Contact supprimé avec succès');
                            }
                        });
                    }
                });
            });

            // Handle assistance operations
            $('#assistanceForm').submit(function(e) {
                e.preventDefault();
                $.ajax({
                    url: $(this).attr('action'),
                    type: 'POST',
                    data: $(this).serialize(),
                    success: function(response) {
                        location.reload();

                        toastr.success('Informations enregistrées avec succès');
                    },
                    error: function(xhr) {
                        toastr.error(xhr.responseJSON.message);
                    }
                });
            });


            // Gestionnaire pour l'upload/replacement des documents
            $('#documentForm').submit(function(e) {
                e.preventDefault();
                let formData = new FormData(this);

                // Vérifier si au moins un fichier est sélectionné
                let hasFiles = false;
                $('input[type="file"]').each(function() {
                    if (this.files.length > 0) {
                        hasFiles = true;
                    }
                });

                if (!hasFiles) {
                    toastr.warning('Veuillez sélectionner au moins un document à uploader');
                    return;
                }

                // Désactiver le bouton et afficher la progression
                let uploadBtn = $('#uploadBtn');
                uploadBtn.prop('disabled', true);
                $('#uploadBtnText').text('Upload en cours...');
                $('#uploadProgress').show();

                $.ajax({
                    url: $(this).attr('action'),
                    type: 'POST',
                    data: formData,
                    processData: false,
                    contentType: false,
                    xhr: function() {
                        let xhr = new window.XMLHttpRequest();
                        xhr.upload.addEventListener("progress", function(evt) {
                            if (evt.lengthComputable) {
                                let percentComplete = (evt.loaded / evt.total) * 100;
                                $('.progress-bar').css('width', percentComplete + '%')
                                    .text(Math.round(percentComplete) + '%');
                            }
                        }, false);
                        return xhr;
                    },
                    success: function(response) {
                        toastr.success(response.message || 'Documents uploadés avec succès');
                        setTimeout(function() {
                            location.reload();
                        }, 1500);
                    },
                    error: function(xhr) {
                        let message = 'Une erreur est survenue lors de l\'upload';
                        if (xhr.responseJSON && xhr.responseJSON.message) {
                            message = xhr.responseJSON.message;
                        }
                        toastr.error(message);
                        uploadBtn.prop('disabled', false);
                        $('#uploadBtnText').text('Uploader');
                        $('#uploadProgress').hide();
                    }
                });
            });

            // Gestionnaire pour le remplacement d'un document
            let documentToReplace = null;

            $(document).on('click', '.replace-document', function() {
                documentToReplace = $(this).data('id');
                let docType = $(this).data('type');
                $('#replace-doc-type').text(docType);
                $('#replaceDocumentModal').modal('show');
            });

            $('#confirmReplace').click(function() {
                let fileInput = $('#replace_piece')[0];
                if (!fileInput.files.length) {
                    toastr.warning('Veuillez sélectionner un fichier');
                    return;
                }

                let formData = new FormData();
                formData.append('_token', '{{ csrf_token() }}');
                formData.append('_method', 'PUT');
                formData.append('piece', fileInput.files[0]);

                $(this).prop('disabled', true).html(
                    '<i class="fas fa-spinner fa-spin"></i> Remplacement...');

                $.ajax({
                    url: `/user/documents/${documentToReplace}`,
                    type: 'POST',
                    data: formData,
                    processData: false,
                    contentType: false,
                    success: function(response) {
                        toastr.success(response.message || 'Document remplacé avec succès');
                        $('#replaceDocumentModal').modal('hide');
                        setTimeout(function() {
                            location.reload();
                        }, 1500);
                    },
                    error: function(xhr) {
                        let message = 'Erreur lors du remplacement du document';
                        if (xhr.responseJSON && xhr.responseJSON.message) {
                            message = xhr.responseJSON.message;
                        }
                        toastr.error(message);
                        $('#confirmReplace').prop('disabled', false).html(
                            '<i class="fas fa-sync"></i> Remplacer');
                    }
                });
            });

            // Réinitialiser le modal quand il est fermé
            $('#replaceDocumentModal').on('hidden.bs.modal', function() {
                $('#replaceDocumentForm')[0].reset();
                $('#confirmReplace').prop('disabled', false).html('<i class="fas fa-sync"></i> Remplacer');
                documentToReplace = null;
            });

            // Gestionnaire pour la suppression
            $(document).on('click', '.delete-document', function() {
                const id = $(this).data('id');
                const row = $(`#document-${id}`);

                Swal.fire({
                    title: 'Confirmer la suppression',
                    text: "Êtes-vous sûr de vouloir supprimer ce document? Cette action est irréversible.",
                    icon: 'warning',
                    showCancelButton: true,
                    confirmButtonColor: '#3085d6',
                    cancelButtonColor: '#d33',
                    confirmButtonText: 'Oui, supprimer!',
                    cancelButtonText: 'Annuler'
                }).then((result) => {
                    if (result.isConfirmed) {
                        $.ajax({
                            url: `/user/documents/${id}`,
                            type: 'DELETE',
                            data: {
                                _token: '{{ csrf_token() }}'
                            },
                            success: function(response) {
                                row.fadeOut(400, function() {
                                    $(this).remove();
                                    // Vérifier s'il reste des documents
                                    if ($('#documentsTable tbody tr').length ===
                                        0) {
                                        $('#documentsTable').closest(
                                            '.table-responsive').remove();
                                        $('#documentsTable').after(
                                            '<div class="alert alert-info mt-3">Aucun document n\'a été uploadé pour cette demande.</div>'
                                            );
                                    }
                                });
                                toastr.success(response.message ||
                                    'Document supprimé avec succès');
                            },
                            error: function(xhr) {
                                toastr.error(
                                    'Erreur lors de la suppression du document');
                            }
                        });
                    }
                });
            });
        });
    </script>
    <script>
        $(document).ready(function() {
            console.log('Document ready - Initialisation Select2');

            // Initialiser Select2 pour les tags (immatriculations multiples)
            $('#immatriculations_select').select2({
                theme: 'bootstrap4',
                placeholder: "Tapez une immatriculation et appuyez sur Entrée",
                tags: true,
                tokenSeparators: [',', ' ', '\n'],
                allowClear: true,
                width: '100%',
                createTag: function(params) {
                    var term = $.trim(params.term);
                    if (term === '') {
                        return null;
                    }

                    // Valider le format (lettres, chiffres, tirets)
                    if (!/^[A-Z0-9\-]+$/i.test(term)) {
                        return null;
                    }

                    return {
                        id: term.toUpperCase(),
                        text: term.toUpperCase(),
                        newTag: true
                    };
                },
                insertTag: function(data, tag) {
                    data.push(tag);
                },
                language: {
                    noResults: function() {
                        return "Aucun résultat";
                    },
                    searching: function() {
                        return "Recherche...";
                    }
                }
            }).on('change', function() {
                console.log('Select2 change event');
                updatePreview();
            });

            // Initialiser Select2 pour les selects simples
            $('.select2-single').select2({
                theme: 'bootstrap4',
                width: '100%',
                placeholder: function() {
                    return $(this).data('placeholder') || 'Sélectionnez...';
                }
            }).on('change', function() {
                console.log('Select2 single change event');
                updatePreview();
            });

            // Fonction de mise à jour de la prévisualisation
            function updatePreview() {
                const immatriculations = $('#immatriculations_select').val() || [];
                const typeId = $('#type_avion_id').val();
                const operatorId = $('#compagnie_aerienne_id').val();

                console.log('Preview update - Immatriculations:', immatriculations);
                console.log('Type ID:', typeId, 'Operator ID:', operatorId);

                if (immatriculations.length > 0 && typeId && operatorId) {
                    // Afficher le type sélectionné
                    const typeOption = $('#type_avion_id option:selected');
                    const typeText = typeOption.data('code') + ' (' + typeOption.data('capacite') + ' places)';
                    $('#selectedTypeDisplay').text(typeText);

                    // Afficher l'opérateur sélectionné
                    const operatorOption = $('#compagnie_aerienne_id option:selected');
                    const operatorText = operatorOption.data('code') ?
                        operatorOption.data('code') + ' ' + operatorOption.text() :
                        operatorOption.text();
                    $('#selectedOperatorDisplay').text(operatorText);

                    // Afficher les immatriculations
                    let preview = '';
                    immatriculations.forEach(function(imm) {
                        preview += '<span class="preview-badge">' + imm + '</span> ';
                    });

                    $('#immatriculationsPreview').html(preview);
                    $('#totalCount').text(immatriculations.length);
                    $('#previewSection').fadeIn(300);
                } else {
                    $('#previewSection').fadeOut(300);
                }
            }
            // Fonction de réinitialisation
            function resetAvionForm() {
                $('#avionForm')[0].reset();
                $('#avion_id').val('');
                $('#immatriculations_select').empty().trigger('change');
                $('#type_avion_id').val('').trigger('change');
                $('#compagnie_aerienne_id').val('').trigger('change');
                $('#formActionText').text('Ajouter les avions');

                $('#previewSection').fadeOut(300);

                // Supprimer les champs cachés ajoutés
                $('#avionForm input[name="immatriculations_list[]"]').remove();
            }

            // Initialisation des tooltips
            $('[data-toggle="tooltip"]').tooltip();

            // Test: Vérifier que Select2 est bien initialisé
            console.log('Select2 initialized:', $('#immatriculations_select').data('select2') ? 'Yes' : 'No');
        });

        $(document).ready(function() {
            // Show edit form for MDN
            $(document).on('click', '.edit-mdn', function() {
                const id = $(this).data('id');
                toggleMdnEditForm(id);
            });

            // Cancel edit
            $(document).on('click', '.cancel-edit-mdn', function() {
                const id = $(this).data('id');
                $('#edit-form-mdn-' + id).hide();
                $('#mdn-' + id).show();
            });

            // Handle MDN form submission
            $('#mdnForm').submit(function(e) {
                e.preventDefault();
                let formData = new FormData(this);

                $.ajax({
                    url: $(this).attr('action'),
                    type: 'POST',
                    data: formData,
                    processData: false,
                    contentType: false,
                    success: function(response) {
                        toastr.success(response.message || "MDN ajouté avec succès.");
                        setTimeout(() => location.reload(), 1000);
                    },
                    error: function(xhr) {
                        let message = xhr.responseJSON?.message || "Une erreur s'est produite.";
                        if (xhr.responseJSON?.errors) {
                            message = Object.values(xhr.responseJSON.errors).flat().join(', ');
                        }
                        toastr.error(message);
                    }
                });
            });

            // Handle MDN update
            $(document).on('submit', 'form[id^="updateMdnForm-"]', function(e) {
                e.preventDefault();
                const form = $(this);
                const id = form.find('input[name="mdn_id"]').val();
                let formData = new FormData(this);

                $.ajax({
                    url: '{{ url('/user/mdns') }}/' + id,
                    type: 'POST',
                    data: formData,
                    processData: false,
                    contentType: false,
                    headers: {
                        'X-HTTP-Method-Override': 'PUT'
                    },
                    success: function(response) {
                        toastr.success(response.message || "MDN mis à jour avec succès.");
                        setTimeout(() => location.reload(), 1000);
                    },
                    error: function(xhr) {
                        let message = xhr.responseJSON?.message || "Une erreur s'est produite.";
                        if (xhr.responseJSON?.errors) {
                            message = Object.values(xhr.responseJSON.errors).flat().join(', ');
                        }
                        toastr.error(message);
                    }
                });
            });

            // Handle MDN delete
            $(document).on('click', '.delete-mdn', function() {
                const id = $(this).data('id');

                if (confirm('Êtes-vous sûr de vouloir supprimer ce MDN ?')) {
                    $.ajax({
                        url: '{{ url('/user/mdns') }}/' + id,
                        type: 'DELETE',
                        data: {
                            _token: '{{ csrf_token() }}'
                        },
                        success: function(response) {
                            toastr.success(response.message || "MDN supprimé avec succès.");
                            $('#mdn-' + id).remove();
                            $('#edit-form-mdn-' + id).remove();
                        },
                        error: function(xhr) {
                            toastr.error(xhr.responseJSON?.message ||
                                "Une erreur s'est produite.");
                        }
                    });
                }
            });
        });

        // Toggle MDN edit form
        function toggleMdnEditForm(id) {
            $('#mdn-' + id).toggle();
            $('#edit-form-mdn-' + id).toggle();

            // Hide other open edit forms
            $('tr[id^="edit-form-mdn-"]').each(function() {
                const formId = $(this).attr('id').replace('edit-form-mdn-', '');
                if (formId != id) {
                    $(this).hide();
                    $('#mdn-' + formId).show();
                }
            });
        }
    </script>
@endpush
