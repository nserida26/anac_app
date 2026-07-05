{{-- resources/views/admin/licences/index.blade.php --}}
@extends('layouts.admin')
@section('title')
    @lang('trans.dashboard_admin')
@endsection
@section('contentheader')
    @lang('trans.dashboard_admin')
@endsection
@section('contentheaderlink')
    <a href="{{ route('licences') }}">
        @lang('trans.dashboard_admin') </a>
@endsection
@section('contentheaderactive')
    @lang('trans.dashboard_admin')
@endsection
@push('css')
    <link rel="stylesheet" href="{{ asset('assets/admin/plugins/datatables-bs4/css/dataTables.bootstrap4.min.css') }}">
    <link rel="stylesheet" href="{{ asset('assets/admin/plugins/datatables-responsive/css/responsive.bootstrap4.min.css') }}">
        {{-- Date Range Picker CSS --}}
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/daterangepicker/daterangepicker.css" />
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/toastr.js/latest/toastr.min.css">
    
<!-- Styles additionnels -->
<style>
.modal-demande-table .badge {
    font-size: 0.85rem;
    padding: 5px 8px;
}
.modal-demande-table .badge i {
    margin-right: 3px;
}
.badge-current {
    background-color: #28a745;
    color: white;
    font-size: 0.7rem;
    padding: 2px 6px;
    border-radius: 10px;
    margin-left: 5px;
}
.demande-row-success {
    background-color: #d4edda !important;
}
.modal-demande-table td {
    vertical-align: middle;
}
.btn-group .btn {
    margin-right: 2px;
}
/* Tooltip personnalis� */
.tooltip-inner {
    max-width: 350px;
    text-align: left;
    background-color: #333;
    padding: 8px 12px;
    white-space: pre-line;
}
</style>
    <style>
        .demande-row-success {
            background-color: #d4edda !important;
            font-weight: bold;
        }
        .badge-current {
            background-color: #28a745;
            color: white;
            padding: 3px 8px;
            border-radius: 12px;
            font-size: 11px;
            margin-left: 5px;
        }
        .badge-expired { background-color: #dc3545; color: white; }
        .badge-expiring-soon { background-color: #ffc107; color: black; }
        .badge-valid { background-color: #28a745; color: white; }
        .badge-blocked { background-color: #6c757d; color: white; }
                {{-- Date filter container styles --}}
        .date-filter-container {
            padding: 15px;
            background: #f8f9fc;
            border-radius: 8px;
            margin-bottom: 20px;
            border: 1px solid #e3e6f0;
        }
        .date-filter-container .filter-group {
            display: flex;
            align-items: center;
            gap: 15px;
            flex-wrap: wrap;
        }
        .date-filter-container label {
            margin-bottom: 0;
            font-weight: 600;
            color: #4e73df;
        }
        .date-filter-container .daterange-picker {
            background: white;
            border: 1px solid #d1d3e2;
            border-radius: 0.35rem;
            padding: 6px 12px;
            cursor: pointer;
            min-width: 260px;
        }

        .dataTables_wrapper .dataTables_filter {
            float: right;
        }
        @media (max-width: 768px) {
            .date-filter-container .filter-group {
                flex-direction: column;
                align-items: stretch;
            }
            .date-filter-container .daterange-picker {
                width: 100%;
            }
        }
        .filter-section {
            background: #f8f9fa;
            padding: 15px;
            border-radius: 5px;
            margin-bottom: 20px;
        }
        .filter-section .form-group {
            margin-bottom: 0;
        }
        .btn-notify {
            margin-right: 5px;
        }
    </style>
    <style>
        .demande-row-success {
            background-color: #d4edda !important;
            font-weight: bold;
        }
        .demande-row-success td {
            border-color: #c3e6cb !important;
        }
        .badge-current {
            background-color: #28a745;
            color: white;
            padding: 3px 8px;
            border-radius: 12px;
            font-size: 11px;
            margin-left: 5px;
        }
        .modal-demande-table {
            font-size: 14px;
        }
        .modal-demande-table th {
            background-color: #f8f9fa;
        }
        .select2-container--open {
            z-index: 9999 !important;
        }
        .btn-group-vertical > .btn {
            margin-bottom: 5px;
        }
        .badge-submitted { background-color: #17a2b8; color: white; }
        .badge-under_review { background-color: #ffc107; color: black; }
        .badge-service_approved { background-color: #28a745; color: white; }
        .badge-paid { background-color: #007bff; color: white; }
        .badge-payment_confirmed { background-color: #20c997; color: white; }
        .badge-rejected { background-color: #dc3545; color: white; }
        .badge-printed{background-color: navy; color: white;}
    </style>
@endpush

@section('content')
    <div class="container-fluid">
        <div class="row">
            <div class="col-md-12">
                <!-- Filter Section -->
                <div class="card">
                    <div class="card-header">
                        <h5 class="mb-0">@lang('trans.filter_licences')</h5>
                    </div>
                    <div class="card-body filter-section">
                        <form method="GET" action="{{ route('licences') }}" class="row" id="licencesFilterForm">
                            <div class="col-md-3">
                                <div class="form-group">
                                    <label>@lang('trans.license_type')</label>
                                    <select name="type" class="form-control" onchange="this.form.submit()">
                                        <option value="all">@lang('trans.all_types')</option>
                                        @foreach($licenseTypes as $type)
                                            <option value="{{ $type }}" {{ request('type') == $type ? 'selected' : '' }}>
                                                {{ $type }}
                                            </option>
                                        @endforeach
                                    </select>
                                </div>
                            </div>

                            <div class="col-md-3">
                                <div class="form-group">
                                    <label>@lang('trans.status')</label>
                                    <select name="status" class="form-control" onchange="this.form.submit()">
                                        <option value="">@lang('trans.all_status')</option>
                                        <option value="valid" {{ request('status') == 'valid' ? 'selected' : '' }}>
                                            @lang('trans.valid')
                                        </option>
                                        <option value="expiring_soon" {{ request('status') == 'expiring_soon' ? 'selected' : '' }}>
                                            @lang('trans.expiring_soon') (15 @lang('trans.days'))
                                        </option>
                                        <option value="expired" {{ request('status') == 'expired' ? 'selected' : '' }}>
                                            @lang('trans.expired')
                                        </option>
                                    </select>
                                </div>
                            </div>

                            <input type="hidden" name="date_from" id="date_from" value="{{ $dateFrom }}">
                            <input type="hidden" name="date_to" id="date_to" value="{{ $dateTo }}">

                            <div class="col-md-3">
                                <div class="form-group">
                                    <label>&nbsp;</label>
                                    <div>
                                        <a href="{{ route('licences') }}" class="btn btn-secondary">
                                            @lang('trans.reset_filters')
                                        </a>
                                    </div>
                                </div>
                            </div>

                            <div class="col-md-3">
                                <div class="form-group">
                                    <label>&nbsp;</label>
                                    <div>
                                        <button type="button" class="btn btn-warning" onclick="sendAllNotifications()">
                                            <i class="fas fa-bell"></i> @lang('trans.send_all_notifications')
                                        </button>
                                    </div>
                                </div>
                            </div>
                        </form>
                    </div>
                </div>
                
                <!-- Licences Table -->
                <div class="card">
                    <div class="card-header">
                        @lang('trans.licences')
                        @if(request('status') == 'expiring_soon')
                            <span class="badge badge-warning ml-2">
                                @lang('trans.showing_expiring_soon')
                            </span>
                        @elseif(request('status') == 'expired')
                            <span class="badge badge-danger ml-2">
                                @lang('trans.showing_expired')
                            </span>
                        @endif
                        @if($dateFrom || $dateTo)
                            <span class="badge badge-info ml-2">
                                @lang('trans.date_range'): {{ $dateFrom }} - {{ $dateTo }}
                            </span>
                        @endif
                    </div>
                    <div class="card-body">
                    {{-- Date Range Filter Section --}}
                    <div class="date-filter-container">
                            <div class="filter-group">
                                <label><i class="fas fa-calendar-alt"></i> @lang('trans.filter_by_date') :</label>
                                <div id="dateRangePicker" class="daterange-picker">
                                    <i class="fas fa-calendar"></i>&nbsp;
                                    <span id="dateRangeLabel">@lang('trans.select_date_range')</span>
                                    <i class="fas fa-caret-down float-right mt-1"></i>
                                </div>
                                <button type="button" id="applyDateFilter" class="btn btn-success">
                                    <i class="fas fa-filter"></i> @lang('trans.apply_filter')
                                </button>
                                <button type="button" id="clearDateFilter" class="btn btn-danger">
                                    <i class="fas fa-eraser"></i> @lang('trans.clear_filter')
                                </button>
                            </div>
                        </div>
                        <div class="table-responsive">
                            <table class="table table-bordered table-striped" id="licences">
                                <thead>
                                    <tr>
                                        <th>@lang('trans.category')</th>
                                        <th>@lang('trans.type')</th>
                                        <th>@lang('trans.license_number')</th>
                                        <th>@lang('trans.fl_name')</th>
                                        <th>@lang('trans.dob')</th>
                                        <th>@lang('trans.address')</th>
                                        <th>@lang('trans.nationality')</th>
                                        <th>@lang('trans.expiry_date')</th>
                                        <th>@lang('trans.status')</th>
                                        <th>@lang('trans.actions')</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @foreach ($licences as $licence)
                                        @php
                                            $expiryStatus = $licence->expiry_status;
                                            $rowClass = '';
                                            if ($expiryStatus['key'] == 'expiring_soon') {
                                                $rowClass = 'table-warning';
                                            } elseif ($expiryStatus['key'] == 'expired') {
                                                $rowClass = 'table-danger';
                                            }
                                        @endphp
                                        <tr class="{{ $rowClass }}">
                                            <td>{{ $licence->categorie_licence }}</td>
                                            <td>{{ $licence->type_licence }}</td>
                                            <td><strong>{{ $licence->numero_licence }}</strong></td>
                                            <td>{{ $licence->np }}</td>
                                            <td>{{ date('d/m/Y', strtotime($licence->date_naissance)) }}</td>
                                            <td>{{ $licence->adresse }}</td>
                                            <td>{{ strtoupper($licence->nationalite) }}</td>
                                            <td>{{ $licence->date_expiration ? date('d/m/Y', strtotime($licence->date_expiration)) : '-' }}</td>
                                            <td>
                                                @if (is_array($expiryStatus))
                                                    @php
                                                        $badgeClass = match($expiryStatus['key']) {
                                                            'expired' => 'badge-expired',
                                                            'expiring_soon' => 'badge-expiring-soon',
                                                            'valid' => 'badge-valid',
                                                            'blocked' => 'badge-blocked',
                                                            default => 'badge-secondary'
                                                        };
                                                    @endphp
                                                    <span class="badge {{ $badgeClass }}">
                                                        @lang('trans.license_status.' . $expiryStatus['key'], $expiryStatus)
                                                    </span>
                                                @else
                                                    <span class="badge badge-danger">@lang('trans.license_status.' . $expiryStatus)</span>
                                                @endif
                                            </td>
                                            <td>
                                                <a href="{{ route('licences.show', $licence) }}" class="btn btn-info btn-sm">
                                                    <i class="fas fa-eye"></i>
                                                </a>
                                                
                                                <!-- Send Notification Button (for expiring soon licences) -->
                                                @if($licence->isNearingExpiry(15) && $licence->licence_valide)
                                                    <button type="button" class="btn btn-warning btn-sm btn-notify" 
                                                            onclick="sendNotification({{ $licence->id }}, '{{ $licence->numero_licence }}')">
                                                        <i class="fab fa-whatsapp"></i>
                                                    </button>
                                                @endif
                                                
                                                <button type="button" class="btn btn-primary btn-sm" 
                                                        data-toggle="modal" 
                                                        data-target="#demandesModal{{ $licence->id }}">
                                                    <i class="fas fa-list"></i>
                                                </button>
                                                
                                                @if (!$licence->licence_valide)
                                                    <form action="{{ route('licences.valider', $licence) }}" method="POST" class="d-inline">
                                                        @csrf
                                                        @method('PATCH')
                                                        <button type="submit" class="btn btn-primary btn-sm" onclick="return confirm('Confirmer la validation de la licence ?')">
                                                            <i class="fas fa-check"></i>
                                                        </button>
                                                    </form>
                                                @endif
                                                
                                                @if ($licence->licence_valide)
                                                    <form action="{{ route('licences.bloquer', $licence) }}" method="POST" class="d-inline">
                                                        @csrf
                                                        @method('PATCH')
                                                        <button type="submit" class="btn btn-danger btn-sm" onclick="return confirm('Confirmer la r�vocation de la licence ?')">
                                                            <i class="fas fa-ban"></i>
                                                        </button>
                                                    </form>
                                                    <a href="{{ route('licences.imprimer', $licence->demande->id) }}" class="btn btn-primary btn-sm" target="_blank">
                                                        <i class="fas fa-print"></i>
                                                    </a>
                                                    <a href="{{ route('authentifications.imprimer', $licence->demande->id) }}"
                                                                    class="btn btn-primary btn-sm"
                                                                    target="_blank">@lang('trans.print_authentication')</a>
                                                @endif
                                                
                                                <form action="{{ route('licences.supprimer', $licence) }}" method="POST" class="d-inline">
                                                    @csrf
                                                    @method('PATCH')
                                                    <button type="submit" class="btn btn-danger btn-sm" onclick="return confirm('Confirmer la suppression de la licence ?')">
                                                        <i class="fas fa-trash"></i>
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
            </div>
        </div>
    </div>

<!-- Demandes Modals -->
@foreach ($licences as $licence)
    @php
        $demandes = $licence->demandeur ? $licence->demandeur->demandes : collect([]);
        $currentDemandeId = $licence->demande_id ?? ($licence->demande->id ?? null);
    @endphp
    
    <div class="modal fade" id="demandesModal{{ $licence->id }}" tabindex="-1" role="dialog" aria-labelledby="demandesModalLabel{{ $licence->id }}" aria-hidden="true">
        <div class="modal-dialog modal-lg" role="document">
            <div class="modal-content">
                <div class="modal-header bg-primary text-white">
                    <h5 class="modal-title" id="demandesModalLabel{{ $licence->id }}">
                        <i class="fas fa-list-alt mr-2"></i>
                        @lang('trans.demands_for_license') : {{ $licence->numero_licence }}
                    </h5>
                    <button type="button" class="close text-white" data-dismiss="modal" aria-label="Close">
                        <span aria-hidden="true">&times;</span>
                    </button>
                </div>
                <div class="modal-body">
                    @if($demandes->count() > 0)
                        <div class="alert alert-info">
                            <i class="fas fa-info-circle"></i>
                            @lang('trans.total_demands'): <strong>{{ $demandes->count() }}</strong>
                            @if($currentDemandeId)
                                <span class="badge badge-success ml-2">
                                    <i class="fas fa-check"></i> @lang('trans.current_demand_highlighted')
                                </span>
                            @endif
                        </div>
                        
                        <div class="table-responsive">
                            <table class="table table-bordered table-striped modal-demande-table">
                                <thead>
                                    <tr>
                                        <th>#</th>
                                        <th>@lang('trans.code')</th>
                                        <th>@lang('trans.demand_type')</th>
                                        <th>@lang('trans.date_demande')</th>
                                        <th>@lang('trans.status')</th>
                                        <th>@lang('trans.license_type')</th>
                                        <th>@lang('trans.medical_validity')</th>
                                        <th>@lang('trans.competences_validity')</th>
                                        <th>@lang('trans.actions')</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @foreach($demandes as $index => $demande)
                                        @php
                                            $isCurrentDemande = ($demande->id == $currentDemandeId);
                                            $rowClass = $isCurrentDemande ? 'demande-row-success' : '';
                                            $etatDemande = $demande->etat_workflow;
                                            
                                            // Calcul de la validit� m�dicale
                                            $medicalStatus = [
                                                'status' => 'none',
                                                'label' => __('trans.no_medical_exam'),
                                                'class' => 'badge-secondary',
                                                'days_left' => null,
                                                'is_expired' => false
                                            ];
                                            
                                            $latestMedical = $demande->medicalExaminations()
                                                ->orderBy('date_examen', 'desc')
                                                ->first();
                                                
                                            if ($latestMedical) {
                                                $examDate = \Carbon\Carbon::parse($latestMedical->date_examen);
                                                $validityMonths = $latestMedical->validite ?? 2; // d�faut 2 mois
                                                $expiryDate = $examDate->copy()->addMonths($validityMonths);
                                                $now = \Carbon\Carbon::now();
                                                $daysLeft = $now->diffInDays($expiryDate, false);
                                                
                                                if ($daysLeft < 0) {
                                                    $medicalStatus = [
                                                        'status' => 'expired',
                                                        'label' => __('trans.expired_since', ['days' => abs($daysLeft)]),
                                                        'class' => 'badge-danger',
                                                        'days_left' => $daysLeft,
                                                        'is_expired' => true,
                                                        'expiry_date' => $expiryDate->format('d/m/Y')
                                                    ];
                                                } elseif ($daysLeft <= 30) {
                                                    $medicalStatus = [
                                                        'status' => 'expiring_soon',
                                                        'label' => __('trans.expires_in_days', ['days' => $daysLeft]),
                                                        'class' => 'badge-warning',
                                                        'days_left' => $daysLeft,
                                                        'is_expired' => false,
                                                        'expiry_date' => $expiryDate->format('d/m/Y')
                                                    ];
                                                } else {
                                                    $medicalStatus = [
                                                        'status' => 'valid',
                                                        'label' => __('trans.valid_for_days', ['days' => $daysLeft]),
                                                        'class' => 'badge-success',
                                                        'days_left' => $daysLeft,
                                                        'is_expired' => false,
                                                        'expiry_date' => $expiryDate->format('d/m/Y')
                                                    ];
                                                }
                                            }
                                            
                                            // Calcul de la validit� des comp�tences
                                            $competenceStatus = [
                                                'status' => 'none',
                                                'label' => __('trans.no_competence'),
                                                'class' => 'badge-secondary',
                                                'expired_count' => 0,
                                                'expiring_soon_count' => 0,
                                                'valid_count' => 0,
                                                'total_count' => 0,
                                                'details' => []
                                            ];
                                            
                                            $competences = $demande->competences()
                                                ->orderBy('date', 'desc')
                                                ->get();
                                                
                                            if ($competences->count() > 0) {
                                                $competenceStatus['total_count'] = $competences->count();
                                                $now = \Carbon\Carbon::now();
                                                
                                                foreach ($competences as $competence) {
                                                    $compDate = \Carbon\Carbon::parse($competence->date);
                                                    $validityMonths = $competence->validite ?? 2; // d�faut 2 mois
                                                    $expiryDate = $compDate->copy()->addMonths($validityMonths);
                                                    $daysLeft = $now->diffInDays($expiryDate, false);
                                                    
                                                    $detail = [
                                                        'type' => $competence->type,
                                                        'date' => $compDate->format('d/m/Y'),
                                                        'validity_months' => $validityMonths,
                                                        'expiry_date' => $expiryDate->format('d/m/Y'),
                                                        'days_left' => $daysLeft,
                                                        'is_expired' => $daysLeft < 0,
                                                        'is_expiring_soon' => $daysLeft >= 0 && $daysLeft <= 60
                                                    ];
                                                    
                                                    if ($daysLeft < 0) {
                                                        $competenceStatus['expired_count']++;
                                                        $detail['status'] = 'expired';
                                                    } elseif ($daysLeft <= 60) {
                                                        $competenceStatus['expiring_soon_count']++;
                                                        $detail['status'] = 'expiring_soon';
                                                    } else {
                                                        $competenceStatus['valid_count']++;
                                                        $detail['status'] = 'valid';
                                                    }
                                                    
                                                    $competenceStatus['details'][] = $detail;
                                                }
                                                
                                                // D�terminer le statut global
                                                if ($competenceStatus['expired_count'] > 0) {
                                                    $competenceStatus['status'] = 'expired';
                                                    $competenceStatus['label'] = __('trans.competence_expired_count', ['count' => $competenceStatus['expired_count']]);
                                                    $competenceStatus['class'] = 'badge-danger';
                                                } elseif ($competenceStatus['expiring_soon_count'] > 0) {
                                                    $competenceStatus['status'] = 'expiring_soon';
                                                    $competenceStatus['label'] = __('trans.competence_expiring_count', ['count' => $competenceStatus['expiring_soon_count']]);
                                                    $competenceStatus['class'] = 'badge-warning';
                                                } else {
                                                    $competenceStatus['status'] = 'valid';
                                                    $competenceStatus['label'] = __('trans.all_competences_valid');
                                                    $competenceStatus['class'] = 'badge-success';
                                                }
                                            }
                                        @endphp
                                        <tr class="{{ $rowClass }}">
                                            <td>{{ $index + 1 }}</td>
                                            <td>
                                                <strong>#{{ $demande->code }}</strong>
                                                @if($isCurrentDemande)
                                                    <span class="badge-current">@lang('trans.current')</span>
                                                @endif
                                            </td>
                                            <td>{{ LaravelLocalization::getCurrentLocale() == 'fr' ? optional($demande->typeDemande)->nom_fr : optional($demande->typeDemande)->nom_en }}</td>
                                            <td>{{ $demande->created_at ? date('d/m/Y H:i', strtotime($demande->created_at)) : '-' }}</td>
                                            <td>
                                                @php
                                                    $badgeClass = match($etatDemande) {
                                                        'submitted' => 'badge-submitted',
                                                        'under_review' => 'badge-under_review',
                                                        'service_approved' => 'badge-service_approved',
                                                        'paid' => 'badge-paid',
                                                        'payment_confirmed' => 'badge-payment_confirmed',
                                                        'rejected' => 'badge-rejected',
                                                        'printed' => 'badge-printed',
                                                        default => 'badge-secondary'
                                                    };
                                                @endphp
                                                <span class="badge {{ $badgeClass }}">
                                                    {{ $etatDemande }}
                                                </span>
                                            </td>
                                            <td>{{ LaravelLocalization::getCurrentLocale() == 'fr' ? optional($demande->typeLicence)->fr : optional($demande->typeLicence)->en }}</td>
                                            <td>
                                                @if($latestMedical)
                                                    <span class="badge {{ $medicalStatus['class'] }}" 
                                                          data-toggle="tooltip" 
                                                          title="{{ __('trans.exam_date') }}: {{ \Carbon\Carbon::parse($latestMedical->date_examen)->format('d/m/Y') }} | {{ __('trans.validity') }}: {{ $latestMedical->validite ?? 2 }} {{ __('trans.months') }} | {{ __('trans.expiry_date') }}: {{ $medicalStatus['expiry_date'] }}">
                                                        @if($medicalStatus['status'] == 'expired')
                                                            <i class="fas fa-times-circle"></i>
                                                        @elseif($medicalStatus['status'] == 'expiring_soon')
                                                            <i class="fas fa-exclamation-triangle"></i>
                                                        @else
                                                            <i class="fas fa-check-circle"></i>
                                                        @endif
                                                        {{ $medicalStatus['label'] }}
                                                    </span>
                                                @else
                                                    <span class="badge {{ $medicalStatus['class'] }}">
                                                        <i class="fas fa-minus-circle"></i>
                                                        {{ $medicalStatus['label'] }}
                                                    </span>
                                                @endif
                                            </td>
                                            <td>
                                                @if($competenceStatus['total_count'] > 0)
                                                    <span class="badge {{ $competenceStatus['class'] }}" 
                                                          data-toggle="tooltip" 
                                                          data-html="true"
                                                          title="@foreach($competenceStatus['details'] as $detail){{ $detail['type'] }}: {{ $detail['date'] }} ({{ $detail['validity_months'] }} mois) - @if($detail['is_expired'])Expir� depuis {{ abs($detail['days_left']) }}j @else Expire le {{ $detail['expiry_date'] }}@endif&#10;@endforeach">
                                                        @if($competenceStatus['status'] == 'expired')
                                                            <i class="fas fa-times-circle"></i>
                                                        @elseif($competenceStatus['status'] == 'expiring_soon')
                                                            <i class="fas fa-exclamation-triangle"></i>
                                                        @else
                                                            <i class="fas fa-check-circle"></i>
                                                        @endif
                                                        {{ $competenceStatus['label'] }}
                                                        <small class="text-muted">({{ $competenceStatus['total_count'] }})</small>
                                                    </span>
                                                @else
                                                    <span class="badge {{ $competenceStatus['class'] }}">
                                                        <i class="fas fa-minus-circle"></i>
                                                        {{ $competenceStatus['label'] }}
                                                    </span>
                                                @endif
                                            </td>
                                            <td>
                                                <div class="btn-group" role="group">
                                                    <a href="{{ route('demandes.show', $demande->id) }}" class="btn btn-sm btn-info" target="_blank" data-toggle="tooltip" title="@lang('trans.view_demand')">
                                                        <i class="fas fa-eye"></i>
                                                    </a>
                                                    @if($demande->licence)
                                                        <a href="{{ route('licences.show', $demande->licence->id) }}" class="btn btn-sm btn-success" target="_blank" data-toggle="tooltip" title="@lang('trans.view_license')">
                                                            <i class="fas fa-id-card"></i>
                                                        </a>
                                                    @endif
                                                </div>
                                            </td>
                                        </tr>
                                    @endforeach
                                </tbody>
                            </table>
                        </div>
                        
                        <!-- L�gende -->
                        <div class="mt-3">
                            <div class="row">
                                <div class="col-md-6">
                                    <h6>@lang('trans.medical_validity_legend'):</h6>
                                    <span class="badge badge-success mr-2"><i class="fas fa-check-circle"></i> @lang('trans.valid')</span>
                                    <span class="badge badge-warning mr-2"><i class="fas fa-exclamation-triangle"></i> @lang('trans.expires_in_30_days')</span>
                                    <span class="badge badge-danger mr-2"><i class="fas fa-times-circle"></i> @lang('trans.expired')</span>
                                    <span class="badge badge-secondary"><i class="fas fa-minus-circle"></i> @lang('trans.not_available')</span>
                                </div>
                                <div class="col-md-6">
                                    <h6>@lang('trans.competence_validity_legend'):</h6>
                                    <span class="badge badge-success mr-2"><i class="fas fa-check-circle"></i> @lang('trans.all_valid')</span>
                                    <span class="badge badge-warning mr-2"><i class="fas fa-exclamation-triangle"></i> @lang('trans.some_expiring')</span>
                                    <span class="badge badge-danger mr-2"><i class="fas fa-times-circle"></i> @lang('trans.some_expired')</span>
                                    <span class="badge badge-secondary"><i class="fas fa-minus-circle"></i> @lang('trans.none')</span>
                                </div>
                            </div>
                        </div>
                    @else
                        <div class="alert alert-warning">
                            <i class="fas fa-exclamation-triangle"></i>
                            @lang('trans.no_demands_found')
                        </div>
                    @endif
                </div>
                <div class="modal-footer">
                    <div class="text-left text-muted small">
                        <span class="badge badge-success" style="background-color: #d4edda; color: #155724; border: 1px solid #c3e6cb;">&nbsp;&nbsp;&nbsp;</span> 
                        @lang('trans.current_demand')
                    </div>
                    <button type="button" class="btn btn-secondary" data-dismiss="modal">
                        <i class="fas fa-times"></i> @lang('trans.close')
                    </button>
                </div>
            </div>
        </div>
    </div>
@endforeach



@endsection

@push('script')
    <script src="{{ asset('assets/admin/plugins/datatables/jquery.dataTables.min.js') }}"></script>
    <script src="{{ asset('assets/admin/plugins/datatables-bs4/js/dataTables.bootstrap4.min.js') }}"></script>
    <script src="{{ asset('assets/admin/plugins/datatables-responsive/js/dataTables.responsive.min.js') }}"></script>
    <script src="{{ asset('assets/admin/plugins/datatables-responsive/js/responsive.bootstrap4.min.js') }}"></script>
    <script type="text/javascript" src="{{ asset('assets/admin/plugins/moment/moment.min.js') }}"></script>
    <script type="text/javascript" src="{{ asset('assets/admin/plugins/daterangepicker/daterangepicker.js') }}"></script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/toastr.js/latest/toastr.min.js"></script>
    
@endpush

@push('custom')
<!-- Script pour initialiser les tooltips -->
<script>
$(document).ready(function() {
    $('[data-toggle="tooltip"]').tooltip({
        placement: 'top',
        html: true
    });
});
</script>
    <script>
    $(function() {
            // Initialize DataTable
            var table = $('#licences').DataTable({
                "paging": true,
                "lengthChange": false,
                "searching": true,
                "ordering": true,
                "info": true,
                "autoWidth": false,
                "responsive": true,
                "columnDefs": [{
                        "targets": [7],
                        "orderable": false,
                        "searchable": false
                    },
                    {
                        "targets": 3,
                        "searchable": true
                    }
                ],
                "order": [[1, 'desc']] // Order by date column descending by default
            });

            // Date Range Picker Configuration (server-side filter on license expiration date)
            let startDate = @json($dateFrom) || null;
            let endDate = @json($dateTo) || null;

            // Initialize the date range picker
            var pickerOptions = {
                autoUpdateInput: false,
                locale: {
                    format: 'YYYY-MM-DD',
                    applyLabel: '@lang("trans.apply")',
                    cancelLabel: '@lang("trans.cancel")',
                    fromLabel: '@lang("trans.from")',
                    toLabel: '@lang("trans.to")',
                    customRangeLabel: '@lang("trans.custom")'
                },
                ranges: {
                    "@lang('trans.today')": [moment(), moment()],
                    '@lang("trans.yesterday")': [moment().subtract(1, 'days'), moment().subtract(1, 'days')],
                    '@lang("trans.last_7_days")': [moment().subtract(6, 'days'), moment()],
                    '@lang("trans.last_30_days")': [moment().subtract(29, 'days'), moment()],
                    '@lang("trans.this_month")': [moment().startOf('month'), moment().endOf('month')],
                    '@lang("trans.last_month")': [moment().subtract(1, 'month').startOf('month'), moment().subtract(1, 'month').endOf('month')]
                }
            };
            if (startDate && endDate) {
                pickerOptions.startDate = moment(startDate, 'YYYY-MM-DD');
                pickerOptions.endDate = moment(endDate, 'YYYY-MM-DD');
            }
            $('#dateRangePicker').daterangepicker(pickerOptions);

            if (startDate && endDate) {
                $('#dateRangeLabel').text(startDate + ' - ' + endDate);
            }

            // Handle date range selection
            $('#dateRangePicker').on('apply.daterangepicker', function(ev, picker) {
                startDate = picker.startDate.format('YYYY-MM-DD');
                endDate = picker.endDate.format('YYYY-MM-DD');
                $('#dateRangeLabel').text(startDate + ' - ' + endDate);
            });

            $('#dateRangePicker').on('cancel.daterangepicker', function(ev, picker) {
                startDate = null;
                endDate = null;
                $('#dateRangeLabel').text('@lang("trans.select_date_range")');
            });

            // Apply filter button click: submit the date range to the server (filters on trans.expiry_date)
            $('#applyDateFilter').on('click', function() {
                if (startDate && endDate) {
                    $('#date_from').val(startDate);
                    $('#date_to').val(endDate);
                    $('#licencesFilterForm').submit();
                } else {
                    alert("@lang('trans.select_date_range_first')");
                }
            });

            // Clear filter button click: remove the date range and reload with other filters kept
            $('#clearDateFilter').on('click', function() {
                startDate = null;
                endDate = null;
                $('#dateRangeLabel').text('@lang("trans.select_date_range")');
                $('#date_from').val('');
                $('#date_to').val('');
                $('#licencesFilterForm').submit();
            });

            // Add simple search inputs for other columns
            table.columns().every(function(index) {
                if (index !== 7) { // Skip actions and date column (handled separately)
                    var column = this;
                    var $input = $('<input type="text" placeholder="@lang("trans.search")" class="form-control form-control-sm">')
                        .appendTo($(column.header()))
                        .on('keyup change', function() {
                            column.search(this.value).draw();
                        });
                }
            });
            
            // Add a custom search input for date column header
            var dateHeader = $(table.column(7).header());
            dateHeader.html(`@lang('trans.date')<br><small class="text-muted">(@lang('trans.use_filter_above'))</small>`);
        });
    

        function sendNotification(licenceId, licenceNumber) {
            if (confirm('@lang('trans.confirm_send_notification') ' + licenceNumber + '?')) {
                $.ajax({
                    url: '{{ route("licences.send-notification", ":id") }}'.replace(':id', licenceId),
                    type: 'POST',
                    data: {
                        _token: '{{ csrf_token() }}'
                    },
                    success: function(response) {
                        alert('@lang('trans.notification_sent_successfully')');
                    },
                    error: function() {
                        alert('@lang('trans.error_sending_notification')');
                    }
                });
            }
        }
        
        function sendAllNotifications() {
            if (confirm('@lang('trans.confirm_send_all_notifications')')) {
                window.location.href = '{{ route("licences.send-all-notifications") }}';
            }
        }
    </script>
@endpush