@extends('layouts.admin')
@section('title')
    @lang('trans.dashboard_admin')
@endsection
@section('contentheader')
    @lang('trans.dashboard_admin')
@endsection
@section('contentheaderlink')
    <a href="{{ route('demandes') }}">
        @lang('trans.dashboard_admin') </a>
@endsection
@section('contentheaderactive')
    @lang('trans.dashboard_admin')
@endsection

@push('css')
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/summernote/0.8.18/summernote-bs4.min.css">
<style>
.modal-xl {
    max-width: 95%;
}

.table td, .table th {
    vertical-align: middle;
}

.form-check {
    justify-content: center;
    display: flex;
    margin-bottom: 0;
}

.form-check-input {
    margin-top: 0;
    cursor: pointer;
}

.form-check-label {
    cursor: pointer;
    margin-left: 5px;
}

.bg-info {
    background-color: #17a2b8 !important;
}

.modal-header .close {
    color: white;
    opacity: 0.8;
}

.modal-header .close:hover {
    opacity: 1;
}

.observation-text {
    resize: vertical;
}

/* Progress indicator styles */
.checklist-progress {
    position: sticky;
    top: 0;
    z-index: 10;
    background: white;
    padding: 10px;
    border-bottom: 1px solid #dee2e6;
    margin-bottom: 15px;
}

/* Responsive table */
@media (max-width: 768px) {
    .table {
        font-size: 12px;
    }
    
    .form-check-label .badge {
        font-size: 10px;
    }
    
    .observation-text {
        min-width: 150px;
    }
}
</style>
<style>
.floating-action-btn {
    position: fixed;
    bottom: 30px;
    right: 80px;
    z-index: 9999;
    animation: pulse 2s infinite;
    box-shadow: 0 4px 15px rgba(0,0,0,0.3);
}

@keyframes pulse {
    0% { transform: scale(1); }
    50% { transform: scale(1.1); }
    100% { transform: scale(1); }
}

.floating-action-btn:hover {
    animation: none;
    transform: scale(1.1);
    transition: transform 0.3s;
}
</style>
    <style>
        #documentViewer {
            width: 210mm;
            height: 297mm;
            max-width: 100%;
            /* Makes it responsive */
            display: block;
            margin: auto;
            /* Center horizontally */
        }
    </style>
@endpush
@section('content')

    <div class="container-fluid">
        <h1></h1>
        <div class="row">
            <!-- left column -->
            <div class="col-md-12">
                <!-- general form elements -->
                 @isset($demandeur)
                <div class="card">
                    <div class="card-header bg-primary text-white">
                        <h4 class="text-center">
                        {{ $demande->code }} - {{ $demande->demandeur->np ?? 'N/A' }} - {{ $demande->demandeur->compagnie->nom_entreprise ?? 'Privé' }} - {{ LaravelLocalization::getCurrentLocale() == 'fr' ? optional($demande->typeDemande)->nom_fr : optional($demande->typeDemande)->nom_en }} - {{ LaravelLocalization::getCurrentLocale() == 'fr' ? optional($demande->typeLicence)->fr : optional($demande->typeLicence)->en }}
                        </h4>
                    </div>
                   
                    <div class="card-body">
                        
                            <div class="row justify-content-center">

                                <div class="col-lg-9">
                                    <table class="table table-bordered table-striped">
                                        <tr>
                                            <th>@lang('trans.fl_name')</th>
                                            <td>{{ $demandeur->np ?? '-' }}</td>
                                        </tr>
                                        <tr>

                                            <th>@lang('trans.dob')</th>
                                            <td>{{ !empty($demandeur->date_naissance) ? date('Y-m-d', strtotime($demandeur->date_naissance)) : '-' }}
                                            </td>
                                        </tr>
                                        <tr>
                                            <th>@lang('trans.lieu_naissance')</th>
                                            <td>{{ $demandeur->lieu_naissance ?? '-' }}</td>
                                        </tr>
                                        <tr>
                                            <th>@lang('trans.address')</th>
                                            <td>{{ $demandeur->adresse ?? '-' }}</td>
                                        </tr>
                                        <tr>
                                            <th>@lang('trans.adresse_employeur')</th>
                                            <td>{{ $demandeur->compagnie->adresse ?? '-' }}</td>
                                        </tr>
                                        <tr>
                                            <th>@lang('trans.signature')</th>
                                            <td class="text-center">
                                                @if (isset($demandeur->signature) && $demandeur->signature != '')
                                                    <img src="{{ asset('/uploads/' . $demandeur->signature) }}"
                                                        alt="User Signature" class="img-thumbnail" width="120">
                                                @else
                                                    -
                                                @endif
                                            </td>
                                        </tr>
                                        
                        
                        
                                    </table>
                                </div>

                                <!-- Profile Picture -->
                                <div class="col-lg-3 text-center">
                                    <img src="{{ asset('/uploads/' . ($demandeur->photo ?? 'default.png')) }}"
                                        alt="Profile Picture" class="img-fluid rounded-circle"
                                        style="width: 150px; height: 150px; object-fit: cover;">
                                </div>
                            </div>
                        
                    </div>
                    
                </div>
                @endisset
                @isset($examens)
                <div class="card">
                    <div class="card-header bg-success text-white">
                        @lang('trans.medical_fitness_by_examiner')
                    </div>
                    <div class="card-body">

                        
                            <div class="row">
                                <div class="col-lg-12 table-responsive">
                                    <table class="table table-striped table-bordered">
                                        <thead>
                                            <tr>

                                                <th>@lang('trans.exam_date')</th>
                                                <th>@lang('trans.validity')</th>
                                                <th>@lang('trans.examiner')</th>
                                                <th>@lang('trans.medical_center')</th>
                                                <th>@lang('trans.view_examiner')</th>
                                                <th>@lang('trans.view_evaluator')</th>

                                            </tr>
                                        </thead>
                                        <tbody>
                                            @foreach ($examens as $examen)
                                                <tr>
                                                    <td>{{ $examen->date_examen }}</td>
                                                    <td>{{ $examen->validite }}</td>
                                                    <td>{{ $examen->examinateur->np }}</td>
                                                    <td>{{ $examen->examinateur->centreMedical->libelle }}</td>
                                                    <td>
                                                        @if ($examen->valider_examinateur)
                                                            @lang('trans.validated')
                                                        @else
                                                            @lang('trans.invalid')
                                                        @endif
                                                    </td>
                                                    <td>
                                                        @if ($examen->valider_evaluateur)
                                                            @lang('trans.validated')
                                                        @else
                                                            @lang('trans.invalid')
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
                @endisset
                @isset($medical_examinations)
                <div class="card">
                    <div class="card-header bg-primary text-white">
                        @lang('trans.medical_fitness')
                    </div>
                    <div class="card-body">

                        
                            <div class="row">
                                <div class="col-lg-12 table-responsive">
                                    <table class="table table-striped table-bordered">
                                        <thead>
                                            <tr>

                                                <th>@lang('trans.exam_date')</th>
                                                <th>@lang('trans.validity')</th>
                                                <th>@lang('trans.medical_center') </th>
                                                <th> @lang('trans.proof')</th>
                                                <th> @lang('trans.validated_by_evaluator')</th>
                                                <th>@lang('trans.actions') </th>

                                            </tr>
                                        </thead>
                                        <tbody>
                                            @foreach ($medical_examinations as $medical_examination)
                                                <tr>
                                                    <td>{{ $medical_examination->date_examen }}</td>
                                                    <td>{{ $medical_examination->validite }}</td>
                                                    <td>{{ $medical_examination->centre_medical }}</td>
                                                    <td>
                                                        @if ($medical_examination->document)
                                                            <button class="btn btn-primary"
                                                                onclick="openPdfModal('{{ asset('/uploads/' . $medical_examination->document) }}')"><i
                                                                    class="fas fa-eye"></i></button>
                                                        @endif


                                                    </td>
                                                    <td>
                                                        @if ($medical_examination->valider_evaluateur)
                                                            @lang('trans.validated')
                                                        @else
                                                            @lang('trans.invalid')
                                                        @endif

                                                    </td>
                                                    <td>
                                                        <button type="button" class="btn btn-warning btn-sm"
                                                            onclick="openDeleteModal('medical_examinations', '{{ $medical_examination->id }}', '{{ $demande->id }}')">
                                                            <i class="fas fa-trash"></i> @lang('trans.delete')
                                                        </button>
                                                        @if (is_null($medical_examination->valider))
                                                            <!-- Show both buttons for NULL state -->
                                                            <button type="button" class="btn btn-success btn-sm mr-1"
                                                                onclick="openDecisionModal('medical_examinations', '{{ $medical_examination->id }}', '{{ $demande->id }}', 'approve')">
                                                                @lang('trans.approve')
                                                            </button>
                                                            <button type="button" class="btn btn-danger btn-sm"
                                                                onclick="openDecisionModal('medical_examinations', '{{ $medical_examination->id }}', '{{ $demande->id }}', 'reject')">
                                                                @lang('trans.reject')
                                                            </button>
                                                        @elseif ($medical_examination->valider == 1)
                                                            <!-- Approved state - show reject option -->
                                                            <span class="badge bg-success">@lang('trans.approved')</span>
                                                            <button type="button" class="btn btn-danger btn-sm ml-2"
                                                                onclick="openDecisionModal('medical_examinations', '{{ $medical_examination->id }}', '{{ $demande->id }}', 'reject')">
                                                                @lang('trans.reject')
                                                            </button>
                                                        @else
                                                            <!-- Rejected state - show approve option -->
                                                            <span class="badge bg-danger">@lang('trans.rejected')</span>
                                                            <button type="button" class="btn btn-success btn-sm ml-2"
                                                                onclick="openDecisionModal('medical_examinations', '{{ $medical_examination->id }}', '{{ $demande->id }}', 'approve')">
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
                 @endisset
