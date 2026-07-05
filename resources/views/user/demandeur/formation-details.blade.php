@extends('user.layouts.app')
@section('title', trans('trans.formation_details'))
@section('contentheader', trans('trans.formation_details'))
@section('contentheaderlink')
    <a href="{{ route('demandeur.dashboard') }}">@lang('trans.dashboard')</a>
@endsection
@section('contentheaderactive', trans('trans.formation_details'))

@section('content')
<div class="container-fluid">
    <div class="row">
        <div class="col-md-12">
            <div class="card">
                <div class="card-header bg-primary text-white">
                    <h5 class="mb-0">
                        <i class="fas fa-chalkboard"></i>
                        @lang('trans.formation_details') #{{ $formation->id }}
                    </h5>
                </div>
                <div class="card-body">
                     {{-- Statut de la formation --}}
                    <div class="row mb-4">
                        <div class="col-md-12">
                            @php
                                $statusColors = [
                                    'planifiee' => 'warning',
                                    'en_cours' => 'info',
                                    'terminee' => 'success',
                                    'annulee' => 'danger'
                                ];
                                $statusColor = $statusColors[$formation->status] ?? 'secondary';
                            @endphp
                            <div class="alert alert-{{ $statusColor }}">
                                <i class="fas fa-info-circle"></i>
                                <strong>@lang('trans.status'):</strong> 
                                <span class="badge badge-{{ $statusColor }} badge-lg">
                                    @lang('trans.' . $formation->status)
                                </span>
                                
                                @if($formation->status != 'terminee' && $formation->status != 'annulee')
                                    <button class="btn btn-sm btn-success float-right update-status" 
                                            data-id="{{ $formation->id }}"
                                            data-status="terminee">
                                        <i class="fas fa-check"></i> @lang('trans.mark_as_completed')
                                    </button>
                                @endif
                            </div>
                        </div>
                    </div>


                    <div class="row">
                        {{-- Section Stagiaire --}}
                        <div class="col-md-6">
                            <div class="card card-outline card-primary">
                                <div class="card-header">
                                    <h5 class="card-title">
                                        <i class="fas fa-user-graduate"></i>
                                        @lang('trans.trainee_information')
                                    </h5>
                                </div>
                                <div class="card-body">
                                    @if($formation->demandeur)
                                        <div class="text-center mb-3">
                                            @if($formation->demandeur->photo)
                                                <img src="{{ asset('uploads/' . $formation->demandeur->photo) }}" 
                                                     class="rounded-circle" 
                                                     style="width: 100px; height: 100px; object-fit: cover;">
                                            @else
                                                <div class="bg-secondary rounded-circle d-inline-flex align-items-center justify-content-center" 
                                                     style="width: 100px; height: 100px;">
                                                    <i class="fas fa-user fa-3x text-white"></i>
                                                </div>
                                            @endif
                                        </div>
                                        
                                        <table class="table table-bordered">
                                            <tr>
                                                <th width="35%">@lang('trans.full_name')</th>
                                                <td>{{ $formation->demandeur->np ?? 'N/A' }}</td>
                                            </tr>
                                            <tr>
                                                <th>@lang('trans.dob')</th>
                                                <td>{{ $formation->demandeur->date_naissance ?? 'N/A' }}</td>
                                            </tr>
                                            <tr>
                                                <th>@lang('trans.place_of_birth')</th>
                                                <td>{{ $formation->demandeur->lieu_naissance ?? 'N/A' }}</td>
                                            </tr>
                                            <tr>
                                                <th>@lang('trans.nationality')</th>
                                                <td>{{ $formation->demandeur->nationalite ?? 'N/A' }}</td>
                                            </tr>
                                            <tr>
                                                <th>@lang('trans.address')</th>
                                                <td>{{ $formation->demandeur->adresse ?? 'N/A' }}</td>
                                            </tr>
                                            <tr>
                                                <th>@lang('trans.email')</th>
                                                <td>{{ $formation->demandeur->user->email ?? 'N/A' }}</td>
                                            </tr>
                                            <tr>
                                                <th>@lang('trans.licence_number')</th>
                                                <td>
                                                    @if($formation->demandeur->licence)
                                                        <strong>{{ $formation->demandeur->licence->numero_licence }}</strong>
                                                        <br>
                                                        <small class="text-muted">
                                                            @lang('trans.type'): {{ $formation->demandeur->licence->type_licence ?? 'N/A' }}
                                                        </small>
                                                        <br>
                                                        <small class="text-muted">
                                                            @lang('trans.category'): {{ $formation->demandeur->licence->categorie_licence ?? 'N/A' }}
                                                        </small>
                                                    @else
                                                        <span class="text-warning">@lang('trans.no_licence')</span>
                                                    @endif
                                                </td>
                                            </tr>
                                        </table>
                                    @else
                                        <div class="alert alert-warning">
                                            @lang('trans.demandeur_not_found')
                                        </div>
                                    @endif
                                </div>
                            </div>
                        </div>

                        {{-- Section Détails Formation --}}
                        <div class="col-md-6">
                            <div class="card card-outline card-success">
                                <div class="card-header">
                                    <h5 class="card-title">
                                        <i class="fas fa-info-circle"></i>
                                        @lang('trans.training_details')
                                    </h5>
                                </div>
                                <div class="card-body">
                                    <table class="table table-bordered">
                                        <tr>
                                            <th width="35%">@lang('trans.training_type')</th>
                                            <td>{{ $formation->typeFormation->nom ?? 'N/A' }}</td>
                                        </tr>
                                        <tr>
                                            <th>@lang('trans.training_title')</th>
                                            <td>{{ $formation->intitule_formation ?? '-' }}</td>
                                        </tr>
                                        <tr>
                                            <th>@lang('trans.training_date')</th>
                                            <td>
                                                @if($formation->date_formation)
                                                    {{ $formation->date_formation->format('d/m/Y') }}
                                                    <br>
                                                    <small class="text-muted">
                                                        @if($formation->date_formation > now())
                                                            <i class="fas fa-clock text-warning"></i> @lang('trans.upcoming')
                                                        @elseif($formation->date_formation < now())
                                                            <i class="fas fa-check-circle text-success"></i> @lang('trans.passed')
                                                        @else
                                                            <i class="fas fa-calendar-day text-info"></i> @lang('trans.today')
                                                        @endif
                                                    </small>
                                                @else
                                                    N/A
                                                @endif
                                            </td>
                                        </tr>
                                        <tr>
                                            <th>@lang('trans.location')</th>
                                            <td>{{ $formation->lieu ?? '-' }}</td>
                                        </tr>
                                        <tr>
                                            <th>@lang('trans.licence_type')</th>
                                            <td>{{ $formation->typeLicence->nom ?? 'N/A' }}</td>
                                        </tr>
                                        <tr>
                                            <th>@lang('trans.training_device')</th>
                                            <td>
                                                @if($formation->dispositifFormation)
                                                    {{ $formation->dispositifFormation->simulateur->libelle ?? 'N/A' }}
                                                    <br>
                                                    <small class="text-muted">
                                                        @lang('trans.certified_until'): 
                                                        {{ $formation->dispositifFormation->date_expiration_certification->format('d/m/Y') ?? 'N/A' }}
                                                    </small>
                                                @else
                                                    -
                                                @endif
                                            </td>
                                        </tr>

                                    </table>
                                </div>
                            </div>

                            {{-- Section Formateurs --}}
                            <div class="card card-outline card-info mt-3">
                                <div class="card-header">
                                    <h5 class="card-title">
                                        <i class="fas fa-chalkboard-teacher"></i>
                                        @lang('trans.trainers')
                                    </h5>
                                </div>
                                <div class="card-body">
                                    <table class="table table-bordered">
                                        <tr>
                                            <th width="35%">@lang('trans.instructor')</th>
                                            <td>
                                                @if($demandeur)
                                                    <strong>{{ $demandeur->np }} </strong>
                                                    <br>
                                                    <small class="text-muted">{{ $demandeur->licence->numero_licence ?? '' }}</small>
                                                @else
                                                    <span class="text-muted">-</span>
                                                @endif
                                            </td>
                                        </tr>
                                        <tr>
                                            <th>@lang('trans.examiner')</th>
                                            <td>
                                                @if($demandeur)
                                                    <strong>{{ $demandeur->np }}</strong>
                                                    <br>
                                                    <small class="text-muted">{{ $demandeur->licence->numero_licence ?? '' }}</small>
                                                @else
                                                    <span class="text-muted">-</span>
                                                @endif
                                            </td>
                                        </tr>
                                    </table>
                                </div>
                            </div>
                        </div>
                    </div>

                    {{-- Section Certificat --}}
                    @if($formation->attestation)
                    <div class="row mt-4">
                        <div class="col-md-12">
                            <div class="card card-outline card-secondary">
                                <div class="card-header">
                                    <h5 class="card-title">
                                        <i class="fas fa-certificate"></i>
                                        @lang('trans.certificate')
                                    </h5>
                                </div>
                                <div class="card-body">
                                    <div class="row">
                                        <div class="col-md-6">
                                            <p>
                                                <i class="fas fa-file-pdf text-danger"></i>
                                                <strong>@lang('trans.certificate_file'):</strong>
                                            </p>
                                            <a href="{{ asset('/uploads/' . $formation->attestation) }}" 
                                               class="btn btn-danger" 
                                               target="_blank">
                                                <i class="fas fa-download"></i> @lang('trans.download_certificate')
                                            </a>
                                            
                                            @if(pathinfo($formation->attestation, PATHINFO_EXTENSION) == 'pdf')
                                                <button class="btn btn-info" data-toggle="modal" data-target="#pdfModal">
                                                    <i class="fas fa-eye"></i> @lang('trans.view_certificate')
                                                </button>
                                            @endif
                                        </div>
                                        <div class="col-md-6">
                                            <div class="alert alert-info">
                                                <i class="fas fa-info-circle"></i>
                                                @lang('trans.certificate_info')
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                    @endif
                </div>
                <div class="card-footer">
                    <a href="{{ url()->previous() }}" class="btn btn-secondary">
                        <i class="fas fa-arrow-left"></i> @lang('trans.back')
                    </a>
                    <a href="{{ route('demandeur.create.formation') }}" class="btn btn-success">
                        <i class="fas fa-plus"></i> @lang('trans.assign_new_training')
                    </a>
                    <a href="{{ route('demandeur.formations.list') }}" class="btn btn-primary">
                        <i class="fas fa-list"></i> @lang('trans.all_trainings')
                    </a>
                </div>
            </div>
        </div>
    </div>
