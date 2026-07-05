@extends('compagnie.layouts.app')
@section('title')
    @lang('trans.dashboard_compagny')
@endsection
@section('contentheader')
    @lang('trans.dashboard_compagny')
@endsection
@section('contentheaderlink')
    <a href="{{ route('compagnie') }}">@lang('trans.dashboard_compagny')</a>
@endsection
@section('contentheaderactive')
    @lang('trans.dashboard_compagny')
@endsection

@push('css')
    <link rel="stylesheet"
        href="https://cdnjs.cloudflare.com/ajax/libs/bootstrap-datepicker/1.9.0/css/bootstrap-datepicker.min.css">
    <link rel="stylesheet"
        href="https://cdnjs.cloudflare.com/ajax/libs/bootstrap-timepicker/0.5.2/css/bootstrap-timepicker.min.css">
    <link href="https://cdnjs.cloudflare.com/ajax/libs/toastr.js/latest/toastr.min.css" rel="stylesheet">
    <link rel="stylesheet" href="{{ asset('assets/admin/plugins/daterangepicker/daterangepicker.css') }}">
    <link rel="stylesheet" href="{{ asset('assets/admin/plugins/select2/css/select2.min.css') }}">
    <link rel="stylesheet" href="{{ asset('assets/admin/plugins/select2-bootstrap4-theme/select2-bootstrap4.min.css') }}">
@endpush

