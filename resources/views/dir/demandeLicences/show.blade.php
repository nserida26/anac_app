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
@endsection
@section('contentheaderactive')
    @lang('trans.dashboard_dir', ['role' => strtoupper(auth()->user()->getRoleNames()->first() ?? '')])
@endsection
@push('css')
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/summernote/0.8.18/summernote-bs4.min.css">

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
                <div class="card">
                    <div class="card-header bg-primary text-white">
                        <h4 class="text-center">
                        {{ $demande->code }} - {{ $demande->demandeur->np ?? 'N/A' }} - {{ $demande->demandeur->compagnie->nom_entreprise ?? 'Privé' }} - {{ LaravelLocalization::getCurrentLocale() == 'fr' ? optional($demande->typeDemande)->nom_fr : optional($demande->typeDemande)->nom_en }} - {{ LaravelLocalization::getCurrentLocale() == 'fr' ? optional($demande->typeLicence)->fr : optional($demande->typeLicence)->en }}
                        </h4>
                    </div>
                    <div class="card-body">
                        @isset($demandeur)
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
                                            <td> {{ $demandeur->compagnie->adresse ?? '-' }}</td>
                                        </tr>
                                        <tr>
                                            <th>@lang('trans.compagny')</th>
                                            <td>
                                            @if (!empty(optional($demande->demandeur)->compagnie))
                                              <span class="badge badge-success">{{ $demande->demandeur->compagnie->nom_entreprise }}</span>
                                              @else
                                              <span class="badge badge-danger">{{ 'Privé' }}</span>
                                              @endif 
                                            </td>
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
                                                            
                                                        @elseif ($medical_examination->valider == 1)
                                                            <!-- Approved state - show reject option -->
                                                            <span class="badge bg-success">@lang('trans.approved')</span>
                                                            
                                                        @else
                                                            <!-- Rejected state - show approve option -->
                                                            <span class="badge bg-danger">@lang('trans.rejected')</span>
                                                            
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
                                                            
                                                        @elseif ($licence_demandeur->valider == 1)
                                                            <!-- Approved state - show reject option -->
                                                            <span class="badge bg-success">@lang('trans.approved')</span>
                                                            
                                                        @else
                                                            <!-- Rejected state - show approve option -->
                                                            <span class="badge bg-danger">@lang('trans.rejected')</span>
                                                            
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
                                                            
                                                        @elseif ($formation_demandeur->valider == 1)
                                                            <!-- Approved state - show status and reject option -->
                                                            <span class="badge bg-success">
                                                                <i class="fas fa-check-circle"></i> @lang('trans.approved')
                                                            </span>
                                                            
                                                        @else
                                                            <!-- Rejected state - show status and approve option -->
                                                            <span class="badge bg-danger">
                                                                <i class="fas fa-times-circle"></i> @lang('trans.rejected')
                                                            </span>
                                                            
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
                                                            
                                                        @elseif ($qualification_demandeur->valider == 1)
                                                            <!-- Approved state - show status and reject option -->
                                                            <span class="badge bg-success">
                                                                <i class="fas fa-check-circle"></i> @lang('trans.approved')
                                                            </span>
                                                            
                                                        @else
                                                            <!-- Rejected state - show status and approve option -->
                                                            <span class="badge bg-danger">
                                                                <i class="fas fa-times-circle"></i> @lang('trans.rejected')
                                                            </span>
                                                            
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
                                                            
                                                        @elseif ($experience_demandeur->valider == 1)
                                                            <!-- Approved state - show status and reject option -->
                                                            <span class="badge bg-success">
                                                                <i class="fas fa-check-circle"></i> @lang('trans.approved')
                                                            </span>
                                                            
                                                        @else
                                                            <!-- Rejected state - show status and approve option -->
                                                            <span class="badge bg-danger">
                                                                <i class="fas fa-times-circle"></i> @lang('trans.rejected')
                                                            </span>
                                                            
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
                                                            
                                                        @elseif ($competence_demandeur->valider == 1)
                                                            <!-- Approved state - show status and reject option -->
                                                            <span class="badge bg-success">
                                                                <i class="fas fa-check-circle"></i> @lang('trans.approved')
                                                            </span>
                                                            
                                                        @else
                                                            <!-- Rejected state - show status and approve option -->
                                                            <span class="badge bg-danger">
                                                                <i class="fas fa-times-circle"></i> @lang('trans.rejected')
                                                            </span>
                                                            
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
                                                            
                                                        @elseif ($entrainement_demandeur->valider == 1)
                                                            <!-- Approved state - show status and reject option -->
                                                            <span class="badge bg-success">
                                                                <i class="fas fa-check-circle"></i> @lang('trans.approved')
                                                            </span>
                                                            
                                                        @else
                                                            <!-- Rejected state - show status and approve option -->
                                                            <span class="badge bg-danger">
                                                                <i class="fas fa-times-circle"></i> @lang('trans.rejected')
                                                            </span>
                                                            
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
                                                            
                                                        @elseif ($interruption_demandeur->valider == 1)
                                                            <!-- Approved state - show status and reject option -->
                                                            <span class="badge bg-success">
                                                                <i class="fas fa-check-circle"></i> @lang('trans.approved')
                                                            </span>
                                                           
                                                        @else
                                                            <!-- Rejected state - show status and approve option -->
                                                            <span class="badge bg-danger">
                                                                <i class="fas fa-times-circle"></i> @lang('trans.rejected')
                                                            </span>
                                                            
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
                                                            
                                                        @elseif ($experience_maintenance_demandeur->valider == 1)
                                                            <!-- Approved state - show status and reject option -->
                                                            <span class="badge bg-success">
                                                                <i class="fas fa-check-circle"></i> @lang('trans.approved')
                                                            </span>
                                                            
                                                        @else
                                                            <!-- Rejected state - show status and approve option -->
                                                            <span class="badge bg-danger">
                                                                <i class="fas fa-times-circle"></i> @lang('trans.rejected')
                                                            </span>
                                                            
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
                                                            
                                                        @elseif ($employeur_demandeur->valider == 1)
                                                            <!-- Approved state - show status and reject option -->
                                                            <span class="badge bg-success">
                                                                <i class="fas fa-check-circle"></i> @lang('trans.approved')
                                                            </span>
                                                            
                                                        @else
                                                            <!-- Rejected state - show status and approve option -->
                                                            <span class="badge bg-danger">
                                                                <i class="fas fa-times-circle"></i> @lang('trans.rejected')
                                                            </span>
                                                            
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

                                                        @elseif ($document->valider == 1)
                                                            <!-- Approved state - show status and reject option -->
                                                            <span class="badge bg-success">
                                                                <i class="fas fa-check-circle"></i> @lang('trans.approved')
                                                            </span>
                                                            
                                                        @else
                                                            <!-- Rejected state - show status and approve option -->
                                                            <span class="badge bg-danger">
                                                                <i class="fas fa-times-circle"></i> @lang('trans.rejected')
                                                            </span>
                                                           
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
                            <small class="text-muted">@lang('trans.allowed_formats'): PDF, DOC, DOCX, JPG, JPEG, PNG (Max: 2MB)</small>
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
@if (!empty($demande->checklist_admin))
        <div class="row">
            <div class="col-md-12">
                <div class="card">
                    <div class="card-header bg-primary text-white">
                        Checklist
                        
                            <div class="card-tools">
                                <a href="{{ asset('uploads/' . $demande->checklist_admin) }}" target="_blank"
                                    class="btn btn-sm btn-success">
                                    <i class="fas fa-eye"></i> @lang('trans.view')
                                </a>
                            </div>
                        
                    </div>
                </div>
            </div>
        </div>
        @endif
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
@endpush
