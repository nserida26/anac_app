{{-- resources/views/centre/formations/show.blade.php --}}
@extends('centre.layouts.app')

@section('title')
    @lang('trans.training_details')
@endsection

@section('contentheader')
    @lang('trans.training_details')
@endsection

@section('contentheaderlink')
    <a href="{{ route('centre.index') }}">@lang('trans.dashboard_center')</a>
@endsection

@section('contentheaderactive')
    @lang('trans.details')
@endsection

@push('css')
<style>
    .detail-card {
        border-radius: 10px;
        box-shadow: 0 2px 4px rgba(0,0,0,0.1);
        margin-bottom: 20px;
    }
    
    .detail-header {
        background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
        color: white;
        padding: 20px;
        border-radius: 10px 10px 0 0;
    }
    
    .detail-body {
        padding: 20px;
        background: white;
        border-radius: 0 0 10px 10px;
    }
    
    .info-row {
        display: flex;
        padding: 10px 0;
        border-bottom: 1px solid #eee;
    }
    
    .info-row:last-child {
        border-bottom: none;
    }
    
    .info-label {
        width: 200px;
        font-weight: 600;
        color: #555;
    }
    
    .info-value {
        flex: 1;
        color: #333;
    }
    
    .demandeur-card {
        background: #f8f9fa;
        border-radius: 10px;
        padding: 20px;
        margin-bottom: 20px;
    }
    
    .licence-badge {
        background: #007bff;
        color: white;
        padding: 5px 15px;
        border-radius: 20px;
        font-size: 14px;
        display: inline-block;
        margin-top: 10px;
    }
</style>
@endpush