@section('content')
    <div class="container-fluid">
        <div class="row justify-content-center">
            <div class="col-md-12">
                <h4 class="text-center">
                    {{ $demandeApprobation->reference }} - {{ $demandeApprobation->saison }} -
                    {{ date('d/m/Y', strtotime($demandeApprobation->date_debut)) }} -
                    {{ date('d/m/Y', strtotime($demandeApprobation->date_fin)) }}</h4>
                <div class="card card-primary">
                    <div class="card-header bg-primary text-white">
                        <h3 class="card-title">Avions</h3>

                        <button type="button" class="btn btn-sm btn-light float-right" id="showAvionFormBtn">
                            <i class="fas fa-plus"></i> Ajouter un avion
                        </button>

                    </div>
                    <div class="card-body">
                        <!-- Formulaire (caché par défaut) -->
                        <form method="POST" id="avionForm" action="{{ route('avions.store') }}" style="display: none;">
                            @csrf
                            <input type="hidden" name="avion_id" id="avion_id" value="">
                            <input type="hidden" name="compagnie_aerienne_id" id="compagnie_aerienne_id"
                                value="{{ $compagnie->id }}">
                            <input type="hidden" name="demande_approbation_id" id="demande_approbation_id"
                                value="{{ $demandeApprobation->id }}">
                            <div class="row">
                                <!-- Immatriculation -->
                                <div class="col-md-6 mb-3">
                                    <div class="form-group">
                                        <label for="immatriculation" class="form-label">Immatriculation </label>
                                        <input type="text" class="form-control" id="immatriculation"
                                            name="immatriculation" placeholder="Immatriculation" required>
                                        <div class="invalid-feedback" id="immatriculation_error"></div>
                                    </div>
                                </div>

                                <!-- Type d'avion -->
                                <div class="col-md-6 mb-3">
                                    <div class="form-group">
                                        <label for="type_avion_id" class="form-label">Type d'avion </label>
                                        <select class="form-control select2" id="type_avion_id" name="type_avion_id"
                                            required>
                                            <option value="">Sélectionnez un type</option>
                                            @foreach ($type_avions as $type)
                                                <option value="{{ $type->id }}">
                                                    {{ $type->code }}
                                                </option>
                                            @endforeach
                                        </select>
                                        <div class="invalid-feedback" id="type_avion_id_error"></div>
                                    </div>
                                </div>
                            </div>



                            <div class="row">
                                <div class="col-md-12">
                                    <button type="submit" class="btn btn-success float-right" id="submitAvionBtn">
                                        <i class="fas fa-save"></i> <span id="formActionText">Ajouter</span>
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
                            <div class="row mt-4" id="avionsTableContainer">
                                <div class="col-lg-12">
                                    <table class="table table-striped table-bordered" id="avionsTable">
                                        <thead>
                                            <tr>
                                                <th>Immatriculation</th>
                                                <th>Type</th>
                                                <th>Dans le programme</th>
                                                <th>Actions</th>
                                            </tr>
                                        </thead>
                                        <tbody>
                                            @foreach ($avions as $avionItem)
                                                <tr id="avion-{{ $avionItem->id }}">
                                                    <td>{{ $avionItem->immatriculation }}</td>
                                                    <td>{{ $avionItem->type->code ?? 'N/A' }}</td>
                                                    <td class="text-center">
                                                        <div class="form-check">
                                                            <input class="form-check-input flight-program-checkbox"
                                                                type="checkbox" value="{{ $avionItem->id }}"
                                                                {{ isset($avionItem->demande_approbation_id) && $avionItem->demande_approbation_id === $demandeApprobation->id ? 'checked disabled' : '' }}
                                                                data-avion-id="{{ $avionItem->id }}">
                                                        </div>
                                                    </td>
                                                    <td>
                                                        <button class="btn btn-warning btn-sm edit-avion"
                                                            data-id="{{ $avionItem->id }}"
                                                            data-immatriculation="{{ $avionItem->immatriculation }}"
                                                            data-type_avion_id="{{ $avionItem->type_avion_id }}">
                                                            <i class="fas fa-edit"></i> Modifier
                                                        </button>
                                                        <button class="btn btn-danger btn-sm delete-avion"
                                                            data-id="{{ $avionItem->id }}">
                                                            <i class="fas fa-trash"></i> Supprimer
                                                        </button>
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
                    <div class="card-header bg-primary text-white">
                        <h3 class="card-title">Programme des vols</h3>

                        <button type="button" class="btn btn-sm btn-light float-right" id="showVolFormBtn">
                            <i class="fas fa-plus"></i> Ajouter un vol
                        </button>

                    </div>
                    <div class="card-body">
                        <!-- Formulaire (caché par défaut) -->
                        <form method="POST" id="volForm" style="display: none;">
                            @csrf
                            <input type="hidden" name="vol_id" id="vol_id" value="">
                            <input type="hidden" name="demande_approbation_id" id="demande_approbation_id"
                                value="{{ $demandeApprobation->id }}">

                            <div class="row">
                                <!-- Numéro de vol -->
                                <div class="col-md-4 mb-3">
                                    <div class="form-group">
                                        <label for="numero_vol" class="form-label">@lang('trans.flight_number') </label>
                                        <input type="text" class="form-control" id="numero_vol" name="numero_vol"
                                            required>
                                        <div class="invalid-feedback" id="numero_vol_error"></div>
                                    </div>
                                </div>

                                <!-- Jour -->
                                <div class="col-md-4 mb-3">
                                    <div class="form-group">
                                        <label for="jours_operation" class="form-label">@lang('trans.operating_days') </label>
                                        <select class="form-control select2" multiple="multiple" id="jours_operation"
                                            name="jours_operation[]" required>
                                            <option value="J1">J1</option>
                                            <option value="J2">J2</option>
                                            <option value="J3">J3</option>
                                            <option value="J4">J4</option>
                                            <option value="J5">J5</option>
                                            <option value="J6">J6</option>
                                            <option value="J7">J7</option>
                                        </select>
                                        <div class="invalid-feedback" id="jour_error"></div>
                                    </div>
                                </div>
                                <!-- Dates -->
                                <div class="col-md-4 mb-3">
                                    <div class="form-group">
                                        <label>@lang('trans.period')</label>

                                        <div class="input-group">
                                            <div class="input-group-prepend">
                                                <span class="input-group-text">
                                                    <i class="far fa-calendar-alt"></i>
                                                </span>
                                            </div>
                                            <input type="text" class="form-control float-right" id="periode"
                                                name="periode">
                                        </div>
                                        <!-- /.input group -->
                                    </div>
                                    <!-- /.form group -->
                                </div>
                            </div>


                            <div class="row">
                                <div class="col-md-6">
                                    <div class="form-group">
                                        <label for="aeroport_depart_id">@lang('trans.origin') </label>
                                        <select class="form-control select2" id="aeroport_depart_id"
                                            name="aeroport_depart_id" required>
                                            @foreach ($aeroports as $aeroport)
                                                <option value="{{ $aeroport->id }}"> {{ $aeroport->codeIATA }}
                                                    -
                                                    {{ $aeroport->codeICAO }}
                                                    {{ $aeroport->nom }} - {{ $aeroport->ville }}</option>
                                            @endforeach
                                        </select>
                                        <div class="invalid-feedback" id="aeroport_depart_id_error"></div>
                                    </div>
                                </div>
                                <div class="col-md-6">
                                    <div class="form-group">
                                        <label for="aeroport_arrivee_id">@lang('trans.destination')</label>

                                        <select class="form-control select2" id="aeroport_arrivee_id"
                                            name="aeroport_arrivee_id" required>
                                            @foreach ($aeroports as $aeroport)
                                                <option value="{{ $aeroport->id }}">{{ $aeroport->codeIATA }}
                                                    -
                                                    {{ $aeroport->codeICAO }}
                                                    {{ $aeroport->nom }} - {{ $aeroport->ville }}</option>
                                            @endforeach
                                        </select>
                                        <div class="invalid-feedback" id="aeroport_arrivee_id_error"></div>
                                    </div>
                                </div>
                            </div>
                            <div class="row">
                                <!-- Dates -->
                                <div class="col-md-6 mb-3">
                                    <div class="form-group">
                                        <label for="heure_depart" class="form-label">@lang('trans.departure_time') </label>
                                        <div class="input-group">
                                            <input type="time" class="form-control" id="heure_depart"
                                                name="heure_depart" required>
                                        </div>
                                        <div class="invalid-feedback" id="heure_depart_error"></div>
                                    </div>
                                </div>

                                <div class="col-md-6 mb-3">
                                    <div class="form-group">
                                        <label for="heure_arrivee" class="form-label">@lang('trans.arrival_time') </label>
                                        <input type="time" class="form-control" id="heure_arrivee"
                                            name="heure_arrivee" required>
                                        <div class="invalid-feedback" id="heure_arrivee_error"></div>
                                    </div>
                                </div>
                            </div>
                            <div class="row">
                                <div class="col-md-12">
                                    <button type="submit" class="btn btn-success float-right" id="submitVolBtn">
                                        <i class="fas fa-save"></i> Ajouter
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
                                                        <button class="btn btn-warning btn-sm edit-vol"
                                                            data-id="{{ $volItem->id }}"
                                                            data-numero_vol="{{ $volItem->numero_vol }}">
                                                            <i class="fas fa-edit"></i> Modifier
                                                        </button>
                                                        <button class="btn btn-danger btn-sm delete-vol"
                                                            data-id="{{ $volItem->id }}">
                                                            <i class="fas fa-trash"></i> Supprimer
                                                        </button>

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
                    <div class="card-header bg-primary text-white">
                        <h3 class="card-title">@lang('trans.second_aeroports')</h3>
                    </div>
                    <div class="card-body">
                        <form method="POST" id="itineraryForm" action="{{ url('/compagnie/itineraires/') }}">
                            @csrf
                            <input type="hidden" value="{{ $demandeApprobation->id }}" id="demande_approbation_id"
                                name="demande_approbation_id">

                            <div class="row">
                                <div class="col-md-6">
                                    <div class="form-group">
                                        <label for="vol_id">@lang('trans.flight')</label>
                                        <select class="form-control select2" id="vol_id" name="vol_id" required>
                                            @foreach ($vols as $vol)
                                                <option value="{{ $vol->id }}">{{ $vol->numero_vol }}
                                                    {{ $vol->period_formatted }} -
                                                    {{ $vol->jours_operation_display }}</option>
                                            @endforeach
                                        </select>
                                    </div>
                                </div>
                                <div class="col-md-6">
                                    <div class="form-group">
                                        <label for="aeroport_id">@lang('trans.aeroport')</label>
                                        <select class="form-control select2" id="aeroport_id" name="aeroport_id"
                                            required>
                                            @foreach ($aeroports as $aeroport)
                                                <option value="{{ $aeroport->id }}">
                                                    {{ $aeroport->codeIATA }} - {{ $aeroport->codeICAO }}
                                                    {{ $aeroport->nom }} - {{ $aeroport->ville }}</option>
                                            @endforeach
                                        </select>
                                    </div>
                                </div>
                            </div>
                            <div class="row">
                                <div class="col-md-6">
                                    <div class="form-group">
                                        <label for="heure_arrivee">@lang('trans.arrival_time')</label>
                                        <input type="time" class="form-control" id="heure_arrivee"
                                            name="heure_arrivee" required>
                                    </div>
                                </div>
                                <div class="col-md-6">
                                    <div class="form-group">
                                        <label for="heure_depart">@lang('trans.departure_time')</label>
                                        <input type="time" class="form-control" id="heure_depart" name="heure_depart"
                                            required>
                                    </div>
                                </div>
                            </div>
                            <div class="row">
                                <div class="col-lg-12">
                                    <button id="submitItinerary" type="submit" class="btn btn-success float-right">
                                        <i class="fas fa-plus"></i> @lang('trans.add')
                                    </button>
                                </div>
                            </div>
                        </form>

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
                                                    ({{ $vol->period_formatted }} - {{ $vol->jours_operation_display }})
                                                </h4>
                                            </div>
                                            <div class="card-body">
                                                <div class="mb-3">
                                                    <strong>Route :</strong> {{ $vol->aeroportDepart->codeICAO }}
                                                    {{ date('Hi', strtotime($vol->heure_depart)) }}
                                                    @if (!empty($routeString))
                                                        -
                                                        {{ $routeString }}
                                                    @endif
                                                    -
                                                    {{ date('Hi', strtotime($vol->heure_arrivee)) }}
                                                    {{ $vol->aeroportArrivee->codeICAO }}

                                                </div>

                                                <table class="table table-striped table-bordered">
                                                    <thead>
                                                        <tr>
                                                            <th>@lang('trans.aeroport')</th>
                                                            <th>@lang('trans.arrival_date')</th>
                                                            <th>@lang('trans.departure_date')</th>
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

                                                                    <button class="btn btn-warning btn-sm edit-itineraire"
                                                                        data-id="{{ $itineraire->id }}">@lang('trans.update')</button>

                                                                    <button class="btn btn-danger btn-sm delete-itineraire"
                                                                        data-id="{{ $itineraire->id }}">@lang('trans.destroy')</button>
                                                                </td>
                                                            </tr>
                                                            <tr id="edit-form-itineraire-{{ $itineraire->id }}"
                                                                style="display: none;">
                                                                <td colspan="4">
                                                                    <form id="updateItineraryForm-{{ $itineraire->id }}">
                                                                        @csrf
                                                                        @method('PUT')
                                                                        <input type="hidden" name="itineraire_id"
                                                                            value="{{ $itineraire->id }}">
                                                                        <input type="hidden"
                                                                            value="{{ $demandeApprobation->id }}"
                                                                            id="demande_approbation_id"
                                                                            name="demande_approbation_id">
                                                                        <input type="hidden"
                                                                            value="{{ $itineraire->vol_id }}"
                                                                            id="vol_id" name="vol_id">
                                                                        <div class="row">
                                                                            <div class="col-md-12">
                                                                                <div class="form-group">
                                                                                    <label>@lang('trans.aeroport')</label>
                                                                                    <select class="form-control select2"
                                                                                        name="aeroport_id" required>
                                                                                        @foreach ($aeroports as $aeroport)
                                                                                            <option
                                                                                                value="{{ $aeroport->id }}"
                                                                                                {{ $itineraire->aeroport_id == $aeroport->id ? 'selected' : '' }}>
                                                                                                {{ $aeroport->codeIATA }} -
                                                                                                {{ $aeroport->codeICAO }}
                                                                                                -
                                                                                                {{ $aeroport->nom }}
                                                                                            </option>
                                                                                        @endforeach
                                                                                    </select>
                                                                                </div>
                                                                            </div>
                                                                        </div>
                                                                        <div class="row">
                                                                            <div class="col-md-6">
                                                                                <div class="form-group">

                                                                                    <label>@lang('trans.arrival_time')</label>
                                                                                    <input type="time"
                                                                                        class="form-control"
                                                                                        id="heure_arrivee"
                                                                                        name="heure_arrivee"
                                                                                        value="{{ date('H:i', strtotime($itineraire->heure_arrivee)) }}"
                                                                                        required>
                                                                                </div>
                                                                            </div>
                                                                            <div class="col-md-6">
                                                                                <div class="form-group">
                                                                                    <label>@lang('trans.departure_time')</label>
                                                                                    <input type="time"
                                                                                        class="form-control"
                                                                                        id="heure_depart"
                                                                                        name="heure_depart"
                                                                                        value="{{ date('H:i', strtotime($itineraire->heure_depart)) }}"
                                                                                        required>
                                                                                </div>
                                                                            </div>

                                                                        </div>
                                                                        <button type="submit"
                                                                            class="btn btn-primary btn-sm update-itineraire"
                                                                            data-id="{{ $itineraire->id }}">@lang('trans.update')</button>
                                                                        <button type="button"
                                                                            class="btn btn-secondary btn-sm cancel-edit"
                                                                            data-id="{{ $itineraire->id }}"
                                                                            data-type="itineraire">@lang('trans.cancel')</button>
                                                                    </form>
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
                    <div class="card-header bg-primary text-white">
                        <h3 class="card-title">Documents</h3>
                    </div>
                    <div class="card-body">
                        <form method="POST" id="documentForm" action="{{ url('/compagnie/documents') }}"
                            enctype="multipart/form-data">
                            @csrf
                            <input type="hidden" name="demande_approbation_id" value="{{ $demandeApprobation->id }}">

                            <div class="row justify-content-center">
                                <div class="col-md-8">
                                    <div class="form-group">
                                        <ol>
                                            @foreach ($requiredDocs as $index => $requiredDoc)
                                                <li>
                                                    <input type="hidden" value="{{ $requiredDoc->id }}"
                                                        id="type_document_id_{{ $index }}"
                                                        name="type_document_id[]">
                                                    {{ $requiredDoc->nom_fr }}

                                                    <input type="file" class="form-control"
                                                        id="piece_{{ $index }}" name="pieces[]"
                                                        accept="application/pdf">
                                                </li>
                                            @endforeach
                                        </ol>
                                    </div>
                                </div>
                            </div>
                            <div class="row">
                                <div class="col-md-12">
                                    <button type="submit" class="btn btn-success mt-4 float-right">
                                        <i class="fas fa-upload"></i> Uploader
                                    </button>
                                </div>
                            </div>
                        </form>

                        @if ($requiredDocs)
                            <div class="row mt-4">
                                <div class="col-12">
                                    <table class="table table-striped" id="documentsTable">
                                        <thead>
                                            <tr>
                                                <th>Type</th>
                                                <th>Document</th>
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
                                                        <button class="btn btn-sm btn-danger delete-document"
                                                            data-id="{{ $document->id }}">
                                                            <i class="fas fa-trash"></i>
                                                        </button>
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
    <!-- Select2 -->
    <script src="{{ asset('assets/admin/plugins/select2/js/select2.full.min.js') }}"></script>
    <link rel="stylesheet"
        href="{{ asset('assets/admin/plugins/select2-bootstrap4-theme/select2-bootstrap4.min.css') }}">
