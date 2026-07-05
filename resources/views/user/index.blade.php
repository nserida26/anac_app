@extends('user.layouts.app')
@section('title')
    @lang('trans.dashboard')
@endsection
@section('contentheader')
    @lang('trans.dashboard')
@endsection
@section('contentheaderlink')
    <a href="">
        @lang('trans.dashboard') </a>
@endsection
@section('contentheaderactive')
    @lang('trans.dashboard')
@endsection
@push('css')
    <link rel="stylesheet" href="{{ asset('assets/admin/plugins/datatables-bs4/css/dataTables.bootstrap4.min.css') }}">
    <link rel="stylesheet" href="{{ asset('assets/admin/plugins/datatables-responsive/css/responsive.bootstrap4.min.css') }}">
    <link rel="stylesheet" href="{{ asset('assets/admin/plugins/toastr/toastr.min.css') }}">
    <link rel="stylesheet" href="{{ asset('assets/admin/plugins/select2/css/select2.min.css') }}">
    <link rel="stylesheet" href="{{ asset('assets/admin/plugins/select2-bootstrap4-theme/select2-bootstrap4.min.css') }}">
    <style>
        .badge-submitted { background-color: #17a2b8; color: white; }
        .badge-under_review { background-color: #ffc107; color: black; }
        .badge-service_approved { background-color: #28a745; color: white; }
        .badge-paid { background-color: #007bff; color: white; }
        .badge-payment_confirmed { background-color: #20c997; color: white; }
        .badge-printed{background-color: navy; color: white;}
        .badge-rejected { background-color: #dc3545; color: white; }
        
        .table-danger {
            background-color: rgba(220, 53, 69, 0.1) !important;
        }

        .btn-group-compact .btn {
            padding: 0.25rem 0.5rem;
            font-size: 0.875rem;
        }

        .modal-issues .list-group-item {
            border-left: 3px solid #dc3545;
        }

        @media (max-width: 768px) {
            td {
                white-space: normal !important;
            }
        }
    </style>
@endpush
@section('content')
    <div class="container-fluid">
        @if (Auth::user()->user_type === 'licence')
            <div class="row">
                <div class="col-md-12">
                    <div class="card">
                        <div class="card-header">@lang('trans.license_applications')</div>
                        <div class="card-body">
                            @isset(Auth::user()->demandeur)
                                <a href="{{ url('user/create') }}" class="btn btn-success btn-sm">
                                    <i class="fa fa-plus" aria-hidden="true"></i> @lang('trans.add')
                                </a>
                            @endisset
                            <div class="table-responsive">
                                <table class="table table-bordered table-striped" id="license_applications">
                                    <thead>
                                        <tr>
                                            <th>@lang('trans.id')</th>
                                            <th>@lang('trans.applicant')</th>
                                            <th>@lang('trans.type_application')</th>
                                            <th>@lang('trans.type_license')</th>
                                            <th>@lang('trans.status')</th>
                                            <th>@lang('trans.actions')</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        @foreach ($demandes as $demande)
                                         @php
                                            $etatDemande = $demande->etat_workflow;
                                            @endphp
                                            <tr>
                                                <td>{{ $demande->code }}</td>
                                                <td>{{ $demande->demandeur->np }}</td>
                                                <td>{{ LaravelLocalization::getCurrentLocale() == 'fr' ? optional($demande->typeDemande)->nom_fr : optional($demande->typeDemande)->nom_en }}
                                                </td>
                                                <td>{{ $demande->typeLicence->nom }}</td>
                                                <td>
                                            @php
                                                $badgeClass = match($etatDemande) {
                                                    'submitted' => 'badge-submitted',
                                                    'under_review' => 'badge-under_review',
                                                    'service_approved' => 'badge-service_approved',
                                                    'paid' => 'badge-paid',
                                                    'payment_confirmed' => 'badge-payment_confirmed',
                                                     'printed' => 'badge-printed',
                                                    'rejected' => 'badge-rejected',
                                                    default => 'badge-secondary'
                                                };
                                            @endphp
                                            <span class="badge {{ $badgeClass }}">
                                                {{ $etatDemande }}
                                            </span>
                                            </td>
                                                <td>
                                                    @if (!$demande->etatDemande->demandeur_cree_demande)
                                                        <a href="{{ route('user.licences.edit', $demande->id) }}"
                                                            class="btn btn-warning btn-sm">@lang('trans.edit')</a>

                                                        @php
                                                            $isValid = true;
                                                        @endphp

                                                        @if ($isValid)
                                                            <form action="{{ route('update-state-licence', $demande->id) }}" method="POST" class="d-inline">
                                                                @csrf
                                                                <input type="hidden" name="action" value="demandeur_cree_demande">
                                                                <input type="hidden" name="is_approved" value="1">
                                                                <button type="submit" class="btn btn-success btn-sm mb-1"
                                                                    onclick="return confirm('Confirmer la validation de la demande ?')">
                                                                    <i class="fas fa-check-circle"></i> @lang('trans.validate')
                                                                </button>
                                                            </form>
                                                        @else
                                                            <div class="alert alert-danger">
                                                                @lang('trans.missing_requirements')
                                                            </div>
                                                        @endif

                                                        <form action="{{ route('user.licences.destroy', $demande->id) }}"
                                                            method="POST" class="d-inline">
                                                            @csrf
                                                            @method('DELETE')
                                                            <button type="submit" class="btn btn-danger btn-sm"
                                                                onclick="return confirm('Confirmer la suppression ?')">@lang('trans.destroy')</button>
                                                        </form>
                                                    @endif
                                                    @if (!empty($demande->paiement) && !$demande->etatDemande->demandeur_payer && !$demande->etatDemande->compagnie_payer)
                                                        <a href="{{ route('user.licences.pay', $demande->paiement->id) }}"
                                                            class="btn btn-primary  btn-sm">Payer</a>
                                                    @endif
                                                    @if (!empty($demande->facture) && !$demande->etatDemande->demandeur_payer && !$demande->etatDemande->compagnie_payer)
                                                        <button class="btn btn-warning btn-sm"
                                                            onclick="openPdfModal('{{ asset('/uploads/' . $demande->facture->facture) }}')">
                                                            Facture</button>
                                                    @endif
                                                    @if (in_array($demande->typeDemande->id, [1, 3, 4, 5, 6, 8]) && !empty($demande->licence) && isset($demande->licence))
                                                        @if (optional($demande->etatDemande)->dg_signer ||
                                                                optional($demande->etatDemande)->dsv_signer ||
                                                                optional($demande->etatDemande)->pel_dsv_signer)
                                                            <a href="{{ route('user.imprimer', $demande->id) }}"
                                                                class="btn btn-primary btn-sm"
                                                                target="_blank">@lang('trans.print_authentication')</a>
                                                        @endif
                                                    @endif

                                                    @if (in_array($demande->typeDemande->id, [7]) && !empty($demande->validation) && isset($demande->validation))
                                                        <a href="{{ route('user.validation', $demande->validation) }}"
                                                            class="btn btn-primary btn-sm" target="_blank">
                                                            @lang('trans.print_validation')</a>
                                                    @endif
                                                    @if ($demande->has_issues && $demande->etatDemande->demandeur_cree_demande && !$demande->etatDemande->pel_valider)
                                                        <button class="btn btn-info btn-sm" data-toggle="modal"
                                                            data-target="#issuesModal-{{ $demande->id }}"
                                                            title="@lang('trans.view_issues')">
                                                            <i class="fas fa-info-circle"></i>
                                                        </button>

                                                        <div class="modal fade" id="issuesModal-{{ $demande->id }}"
                                                            tabindex="-1">
                                                            <div class="modal-dialog modal-lg">
                                                                <div class="modal-content">
                                                                    <div class="modal-header bg-dark text-white">
                                                                        <h5 class="modal-title">
                                                                            @lang('trans.issues_for')
                                                                            {{ $demande->typeDemande->nom_fr ?? '' }}
                                                                        </h5>
                                                                        <button type="button" class="close text-white"
                                                                            data-dismiss="modal">
                                                                            <span>&times;</span>
                                                                        </button>
                                                                    </div>
                                                                    <div class="modal-body">
                                                                        @if (count($demande->invalid_reasons) > 0)
                                                                            <div class="alert alert-warning">
                                                                                <h6><i class="fas fa-exclamation-triangle"></i>
                                                                                    @lang('trans.invalid_components')</h6>
                                                                                <ul>
                                                                                    @foreach ($demande->invalid_reasons as $component)
                                                                                        <li>
                                                                                            <strong>{{ ucfirst(str_replace('_', ' ', $component['type'])) }}:</strong>
                                                                                            {{ $component['identifier'] }}
                                                                                            @if (!empty($component['motif']))
                                                                                                -
                                                                                                <em>{{ $component['motif'] }}</em>
                                                                                            @endif
                                                                                        </li>
                                                                                    @endforeach
                                                                                </ul>
                                                                            </div>
                                                                        @endif

                                                                        @if (count($demande->rejection_reasons_list) > 0)
                                                                            <div class="alert alert-danger">
                                                                                <h6><i class="fas fa-ban"></i>
                                                                                    @lang('trans.rejection_reasons')</h6>
                                                                                <ul>
                                                                                    @foreach ($demande->rejection_reasons_list as $reason)
                                                                                        <li>{{ $reason }}</li>
                                                                                    @endforeach
                                                                                </ul>
                                                                            </div>
                                                                        @endif
                                                                    </div>
                                                                    <div class="modal-footer">
                                                                        <button type="button" class="btn btn-secondary"
                                                                            data-dismiss="modal">
                                                                            @lang('trans.close')
                                                                        </button>
                                                                        @if (auth()->user()->can('edit-demandes'))
                                                                            <a href="{{ route('demandes.edit', $demande->id) }}"
                                                                                class="btn btn-primary">
                                                                                <i class="fas fa-edit"></i>
                                                                                @lang('trans.correct_issues')
                                                                            </a>
                                                                        @endif
                                                                    </div>
                                                                </div>
                                                            </div>
                                                        </div>
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
            </div>
        @endif
        
        {{-- SECTION AUTORISATIONS --}}
        @if (Auth::user()->user_type === 'autorisation')
            <div class="row">
                <div class="col-md-12">
                    <div class="card">
                        <div class="card-header">
                            @lang('trans.autorization_applications')
                            <div class="card-tools">
                                @isset(Auth::user()->demandeur)
                                    <button type="button" class="btn btn-primary" data-toggle="modal"
                                        data-target="#applicationModal">
                                        @lang('trans.add_application')
                                    </button>
                                @endisset
                            </div>
                        </div>
                        <div class="card-body">
                            <div class="table-responsive">
                                <table class="table table-bordered table-striped" id="autorization_applications">
                                    <thead>
                                        <tr>
                                            <th>@lang('trans.creation_date')</th>
                                            <th>@lang('trans.submission_date')</th>
                                            <th>@lang('trans.code')</th>
                                            <th>@lang('trans.type_application')</th>
                                            <th>@lang('trans.type_flight')</th>
                                            <th>@lang('trans.start_date')</th>
                                            <th>@lang('trans.end_date')</th>
                                            <th>@lang('trans.actions')</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        @if ($demandeAutorisations->isNotEmpty())
                                            @foreach ($demandeAutorisations as $demande)
                                                @php
                                                    $etatDemande = $demande->etatDemande ?? null;
                                                    $documentCount = $demande->documents ? $demande->documents->count() : 0;
                                                    $canEdit = $etatDemande && !$etatDemande->compagnie_cree_demande;
                                                    $canSubmit = $canEdit && $documentCount > 0;
                                                    $hasIssues = $demande->has_issues ?? false;
                                                    $typeId = $demande->type->id ?? null;
                                                    $typeVolId = $demande->typeVol->id ?? null;
                                                    $dateDebut = $demande->date_debut ?? null;
                                                    $dateFin = $demande->date_fin ?? null;
                                                    $sousValidite = $demande->sous_validite ?? null;
                                                    $objet = $demande->objet ?? null;
                                                @endphp
                                                <tr>
                                                    <td>{{ $demande->created_at->format('d/m/Y') ?? 'N/A' }}</td>
                                                    <td>{{ $demande->date_soumission_formatted ?? 'N/A' }}</td>
                                                    <td>{{ $demande->code ?? 'N/A' }}</td>
                                                    <td>{{ $demande->type->libelle ?? 'N/A' }}</td>
                                                    <td>{{ $demande->typeVol->nom ?? 'N/A' }}</td>
                                                    <td>{{ $dateDebut ?? 'N/A' }}</td>
                                                    <td>{{ $dateFin ?? 'N/A' }}</td>
                                                    <td>
                                                        <div class="btn-group">
                                                            @if ($canEdit)
                                                                <a href="{{ route('user.autorisations.edit', $demande->id) }}"
                                                                   class="btn btn-warning btn-sm">@lang('trans.edit')</a>
                                                                <button class="btn btn-info btn-sm btn-modify"
                                                                    data-id="{{ $demande->id }}"
                                                                    data-type="{{ $typeId }}"
                                                                    data-type-vol="{{ $typeVolId }}"
                                                                    data-date-debut="{{ $dateDebut }}"
                                                                    data-date-fin="{{ $dateFin }}"
                                                                    data-sous-validite="{{ $sousValidite }}"
                                                                    data-objet="{{ $objet }}"
                                                                    title="@lang('trans.modify')">
                                                                    <i class="fas fa-edit"></i>
                                                                </button>
                                                                <form action="{{ route('user.autorisations.destroy', $demande->id) }}" method="POST" class="d-inline">
                                                                    @csrf @method('DELETE')
                                                                    <button type="submit" class="btn btn-danger btn-sm" 
                                                                            onclick="return confirm('@lang('trans.confirm_delete')')">
                                                                        <i class="fas fa-trash"></i>
                                                                    </button>
                                                                </form>
                                                            @endif

                                                            @if ($canSubmit)
                                                                <form action="{{ route('update-state', $demande->id) }}" method="POST" class="d-inline">
                                                                    @csrf
                                                                    <input type="hidden" name="action" value="compagnie_cree_demande">
                                                                    <input type="hidden" name="is_approved" value="1">
                                                                    <button type="submit" class="btn btn-success btn-sm" 
                                                                            onclick="return confirm('@lang('trans.confirm_submission')')">
                                                                        <i class="fas fa-paper-plane"></i> @lang('trans.send')
                                                                        <span class="badge badge-light">{{ $documentCount }}</span>
                                                                    </button>
                                                                </form>
                                                            @elseif ($canEdit)
                                                                <button class="btn btn-secondary btn-sm" disabled title="@lang('trans.add_docs_first')">
                                                                    <i class="fas fa-paper-plane"></i> @lang('trans.send')
                                                                </button>
                                                            @endif

                                                            @if ($hasIssues)
                                                                <button class="btn btn-warning btn-sm" data-toggle="modal" 
                                                                        data-target="#issuesModalAutorisation-{{ $demande->id }}">
                                                                    <i class="fas fa-exclamation-circle"></i>
                                                                </button>
                                                            @endif

                                                            @php
                                                                $vols = $demande->vols ?? collect();
                                                                $firstVolId = $vols->first()->id ?? null;
                                                                $specialVolIds = [3, 4, 6, 7, 9, 10, 11, 12, 13];
                                                            @endphp

                                                            @if ($typeId === 1 || ($typeId === 2 && $firstVolId && in_array($firstVolId, $specialVolIds)))
                                                                @if (($etatDemande->dg_valider ?? false) || ($etatDemande->dta_dg_valider ?? false))
                                                                    <a target="_blank"
                                                                       href="{{ route('user.print', $demande->autorisation($demande->id)) }}"
                                                                       class="btn btn-warning btn-sm">@lang('trans.print')</a>
                                                                @endif
                                                            @endif

                                                            @if ($typeId === 2)
                                                                @if ($etatDemande->daf_confirme_pay ?? false)
                                                                    <a target="_blank"
                                                                       href="{{ route('user.print', $demande->autorisation($demande->id)) }}"
                                                                       class="btn btn-warning btn-sm">@lang('trans.print')</a>
                                                                @endif
                                                            @endif
                                                        </div>
                                                    </td>
                                                </tr>

                                                {{-- Modal des Erreurs/Issues pour Autorisations --}}
                                                @if ($hasIssues)
                                                    <div class="modal fade" id="issuesModalAutorisation-{{ $demande->id }}" tabindex="-1" role="dialog">
                                                        <div class="modal-dialog modal-lg" role="document">
                                                            <div class="modal-content">
                                                                <div class="modal-header bg-danger text-white">
                                                                    <h5 class="modal-title">
                                                                        <i class="fas fa-bug"></i> @lang('trans.issues_for') : {{ $demande->code }}
                                                                    </h5>
                                                                    <button type="button" class="close text-white" data-dismiss="modal"><span>&times;</span></button>
                                                                </div>
                                                                <div class="modal-body">
                                                                    @if (!empty($demande->invalid_reasons))
                                                                        <div class="alert alert-warning">
                                                                            <h6><i class="fas fa-exclamation-triangle"></i> @lang('trans.invalid_components')</h6>
                                                                            <ul class="mb-0">
                                                                                @foreach ($demande->invalid_reasons as $reason)
                                                                                    <li>
                                                                                        <strong>{{ ucfirst(str_replace('_', ' ', $reason['type'] ?? '')) }}</strong> :
                                                                                        {{ $reason['motif'] ?? 'N/A' }}
                                                                                    </li>
                                                                                @endforeach
                                                                            </ul>
                                                                        </div>
                                                                    @endif

                                                                    @if (!empty($demande->rejection_reasons_list))
                                                                        <div class="alert alert-danger">
                                                                            <h6><i class="fas fa-ban"></i> @lang('trans.rejection_reasons')</h6>
                                                                            <ul class="mb-0">
                                                                                @foreach ($demande->rejection_reasons_list as $reason)
                                                                                    <li>{{ $reason }}</li>
                                                                                @endforeach
                                                                            </ul>
                                                                        </div>
                                                                    @endif
                                                                </div>
                                                                <div class="modal-footer">
                                                                    <button type="button" class="btn btn-secondary" data-dismiss="modal">@lang('trans.close')</button>
                                                                    @if (auth()->user()->can('edit-demandes'))
                                                                        <a href="{{ route('user.autorisations.edit', $demande->id) }}" class="btn btn-primary">
                                                                            <i class="fas fa-tools"></i> @lang('trans.correct_issues')
                                                                        </a>
                                                                    @endif
                                                                </div>
                                                            </div>
                                                        </div>
                                                    </div>
                                                @endif
                                            @endforeach
                                        @else
                                            <tr>
                                                <td colspan="8" class="text-center">@lang('trans.no_data')</td>
                                            </tr>
                                        @endif
                                    </tbody>
                                </table>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            {{-- SECTION PAIEMENTS --}}
            @if ($paiementAutorisations->isNotEmpty())
                <div class="row">
                    <div class="col-md-12">
                        <div class="card">
                            <div class="card-header">@lang('trans.autorization_paiements')</div>
                            <div class="card-body">
                                <div class="table-responsive">
                                    <table class="table table-bordered table-striped" id="paiements">
                                        <thead>
                                            <tr>
                                                <th>@lang('trans.ref')</th>
                                                <th>@lang('trans.creation_date')</th>
                                                <th>@lang('trans.code')</th>
                                                <th>@lang('trans.type_application')</th>
                                                <th>@lang('trans.type_flight')</th>
                                                <th>@lang('trans.method')</th>
                                                <th>@lang('trans.amount')</th>
                                                <th>@lang('trans.date')</th>
                                                <th>@lang('trans.status')</th>
                                                <th>@lang('trans.invoices')</th>
                                                <th>@lang('trans.actions')</th>
                                            </tr>
                                        </thead>
                                        <tbody>
                                            @foreach ($paiementAutorisations as $paiement)
                                                <tr>
                                                    <td>{{ strtoupper($paiement->reference) }}</td>
                                                    <td>{{ date('d/m/Y', strtotime($paiement->created_at)) }}</td>
                                                    <td><span class="badge badge-info">{{ $paiement->demande->code ?? 'N/A' }}</span></td>
                                                    <td>{{ $paiement->demande->type->libelle ?? 'N/A' }}</td>
                                                    <td>{{ $paiement->demande->typeVol->nom ?? 'N/A' }}</td>
                                                    <td>{{ strtoupper($paiement->methode) }}</td>
                                                    <td>{{ $paiement->montant_total }}</td>
                                                    <td>{{ $paiement->date_paiement }}</td>
                                                    <td>{{ strtoupper($paiement->statut) }}</td>
                                                    <td>
                                                        <a href="{{ route('user.autorisations.invoice', $paiement->id) }}"
                                                           class="btn btn-warning btn-sm"
                                                           target="_blank">@lang('trans.print')</a>
                                                    </td>
                                                    <td>
                                                        @if ($paiement && $paiement->statut === 'on_hold')
                                                            <a href="{{ route('user.autorisations.autorisationPay', $paiement) }}"
                                                               class="btn btn-warning btn-sm">@lang('trans.pay')</a>
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
                </div>
            @endif
        @endif
    </div>

    {{-- MODAL APPLICATION --}}
    <!-- Modal -->
<div class="modal fade" id="applicationModal" tabindex="-1" role="dialog" aria-labelledby="applicationModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-xl" role="document">
        <div class="modal-content">
            <div class="modal-header bg-primary text-white">
                <h5 class="modal-title" id="applicationModalLabel">@lang('trans.add_application')</h5>
                <button type="button" class="close text-white" data-dismiss="modal" aria-label="Close">
                    <span aria-hidden="true">&times;</span>
                </button>
            </div>

            <form id="applicationForm" action="{{ route('user.autorisations.store') }}" method="POST">
                @csrf
                <input type="hidden" id="edit_mode" name="edit_mode" value="0">
                <input type="hidden" id="demande_id" name="demande_id" value="">
                
                <div class="modal-body">
                    <div class="row">
                        <div class="col-md-6">
                            <div class="form-group">
                                <label for="type_demande_autorisation_id">@lang('trans.select_type_autorization')<span class="text-danger">*</span></label>
                                <select class="form-control select2" id="type_demande_autorisation_id"
                                    name="type_demande_autorisation_id" required>
                                    <option value="">@lang('trans.select_option')</option>
                                    @foreach ($type_demande_autorisations as $type_demande_autorisation)
                                        <option value="{{ $type_demande_autorisation->id }}">
                                            {{ $type_demande_autorisation->libelle }}
                                        </option>
                                    @endforeach
                                </select>
                            </div>
                        </div>
                        
                        <!-- Type de vol -->
                        <div class="col-md-6">
                            <div class="form-group">
                                <label for="type_vol_id">Type de vol <span class="text-danger">*</span></label>
                                <select class="form-control select2" id="type_vol_id" name="type_vol_id">
                                    <option value="">@lang('trans.select_option')</option>
                                    @foreach ($type_vols as $type)
                                        <option value="{{ $type->id }}" data-nom="{{ $type->nom }}">
                                            {{ $type->nom }}
                                        </option>
                                    @endforeach
                                </select>
                                <div class="invalid-feedback" id="type_vol_id_error"></div>
                                <small id="typeVolInfo" class="form-text text-muted" style="display: none;">
                                    <i class="fas fa-info-circle"></i> Pour "TRANSPORT DÉPOUILLE MORTELLE", le type de vol est automatiquement défini sur "VOL CARGO"
                                </small>
                                <small id="typeVolMultiInfo" class="form-text text-info" style="display: none;">
                                    <i class="fas fa-info-circle"></i> Seuls les types de vol "VOL CARGO" et "VOL CHARTER" sont disponibles pour cette demande.
                                </small>
                            </div>
                        </div>
                        
                        <div class="col-md-4">
                            <div class="form-group">
                                <label for="date_debut">Date de début <span class="text-danger">*</span></label>
                                <input type="date" class="form-control" id="date_debut" name="date_debut" required>
                                <div class="invalid-feedback" id="date_debut_error"></div>
                            </div>
                        </div>
                        <div class="col-md-4">
                            <div class="form-group">
                                <label for="date_fin">Date de fin <span class="text-danger">*</span></label>
                                <input type="date" class="form-control" id="date_fin" name="date_fin" required>
                                <div class="invalid-feedback" id="date_fin_error"></div>
                            </div>
                        </div>
                        <div class="col-md-4">
                            <div class="form-group">
                                <label for="sous_validite">Sous-validite (H)</label>
                                <input type="number" min="12" max="72" step="12"
                                    class="form-control" name="sous_validite" id="sous_validite" 
                                    placeholder="Laisser vide si non applicable">
                                <div class="invalid-feedback" id="sous_validite_error"></div>
                            </div>
                        </div>
                    </div>
                    
                    <!-- Objet du vol -->
                    <div class="row">
                        <div class="col-md-12 mb-3">
                            <div class="form-group">
                                <label for="objet" class="form-label">Objet</label>
                                <textarea class="form-control" id="objet" name="objet" rows="2"
                                    placeholder="Décrivez l'objet du vol..."></textarea>
                                <div class="invalid-feedback" id="objet_error"></div>
                            </div>
                        </div>
                    </div>
                </div>

                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-dismiss="modal">
                        <i class="fas fa-times"></i> Fermer
                    </button>
                    <button type="submit" class="btn btn-success" id="submitBtn">
                        <i class="fas fa-paper-plane"></i> @lang('trans.send')
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>
@endsection

@push('script')
    <script src="{{ asset('assets/admin/plugins/datatables/jquery.dataTables.min.js') }}"></script>
    <script src="{{ asset('assets/admin/plugins/datatables-bs4/js/dataTables.bootstrap4.min.js') }}"></script>
    <script src="{{ asset('assets/admin/plugins/datatables-responsive/js/dataTables.responsive.min.js') }}"></script>
    <script src="{{ asset('assets/admin/plugins/datatables-responsive/js/responsive.bootstrap4.min.js') }}"></script>
    <script src="{{ asset('assets/admin/plugins/toastr/toastr.min.js') }}"></script>
    <script src="{{ asset('assets/admin/plugins/select2/js/select2.full.min.js') }}"></script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/moment.js/2.29.4/moment.min.js"></script>
<script src="https://cdnjs.cloudflare.com/ajax/libs/moment.js/2.29.4/locale/fr.js"></script>
@endpush

@push('custom')
<script>
$(document).ready(function() {
    // Plugin de tri de dates utilisant Moment.js
    jQuery.extend(jQuery.fn.dataTableExt.oSort, {
        "datetime-moment-pre": function(date) {
            if (!date || date === 'N/A' || date === '') {
                return 0;
            }
            
            // Essayer plusieurs formats de date courants
            var formats = [
                'DD/MM/YYYY',
                'DD-MM-YYYY',
                'YYYY-MM-DD',
                'DD/MM/YYYY HH:mm:ss',
                'YYYY-MM-DD HH:mm:ss'
            ];
            
            for (var i = 0; i < formats.length; i++) {
                var momentDate = moment(date, formats[i], true);
                if (momentDate.isValid()) {
                    return momentDate.unix();
                }
            }
            
            // Si aucun format ne correspond, retourner 0
            return 0;
        },

        "datetime-moment-asc": function(a, b) {
            return a - b;
        },

        "datetime-moment-desc": function(a, b) {
            return b - a;
        }
    });

    // Initialisation du DataTable
    var table = $('#autorization_applications').DataTable({
        "order": [[0, "desc"]], // Tri décroissant sur la première colonne
        "columnDefs": [
            {
                "targets": [0, 1], // Colonnes de dates
                "type": "datetime-moment",
                "orderDataType": "dom-data-order"
            },
            {
                "targets": 7, // Colonne actions
                "orderable": false,
                "searchable": false
            }
        ],
        "pageLength": 25,
        "responsive": true,
        "language": {
            "processing": "Traitement en cours...",
            "search": "Rechercher&nbsp;:",
            "lengthMenu": "Afficher _MENU_ &eacute;l&eacute;ments",
            "info": "Affichage de _START_ &agrave; _END_ sur _TOTAL_ &eacute;l&eacute;ments",
            "infoEmpty": "Affichage de 0 &agrave; 0 sur 0 &eacute;l&eacute;ment",
            "infoFiltered": "(filtr&eacute; de _MAX_ &eacute;l&eacute;ments au total)",
            "infoPostFix": "",
            "loadingRecords": "Chargement en cours...",
            "zeroRecords": "Aucun &eacute;l&eacute;ment &agrave; afficher",
            "emptyTable": "Aucune donn&eacute;e disponible dans le tableau",
            "paginate": {
                "first": "Premier",
                "previous": "Pr&eacute;c&eacute;dent",
                "next": "Suivant",
                "last": "Dernier"
            },
            "aria": {
                "sortAscending": ": activer pour trier la colonne par ordre croissant",
                "sortDescending": ": activer pour trier la colonne par ordre d&eacute;croissant"
            }
        },
        "drawCallback": function(settings) {
            console.log('Tableau mis à jour');
        }
    });
    
    // Ajouter des classes Bootstrap aux éléments DataTables
    $('.dataTables_length select').addClass('custom-select custom-select-sm form-control form-control-sm');
    $('.dataTables_filter input').addClass('form-control form-control-sm');
    
    // Réinitialiser le tri si nécessaire
    // table.order([0, 'desc']).draw();
});
$(document).ready(function() {
    // Initialisation des DataTables
    $('#license_applications').DataTable({
        "paging": true,
        "lengthChange": false,
        "searching": true,
        "ordering": true,
        "info": true,
        "autoWidth": false,
        "responsive": true,
        "columnDefs": [{
            "targets": 5,
            "orderable": false
        }]
    });

    

    $('#paiements').DataTable({
        "paging": true,
        "lengthChange": false,
        "searching": true,
        "ordering": true,
        "info": true,
        "autoWidth": false,
        "responsive": true,
        "order": [[1, "desc"]]
    });
});
// ============================================
// INITIALISATION GÉNÉRALE
// ============================================
$(document).ready(function() {
    // Initialisation du select2 pour le type de demande
    $('#type_demande_autorisation_id').select2({
        dropdownParent: $('#applicationModal'),
        placeholder: "@lang('trans.select_option')",
        allowClear: false,
        width: '100%'
    });

    // Initialisation du select2 pour le type de vol
    initTypeVolSelect2('single');
    
    // Événement changement type de demande
    $('#type_demande_autorisation_id').on('change', function() {
        handleTypeDemandeChange($(this).val());
    });
    
    // Si une valeur initiale est déjà sélectionnée
    const initialTypeId = $('#type_demande_autorisation_id').val();
    if (initialTypeId) {
        handleTypeDemandeChange(initialTypeId);
    }
    
    // Événement d'ouverture du modal
    $('#applicationModal').on('shown.bs.modal', function() {
        // Réinitialiser select2 après l'ouverture du modal
        initTypeVolSelect2('single');
    });
    
    // Événement de fermeture du modal
    $('#applicationModal').on('hidden.bs.modal', function() {
        resetModalForNew();
    });
    
    // Soumission du formulaire
    $('#applicationForm').on('submit', function(e) {
        e.preventDefault();
        submitApplicationForm();
    });
    
    // Validation en temps réel des dates
    $('#date_debut, #date_fin').on('change', function() {
        validateDates();
    });
    
    // Boutons modifier dans le tableau
    $(document).on('click', '.btn-modify', function() {
        loadDemandeForEdit($(this));
    });
});

// ============================================
// FONCTIONS PRINCIPALES
// ============================================

/**
 * Initialise le select2 pour le type de vol
 * @param {string} mode - 'single' ou 'multiple'
 */
function initTypeVolSelect2(mode) {
    // Détruire l'instance précédente si elle existe
    if ($('#type_vol_id').hasClass('select2-hidden-accessible')) {
        $('#type_vol_id').select2('destroy');
    }
    
    const options = {
        dropdownParent: $('#applicationModal'),
        placeholder: "@lang('trans.select_option')",
        allowClear: true,
        width: '100%'
    };
    
    if (mode === 'multiple') {
        options.allowClear = true;
        options.closeOnSelect = false;
        options.templateResult = function(option) {
            if (!option.id) return option.text;
            if ([1, 2].includes(parseInt(option.id))) {
                return option.text;
            }
            return null;
        };
        options.templateSelection = function(option) {
            if (!option.id) return option.text;
            if ([1, 2].includes(parseInt(option.id))) {
                return option.text;
            }
            return null;
        };
    }
    
    $('#type_vol_id').select2(options);
}

/**
 * Gère le changement du type de demande
 * @param {string|number} typeDemandeId - L'ID du type de demande sélectionné
 */
function handleTypeDemandeChange(typeDemandeId) {
    const typeVolSelect = $('#type_vol_id');
    const typeVolInfo = $('#typeVolInfo');
    const typeVolMultiInfo = $('#typeVolMultiInfo');
    
    // Nettoyer les messages d'erreur
    clearErrors();
    
    // Réinitialiser le select
    typeVolSelect.prop('disabled', false);
    typeVolSelect.find('option').prop('disabled', false).show();
    
    if (typeDemandeId == 4) {
        // ========================================
        // TYPE 4 : TRANSPORT DÉPOUILLE MORTELLE
        // ========================================
        setupType4DepouilleMortelle(typeVolSelect, typeVolInfo, typeVolMultiInfo);
        
    } else if (typeDemandeId == 3) {
        // ========================================
        // TYPE 3 : MULTIPLE (MULTI-SELECT)
        // ========================================
        setupType3MultiSelect(typeVolSelect, typeVolInfo, typeVolMultiInfo);
        
    } else {
        // ========================================
        // AUTRES TYPES : SELECT SIMPLE NORMAL
        // ========================================
        setupTypeNormal(typeVolSelect, typeVolInfo, typeVolMultiInfo);
    }
}

/**
 * Configure le select pour le type 4 (Transport dépouille mortelle)
 */
function setupType4DepouilleMortelle(typeVolSelect, typeVolInfo, typeVolMultiInfo) {
    // Garder le select actif (important pour l'envoi du formulaire)
    typeVolSelect.prop('disabled', false);
    typeVolSelect.removeAttr('multiple');
    typeVolSelect.attr('name', 'type_vol_id');
    
    // Masquer toutes les options sauf VOL CARGO (id=1)
    typeVolSelect.find('option').each(function() {
        const val = $(this).val();
        if (val === '1') {
            $(this).prop('disabled', false).show();
        } else if (val === '') {
            $(this).prop('disabled', true); // Désactiver l'option vide
        } else {
            $(this).prop('disabled', true).hide();
        }
    });
    
    // Sélectionner automatiquement VOL CARGO
    typeVolSelect.val('1');
    
    // Afficher le message d'information
    typeVolInfo.show();
    typeVolMultiInfo.hide();
    
    // Réinitialiser select2 en mode single
    initTypeVolSelect2('single');
    
    // Empêcher l'ouverture du dropdown (une seule option disponible)
    typeVolSelect.off('select2:opening').on('select2:opening', function(e) {
        e.preventDefault();
    });
}

/**
 * Configure le select pour le type 3 (Multi-sélection)
 */
function setupType3MultiSelect(typeVolSelect, typeVolInfo, typeVolMultiInfo) {
    // Configurer en mode multi-select
    typeVolSelect.prop('disabled', false);
    typeVolSelect.attr('multiple', 'multiple');
    typeVolSelect.attr('name', 'type_vol_id[]');
    
    // Filtrer : uniquement VOL CARGO (id=1) et VOL CHARTER (id=2)
    typeVolSelect.find('option').each(function() {
        const val = $(this).val();
        if (val === '' || ![1, 2].includes(parseInt(val))) {
            $(this).prop('disabled', true).hide();
        } else {
            $(this).prop('disabled', false).show();
        }
    });
    
    // Vider la sélection
    typeVolSelect.val([]);
    
    // Afficher les messages
    typeVolInfo.hide();
    typeVolMultiInfo.show();
    
    // Réinitialiser select2 en mode multiple
    initTypeVolSelect2('multiple');
}

/**
 * Configure le select pour les types normaux (single select)
 */
function setupTypeNormal(typeVolSelect, typeVolInfo, typeVolMultiInfo) {
    // Mode single select normal
    typeVolSelect.prop('disabled', false);
    typeVolSelect.removeAttr('multiple');
    typeVolSelect.attr('name', 'type_vol_id');
    
    // Réactiver toutes les options
    typeVolSelect.find('option').prop('disabled', false).show();
    
    // Vider la sélection
    typeVolSelect.val('');
    
    // Cacher les messages
    typeVolInfo.hide();
    typeVolMultiInfo.hide();
    
    // Réinitialiser select2 en mode single
    initTypeVolSelect2('single');
    
    // Supprimer l'événement d'ouverture bloquant
    typeVolSelect.off('select2:opening');
}

/**
 * Charge les données d'une demande pour modification
 * @param {jQuery} button - Le bouton cliqué
 */
function loadDemandeForEdit(button) {
    const demandeId = button.data('id');
    const demandeData = {
        type: button.data('type'),
        typeVol: button.data('type-vol'),
        dateDebut: button.data('date-debut'),
        dateFin: button.data('date-fin'),
        sousValidite: button.data('sous-validite'),
        objet: button.data('objet')
    };
    
    console.log('Chargement demande pour modification:', demandeId, demandeData);
    
    // Passer en mode édition
    $('#edit_mode').val('1');
    $('#demande_id').val(demandeId);
    $('#applicationModalLabel').text("@lang('trans.modify_application')");
    
    // Remplir les champs
    $('#date_debut').val(demandeData.dateDebut);
    $('#date_fin').val(demandeData.dateFin);
    $('#sous_validite').val(demandeData.sousValidite);
    $('#objet').val(demandeData.objet);
    
    // Déclencher le changement de type (important pour configurer le select type_vol)
    $('#type_demande_autorisation_id').val(demandeData.type).trigger('change');
    
    // Attendre que le DOM soit mis à jour puis définir la valeur du type_vol
    setTimeout(function() {
        const typeVolSelect = $('#type_vol_id');
        
        if (demandeData.type == 3) {
            // Multi-select : convertir en tableau
            let typeVolArray = [];
            if (Array.isArray(demandeData.typeVol)) {
                typeVolArray = demandeData.typeVol.map(Number);
            } else if (demandeData.typeVol) {
                typeVolArray = String(demandeData.typeVol).split(',').map(Number);
            }
            typeVolSelect.val(typeVolArray).trigger('change');
        } else if (demandeData.type == 4) {
            // Type 4 : forcer VOL CARGO
            typeVolSelect.val('1').trigger('change');
        } else {
            // Single select normal
            typeVolSelect.val(demandeData.typeVol).trigger('change');
        }
    }, 300);
    
    // Mettre à jour l'URL du formulaire pour la modification
    $('#applicationForm').attr('action', "{{ route('user.autorisations.update', ':id') }}".replace(':id', demandeId));
    
    // Ajouter le champ _method pour Laravel (PUT)
    if ($('#applicationForm input[name="_method"]').length === 0) {
        $('#applicationForm').append('<input type="hidden" name="_method" value="PUT">');
    }
    
    // Ouvrir le modal
    $('#applicationModal').modal('show');
}

/**
 * Réinitialise le modal pour une nouvelle demande
 */
function resetModalForNew() {
    // Réinitialiser les champs cachés
    $('#edit_mode').val('0');
    $('#demande_id').val('');
    $('#applicationModalLabel').text("@lang('trans.add_application')");
    
    // Réinitialiser le formulaire
    $('#applicationForm')[0].reset();
    
    // Supprimer le champ _method
    $('#applicationForm input[name="_method"]').remove();
    
    // Réinitialiser l'URL
    $('#applicationForm').attr('action', "{{ route('user.autorisations.store') }}");
    
    // Réinitialiser le type de demande
    $('#type_demande_autorisation_id').val('').trigger('change');
    
    // Réinitialiser type_vol
    const typeVolSelect = $('#type_vol_id');
    typeVolSelect.find('option').prop('disabled', false).show();
    typeVolSelect.val('');
    typeVolSelect.prop('disabled', false);
    typeVolSelect.removeAttr('multiple');
    typeVolSelect.attr('name', 'type_vol_id');
    
    // Cacher les messages
    $('#typeVolInfo').hide();
    $('#typeVolMultiInfo').hide();
    
    // Supprimer l'événement d'ouverture bloquant
    typeVolSelect.off('select2:opening');
    
    // Réinitialiser select2
    initTypeVolSelect2('single');
    
    // Nettoyer les erreurs
    clearErrors();
}

/**
 * Valide les dates (début et fin)
 * @returns {boolean}
 */
function validateDates() {
    let isValid = true;
    const dateDebut = $('#date_debut').val();
    const dateFin = $('#date_fin').val();
    
    // Réinitialiser les erreurs
    $('#date_debut').removeClass('is-invalid');
    $('#date_fin').removeClass('is-invalid');
    $('#date_debut_error').text('');
    $('#date_fin_error').text('');
    
    if (!dateDebut) {
        $('#date_debut').addClass('is-invalid');
        $('#date_debut_error').text('La date de début est obligatoire.');
        isValid = false;
    } else {
        const today = new Date();
        today.setHours(0, 0, 0, 0);
        const debutDate = new Date(dateDebut);
        
        if (debutDate < today) {
            $('#date_debut').addClass('is-invalid');
            $('#date_debut_error').text('La date de début ne peut pas être dans le passé.');
            isValid = false;
        }
    }
    
    if (!dateFin) {
        $('#date_fin').addClass('is-invalid');
        $('#date_fin_error').text('La date de fin est obligatoire.');
        isValid = false;
    } else if (dateDebut && dateFin < dateDebut) {
        $('#date_fin').addClass('is-invalid');
        $('#date_fin_error').text('La date de fin doit être après la date de début.');
        isValid = false;
    }
    
    return isValid;
}

/**
 * Nettoie tous les messages d'erreur
 */
function clearErrors() {
    $('.is-invalid').removeClass('is-invalid');
    $('.invalid-feedback').text('');
    toastr.clear();
}

/**
 * Affiche les erreurs de validation
 * @param {Object} errors - Les erreurs de validation
 */
function displayValidationErrors(errors) {
    // Nettoyer les anciennes erreurs
    clearErrors();
    
    // Parcourir les erreurs
    $.each(errors, function(field, messages) {
        const input = $('[name="' + field + '"]');
        const errorDiv = $('#' + field + '_error');
        
        if (input.length) {
            input.addClass('is-invalid');
        }
        
        if (errorDiv.length && messages[0]) {
            errorDiv.text(messages[0]);
        } else if (messages[0]) {
            toastr.error(messages[0]);
        }
    });
}

/**
 * Soumet le formulaire de demande
 */
function submitApplicationForm() {
    // Désactiver le bouton pour éviter double soumission
    const submitBtn = $('#submitBtn');
    submitBtn.prop('disabled', true);
    submitBtn.html('<i class="fas fa-spinner fa-spin"></i> Envoi en cours...');
    
    // S'assurer que pour le type 4, type_vol_id = 1
    const typeDemandeId = $('#type_demande_autorisation_id').val();
    if (typeDemandeId == 4) {
        $('#type_vol_id').val('1');
    }
    
    // Récupérer les données du formulaire
    const formData = $('#applicationForm').serialize();
    const url = $('#applicationForm').attr('action');
    const isEdit = $('#edit_mode').val() === '1';
    
    console.log('Soumission formulaire:', {
        url: url,
        isEdit: isEdit,
        data: formData
    });
    
    $.ajax({
        url: url,
        method: 'POST',
        data: formData,
        success: function(response) {
            console.log('Succès:', response);
            
            // Fermer le modal
            $('#applicationModal').modal('hide');
            
            // Afficher le message de succès
            const message = isEdit ? 
                "@lang('trans.updated_successfully')" : 
                "@lang('trans.success')";
            
            toastr.success(message);
            
            // Recharger la page après un court délai
            setTimeout(function() {
                window.location.reload();
            }, 1500);
        },
        error: function(xhr) {
            console.error('Erreur:', xhr);
            
            // Réactiver le bouton
            submitBtn.prop('disabled', false);
            submitBtn.html('<i class="fas fa-paper-plane"></i> @lang("trans.send")');
            
            if (xhr.status === 422) {
                // Erreurs de validation
                const errors = xhr.responseJSON.errors;
                displayValidationErrors(errors);
            } else if (xhr.status === 500) {
                toastr.error('Erreur serveur. Veuillez réessayer plus tard.');
            } else {
                toastr.error("@lang('trans.error_occurred')");
            }
        }
    });
}

// ============================================
// VALIDATION EN TEMPS RÉEL
// ============================================

$('#date_debut, #date_fin').on('change', function() {
    validateDates();
});

$('#sous_validite').on('input', function() {
    const val = parseInt($(this).val());
    const errorDiv = $('#sous_validite_error');
    
    $(this).removeClass('is-invalid');
    errorDiv.text('');
    
    if ($(this).val() && (val < 12 || val > 72)) {
        $(this).addClass('is-invalid');
        errorDiv.text('La sous-validité doit être comprise entre 12 et 72 heures.');
    } else if ($(this).val() && val % 12 !== 0) {
        $(this).addClass('is-invalid');
        errorDiv.text('La sous-validité doit être un multiple de 12.');
    }
});

$('#objet').on('input', function() {
    const maxLength = 500;
    const currentLength = $(this).val().length;
    const errorDiv = $('#objet_error');
    
    $(this).removeClass('is-invalid');
    errorDiv.text('');
    
    if (currentLength > maxLength) {
        $(this).addClass('is-invalid');
        errorDiv.text('L\'objet ne doit pas dépasser ' + maxLength + ' caractères.');
    }
});
</script>
@endpush