@isset($formations)
    <div class="card">
        <div class="card-header bg-danger text-white">
            @lang('trans.training')
        </div>
        <div class="card-body">
            <div class="row">
                <div class="col-lg-12 table-responsive">
                    <table class="table table-striped table-bordered">
                        <thead>
                            <tr>
                                <th>@lang('trans.training')</th>
                                <th>@lang('trans.training_center')</th>
                                <th>@lang('trans.location')</th>
                                <th>@lang('trans.training_date')</th>
                                <th>@lang('trans.proof')</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach ($formations as $formation)
                                <tr>
                                    <td>{{ $formation->typeFormation?->nom ?? 'N/A' }}</td>
                                    <td>{{ $formation->centreFormation?->libelle ?? 'N/A' }}</td>
                                    <td>{{ $formation->lieu ?? 'N/A' }}</td>
                                    <td>{{ $formation->date_formation ? $formation->date_formation->format('Y-m-d') : 'N/A' }}</td>
                                    <td>
                                        @if ($formation->attestation)
                                            <button class="btn btn-primary"
                                                onclick="openPdfModal('{{ asset('/uploads/' . $formation->attestation) }}')">
                                                <i class="fas fa-eye"></i>
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
@endisset
                @isset($licence_demandeurs)
                <div class="card">
                    <div class="card-header bg-primary text-white">
                        @lang('trans.license')
                    </div>

                    <div class="card-body">
                        
                            <div class="row">
                                <div class="col-lg-12 table-responsive">
                                    <table class="table table-striped table-bordered">
                                        <thead>
                                            <tr>
                                                <th>@lang('trans.license_date')</th>
                                                <th>@lang('trans.license_number')</th>
                                                <th>@lang('trans.authority')</th>
                                                <th>@lang('trans.location')</th>
                                                <th>@lang('trans.proof')</th>
                                                <th>@lang('trans.actions')</th>
                                            </tr>
                                        </thead>
                                        <tbody>
                                            @foreach ($licence_demandeurs as $licence_demandeur)
                                                <tr>
                                                    <td>{{ $licence_demandeur->date_licence }}</td>
                                                    <td>{{ $licence_demandeur->num_licence }}</td>
                                                    <td>{{ $licence_demandeur->autorite->libelle }}</td>
                                                    <td>{{ $licence_demandeur->lieu_delivrance }}</td>
                                                    <td>
                                                        @if ($licence_demandeur->document)
                                                            <button class="btn btn-primary"
                                                                onclick="openPdfModal('{{ asset('/uploads/' . $licence_demandeur->document) }}')"><i
                                                                    class="fas fa-eye"></i></button>
                                                        @endif

                                                    </td>
                                                    <td>
                                                        <button type="button" class="btn btn-warning btn-sm"
                                                            onclick="openDeleteModal('licence_demandeurs', '{{ $licence_demandeur->id }}', '{{ $demande->id }}')">
                                                            <i class="fas fa-trash"></i> @lang('trans.delete')
                                                        </button>
                                                        @if (is_null($licence_demandeur->valider))
                                                            <!-- Show both buttons for NULL state -->
                                                            <button type="button" class="btn btn-success btn-sm mr-1"
                                                                onclick="openDecisionModal('licence_demandeurs', '{{ $licence_demandeur->id }}', '{{ $demande->id }}', 'approve')">
                                                                @lang('trans.approve')
                                                            </button>
                                                            <button type="button" class="btn btn-danger btn-sm"
                                                                onclick="openDecisionModal('licence_demandeurs', '{{ $licence_demandeur->id }}', '{{ $demande->id }}', 'reject')">
                                                                @lang('trans.reject')
                                                            </button>
                                                        @elseif ($licence_demandeur->valider == 1)
                                                            <!-- Approved state - show reject option -->
                                                            <span class="badge bg-success">@lang('trans.approved')</span>
                                                            <button type="button" class="btn btn-danger btn-sm ml-2"
                                                                onclick="openDecisionModal('licence_demandeurs', '{{ $licence_demandeur->id }}', '{{ $demande->id }}', 'reject')">
                                                                @lang('trans.reject')
                                                            </button>
                                                        @else
                                                            <!-- Rejected state - show approve option -->
                                                            <span class="badge bg-danger">@lang('trans.rejected')</span>
                                                            <button type="button" class="btn btn-success btn-sm ml-2"
                                                                onclick="openDecisionModal('licence_demandeurs', '{{ $licence_demandeur->id }}', '{{ $demande->id }}', 'approve')">
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
                 @endisset
                 @isset($formation_demandeurs)
                <div class="card">
                    <div class="card-header bg-primary text-white">
                        @lang('trans.training')
                    </div>
                    <div class="card-body">
                       
                            <div class="row">
                                <div class="col-lg-12 table-responsive">
                                    <table class="table table-striped table-bordered">
                                        <thead>
                                            <tr>

                                                <th>@lang('trans.training_date')</th>
                                                <th>@lang('trans.training_center')</th>
                                                <th>@lang('trans.training_location')</th>
                                                <th>@lang('trans.proof')</th>
                                                <th>@lang('trans.actions')</th>
                                            </tr>
                                        </thead>
                                        <tbody>
                                            @foreach ($formation_demandeurs as $formation_demandeur)
                                                <tr>

                                                    <td>{{ $formation_demandeur->date_formation }}</td>
                                                    <td>{{ $formation_demandeur->centre_formation }}</td>
                                                    <td>{{ $formation_demandeur->lieu }}</td>
                                                    <td>
                                                        @if ($formation_demandeur->document)
                                                            <button class="btn btn-primary"
                                                                onclick="openPdfModal('{{ asset('/uploads/' . $formation_demandeur->document) }}')"><i
                                                                    class="fas fa-eye"></i></button>
                                                        @endif

                                                    </td>
                                                    <td>
                                                       <button type="button" class="btn btn-warning btn-sm"
                                                            onclick="openDeleteModal('formation_demandeurs', '{{ $formation_demandeur->id }}', '{{ $demande->id }}')">
                                                            <i class="fas fa-trash"></i> @lang('trans.delete')
                                                        </button>
                                                        @if (is_null($formation_demandeur->valider))
                                                            <!-- Unreviewed state - show both approve and reject buttons -->
                                                            <button type="button" class="btn btn-success btn-sm mr-1"
                                                                onclick="openDecisionModal('formation_demandeurs', '{{ $formation_demandeur->id }}', '{{ $demande->id }}', 'approve')">
                                                                <i class="fas fa-check"></i> @lang('trans.approve')
                                                            </button>
                                                            <button type="button" class="btn btn-danger btn-sm"
                                                                onclick="openDecisionModal('formation_demandeurs', '{{ $formation_demandeur->id }}', '{{ $demande->id }}', 'reject')">
                                                                <i class="fas fa-times"></i> @lang('trans.reject')
                                                            </button>
                                                        @elseif ($formation_demandeur->valider == 1)
                                                            <!-- Approved state - show status and reject option -->
                                                            <span class="badge bg-success">
                                                                <i class="fas fa-check-circle"></i> @lang('trans.approved')
                                                            </span>
                                                            <button type="button" class="btn btn-danger btn-sm ml-2"
                                                                onclick="openDecisionModal('formation_demandeurs', '{{ $formation_demandeur->id }}', '{{ $demande->id }}', 'reject')">
                                                                <i class="fas fa-times"></i> @lang('trans.reject')
                                                            </button>
                                                        @else
                                                            <!-- Rejected state - show status and approve option -->
                                                            <span class="badge bg-danger">
                                                                <i class="fas fa-times-circle"></i> @lang('trans.rejected')
                                                            </span>
                                                            <button type="button" class="btn btn-success btn-sm ml-2"
                                                                onclick="openDecisionModal('formation_demandeurs', '{{ $formation_demandeur->id }}', '{{ $demande->id }}', 'approve')">
                                                                <i class="fas fa-check"></i> @lang('trans.approve')
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
                @endisset
                @isset($qualification_demandeurs)
                <div class="card">
                    <div class="card-header bg-primary text-white">
                        @lang('trans.ratings')
                    </div>
                    <div class="card-body">
                        <br>
                        
                            <div class="row">
                                <div class="col-lg-12 table-responsive">
                                    <table class="table table-striped table-bordered">
                                        <thead>
                                            <tr>
                                                <th>@lang('trans.ratings')</th>
                                                @if (in_array($demande->typeLicence->id, [27, 28, 29, 30, 31, 32, 37, 38, 39]))
                                                    <th>@lang('trans.plane_type')</th>
                                                    <th>@lang('trans.machine')</th>
                                                @endif
                                                @if (in_array($demande->typeLicence->id, [27, 28, 29, 30, 31, 32]))
                                                    <th>@lang('trans.engine_type')</th>
                                                @endif
                                                @if ($demande->typeLicence->id !== 33)
                                                    <th>@lang('trans.privilege')</th>
                                                @endif
                                                @if (in_array($demande->typeLicence->id, [37, 38]))
                                                    <th>@lang('trans.amt')</th>
                                                @endif
                                                @if ($demande->typeLicence->id === 35)
                                                    <th>@lang('trans.atc')</th>
                                                @endif
                                                @if ($demande->typeLicence->id === 34)
                                                    <th>@lang('trans.rpa')</th>
                                                @endif
                                                @if ($demande->typeLicence->id === 33)
                                                    <th>@lang('trans.ulm')</th>
                                                @endif
                                                <th>@lang('trans.exam_date')</th>
                                                <th>@lang('trans.training_center')</th>
                                                <th>@lang('trans.location')</th>
                                                <th>@lang('trans.proof')</th>
                                                <th>@lang('trans.actions') </th>
                                            </tr>
                                        </thead>
                                        <tbody>
                                            @foreach ($qualification_demandeurs as $qualification_demandeur)
                                                <tr>
                                                    <td>{{ $qualification_demandeur->qualification }}</td>
                                                    @if (in_array($demande->typeLicence->id, [27, 28, 29, 30, 31, 32, 37, 38, 39]))
                                                        <td>{{ optional($qualification_demandeur->typeAvion)->code }}</td>
                                                        <td>{{ $qualification_demandeur->machine }}</td>
                                                    @endif
                                                    @if (in_array($demande->typeLicence->id, [27, 28, 29, 30, 31, 32]))
                                                        <td>{{ $qualification_demandeur->type_moteur }}</td>
                                                    @endif
                                                    @if ($demande->typeLicence->id !== 33)
                                                        <td>{{ $qualification_demandeur->type_privilege }}</td>
                                                    @endif
                                                    @if (in_array($demande->typeLicence->id, [37, 38]))
                                                        <td>{{ $qualification_demandeur->amt_display }}</td>
                                                    @endif
                                                    @if ($demande->typeLicence->id === 35)
                                                        <td>{{ $qualification_demandeur->atc_display }}</td>
                                                    @endif
                                                    @if ($demande->typeLicence->id === 34)
                                                        <td>{{ $qualification_demandeur->rpa }}</td>
                                                    @endif
                                                    @if ($demande->typeLicence->id === 33)
                                                        <td>{{ $qualification_demandeur->ulm }}</td>
                                                    @endif
                                                    <td>{{ $qualification_demandeur->date_examen }}</td>
                                                    <td>{{ $qualification_demandeur->centre_formation }}</td>
                                                    <td>{{ $qualification_demandeur->lieu }}</td>
                                                    <td>
                                                        @if ($qualification_demandeur->document)
                                                            <button class="btn btn-primary"
                                                                onclick="openPdfModal('{{ asset('/uploads/' . $qualification_demandeur->document) }}')"><i
                                                                    class="fas fa-eye"></i></button>
                                                        @endif

                                                    </td>
                                                    <td>
                                                       <button type="button" class="btn btn-warning btn-sm"
                                                            onclick="openDeleteModal('qualification_demandeurs', '{{ $qualification_demandeur->id }}', '{{ $demande->id }}')">
                                                            <i class="fas fa-trash"></i> @lang('trans.delete')
                                                        </button>
                                                        @if (is_null($qualification_demandeur->valider))
                                                            <!-- Unreviewed state - show both approve and reject buttons -->
                                                            <button type="button" class="btn btn-success btn-sm mr-1"
                                                                onclick="openDecisionModal('qualification_demandeurs', '{{ $qualification_demandeur->id }}', '{{ $demande->id }}', 'approve')">
                                                                <i class="fas fa-check"></i> @lang('trans.approve')
                                                            </button>
                                                            <button type="button" class="btn btn-danger btn-sm"
                                                                onclick="openDecisionModal('qualification_demandeurs', '{{ $qualification_demandeur->id }}', '{{ $demande->id }}', 'reject')">
                                                                <i class="fas fa-times"></i> @lang('trans.reject')
                                                            </button>
                                                        @elseif ($qualification_demandeur->valider == 1)
                                                            <!-- Approved state - show status and reject option -->
                                                            <span class="badge bg-success">
                                                                <i class="fas fa-check-circle"></i> @lang('trans.approved')
                                                            </span>
                                                            <button type="button" class="btn btn-danger btn-sm ml-2"
                                                                onclick="openDecisionModal('qualification_demandeurs', '{{ $qualification_demandeur->id }}', '{{ $demande->id }}', 'reject')">
                                                                <i class="fas fa-times"></i> @lang('trans.reject')
                                                            </button>
                                                        @else
                                                            <!-- Rejected state - show status and approve option -->
                                                            <span class="badge bg-danger">
                                                                <i class="fas fa-times-circle"></i> @lang('trans.rejected')
                                                            </span>
                                                            <button type="button" class="btn btn-success btn-sm ml-2"
                                                                onclick="openDecisionModal('qualification_demandeurs', '{{ $qualification_demandeur->id }}', '{{ $demande->id }}', 'approve')">
                                                                <i class="fas fa-check"></i> @lang('trans.approve')
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
                @endisset
                @isset($experience_demandeurs)
                <div class="card">
                    <div class="card-header bg-primary text-white">
                        @lang('trans.flights')
                    </div>

                    <div class="card-body">
                        
                            <div class="row">
                                <div class="col-lg-12 table-responsive">
                                    <table class="table table-striped table-bordered">
                                        <thead>
                                            <tr>

                                                <th>@lang('trans.flights_type')</th>
                                                <th>@lang('trans.total')</th>
                                                <th>@lang('trans.six')</th>
                                                <th>@lang('trans.three')</th>
                                                <th>@lang('trans.proof')</th>
                                                <th>@lang('trans.actions')</th>
                                            </tr>
                                        </thead>
                                        <tbody>
                                            @foreach ($experience_demandeurs as $experience_demandeur)
                                                <tr>
                                                    <td>{{ $experience_demandeur->nature }}</td>
                                                    <td>{{ $experience_demandeur->total }}</td>
                                                    <td>{{ $experience_demandeur->six_mois }}</td>
                                                    <td>{{ $experience_demandeur->trois_mois }}</td>
                                                    <td>
                                                        @if ($experience_demandeur->document)
                                                            <button class="btn btn-primary"
                                                                onclick="openPdfModal('{{ asset('/uploads/' . $experience_demandeur->document) }}')"><i
                                                                    class="fas fa-eye"></i></button>
                                                        @endif

                                                    </td>
                                                    <td>
                                                       <button type="button" class="btn btn-warning btn-sm"
                                                            onclick="openDeleteModal('experience_demandeurs', '{{ $experience_demandeur->id }}', '{{ $demande->id }}')">
                                                            <i class="fas fa-trash"></i> @lang('trans.delete')
                                                        </button>
                                                        @if (is_null($experience_demandeur->valider))
                                                            <!-- Unreviewed state - show both approve and reject buttons -->
                                                            <button type="button" class="btn btn-success btn-sm mr-1"
                                                                onclick="openDecisionModal('experience_demandeurs', '{{ $experience_demandeur->id }}', '{{ $demande->id }}', 'approve')">
                                                                <i class="fas fa-check"></i> @lang('trans.approve')
                                                            </button>
                                                            <button type="button" class="btn btn-danger btn-sm"
                                                                onclick="openDecisionModal('experience_demandeurs', '{{ $experience_demandeur->id }}', '{{ $demande->id }}', 'reject')">
                                                                <i class="fas fa-times"></i> @lang('trans.reject')
                                                            </button>
                                                        @elseif ($experience_demandeur->valider == 1)
                                                            <!-- Approved state - show status and reject option -->
                                                            <span class="badge bg-success">
                                                                <i class="fas fa-check-circle"></i> @lang('trans.approved')
                                                            </span>
                                                            <button type="button" class="btn btn-danger btn-sm ml-2"
                                                                onclick="openDecisionModal('experience_demandeurs', '{{ $experience_demandeur->id }}', '{{ $demande->id }}', 'reject')">
                                                                <i class="fas fa-times"></i> @lang('trans.reject')
                                                            </button>
                                                        @else
                                                            <!-- Rejected state - show status and approve option -->
                                                            <span class="badge bg-danger">
                                                                <i class="fas fa-times-circle"></i> @lang('trans.rejected')
                                                            </span>
                                                            <button type="button" class="btn btn-success btn-sm ml-2"
                                                                onclick="openDecisionModal('experience_demandeurs', '{{ $experience_demandeur->id }}', '{{ $demande->id }}', 'approve')">
                                                                <i class="fas fa-check"></i> @lang('trans.approve')
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
                @endisset
                @isset($competence_demandeurs)
                <div class="card">
                    <div class="card-header bg-primary text-white">
                        @lang('trans.control')
                    </div>
                    <div class="card-body">
                        
                            <div class="row">
                                <div class="col-lg-12 table-responsive">
                                    <table class="table table-striped table-bordered">
                                        <thead>
                                            <tr>

                                                <th> @lang('trans.type')</th>
                                                <th> @lang('trans.level')</th>
                                                <th> @lang('trans.date')</th>
                                                <th> @lang('trans.validity')</th>
                                                <th> @lang('trans.location')</th>
                                                <th> @lang('trans.proof')</th>
                                                <th> @lang('trans.actions')</th>
                                            </tr>
                                        </thead>
                                        <tbody>
                                            @foreach ($competence_demandeurs as $competence_demandeur)
                                                <tr>
                                                    <td>{{ $competence_demandeur->type }}</td>
                                                    <td>{{ $competence_demandeur->niveau }}</td>
                                                    <td>{{ $competence_demandeur->date }}</td>
                                                    <td>{{ $competence_demandeur->validite }}</td>
                                                    <td>{{ $competence_demandeur->centre_formation }}</td>
                                                    <td>
                                                        @if ($competence_demandeur->document)
                                                            <button class="btn btn-primary"
                                                                onclick="openPdfModal('{{ asset('/uploads/' . $competence_demandeur->document) }}')"><i
                                                                    class="fas fa-eye"></i></button>
                                                        @endif

                                                    </td>
                                                    <td>
                                                       <button type="button" class="btn btn-warning btn-sm"
                                                            onclick="openDeleteModal('competence_demandeurs', '{{ $competence_demandeur->id }}', '{{ $demande->id }}')">
                                                            <i class="fas fa-trash"></i> @lang('trans.delete')
                                                        </button>
                                                        @if (is_null($competence_demandeur->valider))
                                                            <!-- Unreviewed state - show both approve and reject buttons -->
                                                            <button type="button" class="btn btn-success btn-sm mr-1"
                                                                onclick="openDecisionModal('competence_demandeurs', '{{ $competence_demandeur->id }}', '{{ $demande->id }}', 'approve')">
                                                                <i class="fas fa-check"></i> @lang('trans.approve')
                                                            </button>
                                                            <button type="button" class="btn btn-danger btn-sm"
                                                                onclick="openDecisionModal('competence_demandeurs', '{{ $competence_demandeur->id }}', '{{ $demande->id }}', 'reject')">
                                                                <i class="fas fa-times"></i> @lang('trans.reject')
                                                            </button>
                                                        @elseif ($competence_demandeur->valider == 1)
                                                            <!-- Approved state - show status and reject option -->
                                                            <span class="badge bg-success">
                                                                <i class="fas fa-check-circle"></i> @lang('trans.approved')
                                                            </span>
                                                            <button type="button" class="btn btn-danger btn-sm ml-2"
                                                                onclick="openDecisionModal('competence_demandeurs', '{{ $competence_demandeur->id }}', '{{ $demande->id }}', 'reject')">
                                                                <i class="fas fa-times"></i> @lang('trans.reject')
                                                            </button>
                                                        @else
                                                            <!-- Rejected state - show status and approve option -->
                                                            <span class="badge bg-danger">
                                                                <i class="fas fa-times-circle"></i> @lang('trans.rejected')
                                                            </span>
                                                            <button type="button" class="btn btn-success btn-sm ml-2"
                                                                onclick="openDecisionModal('competence_demandeurs', '{{ $competence_demandeur->id }}', '{{ $demande->id }}', 'approve')">
                                                                <i class="fas fa-check"></i> @lang('trans.approve')
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
                @endisset
                @isset($entrainement_demandeurs)
                <div class="card">
                    <div class="card-header bg-primary text-white">
                        @lang('trans.periodic_control')
                    </div>
                    <div class="card-body">
                        
                            <div class="row">
                                <div class="col-lg-12 table-responsive">
                                    <table class="table table-striped table-bordered">
                                        <thead>
                                            <tr>

                                                <th>@lang('trans.type')</th>

                                                <th>@lang('trans.date')</th>
                                                <th>@lang('trans.validity')</th>
                                                <th>@lang('trans.location')</th>
                                                <th>@lang('trans.proof')</th>
                                                <th>@lang('trans.actions')</th>
                                            </tr>
                                        </thead>
                                        <tbody>
                                            @foreach ($entrainement_demandeurs as $entrainement_demandeur)
                                                <tr>
                                                    <td>{{ $entrainement_demandeur->type }}</td>
                                                    <td>{{ $entrainement_demandeur->date }}</td>
                                                    <td>{{ $entrainement_demandeur->validite }}</td>
                                                    <td>{{ $entrainement_demandeur->centre_formation }}</td>
                                                    <td>
                                                        @if ($entrainement_demandeur->document)
                                                            <button class="btn btn-primary"
                                                                onclick="openPdfModal('{{ asset('/uploads/' . $entrainement_demandeur->document) }}')"><i
                                                                    class="fas fa-eye"></i></button>
                                                        @endif

                                                    </td>
                                                    <td>
                                                         <button type="button" class="btn btn-warning btn-sm"
                                                            onclick="openDeleteModal('training_demandeurs', '{{ $entrainement_demandeur->id }}', '{{ $demande->id }}')">
                                                            <i class="fas fa-trash"></i> @lang('trans.delete')
                                                        </button>
                                                        @if (is_null($entrainement_demandeur->valider))
                                                            <!-- Unreviewed state - show both approve and reject buttons -->
                                                            <button type="button" class="btn btn-success btn-sm mr-1"
                                                                onclick="openDecisionModal('training_demandeurs', '{{ $entrainement_demandeur->id }}', '{{ $demande->id }}', 'approve')">
                                                                <i class="fas fa-check"></i> @lang('trans.approve')
                                                            </button>
                                                            <button type="button" class="btn btn-danger btn-sm"
                                                                onclick="openDecisionModal('training_demandeurs', '{{ $entrainement_demandeur->id }}', '{{ $demande->id }}', 'reject')">
                                                                <i class="fas fa-times"></i> @lang('trans.reject')
                                                            </button>
                                                        @elseif ($entrainement_demandeur->valider == 1)
                                                            <!-- Approved state - show status and reject option -->
                                                            <span class="badge bg-success">
                                                                <i class="fas fa-check-circle"></i> @lang('trans.approved')
                                                            </span>
                                                            <button type="button" class="btn btn-danger btn-sm ml-2"
                                                                onclick="openDecisionModal('training_demandeurs', '{{ $entrainement_demandeur->id }}', '{{ $demande->id }}', 'reject')">
                                                                <i class="fas fa-times"></i> @lang('trans.reject')
                                                            </button>
                                                        @else
                                                            <!-- Rejected state - show status and approve option -->
                                                            <span class="badge bg-danger">
                                                                <i class="fas fa-times-circle"></i> @lang('trans.rejected')
                                                            </span>
                                                            <button type="button" class="btn btn-success btn-sm ml-2"
                                                                onclick="openDecisionModal('training_demandeurs', '{{ $entrainement_demandeur->id }}', '{{ $demande->id }}', 'approve')">
                                                                <i class="fas fa-check"></i> @lang('trans.approve')
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
                 @endisset
                {{-- Interupptions --}}
                @isset($interruption_demandeurs)
                <div class="card">
                    <div class="card-header bg-primary text-white">
                        @lang('trans.interruptions')
                    </div>

                    <div class="card-body">
                        
                            <div class="row">
                                <div class="col-lg-12 table-responsive">
                                    <table class="table table-striped table-bordered">
                                        <thead>
                                            <tr>
                                                <th>@lang('trans.start_date')</th>
                                                <th>@lang('trans.end_date')</th>
                                                <th>@lang('trans.reason')</th>
                                                <th>@lang('trans.proof')</th>
                                                <th>@lang('trans.actions')</th>
                                            </tr>
                                        </thead>
                                        <tbody>
                                            @foreach ($interruption_demandeurs as $interruption_demandeur)
                                                <tr>
                                                    <td>{{ $interruption_demandeur->date_debut }}</td>
                                                    <td>{{ $interruption_demandeur->date_fin }}</td>
                                                    <td>{{ $interruption_demandeur->raison }}</td>
                                                    <td>
                                                        @if ($interruption_demandeur->document)
                                                            <button class="btn btn-primary"
                                                                onclick="openPdfModal('{{ asset('/uploads/' . $interruption_demandeur->document) }}')"><i
                                                                    class="fas fa-eye"></i></button>
                                                        @endif

                                                    </td>
                                                    <td>
                                                       <button type="button" class="btn btn-warning btn-sm"
                                                            onclick="openDeleteModal('interruption_demandeurs', '{{ $interruption_demandeur->id }}', '{{ $demande->id }}')">
                                                            <i class="fas fa-trash"></i> @lang('trans.delete')
                                                        </button>
                                                        @if (is_null($interruption_demandeur->valider))
                                                            <!-- Unreviewed state - show both approve and reject buttons -->
                                                            <button type="button" class="btn btn-success btn-sm mr-1"
                                                                onclick="openDecisionModal('interruption_demandeurs', '{{ $interruption_demandeur->id }}', '{{ $demande->id }}', 'approve')">
                                                                <i class="fas fa-check"></i> @lang('trans.approve')
                                                            </button>
                                                            <button type="button" class="btn btn-danger btn-sm"
                                                                onclick="openDecisionModal('interruption_demandeurs', '{{ $interruption_demandeur->id }}', '{{ $demande->id }}', 'reject')">
                                                                <i class="fas fa-times"></i> @lang('trans.reject')
                                                            </button>
                                                        @elseif ($interruption_demandeur->valider == 1)
                                                            <!-- Approved state - show status and reject option -->
                                                            <span class="badge bg-success">
                                                                <i class="fas fa-check-circle"></i> @lang('trans.approved')
                                                            </span>
                                                            <button type="button" class="btn btn-danger btn-sm ml-2"
                                                                onclick="openDecisionModal('interruption_demandeurs', '{{ $interruption_demandeur->id }}', '{{ $demande->id }}', 'reject')">
                                                                <i class="fas fa-times"></i> @lang('trans.reject')
                                                            </button>
                                                        @else
                                                            <!-- Rejected state - show status and approve option -->
                                                            <span class="badge bg-danger">
                                                                <i class="fas fa-times-circle"></i> @lang('trans.rejected')
                                                            </span>
                                                            <button type="button" class="btn btn-success btn-sm ml-2"
                                                                onclick="openDecisionModal('interruption_demandeurs', '{{ $interruption_demandeur->id }}', '{{ $demande->id }}', 'approve')">
                                                                <i class="fas fa-check"></i> @lang('trans.approve')
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
                @endisset
                {{-- Expérience en maintenance d'aéronefs --}}
                @isset($experience_maintenance_demandeurs)
                <div class="card">
                    <div class="card-header bg-primary text-white">

                        @lang('trans.maintenance')
                    </div>

                    <div class="card-body">
                        
                            <div class="row">
                                <div class="col-lg-12 table-responsive">
                                    <table class="table table-striped table-bordered">
                                        <thead>
                                            <tr>



                                                <th>@lang('trans.start_date')</th>
                                                <th>@lang('trans.end_date')</th>
                                                <th>@lang('trans.description')</th>
                                                <th>@lang('trans.proof')</th>
                                                <th>@lang('trans.actions')</th>
                                            </tr>
                                        </thead>
                                        <tbody>
                                            @foreach ($experience_maintenance_demandeurs as $experience_maintenance_demandeur)
                                                <tr>
                                                    <td>{{ $experience_maintenance_demandeur->date_debut }}</td>
                                                    <td>{{ $experience_maintenance_demandeur->date_fin }}</td>
                                                    <td>{{ $experience_maintenance_demandeur->description_maintenance }}
                                                    </td>
                                                    <td>
                                                        @if ($experience_maintenance_demandeur->document)
                                                            <button class="btn btn-primary"
                                                                onclick="openPdfModal('{{ asset('/uploads/' . $experience_maintenance_demandeur->document) }}')"><i
                                                                    class="fas fa-eye"></i></button>
                                                        @endif

                                                    </td>
                                                    <td>
                                                         <button type="button" class="btn btn-warning btn-sm"
                                                            onclick="openDeleteModal('experience_maintenance_demandeurs', '{{ $experience_maintenance_demandeur->id }}', '{{ $demande->id }}')">
                                                            <i class="fas fa-trash"></i> @lang('trans.delete')
                                                        </button>
                                                        @if (is_null($experience_maintenance_demandeur->valider))
                                                            <!-- Unreviewed state - show both approve and reject buttons -->
                                                            <button type="button" class="btn btn-success btn-sm mr-1"
                                                                onclick="openDecisionModal('experience_maintenance_demandeurs', '{{ $experience_maintenance_demandeur->id }}', '{{ $demande->id }}', 'approve')">
                                                                <i class="fas fa-check"></i> @lang('trans.approve')
                                                            </button>
                                                            <button type="button" class="btn btn-danger btn-sm"
                                                                onclick="openDecisionModal('experience_maintenance_demandeurs', '{{ $experience_maintenance_demandeur->id }}', '{{ $demande->id }}', 'reject')">
                                                                <i class="fas fa-times"></i> @lang('trans.reject')
                                                            </button>
                                                        @elseif ($experience_maintenance_demandeur->valider == 1)
                                                            <!-- Approved state - show status and reject option -->
                                                            <span class="badge bg-success">
                                                                <i class="fas fa-check-circle"></i> @lang('trans.approved')
                                                            </span>
                                                            <button type="button" class="btn btn-danger btn-sm ml-2"
                                                                onclick="openDecisionModal('experience_maintenance_demandeurs', '{{ $experience_maintenance_demandeur->id }}', '{{ $demande->id }}', 'reject')">
                                                                <i class="fas fa-times"></i> @lang('trans.reject')
                                                            </button>
                                                        @else
                                                            <!-- Rejected state - show status and approve option -->
                                                            <span class="badge bg-danger">
                                                                <i class="fas fa-times-circle"></i> @lang('trans.rejected')
                                                            </span>
                                                            <button type="button" class="btn btn-success btn-sm ml-2"
                                                                onclick="openDecisionModal('experience_maintenance_demandeurs', '{{ $experience_maintenance_demandeur->id }}', '{{ $demande->id }}', 'approve')">
                                                                <i class="fas fa-check"></i> @lang('trans.approve')
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
                 @endisset
                {{-- Employeurs --}}
                @isset($employeur_demandeurs)
                <div class="card">
                    <div class="card-header bg-primary text-white">
                        @lang('trans.employers')
                    </div>

                    <div class="card-body">
                        
                            <div class="row">
                                <div class="col-lg-12 table-responsive">
                                    <table class="table table-striped table-bordered">
                                        <thead>
                                            <tr>


                                                <th>@lang('trans.employer')</th>
                                                <th>@lang('trans.start_date')</th>
                                                <th>@lang('trans.end_date')</th>
                                                <th>@lang('trans.role')</th>
                                                <th>@lang('trans.proof')</th>
                                                <th>@lang('trans.actions')</th>
                                            </tr>
                                        </thead>
                                        <tbody>
                                            @foreach ($employeur_demandeurs as $employeur_demandeur)
                                                <tr>
                                                    <td>{{ $employeur_demandeur->employeur }}</td>
                                                    <td>{{ $employeur_demandeur->periode_du }}</td>
                                                    <td>{{ $employeur_demandeur->periode_au }}</td>
                                                    <td>{{ $employeur_demandeur->fonction }}</td>
                                                    <td>
                                                        @if ($employeur_demandeur->document)
                                                            <button class="btn btn-primary"
                                                                onclick="openPdfModal('{{ asset('/uploads/' . $employeur_demandeur->document) }}')"><i
                                                                    class="fas fa-eye"></i></button>
                                                        @endif

                                                    </td>
                                                    <td>
                                                       <button type="button" class="btn btn-warning btn-sm"
                                                            onclick="openDeleteModal('employeur_demandeurs', '{{ $employeur_demandeur->id }}', '{{ $demande->id }}')">
                                                            <i class="fas fa-trash"></i> @lang('trans.delete')
                                                        </button>
                                                        @if (is_null($employeur_demandeur->valider))
                                                            <!-- Unreviewed state - show both approve and reject buttons -->
                                                            <button type="button" class="btn btn-success btn-sm mr-1"
                                                                onclick="openDecisionModal('employeur_demandeurs', '{{ $employeur_demandeur->id }}', '{{ $demande->id }}', 'approve')">
                                                                <i class="fas fa-check"></i> @lang('trans.approve')
                                                            </button>
                                                            <button type="button" class="btn btn-danger btn-sm"
                                                                onclick="openDecisionModal('employeur_demandeurs', '{{ $employeur_demandeur->id }}', '{{ $demande->id }}', 'reject')">
                                                                <i class="fas fa-times"></i> @lang('trans.reject')
                                                            </button>
                                                        @elseif ($employeur_demandeur->valider == 1)
                                                            <!-- Approved state - show status and reject option -->
                                                            <span class="badge bg-success">
                                                                <i class="fas fa-check-circle"></i> @lang('trans.approved')
                                                            </span>
                                                            <button type="button" class="btn btn-danger btn-sm ml-2"
                                                                onclick="openDecisionModal('employeur_demandeurs', '{{ $employeur_demandeur->id }}', '{{ $demande->id }}', 'reject')">
                                                                <i class="fas fa-times"></i> @lang('trans.reject')
                                                            </button>
                                                        @else
                                                            <!-- Rejected state - show status and approve option -->
                                                            <span class="badge bg-danger">
                                                                <i class="fas fa-times-circle"></i> @lang('trans.rejected')
                                                            </span>
                                                            <button type="button" class="btn btn-success btn-sm ml-2"
                                                                onclick="openDecisionModal('employeur_demandeurs', '{{ $employeur_demandeur->id }}', '{{ $demande->id }}', 'approve')">
                                                                <i class="fas fa-check"></i> @lang('trans.approve')
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
                @endisset
                {{-- --}}
                @isset($documents)
                <div class="card">
                    <div class="card-header bg-primary text-white">
                        @lang('trans.attachments')
                    </div>

                    <div class="card-body">
                        
                            <div class="row">
                                <div class="col-lg-12 table-responsive">
                                    <table class="table table-striped table-bordered">
                                        <thead>
                                            <tr>

                                                <th>@lang('trans.title')</th>

                                                <th>@lang('trans.attachment')</th>
                                                <th>@lang('trans.actions')</th>
                                            </tr>
                                        </thead>
                                        <tbody>
                                            @foreach ($documents as $document)
                                                <tr>
                                                    <td>{{ LaravelLocalization::getCurrentLocale() == 'fr' ? $document->nom_fr : $document->nom_en }}
                                                    </td>
                                                    <td>
                                                        @if (!empty($document->url))
                                                            <button class="btn btn-primary"
                                                                onclick="openPdfModal('{{ asset('/uploads/' . $document->url) }}')"><i
                                                                    class="fas fa-eye"></i></button>
                                                        @else
                                                            -
                                                        @endif

                                                    </td>
                                                    <td>
                                                       <button type="button" class="btn btn-warning btn-sm"
                                                            onclick="openDeleteModal('documents', '{{ $document->id }}', '{{ $demande->id }}')">
                                                            <i class="fas fa-trash"></i> @lang('trans.delete')
                                                        </button>
                                                        @if (is_null($document->valider))
                                                            <!-- Unreviewed state - show both approve and reject buttons -->
                                                            <button type="button" class="btn btn-success btn-sm mr-1"
                                                                onclick="openDecisionModal('documents', '{{ $document->id }}', '{{ $demande->id }}', 'approve')">
                                                                <i class="fas fa-check"></i> @lang('trans.approve')
                                                            </button>
                                                            <button type="button" class="btn btn-danger btn-sm"
                                                                onclick="openDecisionModal('documents', '{{ $document->id }}', '{{ $demande->id }}', 'reject')">
                                                                <i class="fas fa-times"></i> @lang('trans.reject')
                                                            </button>
                                                        @elseif ($document->valider == 1)
                                                            <!-- Approved state - show status and reject option -->
                                                            <span class="badge bg-success">
                                                                <i class="fas fa-check-circle"></i> @lang('trans.approved')
                                                            </span>
                                                            <button type="button" class="btn btn-danger btn-sm ml-2"
                                                                onclick="openDecisionModal('documents', '{{ $document->id }}', '{{ $demande->id }}', 'reject')">
                                                                <i class="fas fa-times"></i> @lang('trans.reject')
                                                            </button>
                                                        @else
                                                            <!-- Rejected state - show status and approve option -->
                                                            <span class="badge bg-danger">
                                                                <i class="fas fa-times-circle"></i> @lang('trans.rejected')
                                                            </span>
                                                            <button type="button" class="btn btn-success btn-sm ml-2"
                                                                onclick="openDecisionModal('documents', '{{ $document->id }}', '{{ $demande->id }}', 'approve')">
                                                                <i class="fas fa-check"></i> @lang('trans.approve')
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
                @endisset

            </div>
            <!-- /.card-body -->
        </div>

@if (isset($demande) && $demande)
            <div class="row">
            <div class="col-md-12">
    <div class="card">
        <div class="card-header bg-info text-white">
            @lang('trans.document_pieces')
        </div>

        <div class="card-body">
            <form id="pieceForm" enctype="multipart/form-data">
                @csrf
                <input type="hidden" value="{{ $demande->id }}" id="demande_id" name="demande_id">
                
                <div class="row">
                    <div class="col-lg-5">
                        <div class="form-group">
                            <label for="titre">@lang('trans.title')</label>
                            <input type="text" class="form-control" id="titre" name="titre" 
                                   placeholder="@lang('trans.enter_title')" required>
                        </div>
                    </div>

                    <div class="col-lg-5">
                        <div class="form-group">
                            <label for="document">@lang('trans.document')</label>
                            <input type="file" class="form-control" id="document" name="document" 
                                   placeholder="" accept=".pdf,.doc,.docx,.jpg,.jpeg,.png" required>
                            <small class="text-muted">@lang('trans.allowed_formats'): PDF, DOC, DOCX, JPG, JPEG, PNG (Max: 10MB)</small>
                        </div>
                    </div>

                    <div class="col-lg-2">
                        <div class="form-group">
                            <label>&nbsp;</label>
                            <button type="submit" class="btn btn-success form-control">
                                <i class="fas fa-plus"></i> @lang('trans.add')
                            </button>
                        </div>
                    </div>
                </div>
            </form>

            <br>

            @if(isset($demande->pieces) && $demande->pieces->count() > 0)
                <div class="row">
                    <div class="col-lg-12">
                        <table class="table table-striped table-bordered">
                            <thead>
                                <tr>
                                    <th>@lang('trans.title')</th>
                                    <th>@lang('trans.document')</th>
                                    <th>@lang('trans.date_added')</th>
                                    <th>@lang('trans.actions')</th>
                                </tr>
                            </thead>
                            <tbody>
                                @foreach ($demande->pieces as $piece)
                                    <tr>
                                        <td>{{ $piece->titre }}</td>
                                        <td>
                                            @if ($piece->url)
                                                <button class="btn btn-sm btn-info" 
                                                        onclick="openPdfModal('{{ asset('/uploads/pieces/' . $piece->url) }}')">
                                                    <i class="fas fa-eye"></i> @lang('trans.view')
                                                </button>
                                                
                                            @else
                                                <span class="badge badge-warning">@lang('trans.no_file')</span>
                                            @endif
                                        </td>
                                        <td>{{ $piece->created_at ? $piece->created_at->format('d/m/Y H:i') : '' }}</td>
                                        <td>
                                            <button class="btn btn-warning btn-sm edit-piece" 
                                                    data-id="{{ $piece->id }}"
                                                    data-titre="{{ $piece->titre }}">
                                                <i class="fas fa-edit"></i> @lang('trans.update')
                                            </button>
                                            <button class="btn btn-danger btn-sm delete-piece" 
                                                    data-id="{{ $piece->id }}">
                                                <i class="fas fa-trash"></i> @lang('trans.destroy')
                                            </button>
                                        </td>
                                    </tr>

                                    <!-- Edit Form (Hidden) -->
                                    <tr id="edit-form-piece-{{ $piece->id }}" style="display: none;">
                                        <td colspan="4">
                                            <form id="updatePieceForm-{{ $piece->id }}" enctype="multipart/form-data">
                                                @csrf
                                                @method('PUT')
                                                <input type="hidden" name="piece_id" value="{{ $piece->id }}">

                                                <div class="row">
                                                    <div class="col-lg-5">
                                                        <div class="form-group">
                                                            <label for="edit_titre_{{ $piece->id }}">@lang('trans.title')</label>
                                                            <input type="text" class="form-control" 
                                                                   name="titre" id="edit_titre_{{ $piece->id }}"
                                                                   value="{{ $piece->titre }}" required>
                                                        </div>
                                                    </div>

                                                    <div class="col-lg-5">
                                                        <div class="form-group">
                                                            <label for="edit_document_{{ $piece->id }}">@lang('trans.document')</label>
                                                            <input type="file" class="form-control" 
                                                                   name="document" accept=".pdf,.doc,.docx,.jpg,.jpeg,.png">
                                                            @if($piece->url)
                                                                <small class="text-muted">
                                                                    @lang('trans.current_file'): {{ $piece->url }}
                                                                </small>
                                                            @endif
                                                        </div>
                                                    </div>

                                                    <div class="col-lg-2">
                                                        <div class="form-group">
                                                            <label>&nbsp;</label>
                                                            <button type="submit" class="btn btn-primary btn-sm form-control update-piece" 
                                                                    data-id="{{ $piece->id }}">
                                                                @lang('trans.save')
                                                            </button>
                                                        </div>
                                                    </div>
                                                </div>
                                                <div class="row">
                                                    <div class="col-lg-12">
                                                        <button type="button" class="btn btn-secondary btn-sm" 
                                                                onclick="togglePieceEditForm({{ $piece->id }})">
                                                            @lang('trans.cancel')
                                                        </button>
                                                    </div>
                                                </div>
                                            </form>
                                        </td>
                                    </tr>
                                @endforeach
                            </tbody>
                        </table>
                    </div>
                </div>
            @else
                <div class="alert alert-info">
                    @lang('trans.no_pieces_added')
                </div>
            @endif
        </div>
    </div>
            </div>
        </div>

@endif
<div class="row">
            <div class="col-md-12">
                <div class="card">
                    <div class="card-header bg-primary text-white">
                        Checklist
                        @if (!empty($demande->checklist_admin))
                            <div class="card-tools">
                                <a href="{{ asset('uploads/' . $demande->checklist_admin) }}" target="_blank"
                                    class="btn btn-sm btn-success">
                                    <i class="fas fa-eye"></i> @lang('trans.view')
                                </a>
                            </div>
                        @endif
                    </div>
                    <div class="card-body">

                        <form action="{{ route('dsv.checklist', ['demande' => $demande]) }}" method="POST"
                            enctype="multipart/form-data" class="mb-4">
                            @csrf
                            <div class="mb-3">
                                <label for="checklistFile" class="form-label">@lang('trans.checklist')</label>
                                <input class="form-control" type="file" id="checklistFile" name="checklist"
                                    accept=".pdf" required>
                                <div class="form-text">@lang('trans.checklist_indication')</div>
                            </div>
                            <button type="submit" class="btn btn-primary float-right">
                                <i class="fa fa-paper-plane" aria-hidden="true"></i>
                            </button>
                        </form>

                    </div>
                </div>
                <div class="card">
                    <div class="card-header bg-primary text-white">
                        @lang('trans.description')
                    </div>
                    <div class="card-body">
                        <!-- Formulaire -->
                        @if (empty($demande->description))
                            <form action="{{ route('demandes.update', $demande) }}" method="POST"
                                enctype="multipart/form-data">
                                @csrf
                                <div class="mb-3">
                                    <label class="form-label">@lang('trans.description')</label>
                                    <textarea name="description" class="form-control summernote" rows="3"></textarea>
                                </div>
                                <button type="submit" class="btn btn-success float-right">@lang('trans.send')</button>
                            </form>
                        @else
                            {!! $demande->description !!}
                        @endif

                    </div>
                </div>
            </div>
        </div>
                             <!-- Button to Open Modal -->
                    <div class="row">
                        <div class="col-md-12 text-center">
                            <button type="button" class="btn btn-success btn-lg" data-toggle="modal" data-target="#checklistModal">
                                <i class="fas fa-check-double"></i> @lang('trans.checklist')
                            </button>
                        </div>
                    </div>
       <div class="row">
            <div class="col-md-12">
                </div>
                </div>
    </div>
<div class="floating-action-btn">
    
        <button type="button" class="btn btn-success btn-lg rounded-circle shadow-lg" data-toggle="modal" data-target="#checklistModal">
                                <i class="fas fa-check-double"></i> 
                            </button>

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
                    <form id="decisionForm" method="POST" action="{{ route('handle_approval') }}">
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
    <!-- Large Modal for Checklist -->
<div class="modal fade" id="checklistModal" tabindex="-1" role="dialog" aria-labelledby="checklistModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-lg" role="document">
        <div class="modal-content">
            <div class="modal-header bg-primary text-white">
                <h5 class="modal-title" id="checklistModalLabel">
                    <i class="fas fa-clipboard-list"></i> 
                    Checklist d'évaluation de {{ $demande->typeDemande->nom_fr }} de {{ $demande->typeLicence->fr }} - Demande #{{ $demande->code }}
                </h5>
                <button type="button" class="close text-white" data-dismiss="modal" aria-label="Close">
                    <span aria-hidden="true">&times;</span>
                </button>
            </div>
            
            <form action="{{ route('admin.checklists.update', $demande) }}" method="POST" id="checklistForm">
                @csrf
                @method('PUT')
                
                <div class="modal-body" style="max-height: 70vh; overflow-y: auto;">
                    <div class="table-responsive">
                        <table class="table table-bordered table-striped">
                            <thead class="bg-light">
                                <tr>
                                    <th style="width: 5%">N°</th>
                                    <th style="width: 20%">EXIGENCES DU RTA 1 PEL</th>
                                    <th style="width: 15%" colspan="2">ETAT</th>
                                    <th style="width: 20%" colspan="3">MISE EN OEUVRE</th>
                                    <th style="width: 40%">OBSERVATIONS</th>
                                </tr>
                                <tr>
                                    <th></th>
                                    <th></th>
                                    <th style="width: 5%">OUI</th>
                                    <th style="width: 5%">NON</th>
                                    <th style="width: 5%">S</th>
                                    <th style="width: 5%">NS</th>
                                    <th style="width: 5%">S/O</th>
                                    <th></th>
                                </tr>
                            </thead>
                            <tbody>
    @if(isset($checklists) && $checklists->count() > 0)
        @foreach($checklists as $section => $items)
            @if($items && count($items) > 0)
                {{-- En-tête de section --}}
                <tr class="bg-info text-white">
                    <td colspan="8">
                        <strong>{{ $section ?: 'Sans section' }}</strong>
                    </td>
                </tr>

                {{-- Items de la section --}}
                @foreach($items as $index => $checklist)
                    @if($checklist && is_object($checklist) && isset($checklist->id))
                        @php
                            $reponse = $reponses && $reponses->has($checklist->id) ? $reponses->get($checklist->id) : null;
                            $globalIndex = $loop->parent->index * 100 + $loop->index;
                        @endphp
                        <tr>
                            <td class="text-center">{{ $checklist->numero ?? 'N/A' }}</td>
                            <td>
                                @if(!empty($checklist->index))
                                    <small class="text-muted">{{ $checklist->index }}</small><br>
                                @endif
                                {{ $checklist->libelle ?? 'Libelle non défini' }}
                                <input type="hidden" 
                                       name="reponses[{{ $globalIndex }}][checklist_id]" 
                                       value="{{ $checklist->id }}">
                            </td>
                            
                            {{-- ETAT OUI/NON --}}
                            <td class="text-center">
                                <div class="form-check">
                                    <input class="form-check-input etat-radio" 
                                           type="radio" 
                                           name="reponses[{{ $globalIndex }}][etat]" 
                                           value="OUI"
                                           id="etat_oui_{{ $checklist->id }}"
                                           data-checklist-id="{{ $checklist->id }}"
                                           {{ $reponse && $reponse->etat == 'OUI' ? 'checked' : '' }}>
                                    <label class="form-check-label" for="etat_oui_{{ $checklist->id }}">
                                        <span class="badge badge-success">OUI</span>
                                    </label>
                                </div>
                            </td>
                            <td class="text-center">
                                <div class="form-check">
                                    <input class="form-check-input etat-radio" 
                                           type="radio" 
                                           name="reponses[{{ $globalIndex }}][etat]" 
                                           value="NON"
                                           id="etat_non_{{ $checklist->id }}"
                                           data-checklist-id="{{ $checklist->id }}"
                                           {{ $reponse && $reponse->etat == 'NON' ? 'checked' : '' }}>
                                    <label class="form-check-label" for="etat_non_{{ $checklist->id }}">
                                        <span class="badge badge-danger">NON</span>
                                    </label>
                                </div>
                            </td>
                            
                            {{-- MISE EN OEUVRE S/NS/SO --}}
                            <td class="text-center">
                                <div class="form-check">
                                    <input class="form-check-input mise-radio" 
                                           type="radio" 
                                           name="reponses[{{ $globalIndex }}][mise_en_oeuvre]" 
                                           value="S"
                                           id="me_s_{{ $checklist->id }}"
                                           data-checklist-id="{{ $checklist->id }}"
                                           {{ $reponse && $reponse->mise_en_oeuvre == 'S' ? 'checked' : '' }}>
                                    <label class="form-check-label" for="me_s_{{ $checklist->id }}">
                                        <span class="badge badge-primary">S</span>
                                    </label>
                                </div>
                            </td>
                            <td class="text-center">
                                <div class="form-check">
                                    <input class="form-check-input mise-radio" 
                                           type="radio" 
                                           name="reponses[{{ $globalIndex }}][mise_en_oeuvre]" 
                                           value="NS"
                                           id="me_ns_{{ $checklist->id }}"
                                           data-checklist-id="{{ $checklist->id }}"
                                           {{ $reponse && $reponse->mise_en_oeuvre == 'NS' ? 'checked' : '' }}>
                                    <label class="form-check-label" for="me_ns_{{ $checklist->id }}">
                                        <span class="badge badge-warning">NS</span>
                                    </label>
                                </div>
                            </td>
                            <td class="text-center">
                                <div class="form-check">
                                    <input class="form-check-input mise-radio" 
                                           type="radio" 
                                           name="reponses[{{ $globalIndex }}][mise_en_oeuvre]" 
                                           value="S/O"
                                           id="me_so_{{ $checklist->id }}"
                                           data-checklist-id="{{ $checklist->id }}"
                                           {{ $reponse && $reponse->mise_en_oeuvre == 'S/O' ? 'checked' : '' }}>
                                    <label class="form-check-label" for="me_so_{{ $checklist->id }}">
                                        <span class="badge badge-secondary">S/O</span>
                                    </label>
                                </div>
                            </td>
                            
                            {{-- OBSERVATIONS --}}
                            <td>
                                <textarea class="form-control form-control-sm observation-text" 
                                          name="reponses[{{ $globalIndex }}][observations]" 
                                          rows="2"
                                          data-checklist-id="{{ $checklist->id }}">{{ $reponse->observations ?? '' }}</textarea>
                            </td>
                        </tr>
                    @else
                        <tr>
                            <td colspan="8" class="text-danger">
                                Erreur: Checklist invalide détectée
                            </td>
                        </tr>
                    @endif
                @endforeach
            @endif
        @endforeach
    @else
        <tr>
            <td colspan="8" class="text-center text-warning">
                <i class="fas fa-exclamation-triangle"></i> 
                Aucune checklist disponible pour cette demande
            </td>
        </tr>
    @endif
</tbody>
                        </table>
                    </div>
                </div>
                
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-dismiss="modal">
                        <i class="fas fa-times"></i> Fermer
                    </button>
                    <button type="submit" class="btn btn-primary" id="saveChecklistBtn">
                        <i class="fas fa-save"></i> Enregistrer la checklist
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>
<!-- Delete Confirmation Modal -->
<div class="modal fade" id="deleteModal" tabindex="-1" role="dialog" aria-labelledby="deleteModalLabel" aria-hidden="true">
    <div class="modal-dialog" role="document">
        <div class="modal-content">
            <div class="modal-header bg-warning">
                <h5 class="modal-title" id="deleteModalLabel">
                    <i class="fas fa-exclamation-triangle"></i> @lang('trans.confirm_delete')
                </h5>
                <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                    <span aria-hidden="true">&times;</span>
                </button>
            </div>
            <div class="modal-body">
                <p>@lang('trans.delete_confirmation_message')</p>
                <p class="text-danger"><strong>@lang('trans.delete_warning')</strong></p>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-dismiss="modal">
                    <i class="fas fa-times"></i> @lang('trans.cancel')
                </button>
                <form id="deleteForm" method="POST" action="">
                    @csrf
                    @method('DELETE')
                    <button type="submit" class="btn btn-danger">
                        <i class="fas fa-trash"></i> @lang('trans.delete')
                    </button>
                </form>
            </div>
        </div>
    </div>
</div>
@endsection
@push('script')
    <script src="https://cdnjs.cloudflare.com/ajax/libs/summernote/0.8.18/summernote-bs4.min.js"></script>
@endpush
@push('custom')
    <script>
    function openDeleteModal(table, itemId, demandeId) {
    // Set the form action URL based on the table and item
    let deleteUrl = '';
    
    switch(table) {
        case 'medical_examinations':
            deleteUrl = `/medical-examinations/${itemId}`;
            break;
        case 'licence_demandeurs':
            deleteUrl = `/licence-demandeurs/${itemId}`;
            break;
        case 'formation_demandeurs':
            deleteUrl = `/formation-demandeurs/${itemId}`;
            break;
        case 'qualification_demandeurs':
            deleteUrl = `/qualification-demandeurs/${itemId}`;
            break;
        case 'experience_demandeurs':
            deleteUrl = `/experience-demandeurs/${itemId}`;
            break;
        case 'competence_demandeurs':
            deleteUrl = `/competence-demandeurs/${itemId}`;
            break;
        case 'training_demandeurs':
            deleteUrl = `/training-demandeurs/${itemId}`;
            break;
        case 'interruption_demandeurs':
            deleteUrl = `/interruption-demandeurs/${itemId}`;
            break;
        case 'experience_maintenance_demandeurs':
            deleteUrl = `/experience-maintenance-demandeurs/${itemId}`;
            break;
        case 'employeur_demandeurs':
            deleteUrl = `/employeur-demandeurs/${itemId}`;
            break;
        case 'documents':
            deleteUrl = `/documents/${itemId}`;
            break;
    }
    
    // Set the form action
    document.getElementById('deleteForm').action = deleteUrl;
    
    // Add hidden input for demande_id if needed
    let existingInput = document.getElementById('delete_demande_id');
    if (!existingInput) {
        let input = document.createElement('input');
        input.type = 'hidden';
        input.name = 'demande_id';
        input.value = demandeId;
        input.id = 'delete_demande_id';
        document.getElementById('deleteForm').appendChild(input);
    } else {
        existingInput.value = demandeId;
    }
    
    // Show the modal
    $('#deleteModal').modal('show');
}
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
            $('.summernote').summernote({
                height: 200, // Set height of the editor
                placeholder: 'Enter your text...',

            });
        });
    </script>
    <script>
        $(document).ready(function() {
            // Add new piece
            $('#pieceForm').on('submit', function(e) {
                e.preventDefault();
                
                var formData = new FormData(this);
                
                $.ajax({
                    url: '{{ route("pieces.store") }}',
                    type: 'POST',
                    data: formData,
                    processData: false,
                    contentType: false,
                    success: function(response) {
                        location.reload();
                    },
                    error: function(xhr) {
                        
                        console.log(xhr.responseText);
                    }
                });
            });

            // Edit piece - show form
            $('.edit-piece').on('click', function() {
                var pieceId = $(this).data('id');
                togglePieceEditForm(pieceId);
            });

            // Update piece
            $('[id^="updatePieceForm-"]').on('submit', function(e) {
                e.preventDefault();
                
                var form = $(this);
                var pieceId = form.find('input[name="piece_id"]').val();
                var formData = new FormData(this);
                
                $.ajax({
                    url: '{{ url("admin/pieces/update") }}/' + pieceId,
                    type: 'POST', // Using POST for PUT with _method
                    data: formData,
                    processData: false,
                    contentType: false,
                    success: function(response) {
                        location.reload();
                    },
                    error: function(xhr) {
                        
                        console.log(xhr.responseText);
                    }
                });
            });

            // Delete piece
            $('.delete-piece').on('click', function() {
                var pieceId = $(this).data('id');
                
                if (confirm('@lang('trans.confirm_delete')')) {
                    $.ajax({
                        url: '{{ url("admin/pieces/destroy") }}/' + pieceId,
                        type: 'DELETE',
                        data: {
                            _token: '{{ csrf_token() }}'
                        },
                        success: function(response) {
                           location.reload();
                        },
                        error: function(xhr) {
                            
                            console.log(xhr.responseText);
                        }
                    });
                }
            });
        });

        function togglePieceEditForm(pieceId) {
            var formRow = $('#edit-form-piece-' + pieceId);
            
            // Hide all other edit forms
            $('[id^="edit-form-piece-"]').not(formRow).hide();
            
            // Toggle current form
            if (formRow.is(':visible')) {
                formRow.hide();
            } else {
                formRow.show();
            }
        }
    </script>
    <script>
    // Add this to your main JavaScript file or in a script tag
$(document).ready(function() {
    // Auto-open modal if it was open before redirect
    @if(session('open_modal'))
        $('#checklistModal').modal('show');
    @endif
    
    // Handle AJAX form submission for auto-save
    $('#checklistForm').on('submit', function(e) {
        // Allow normal submission
        return true;
    });
});
$(document).ready(function() {
    // Auto-save functionality (optional)
    let autoSaveTimer;
    let isSaving = false;
    
    // Function to show saving indicator
    function showSavingIndicator() {
        if (!$('#savingIndicator').length) {
            $('.modal-footer').prepend('<span id="savingIndicator" class="text-muted mr-3"><i class="fas fa-spinner fa-spin"></i> Sauvegarde automatique...</span>');
        }
    }
    
    function hideSavingIndicator() {
        $('#savingIndicator').remove();
    }
    
    // Auto-save on any change (optional - uncomment to enable)
    /*
    $('.etat-radio, .mise-radio, .observation-text').on('change', function() {
        clearTimeout(autoSaveTimer);
        autoSaveTimer = setTimeout(function() {
            if (!isSaving) {
                autoSaveForm();
            }
        }, 2000);
    });
    
    function autoSaveForm() {
        isSaving = true;
        showSavingIndicator();
        
        var formData = $('#checklistForm').serialize();
        
        $.ajax({
            url: $('#checklistForm').attr('action'),
            type: 'POST',
            data: formData + '&_method=PUT',
            success: function(response) {
                setTimeout(function() {
                    hideSavingIndicator();
                    isSaving = false;
                    // Optional: Show success toast
                    showToast('success', 'Checklist sauvegardée automatiquement');
                }, 500);
            },
            error: function(xhr) {
                hideSavingIndicator();
                isSaving = false;
                console.error('Auto-save error:', xhr);
                showToast('error', 'Erreur lors de la sauvegarde automatique');
            }
        });
    }
    */
    
    // Show toast notification
    function showToast(type, message) {
        // You can implement toast notification here
        // Example using Bootstrap toast or sweet alert
        if (type === 'success') {
            toastr.success(message);
        } else {
            toastr.error(message);
        }
    }
    
    // Progress calculation (optional)
    function calculateProgress() {
        let totalItems = $('.etat-radio').length / 2; // Each item has OUI and NON radios
        let answeredItems = 0;
        
        $('tr:has(.etat-radio)').each(function() {
            if ($(this).find('.etat-radio:checked').length > 0) {
                answeredItems++;
            }
        });
        
        let progress = totalItems > 0 ? (answeredItems / totalItems) * 100 : 0;
        return Math.round(progress);
    }
    
    // Update progress bar (optional - add to modal if needed)
    function updateProgress() {
        let progress = calculateProgress();
        if ($('#checklistProgress').length) {
            $('#checklistProgress').css('width', progress + '%').attr('aria-valuenow', progress);
            $('#progressText').text(progress + '%');
        }
    }
    
    // Form validation before submit
    $('#checklistForm').on('submit', function(e) {
        // Optional: Add validation logic here
        let confirmSave = confirm('Êtes-vous sûr de vouloir enregistrer la checklist ?');
        if (!confirmSave) {
            e.preventDefault();
            return false;
        }
        
        // Show loading state on button
        $('#saveChecklistBtn').html('<i class="fas fa-spinner fa-spin"></i> Enregistrement...').prop('disabled', true);
        
        return true;
    });
    
    // Handle modal close confirmation if there are unsaved changes
    let formChanged = false;
    
    $('.etat-radio, .mise-radio, .observation-text').on('change', function() {
        formChanged = true;
    });
    
    $('#checklistModal').on('hide.bs.modal', function(e) {
        if (formChanged) {
            let confirmClose = confirm('Vous avez des modifications non enregistrées. Voulez-vous vraiment fermer ?');
            if (!confirmClose) {
                e.preventDefault();
                return false;
            }
        }
        return true;
    });
    
    $('#checklistModal').on('shown.bs.modal', function() {
        formChanged = false;
    });
    
    // Initialize tooltips if needed
    $('[data-toggle="tooltip"]').tooltip();
    
    // Optional: Add keyboard shortcuts
    $(document).keydown(function(e) {
        // Ctrl + S to save
        if ((e.ctrlKey || e.metaKey) && e.keyCode == 83) {
            e.preventDefault();
            $('#checklistForm').submit();
            return false;
        }
    });
    
    // Log console message for debugging
    console.log('Checklist modal initialized');
});
</script>

@endpush
