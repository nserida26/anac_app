@extends('layouts.admin')
@section('title')
    @lang('trans.dashboard_admin')
@endsection
@section('contentheader')
    @lang('trans.dashboard_admin')
@endsection
@section('contentheaderlink')
    <a href="{{ route('autorisations') }}">
        @lang('trans.dashboard_admin') </a>
@endsection
@section('contentheaderactive')
    @lang('trans.dashboard_admin')
@endsection

@push('css')
    <style>
        .info-section {
            margin-bottom: 30px;
        }
        .info-section h4 {
            background: #f4f6f9;
            padding: 10px 15px;
            border-left: 4px solid #007bff;
            margin-bottom: 15px;
        }
        .badge-status {
            font-size: 0.9rem;
            padding: 5px 10px;
        }
        .table-details th {
            width: 200px;
            background-color: #f8f9fa;
        }
        .document-link {
            margin-right: 10px;
        }
    </style>
@endpush

@section('content')
    <div class="container-fluid">
        <div class="row">
            <div class="col-md-12">
                <div class="card">
                    <div class="card-header bg-primary text-white d-flex justify-content-between align-items-center">
                        <h3 class="card-title mb-0">@lang('trans.authorization_details')</h3>
                        @isset($autorisation)
                            @php
                                $statusClass = match($autorisation->statut) {
                                    'validé' => 'success',
                                    'rejeté' => 'danger',
                                    default => 'warning'
                                };
                                $statusLabel = match($autorisation->statut) {
                                    'validé' => __('trans.validated'),
                                    'rejeté' => __('trans.rejected'),
                                    default => __('trans.pending')
                                };
                            @endphp
                            <span class="badge badge-{{ $statusClass }} badge-status">
                                {{ $statusLabel }}
                            </span>
                        @endisset
                    </div>
                    
                    <div class="card-body">
                        @isset($autorisation)
                            @php
                                $demande = $autorisation->demande;
                                $vols = $demande ? $demande->vols : collect();
                                $avions = $demande ? $demande->avions : collect();
                                $equipes = $demande ? $demande->equipe : collect();
                                $frets = $demande ? $demande->fret : collect();
                                $documents = $demande ? $demande->documents : collect();
                                $personnes = $demande ? $demande->personnes : collect();
                                $mdns = $demande ? $demande->mdns : collect();
                                $receivingParties = $demande ? $demande->receivingParties : collect();
                                $paiement = $demande ? $demande->paiement : null;
                            @endphp

                            <!-- Section 1: Informations Générales de l'Autorisation -->
                            <div class="info-section">
                                <h4><i class="fas fa-certificate mr-2"></i>@lang('trans.authorization_information')</h4>
                                <div class="table-responsive">
                                    <table class="table table-bordered table-details">
                                        <tr>
                                            <th>@lang('trans.code')</th>
                                            <td><strong>{{ $autorisation->code_autorisation ?? '-' }}</strong></td>
                                        </tr>
                                        <tr>
                                            <th>@lang('trans.deliverance_date')</th>
                                            <td>{{ !empty($autorisation->date_delivrance) ? date('d/m/Y', strtotime($autorisation->date_delivrance)) : '-' }}</td>
                                        </tr>
                                        <tr>
                                            <th>@lang('trans.expiration_date')</th>
                                            <td>{{ !empty($autorisation->date_expiration) ? date('d/m/Y', strtotime($autorisation->date_expiration)) : '-' }}</td>
                                        </tr>
                                        <tr>
                                            <th>@lang('trans.status')</th>
                                            <td>
                                                <span class="badge badge-{{ $statusClass }}">
                                                    {{ $statusLabel }}
                                                </span>
                                            </td>
                                        </tr>
                                        @if($autorisation->nom_signataire)
                                        <tr>
                                            <th>@lang('trans.signatory')</th>
                                            <td>{{ $autorisation->nom_signataire }}</td>
                                        </tr>
                                        @endif
                                    </table>
                                </div>
                            </div>

                            <!-- Section 2: Informations de la Demande -->
                            @if($demande)
                            <div class="info-section">
                                <h4><i class="fas fa-file-alt mr-2"></i>@lang('trans.request_information') #{{ $demande->code ?? $demande->id }}</h4>
                                <div class="table-responsive">
                                    <table class="table table-bordered table-details">
                                        <tr>
                                            <th>@lang('trans.request_code')</th>
                                            <td>{{ $demande->code ?? '-' }}</td>
                                        </tr>
                                        <tr>
                                            <th>@lang('trans.request_type')</th>
                                            <td>{{ $demande->type ? $demande->type->nom : '-' }}</td>
                                        </tr>
                                        <tr>
                                            <th>@lang('trans.flight_types')</th>
                                            <td>
                                                @if($demande->type_vols_list && $demande->type_vols_list->isNotEmpty())
                                                    {{ $demande->type_vols_list->pluck('nom')->implode(', ') }}
                                                @elseif($demande->typeVol)
                                                    {{ $demande->typeVol->nom }}
                                                @else
                                                    {{ $demande->type_vol_ids ? (is_array($demande->type_vol_ids) ? implode(', ', $demande->type_vol_ids) : $demande->type_vol_ids) : '-' }}
                                                @endif
                                            </td>
                                        </tr>
                                        <tr>
                                            <th>@lang('trans.purpose')</th>
                                            <td>{{ $demande->objet ?? '-' }}</td>
                                        </tr>
                                        <tr>
                                            <th>@lang('trans.validity_period')</th>
                                            <td>
                                                @if($demande->date_debut && $demande->date_fin)
                                                    @lang('trans.from') {{ date('d/m/Y', strtotime($demande->date_debut)) }} 
                                                    @lang('trans.to') {{ date('d/m/Y', strtotime($demande->date_fin)) }}
                                                @else
                                                    -
                                                @endif
                                            </td>
                                        </tr>
                                        <tr>
                                            <th>@lang('trans.submission_date')</th>
                                            <td>{{ $demande->date_soumission ? date('d/m/Y H:i', strtotime($demande->date_soumission)) : '-' }}</td>
                                        </tr>
                                        <tr>
                                            <th>@lang('trans.validation_date')</th>
                                            <td>{{ $demande->date_validation ? date('d/m/Y H:i', strtotime($demande->date_validation)) : '-' }}</td>
                                        </tr>
                                        <tr>
                                            <th>@lang('trans.applicant')</th>
                                            <td>{{ $demande->user ? $demande->user->name . ' (' . $demande->user->email . ')' : '-' }}</td>
                                        </tr>
                                        @if($demande->points)
                                        <tr>
                                            <th>@lang('trans.points')</th>
                                            <td>{{ $demande->points }}</td>
                                        </tr>
                                        @endif
                                    </table>
                                </div>
                            </div>
                            @endif

                            <!-- Section 3: Vols -->
                            @if($vols->isNotEmpty())
                            <div class="info-section">
                                <h4><i class="fas fa-plane-departure mr-2"></i>@lang('trans.flights') ({{ $vols->count() }})</h4>
                                @foreach($vols as $index => $vol)
                                @php
                                    $volStatusClass = isset($vol->valider) ? ($vol->valider ? 'success' : (is_null($vol->valider) ? 'warning' : 'danger')) : 'secondary';
                                    $volStatusLabel = isset($vol->valider) ? ($vol->valider ? __('trans.validated') : (is_null($vol->valider) ? __('trans.awaiting') : __('trans.rejected'))) : __('trans.not_available');
                                @endphp
                                <div class="card mb-3">
                                    <div class="card-header bg-light">
                                        <strong>@lang('trans.flight') #{{ $index + 1 }} - {{ $vol->numero_vol ?? __('trans.not_available') }}</strong>
                                        <span class="badge badge-{{ $volStatusClass }} float-right">
                                            {{ $volStatusLabel }}
                                        </span>
                                    </div>
                                    <div class="card-body">
                                        <table class="table table-sm table-bordered">
                                            <tr>
                                                <th width="200">@lang('trans.flight_number')</th>
                                                <td>{{ $vol->numero_vol ?? '-' }}</td>
                                            </tr>
                                            <tr>
                                                <th>@lang('trans.departure_airport')</th>
                                                <td>{{ $vol->aeroportDepart ? $vol->aeroportDepart->nom . ' (' . $vol->aeroportDepart->codeIATA . ')' : '-' }}</td>
                                            </tr>
                                            <tr>
                                                <th>@lang('trans.arrival_airport')</th>
                                                <td>{{ $vol->aeroportArrivee ? $vol->aeroportArrivee->nom . ' (' . $vol->aeroportArrivee->codeIATA . ')' : '-' }}</td>
                                            </tr>
                                            <tr>
                                                <th>@lang('trans.departure_date')</th>
                                                <td>{{ $vol->date_depart ?? '-' }}</td>
                                            </tr>
                                            <tr>
                                                <th>@lang('trans.arrival_date')</th>
                                                <td>{{ $vol->date_arrivee ?? '-' }}</td>
                                            </tr>
                                            <tr>
                                                <th>@lang('trans.passengers_count')</th>
                                                <td>{{ $vol->nbr_passagers ?? '-' }}</td>
                                            </tr>
                                            @if($vol->objet)
                                            <tr>
                                                <th>@lang('trans.flight_purpose')</th>
                                                <td>{{ $vol->objet }}</td>
                                            </tr>
                                            @endif
                                            @if($vol->motif)
                                            <tr>
                                                <th>@lang('trans.rejection_reason')</th>
                                                <td><span class="text-danger">{{ $vol->motif }}</span></td>
                                            </tr>
                                            @endif
                                        </table>
                                    </div>
                                </div>
                                @endforeach
                            </div>
                            @else
                            <div class="info-section">
                                <h4><i class="fas fa-plane-departure mr-2"></i>@lang('trans.flights')</h4>
                                <p class="text-muted">@lang('trans.no_flights')</p>
                            </div>
                            @endif

                            <!-- Section 4: Avions -->
                            @if($avions->isNotEmpty())
                            <div class="info-section">
                                <h4><i class="fas fa-plane mr-2"></i>@lang('trans.aircrafts') ({{ $avions->count() }})</h4>
                                @foreach($avions as $index => $avion)
                                @php
                                    $avionStatusClass = isset($avion->valider) ? ($avion->valider ? 'success' : (is_null($avion->valider) ? 'warning' : 'danger')) : 'secondary';
                                    $avionStatusLabel = isset($avion->valider) ? ($avion->valider ? __('trans.validated') : (is_null($avion->valider) ? __('trans.awaiting') : __('trans.rejected'))) : __('trans.not_available');
                                @endphp
                                <div class="card mb-3">
                                    <div class="card-header bg-light">
                                        <strong>@lang('trans.aircraft') #{{ $index + 1 }} - {{ $avion->immatriculation ?? __('trans.not_available') }}</strong>
                                        <span class="badge badge-{{ $avionStatusClass }} float-right">
                                            {{ $avionStatusLabel }}
                                        </span>
                                    </div>
                                    <div class="card-body">
                                        <table class="table table-sm table-bordered">
                                            <tr>
                                                <th width="200">@lang('trans.registration')</th>
                                                <td>{{ $avion->immatriculation ?? '-' }}</td>
                                            </tr>
                                            <tr>
                                                <th>@lang('trans.aircraft_type')</th>
                                                <td>{{ $avion->type ? $avion->type->code . ' (' . __('trans.capacity') . ': ' . $avion->type->capacite . ')' : '-' }}</td>
                                            </tr>
                                            <tr>
                                                <th>@lang('trans.owner')</th>
                                                <td>{{ $avion->proprietaire ? $avion->proprietaire->nom : '-' }}</td>
                                            </tr>
                                            <tr>
                                                <th>@lang('trans.company')</th>
                                                <td>{{ $avion->compagnie ? $avion->compagnie->nom_entreprise : '-' }}</td>
                                            </tr>
                                            @if($avion->motif)
                                            <tr>
                                                <th>@lang('trans.aircraft_rejection_reason')</th>
                                                <td><span class="text-danger">{{ $avion->motif }}</span></td>
                                            </tr>
                                            @endif
                                        </table>
                                    </div>
                                </div>
                                @endforeach
                            </div>
                            @else
                            <div class="info-section">
                                <h4><i class="fas fa-plane mr-2"></i>@lang('trans.aircrafts')</h4>
                                <p class="text-muted">@lang('trans.no_aircrafts')</p>
                            </div>
                            @endif

                            <!-- Section 5: Équipage -->
                            @if($equipes->isNotEmpty())
                            <div class="info-section">
                                <h4><i class="fas fa-users mr-2"></i>@lang('trans.crew') ({{ $equipes->count() }})</h4>
                                <div class="table-responsive">
                                    <table class="table table-bordered table-striped">
                                        <thead>
                                            <tr>
                                                <th>#</th>
                                                <th>@lang('trans.fullname')</th>
                                                <th>@lang('trans.function')</th>
                                                <th>@lang('trans.license_number')</th>
                                                <th>@lang('trans.validation')</th>
                                                @if($equipes->contains('motif'))
                                                <th>@lang('trans.rejection_reason')</th>
                                                @endif
                                            </tr>
                                        </thead>
                                        <tbody>
                                            @foreach($equipes as $index => $membre)
                                            @php
                                                $membreStatusClass = isset($membre->valider) ? ($membre->valider ? 'success' : (is_null($membre->valider) ? 'warning' : 'danger')) : 'secondary';
                                                $membreStatusLabel = isset($membre->valider) ? ($membre->valider ? __('trans.validated') : (is_null($membre->valider) ? __('trans.awaiting') : __('trans.rejected'))) : '-';
                                            @endphp
                                            <tr>
                                                <td>{{ $index + 1 }}</td>
                                                <td>{{ $membre->nom ?? '-' }} {{ $membre->prenom ?? '' }}</td>
                                                <td>{{ $membre->fonction ?? '-' }}</td>
                                                <td>{{ $membre->numero_licence ?? '-' }}</td>
                                                <td>
                                                    <span class="badge badge-{{ $membreStatusClass }}">
                                                        {{ $membreStatusLabel }}
                                                    </span>
                                                </td>
                                                @if($equipes->contains('motif'))
                                                <td>{{ $membre->motif ?? '-' }}</td>
                                                @endif
                                            </tr>
                                            @endforeach
                                        </tbody>
                                    </table>
                                </div>
                            </div>
                            @else
                            <div class="info-section">
                                <h4><i class="fas fa-users mr-2"></i>@lang('trans.crew')</h4>
                                <p class="text-muted">@lang('trans.no_crew')</p>
                            </div>
                            @endif

                            <!-- Section 6: Fret -->
                            @if($frets->isNotEmpty())
                            <div class="info-section">
                                <h4><i class="fas fa-boxes mr-2"></i>@lang('trans.freight_details') ({{ $frets->count() }})</h4>
                                <div class="table-responsive">
                                    <table class="table table-bordered table-striped">
                                        <thead>
                                            <tr>
                                                <th>#</th>
                                                <th>@lang('trans.nature')</th>
                                                <th>@lang('trans.quantity')</th>
                                                <th>@lang('trans.weight_kg')</th>
                                                <th>@lang('trans.validation')</th>
                                                @if($frets->contains('motif'))
                                                <th>@lang('trans.rejection_reason')</th>
                                                @endif
                                            </tr>
                                        </thead>
                                        <tbody>
                                            @foreach($frets as $index => $fret)
                                            @php
                                                $fretStatusClass = isset($fret->valider) ? ($fret->valider ? 'success' : (is_null($fret->valider) ? 'warning' : 'danger')) : 'secondary';
                                                $fretStatusLabel = isset($fret->valider) ? ($fret->valider ? __('trans.validated') : (is_null($fret->valider) ? __('trans.awaiting') : __('trans.rejected'))) : '-';
                                            @endphp
                                            <tr>
                                                <td>{{ $index + 1 }}</td>
                                                <td>{{ $fret->nature ?? '-' }}</td>
                                                <td>{{ $fret->quantite ?? '-' }}</td>
                                                <td>{{ $fret->poids ? $fret->poids . ' kg' : '-' }}</td>
                                                <td>
                                                    <span class="badge badge-{{ $fretStatusClass }}">
                                                        {{ $fretStatusLabel }}
                                                    </span>
                                                </td>
                                                @if($frets->contains('motif'))
                                                <td>{{ $fret->motif ?? '-' }}</td>
                                                @endif
                                            </tr>
                                            @endforeach
                                        </tbody>
                                    </table>
                                </div>
                            </div>
                            @else
                            <div class="info-section">
                                <h4><i class="fas fa-boxes mr-2"></i>@lang('trans.freight_details')</h4>
                                <p class="text-muted">@lang('trans.no_freight')</p>
                            </div>
                            @endif

                            <!-- Section 7: Personnes -->
                            @if($personnes->isNotEmpty())
                            <div class="info-section">
                                <h4><i class="fas fa-user-friends mr-2"></i>@lang('trans.persons') ({{ $personnes->count() }})</h4>
                                <div class="table-responsive">
                                    <table class="table table-bordered table-striped">
                                        <thead>
                                            <tr>
                                                <th>#</th>
                                                <th>@lang('trans.fullname')</th>
                                                <th>@lang('trans.nationality')</th>
                                                <th>@lang('trans.passport_number')</th>
                                                <th>@lang('trans.validation')</th>
                                            </tr>
                                        </thead>
                                        <tbody>
                                            @foreach($personnes as $index => $personne)
                                            @php
                                                $persStatusClass = isset($personne->valider) ? ($personne->valider ? 'success' : (is_null($personne->valider) ? 'warning' : 'danger')) : 'secondary';
                                                $persStatusLabel = isset($personne->valider) ? ($personne->valider ? __('trans.validated') : (is_null($personne->valider) ? __('trans.awaiting') : __('trans.rejected'))) : '-';
                                            @endphp
                                            <tr>
                                                <td>{{ $index + 1 }}</td>
                                                <td>{{ $personne->nom_prenom ?? $personne->nom . ' ' . $personne->prenom ?? '-' }}</td>
                                                <td>{{ $personne->nationalite ?? '-' }}</td>
                                                <td>{{ $personne->num_passeport ?? '-' }}</td>
                                                <td>
                                                    <span class="badge badge-{{ $persStatusClass }}">
                                                        {{ $persStatusLabel }}
                                                    </span>
                                                </td>
                                            </tr>
                                            @endforeach
                                        </tbody>
                                    </table>
                                </div>
                            </div>
                            @endif

                            <!-- Section 8: MDN -->
                            @if($mdns->isNotEmpty())
                            <div class="info-section">
                                <h4><i class="fas fa-exclamation-triangle mr-2"></i>@lang('trans.mdn') ({{ $mdns->count() }})</h4>
                                <div class="table-responsive">
                                    <table class="table table-bordered table-striped">
                                        <thead>
                                            <tr>
                                                <th>#</th>
                                                <th>@lang('trans.mdn_number')</th>
                                                <th>@lang('trans.type')</th>
                                                <th>@lang('trans.mdn_date')</th>
                                                <th>@lang('trans.validation')</th>
                                            </tr>
                                        </thead>
                                        <tbody>
                                            @foreach($mdns as $index => $mdn)
                                            @php
                                                $mdnStatusClass = isset($mdn->valider) ? ($mdn->valider ? 'success' : (is_null($mdn->valider) ? 'warning' : 'danger')) : 'secondary';
                                                $mdnStatusLabel = isset($mdn->valider) ? ($mdn->valider ? __('trans.validated') : (is_null($mdn->valider) ? __('trans.awaiting') : __('trans.rejected'))) : '-';
                                            @endphp
                                            <tr>
                                                <td>{{ $index + 1 }}</td>
                                                <td>{{ $mdn->numero_mdn ?? '-' }}</td>
                                                <td>{{ $mdn->type ?? '-' }}</td>
                                                <td>{{ isset($mdn->date) ? date('d/m/Y', strtotime($mdn->date)) : '-' }}</td>
                                                <td>
                                                    <span class="badge badge-{{ $mdnStatusClass }}">
                                                        {{ $mdnStatusLabel }}
                                                    </span>
                                                </td>
                                            </tr>
                                            @endforeach
                                        </tbody>
                                    </table>
                                </div>
                            </div>
                            @endif

                            <!-- Section 9: Documents -->
                            @if($documents->isNotEmpty())
                            <div class="info-section">
                                <h4><i class="fas fa-file-pdf mr-2"></i>@lang('trans.documents') ({{ $documents->count() }})</h4>
                                <div class="table-responsive">
                                    <table class="table table-bordered table-striped">
                                        <thead>
                                            <tr>
                                                <th>#</th>
                                                <th>@lang('trans.document_type')</th>
                                                <th>@lang('trans.file')</th>
                                                <th>@lang('trans.upload_date')</th>
                                                <th>@lang('trans.validation')</th>
                                            </tr>
                                        </thead>
                                        <tbody>
                                            @foreach($documents as $index => $document)
                                            @php
                                                $docStatusClass = isset($document->valider) ? ($document->valider ? 'success' : (is_null($document->valider) ? 'warning' : 'danger')) : 'secondary';
                                                $docStatusLabel = isset($document->valider) ? ($document->valider ? __('trans.validated') : (is_null($document->valider) ? __('trans.awaiting') : __('trans.rejected'))) : '-';
                                            @endphp
                                            <tr>
                                                <td>{{ $index + 1 }}</td>
                                                <td>{{ $document->typeDocument ? (LaravelLocalization::getCurrentLocale() == 'fr' ? $document->typeDocument->nom_fr : $document->typeDocument->nom_en) : __('trans.not_available') }}</td>
                                                <td>
                                                    @if($document->url)
                                                        <a href="{{ asset('/uploads/' . $document->url) }}" target="_blank" class="btn btn-sm btn-primary">
                                                            <i class="fas fa-eye"></i> @lang('trans.view')
                                                        </a>
                                                    @else
                                                        @lang('trans.document_not_available')
                                                    @endif
                                                </td>
                                                <td>{{ $document->created_at ? $document->created_at->format('d/m/Y H:i') : '-' }}</td>
                                                <td>
                                                    <span class="badge badge-{{ $docStatusClass }}">
                                                        {{ $docStatusLabel }}
                                                    </span>
                                                </td>
                                            </tr>
                                            @endforeach
                                        </tbody>
                                    </table>
                                </div>
                            </div>
                            @else
                            <div class="info-section">
                                <h4><i class="fas fa-file-pdf mr-2"></i>@lang('trans.documents')</h4>
                                <p class="text-muted">@lang('trans.no_documents')</p>
                            </div>
                            @endif

                            <!-- Section 10: Signatures et Cachet -->
                            <div class="info-section">
                                <h4><i class="fas fa-signature mr-2"></i>@lang('trans.signatures_and_stamp')</h4>
                                <div class="row text-center">
                                    <div class="col-md-4">
                                        <h5>@lang('trans.stamp')</h5>
                                        @if(isset($autorisation->cachet) && $autorisation->cachet != '')
                                            <img src="{{ asset('/uploads/' . $autorisation->cachet) }}" alt="@lang('trans.stamp')" class="img-thumbnail" width="150">
                                        @else
                                            <p class="text-muted">@lang('trans.no_stamp')</p>
                                        @endif
                                    </div>
                                    <div class="col-md-4">
                                        <h5>@lang('trans.signature_dg')</h5>
                                        @if(isset($autorisation->signature_dg) && $autorisation->signature_dg != '')
                                            <img src="{{ asset('/uploads/' . $autorisation->signature_dg) }}" alt="@lang('trans.signature_dg')" class="img-thumbnail" width="150">
                                        @else
                                            <p class="text-muted">@lang('trans.no_signature_dg')</p>
                                        @endif
                                    </div>
                                    <div class="col-md-4">
                                        <h5>@lang('trans.signature_dta')</h5>
                                        @if(isset($autorisation->signature_dta) && $autorisation->signature_dta != '')
                                            <img src="{{ asset('/uploads/' . $autorisation->signature_dta) }}" alt="@lang('trans.signature_dta')" class="img-thumbnail" width="150">
                                        @else
                                            <p class="text-muted">@lang('trans.no_signature_dta')</p>
                                        @endif
                                    </div>
                                </div>
                            </div>

                        @else
                            <div class="alert alert-warning">
                                <i class="fas fa-exclamation-triangle"></i> @lang('trans.no_authorization_found')
                            </div>
                        @endisset
                    </div>
                </div>
            </div>
        </div>
    </div>
@endsection

@push('script')
<script>
    $(document).ready(function() {
        // Initialiser les tooltips si nécessaire
        $('[data-toggle="tooltip"]').tooltip();
    });
</script>
@endpush

@push('custom')
@endpush