@section('content')
<div class="container-fluid">
    <div class="row">
        <div class="col-md-12">
            {{-- Bouton retour --}}
            <div class="mb-3">
                <a href="{{ route('centre.index') }}" class="btn btn-secondary">
                    <i class="fas fa-arrow-left"></i> @lang('trans.back_to_list')
                </a>
                
                @if($formation->attestation)
                <a href="{{ asset('/uploads/' . $formation->attestation) }}" class="btn btn-success" target="_blank">
                    <i class="fas fa-download"></i> @lang('trans.download_certificate')
                </a>
                @endif
            </div>
            
            {{-- Informations du détenteur de licence --}}
            <div class="detail-card">
                <div class="detail-header">
                    <h4 class="mb-0">
                        <i class="fas fa-user-graduate mr-2"></i>
                        @lang('trans.licence_holder_information')
                    </h4>
                </div>
                <div class="detail-body">
                    @php
                        $demandeur = $formation->demandeur;
                    @endphp
                    
                    <div class="row">
                        <div class="col-md-6">
                            <div class="info-row">
                                <div class="info-label">@lang('trans.full_name'):</div>
                                <div class="info-value">{{ $demandeur->np ?? 'N/A' }}</div>
                            </div>
                            <div class="info-row">
                                <div class="info-label">@lang('trans.birth_date'):</div>
                                <div class="info-value">{{ $demandeur->date_naissance ? $demandeur->date_naissance : 'N/A' }}</div>
                            </div>
                            <div class="info-row">
                                <div class="info-label">@lang('trans.birth_place'):</div>
                                <div class="info-value">{{ $demandeur->lieu_naissance ?? 'N/A' }}</div>
                            </div>
                            <div class="info-row">
                                <div class="info-label">@lang('trans.nationality'):</div>
                                <div class="info-value">{{ $demandeur->nationalite ?? 'N/A' }}</div>
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="info-row">
                                <div class="info-label">@lang('trans.email'):</div>
                                <div class="info-value">{{ $demandeur->user->email ?? 'N/A' }}</div>
                            </div>
                            <div class="info-row">
                                <div class="info-label">@lang('trans.address'):</div>
                                <div class="info-value">{{ $demandeur->adresse ?? 'N/A' }}</div>
                            </div>
                            @if($demandeur->licence)
                            <div class="info-row">
                                <div class="info-label">@lang('trans.licence'):</div>
                                <div class="info-value">
                                    <span class="licence-badge">
                                        <i class="fas fa-id-card mr-1"></i>
                                        {{ $demandeur->licence->numero_licence }}
                                    </span>
                                    <div class="mt-2">
                                        <small>
                                            <strong>@lang('trans.category'):</strong> {{ $demandeur->licence->categorie_licence ?? 'N/A' }}<br>
                                            <strong>@lang('trans.type'):</strong> {{ $demandeur->licence->type_licence ?? 'N/A' }}<br>
                                            <strong>@lang('trans.expiry_date'):</strong> 
                                            {{ $demandeur->licence->date_expiration ? $demandeur->licence->date_expiration : 'N/A' }}
                                        </small>
                                    </div>
                                </div>
                            </div>
                            @endif
                        </div>
                    </div>
                </div>
            </div>
            
            {{-- Détails de la formation --}}
            <div class="detail-card">
                <div class="detail-header">
                    <h4 class="mb-0">
                        <i class="fas fa-chalkboard mr-2"></i>
                        @lang('trans.training_information')
                    </h4>
                </div>
                <div class="detail-body">
                    <div class="row">
                        <div class="col-md-6">
                            <div class="info-row">
                                <div class="info-label">@lang('trans.training_type'):</div>
                                <div class="info-value">{{ $formation->typeFormation->nom ?? 'N/A' }}</div>
                            </div>
                            
                            @if($formation->typeLicence)
                            <div class="info-row">
                                <div class="info-label">@lang('trans.licence_type'):</div>
                                <div class="info-value">{{ $formation->typeLicence->nom }}</div>
                            </div>
                            @endif
                            
                            @if($formation->intitule_formation)
                            <div class="info-row">
                                <div class="info-label">@lang('trans.training_title'):</div>
                                <div class="info-value">{{ $formation->intitule_formation }}</div>
                            </div>
                            @endif
                            
                            <div class="info-row">
                                <div class="info-label">@lang('trans.training_date'):</div>
                                <div class="info-value">{{ $formation->date_formation }}</div>
                            </div>
                            
                            @if($formation->lieu)
                            <div class="info-row">
                                <div class="info-label">@lang('trans.location'):</div>
                                <div class="info-value">{{ $formation->lieu }}</div>
                            </div>
                            @endif
                        </div>
                        
                        <div class="col-md-6">
                            @if($formation->instructeur)
                            <div class="info-row">
                                <div class="info-label">@lang('trans.instructor'):</div>
                                <div class="info-value">
                                    {{ $formation->instructeur->nom_complet }}<br>
                                    <small class="text-muted">
                                        @lang('trans.licence'): {{ $formation->instructeur->numero_licence }}
                                    </small>
                                </div>
                            </div>
                            @endif
                            
                            @if($formation->examinateur)
                            <div class="info-row">
                                <div class="info-label">@lang('trans.examiner'):</div>
                                <div class="info-value">
                                    {{ $formation->examinateur->nom_complet }}<br>
                                    <small class="text-muted">
                                        @lang('trans.licence'): {{ $formation->examinateur->numero_licence_examinateur }}
                                    </small>
                                </div>
                            </div>
                            @endif
                            
                            @if($formation->dispositifFormation)
                            <div class="info-row">
                                <div class="info-label">@lang('trans.training_device'):</div>
                                <div class="info-value">
                                    {{ $formation->dispositifFormation->simulateur->libelle ?? 'N/A' }}<br>
                                    <small class="text-muted">
                                        @lang('trans.status'): 
                                        @if($formation->dispositifFormation->statut == 'operationnel')
                                            <span class="badge badge-success">@lang('trans.operational')</span>
                                        @elseif($formation->dispositifFormation->statut == 'maintenance')
                                            <span class="badge badge-warning">@lang('trans.maintenance')</span>
                                        @else
                                            <span class="badge badge-danger">@lang('trans.out_of_service')</span>
                                        @endif
                                    </small>
                                </div>
                            </div>
                            @endif
                        </div>
                    </div>
                </div>
            </div>
            
            {{-- Informations supplémentaires --}}
            <div class="detail-card">
                <div class="detail-header">
                    <h4 class="mb-0">
                        <i class="fas fa-info-circle mr-2"></i>
                        @lang('trans.additional_information')
                    </h4>
                </div>
                <div class="detail-body">
                    <div class="row">
                        <div class="col-md-6">
                            <div class="info-row">
                                <div class="info-label">@lang('trans.centre_formation'):</div>
                                <div class="info-value">{{ $formation->centreFormation->libelle ?? 'N/A' }}</div>
                            </div>
                            <div class="info-row">
                                <div class="info-label">@lang('trans.created_at'):</div>
                                <div class="info-value">{{ $formation->created_at }}</div>
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="info-row">
                                <div class="info-label">@lang('trans.updated_at'):</div>
                                <div class="info-value">{{ $formation->updated_at }}</div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection