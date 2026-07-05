@extends('sec.layouts.app')
@section('title')
    @lang('trans.dashboard_sec')
@endsection
@section('contentheader')
    @lang('trans.dashboard_sec')
@endsection
@section('contentheaderlink')
    @if (auth()->user()->hasRole('sla'))
        <a href="{{ route('sla') }}">
            @lang('trans.dashboard_sec') </a>
    @endif
    @if (auth()->user()->hasRole('sma'))
        <a href="{{ route('sma') }}">
            @lang('trans.dashboard_sec') </a>
    @endif
@endsection
@section('contentheaderactive')
    @lang('trans.dashboard_sec')
@endsection
@push('css')
    <style>
        #documentViewer {

            width: 105mm;
            height: 148mm;
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
                <div class="card">
                    <div class="card-header bg-primary text-white">
                        Profile
                    </div>
                    <div class="card-body">
                        @isset($demandeur)
                            <div class="row justify-content-center">

                                <div class="col-lg-9 table-responsive">
                                    <table class="table table-bordered table-striped">
                                        <tr>
                                            <th>@lang('trans.fl_name')</th>
                                            <td>{{ $demandeur->np ?? '-' }}</td>
                                        </tr>
                                        <tr>
                                            <th>@lang('trans.dob')</th>
                                            <td>{{ $demandeur->date_naissance ?? '-' }}</td>
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
                        @endisset
                    </div>
                </div>


                <!----->
                @if (auth()->user()->hasRole('sma'))
                <div class="card">
                    <div class="card-header bg-success text-white">
                        @lang('trans.medical_fitness_by_examiner')
                    </div>
                    <div class="card-body">

                        @isset($examens)
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
                        @endisset
                    </div>

                </div>
                <div class="card">
                    <div class="card-header bg-primary text-white">
                        @lang('trans.medical_fitness')
                    </div>
                    <div class="card-body">

                        @isset($medical_examinations)
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
                        @endisset
                    </div>

                </div>
                @endif
                @if (auth()->user()->hasRole('sla'))
                <div class="card">
                    <div class="card-header bg-danger text-white">
                        @lang('trans.training')
                    </div>
                    <div class="card-body">

                        @isset($formations)
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
                                                    <td>{{ $formation->typeFormation->nom }}</td>
                                                    <td>{{ $formation->centreFormation->libelle }}</td>
                                                    <td>{{ $formation->lieu }}</td>
                                                    <td>{{ $formation->date_formation }}</td>
                                                    <td>
                                                        @if ($formation->attestation)
                                                            <button class="btn btn-primary"
                                                                onclick="openPdfModal('{{ asset('/uploads/' . $formation->attestation) }}')"><i
                                                                    class="fas fa-eye"></i></button>
                                                        @endif
                                                    </td>



                                                </tr>
                                            @endforeach
                                        </tbody>
                                    </table>
                                </div>
                            </div>
                        @endisset
                    </div>

                </div>
                <div class="card">
                    <div class="card-header bg-primary text-white">
                        @lang('trans.license')
                    </div>

                    <div class="card-body">
                        @isset($licence_demandeurs)
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
                        @endisset
                    </div>
                </div>
                <div class="card">
                    <div class="card-header bg-primary text-white">
                        @lang('trans.training')
                    </div>
                    <div class="card-body">
                        @isset($formation_demandeurs)
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
                        @endisset

                    </div>

                </div>
                <div class="card">
                    <div class="card-header bg-primary text-white">
                        @lang('trans.ratings')
                    </div>
                    <div class="card-body">
                        <br>
                        @isset($qualification_demandeurs)
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
                        @endisset

                    </div>

                </div>
                <div class="card">
                    <div class="card-header bg-primary text-white">
                        @lang('trans.flights')
                    </div>

                    <div class="card-body">
                        @isset($experience_demandeurs)
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
                        @endisset
                    </div>

                </div>
                <div class="card">
                    <div class="card-header bg-primary text-white">
                        @lang('trans.control')
                    </div>
                    <div class="card-body">
                        @isset($competence_demandeurs)
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
                        @endisset

                    </div>

                </div>
                <div class="card">
                    <div class="card-header bg-primary text-white">
                        @lang('trans.periodic_control')
                    </div>
                    <div class="card-body">
                        @isset($entrainement_demandeurs)
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
                                                        @if (is_null($entrainement_demandeur->valider))
                                                            <!-- Unreviewed state - show both approve and reject buttons -->
                                                            <button type="button" class="btn btn-success btn-sm mr-1"
                                                                onclick="openDecisionModal('entrainement_demandeurs', '{{ $entrainement_demandeur->id }}', '{{ $demande->id }}', 'approve')">
                                                                <i class="fas fa-check"></i> @lang('trans.approve')
                                                            </button>
                                                            <button type="button" class="btn btn-danger btn-sm"
                                                                onclick="openDecisionModal('entrainement_demandeurs', '{{ $entrainement_demandeur->id }}', '{{ $demande->id }}', 'reject')">
                                                                <i class="fas fa-times"></i> @lang('trans.reject')
                                                            </button>
                                                        @elseif ($entrainement_demandeur->valider == 1)
                                                            <!-- Approved state - show status and reject option -->
                                                            <span class="badge bg-success">
                                                                <i class="fas fa-check-circle"></i> @lang('trans.approved')
                                                            </span>
                                                            <button type="button" class="btn btn-danger btn-sm ml-2"
                                                                onclick="openDecisionModal('entrainement_demandeurs', '{{ $entrainement_demandeur->id }}', '{{ $demande->id }}', 'reject')">
                                                                <i class="fas fa-times"></i> @lang('trans.reject')
                                                            </button>
                                                        @else
                                                            <!-- Rejected state - show status and approve option -->
                                                            <span class="badge bg-danger">
                                                                <i class="fas fa-times-circle"></i> @lang('trans.rejected')
                                                            </span>
                                                            <button type="button" class="btn btn-success btn-sm ml-2"
                                                                onclick="openDecisionModal('entrainement_demandeurs', '{{ $entrainement_demandeur->id }}', '{{ $demande->id }}', 'approve')">
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
                        @endisset
                    </div>


                </div>
                {{-- Interupptions --}}
                <div class="card">
                    <div class="card-header bg-primary text-white">
                        @lang('trans.interruptions')
                    </div>

                    <div class="card-body">
                        @isset($interruption_demandeurs)
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
                        @endisset
                    </div>


                </div>
                {{-- Expérience en maintenance d'aéronefs --}}
                <div class="card">
                    <div class="card-header bg-primary text-white">

                        @lang('trans.maintenance')
                    </div>

                    <div class="card-body">
                        @isset($experience_maintenance_demandeurs)
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
                        @endisset
                    </div>


                </div>
                {{-- Employeurs --}}
                <div class="card">
                    <div class="card-header bg-primary text-white">
                        @lang('trans.employers')
                    </div>

                    <div class="card-body">
                        @isset($employeur_demandeurs)
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
                        @endisset
                    </div>


                </div>
                {{-- --}}
                <div class="card">
                    <div class="card-header bg-primary text-white">
                        @lang('trans.attachments')
                    </div>

                    <div class="card-body">
                        @isset($documents)
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
                        @endisset
                    </div>
                </div>
                @endif
            </div>
            <!-- /.card-body -->
        </div>
        <div class="row">
            <div class="col-md-12">
                <div class="card">
                    <div class="card-header bg-primary text-white">
                        Checklist

                        @if (auth()->user()->hasRole('sma') && !empty($demande->checklist_sma))
                            <div class="card-tools">
                                <a href="{{ asset('uploads/' . $demande->checklist_sma) }}" target="_blank"
                                    class="btn btn-sm btn-success">
                                    <i class="fas fa-eye"></i> Voir
                                </a>
                            </div>
                        @endif
                        @if (auth()->user()->hasRole('sla') && !empty($demande->checklist_sla))
                            <div class="card-tools">
                                <a href="{{ asset('uploads/' . $demande->checklist_sla) }}" target="_blank"
                                    class="btn btn-sm btn-success">
                                    <i class="fas fa-eye"></i> Voir
                                </a>
                            </div>
                        @endif
                    </div>
                    <div class="card-body">
                        <form action="{{ route('dsv.checklist', ['demande' => $demande]) }}" method="POST"
                            enctype="multipart/form-data" class="mb-4">
                            @csrf
                            <div class="mb-3">
                                <label for="checklistFile" class="form-label">Fichier Checklist (PDF
                                    uniquement)</label>
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
@endsection
@push('script')
    <script src="https://cdnjs.cloudflare.com/ajax/libs/summernote/0.8.18/summernote-bs4.min.js"></script>
@endpush
@push('custom')
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
            $('.summernote').summernote({
                height: 200, // Set height of the editor
                placeholder: 'Enter your text...',

            });
        });
    </script>
@endpush