@endpush
@push('custom')
    <script>
        $(document).ready(function() {

            $('.flight-program-checkbox').change(function() {
                const avionId = $(this).val();
                const demandeId = $('#demande_approbation_id').val();

                const isChecked = $(this).is(':checked');

                $.ajax({
                    url: "{{ route('compagnie.updateProgramStatus') }}",
                    method: 'POST',
                    data: {
                        _token: "{{ csrf_token() }}",
                        demande_approbation_id: demandeId,
                        avion_id: avionId
                    },
                    success: function(response) {
                        toastr.success('Statut du programme mis à jour');
                        location.reload();

                    },
                    error: function() {
                        toastr.error('Erreur lors de la mise à jour');
                    }
                });
            });

        });

        $(function() {
            //Initialize Select2 Elements
            $('.select2').select2()

        });
    </script>
    <script>
        $(document).ready(function() {

            $('#periode').daterangepicker({
                locale: {
                    format: 'YYYY/MM/DD',
                    separator: ' - ',
                    applyLabel: 'Valider',
                    cancelLabel: 'Annuler',
                    fromLabel: 'De',
                    toLabel: 'À',
                    daysOfWeek: ['Di', 'Lu', 'Ma', 'Me', 'Je', 'Ve', 'Sa'],
                    monthNames: ['Janvier', 'Février', 'Mars', 'Avril', 'Mai', 'Juin', 'Juillet', 'Août',
                        'Septembre', 'Octobre', 'Novembre', 'Décembre'
                    ],
                    firstDay: 1
                },
                startDate: '{{ $demandeApprobation->date_debut ?? Carbon\Carbon::now()->format('Y/m/d') }}',
                endDate: '{{ $demandeApprobation->date_fin ?? Carbon\Carbon::now()->addMonth()->format('Y/m/d') }}',
                opens: 'right',
                autoUpdateInput: true
            });
            $('#periode').val(
                '{{ $demandeApprobation->date_debut->format('Y/m/d') }} - {{ $demandeApprobation->date_fin->format('Y/m/d') }}'
            );

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

                // Remplir le formulaire avec les données de l'avion
                $('#avion_id').val(avionId);
                $('#immatriculation').val($(this).data('immatriculation'));
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
                            url: '/compagnie/avions/' + avionId,
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
                const url = $('#avion_id').val() ? '/compagnie/avions/' + $('#avion_id').val() :
                    '/compagnie/avions';
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
        $(document).ready(function() {
            // Fonction pour formater la date pour l'input time
            function formatDateForInput(dateString) {
                if (!dateString) return '';
                const date = new Date(dateString);
                const pad = num => num.toString().padStart(2, '0');
                return `${date.getFullYear()}-${pad(date.getMonth()+1)}-${pad(date.getDate())}T${pad(date.getHours())}:${pad(date.getMinutes())}`;
            }

            // Afficher le formulaire quand on clique sur "Ajouter un vol"
            $('#showVolFormBtn').click(function() {
                $('#volForm').show();
                $('#volsTableContainer, #noVolsAlert').hide();
                $('#showVolFormBtn').hide();
                $('#vol_id').val(''); // Reset l'ID pour une nouvelle création
                $('#submitVolBtn').html('<i class="fas fa-save"></i> Ajouter');
                $('#volForm')[0].reset(); // Reset le formulaire
                $('.invalid-feedback').text(''); // Effacer les messages d'erreur
                $('.is-invalid').removeClass('is-invalid'); // Enlever les classes d'erreur
            });

            // Cacher le formulaire quand on clique sur Annuler
            $('#cancelVolFormBtn').click(function() {
                $('#volForm').hide();
                $('#volsTableContainer, #noVolsAlert').show();
                $('#showVolFormBtn').show();
            });

            // Quand on clique sur Modifier pour un vol existant
            $(document).on('click', '.edit-vol', function() {
                const volId = $(this).data('id');

                // Remplir le formulaire avec les données du vol
                const row = $(this).closest('tr');

                // Get all data from the table row or data attributes
                const numeroVol = $(this).data('numero_vol');
                const joursOperation = row.find('td:eq(1)').text().split(
                    ', '); // Assuming jours_operation_display is comma-separated
                const aeroportDepart = row.find('td:eq(2)').text();
                const aeroportArrivee = row.find('td:eq(3)').text();
                const heureDepart = row.find('td:eq(4)').text();
                const heureArrivee = row.find('td:eq(5)').text();
                const periode = row.find('td:eq(6)')
                    .text(); // Assuming period_formatted is in a readable format

                // Fill the form with all flight data
                $('#vol_id').val(volId);
                $('#numero_vol').val(numeroVol);

                // Set operating days (assuming they're in format like "J1, J3, J5")
                $('#jours_operation').val(joursOperation).trigger('change');

                // Set airports (you'll need to match by ICAO code)
                $('#aeroport_depart_id').val(function() {
                    return $(this).find('option').filter(function() {
                        return $(this).text().includes(aeroportDepart);
                    }).val();
                }).trigger('change');

                $('#aeroport_arrivee_id').val(function() {
                    return $(this).find('option').filter(function() {
                        return $(this).text().includes(aeroportArrivee);
                    }).val();
                }).trigger('change');

                // Set times
                $('#heure_depart').val(heureDepart);
                $('#heure_arrivee').val(heureArrivee);

                // Set period (you might need to parse the displayed text into date range)
                $('#periode').val(periode);
                // Afficher le formulaire
                $('#volForm').show();
                $('#volsTableContainer, #noVolsAlert').hide();
                $('#showVolFormBtn').hide();
                $('#submitVolBtn').html('<i class="fas fa-save"></i> Mettre à jour');

                // Scroller vers le formulaire
                $('html, body').animate({
                    scrollTop: $('#volForm').offset().top
                }, 500);
            });

            // Après soumission réussie du formulaire
            $('#volForm').submit(function(e) {
                e.preventDefault();

                const formData = $(this).serialize();
                const url = $('#vol_id').val() ? '/compagnie/vols/' + $('#vol_id').val() :
                    '/compagnie/vols';
                const method = $('#vol_id').val() ? 'PUT' : 'POST';

                $.ajax({
                    url: url,
                    type: method,
                    data: formData,
                    success: function(response) {
                        // Cacher le formulaire et recharger la liste
                        $('#volForm').hide();
                        $('#showVolFormBtn').show();
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
                            url: '/compagnie/vols/' + volId,
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
            // Handle itinerary operations
            $('#itineraryForm').submit(function(e) {
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

            $(document).on('submit', 'form[id^="updateItineraryForm-"]', function(e) {
                e.preventDefault();
                const formId = $(this).attr('id');
                const id = formId.split('-')[1];

                $.ajax({
                    url: `/compagnie/itineraires/${id}`,
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

            $(document).on('click', '.delete-itineraire', function() {
                const id = $(this).data('id');

                Swal.fire({
                    title: 'Confirmer la suppression',
                    text: "Êtes-vous sûr de vouloir supprimer cet itinéraire?",
                    icon: 'warning',
                    showCancelButton: true,
                    confirmButtonColor: '#3085d6',
                    cancelButtonColor: '#d33',
                    confirmButtonText: 'Oui, supprimer!'
                }).then((result) => {
                    if (result.isConfirmed) {
                        $.ajax({
                            url: `/compagnie/itineraires/${id}`,
                            type: 'DELETE',
                            data: {
                                _token: '{{ csrf_token() }}'
                            },
                            success: function() {
                                $(`#itineraire-${id}, #edit-form-itineraire-${id}`)
                                    .remove();
                                toastr.success('Itinéraire supprimé avec succès');
                                location.reload();
                            }
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

            // Cancel edit button
            $(document).on('click', '.cancel-edit', function() {
                const id = $(this).data('id');
                const type = $(this).data('type');
                toggleEditForm(id, type);
            });

            // Show edit form
            $(document).on('click', '.edit-itineraire', function() {
                const id = $(this).data('id');
                const type = $(this).closest('tr').attr('id').split('-')[0];
                toggleEditForm(id, type);
            });

            // Handle document operations
            $('#documentForm').submit(function(e) {
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

            $(document).on('click', '.delete-document', function() {
                const id = $(this).data('id');

                Swal.fire({
                    title: 'Confirmer la suppression',
                    text: "Êtes-vous sûr de vouloir supprimer ce document?",
                    icon: 'warning',
                    showCancelButton: true,
                    confirmButtonColor: '#3085d6',
                    cancelButtonColor: '#d33',
                    confirmButtonText: 'Oui, supprimer!'
                }).then((result) => {
                    if (result.isConfirmed) {
                        $.ajax({
                            url: `/compagnie/documents/${id}`,
                            type: 'DELETE',
                            data: {
                                _token: '{{ csrf_token() }}'
                            },
                            success: function() {
                                $(`#document-${id}`).remove();
                                toastr.success('Document supprimé avec succès');
                            }
                        });
                    }
                });
            });
        });
    </script>
@endpush