</div>

{{-- Modal pour afficher PDF --}}
@if($formation->attestation && pathinfo($formation->attestation, PATHINFO_EXTENSION) == 'pdf')
<div class="modal fade" id="pdfModal" tabindex="-1" role="dialog">
    <div class="modal-dialog modal-lg" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title">
                    <i class="fas fa-certificate"></i> @lang('trans.certificate')
                </h5>
                <button type="button" class="close" data-dismiss="modal">
                    <span>&times;</span>
                </button>
            </div>
            <div class="modal-body">
                <iframe src="{{ asset('uploads/' . $formation->attestation) }}" 
                        style="width: 100%; height: 500px;" 
                        frameborder="0">
                </iframe>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-dismiss="modal">
                    @lang('trans.close')
                </button>
                <a href="{{ asset('uploads/' . $formation->attestation) }}" 
                   class="btn btn-danger" 
                   download>
                    <i class="fas fa-download"></i> @lang('trans.download')
                </a>
            </div>
        </div>
    </div>
</div>
@endif



@push('css')
<style>
    .badge-lg {
        font-size: 14px;
        padding: 5px 10px;
    }
    .table th {
        background-color: #f8f9fa;
    }
    .card-outline {
        box-shadow: 0 1px 2px rgba(0,0,0,0.05);
    }
</style>
@endpush
@endsection