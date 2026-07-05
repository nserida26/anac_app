@extends('daf.layouts.app')

@section('title')
    @lang('trans.dashboard_dir', ['role' => strtoupper(auth()->user()->getRoleNames()->first() ?? '')])
@endsection

@section('contentheader')
    @lang('trans.dashboard_dir', ['role' => strtoupper(auth()->user()->getRoleNames()->first() ?? '')])
@endsection

@section('contentheaderlink')
    <a href="{{ route('daf') }}">
        @lang('trans.dashboard_dir', ['role' => strtoupper(auth()->user()->getRoleNames()->first() ?? '')])
    </a>
@endsection

@section('contentheaderactive')
    @lang('trans.dashboard_dir', ['role' => strtoupper(auth()->user()->getRoleNames()->first() ?? '')])
@endsection

@push('css')
    <link rel="stylesheet" href="{{ asset('assets/admin/plugins/datatables-bs4/css/dataTables.bootstrap4.min.css') }}">
    <link rel="stylesheet" href="{{ asset('assets/admin/plugins/datatables-responsive/css/responsive.bootstrap4.min.css') }}">
    <link rel="stylesheet" href="{{ asset('assets/admin/plugins/select2/css/select2.min.css') }}">
    <link rel="stylesheet" href="{{ asset('assets/admin/plugins/select2-bootstrap4-theme/select2-bootstrap4.min.css') }}">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/toastr.js/latest/toastr.min.css" integrity="sha512-vKMx8UnXk60zUwyUnUPM3HbQo8QfmNx7+ltw8Pm5zLusl1XIfwcxo8DbWCqMGKaWeNxWA8yrx5v3SaVpMvR3CA==" crossorigin="anonymous" referrerpolicy="no-referrer" />
    <style>
        .select2-container--default .select2-selection--multiple .select2-selection__choice {
            background-color: #007bff;
            border-color: #006fe6;
            color: #fff;
        }
        .section-header {
            background-color: #f8f9fa;
            padding: 10px 15px;
            border-radius: 5px;
            margin-bottom: 15px;
            font-weight: bold;
        }
        .badge-status {
            padding: 5px 10px;
            border-radius: 20px;
            font-size: 0.85rem;
        }
        .filter-section {
            background-color: #f4f6f9;
            padding: 20px;
            border-radius: 8px;
            margin-bottom: 20px;
        }
    </style>
@endpush

@section('content')
    <div class="container-fluid">
        
        {{-- Filtres avancés --}}
        <div class="row">
            <div class="col-12">
                <div class="filter-section">
                    <h5 class="mb-3">
                        <i class="fas fa-filter mr-2"></i>@lang('trans.filters')
                    </h5>
                    <div class="row">
                        <div class="col-md-3">
                            <div class="form-group">
                                <label>@lang('trans.date_range')</label>
                                <input type="text" class="form-control daterange" placeholder="@lang('trans.select_dates')">
                            </div>
                        </div>
                        <div class="col-md-3">
                            <div class="form-group">
                                <label>@lang('trans.status')</label>
                                <select class="form-control select2-filter" multiple>
                                    <option value="en_attente">@lang('trans.pending')</option>
                                    <option value="valide">@lang('trans.validated')</option>
                                    <option value="paye">@lang('trans.paid')</option>
                                    <option value="annule">@lang('trans.cancelled')</option>
                                </select>
                            </div>
                        </div>
                        <div class="col-md-3">
                            <div class="form-group">
                                <label>@lang('trans.applicant')</label>
                                <select class="form-control select2-filter">
                                    <option value="">@lang('trans.all_applicants')</option>
                                    @foreach($demandeurs ?? [] as $demandeur)
                                        <option value="{{ $demandeur->id }}">{{ $demandeur->np }}</option>
                                    @endforeach
                                </select>
                            </div>
                        </div>
                        <div class="col-md-3">
                            <div class="form-group">
                                <label>&nbsp;</label>
                                <button class="btn btn-primary btn-block" type="button" id="applyFilters">
                                    <i class="fas fa-search mr-2"></i>@lang('trans.apply_filters')
                                </button>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        {{-- Section : Ordres de Paiement --}}
        <div class="row">
            <div class="col-md-12">
                <div class="card">
                    <div class="card-header d-flex justify-content-between align-items-center">
                        <span>
                            <i class="fas fa-file-invoice mr-2"></i>@lang('trans.payment_orders')
                        </span>
                        <div class="bulk-actions" style="display: none;">
                            <button class="btn btn-success btn-sm" id="bulkCreateInvoice">
                                <i class="fas fa-file-invoice mr-1"></i>@lang('trans.create_bulk_invoices')
                            </button>
                        </div>
                    </div>
                    <div class="card-body">
                        <div class="table-responsive">
                            <table class="table table-bordered table-hover" id="ordersTable">
                                <thead>
                                    <tr>
                                        <th width="5%">
                                            <div class="custom-control custom-checkbox">
                                                <input type="checkbox" class="custom-control-input" id="selectAllOrders">
                                                <label class="custom-control-label" for="selectAllOrders"></label>
                                            </div>
                                        </th>
                                        <th width="15%">@lang('trans.reference')</th>
                                        <th width="20%">@lang('trans.applicant')</th>
                                        <th width="15%">@lang('trans.date')</th>
                                        <th width="15%">@lang('trans.amount')</th>
                                        <th width="15%">@lang('trans.status')</th>
                                        <th width="15%">@lang('trans.actions')</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @forelse($ordres as $ordre)
                                        <tr>
                                            <td>
                                                @if($ordre->statut === 'Validé' && empty($ordre->demande->facture))
                                                    <div class="custom-control custom-checkbox">
                                                        <input type="checkbox" class="custom-control-input order-checkbox" 
                                                               id="order{{ $ordre->id }}" value="{{ $ordre->id }}">
                                                        <label class="custom-control-label" for="order{{ $ordre->id }}"></label>
                                                    </div>
                                                @endif
                                            </td>
                                            <td>
                                                <span class="font-weight-bold">{{ $ordre->reference }}</span>
                                            </td>
                                            <td>
                                                @if(empty($ordre->demande->demandeur->compagnie))
                                                    {{ $ordre->demande->demandeur->np }}
                                                @else
                                                    <div>
                                                        <strong>{{ $ordre->demande->demandeur->compagnie->nom_entreprise }}</strong>
                                                        <br>
                                                        <small class="text-muted">{{ $ordre->demande->demandeur->np }}</small>
                                                    </div>
                                                @endif
                                            </td>
                                            <td>{{ \Carbon\Carbon::parse($ordre->date_ordre)->format('d/m/Y') }}</td>
                                            <td class="text-right">
                                                <strong>{{ number_format($ordre->montant, 0, ',', ' ') }} MRU</strong>
                                            </td>
                                            <td>
                                                @php
                                                    $statusClass = match($ordre->statut) {
                                                        'Validé' => 'success',
                                                        'En attente' => 'warning',
                                                        'Annulé' => 'danger',
                                                        default => 'secondary'
                                                    };
                                                @endphp
                                                <span class="badge badge-{{ $statusClass }} badge-status">
                                                    {{ $ordre->statut }}
                                                </span>
                                            </td>
                                            <td>
                                                <div class="btn-group" role="group">
                                                    @if($ordre->statut === 'Validé' && empty($ordre->demande->facture))
                                                        <a href="{{ route('daf.create', $ordre) }}" 
                                                           class="btn btn-sm btn-primary" 
                                                           data-toggle="tooltip" 
                                                           title="@lang('trans.create_invoice')">
                                                            <i class="fas fa-file-invoice"></i>
                                                        </a>
                                                    @endif
                                                    <a href="{{ route('dsv.ordre', $ordre->id) }}" 
                                                       class="btn btn-sm btn-warning" 
                                                       target="_blank"
                                                       data-toggle="tooltip" 
                                                       title="@lang('trans.print_order')">
                                                        <i class="fas fa-print"></i>
                                                    </a>
                                                    <a href="{{ route('daf.show', $ordre) }}" 
                                                       class="btn btn-sm btn-info"
                                                       data-toggle="tooltip" 
                                                       title="@lang('trans.view_details')">
                                                        <i class="fas fa-eye"></i>
                                                    </a>
                                                </div>
                                            </td>
                                        </tr>
                                    @empty
                                        <tr>
                                            <td colspan="7" class="text-center text-muted py-4">
                                                <i class="fas fa-inbox fa-2x mb-2"></i><br>
                                                @lang('trans.no_payment_orders_found')
                                            </td>
                                        </tr>
                                    @endforelse
                                </tbody>
                            </table>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        {{-- Section : Factures --}}
        <div class="row mt-4">
            <div class="col-md-12">
                <div class="card">
                    <div class="card-header">
                        <i class="fas fa-file-invoice-dollar mr-2"></i>@lang('trans.invoices')
                    </div>
                    <div class="card-body">
                        <div class="table-responsive">
                            <table class="table table-bordered table-hover" id="invoicesTable">
                                <thead>
                                    <tr>
                                        <th>@lang('trans.reference')</th>
                                        <th>@lang('trans.applicant')</th>
                                        <th>@lang('trans.issue_date')</th>
                                        <th>@lang('trans.due_date')</th>
                                        <th>@lang('trans.amount')</th>
                                        <th>@lang('trans.status')</th>
                                        <th>@lang('trans.actions')</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @forelse($factures as $facture)
                                        <tr>
                                            <td><span class="font-weight-bold">{{ $facture->reference }}</span></td>
                                            <td>
                                                @if(empty($facture->demande->demandeur->compagnie))
                                                    {{ $facture->demande->demandeur->np }}
                                                @else
                                                    <div>
                                                        <strong>{{ $facture->demande->demandeur->compagnie->nom_entreprise }}</strong>
                                                        <br>
                                                        <small class="text-muted">{{ $facture->demande->demandeur->np }}</small>
                                                    </div>
                                                @endif
                                            </td>
                                            <td>{{ \Carbon\Carbon::parse($facture->date_facture)->format('d/m/Y') }}</td>
                                            <td>{{ \Carbon\Carbon::parse($facture->date_limite)->format('d/m/Y') }}</td>
                                            <td class="text-right">
                                                <strong>{{ number_format($facture->montant, 0, ',', ' ') }} MRU</strong>
                                            </td>
                                            <td>
                                                @php
                                                    $statusClass = match($facture->statut) {
                                                        'Validée' => 'success',
                                                        'En attente' => 'warning',
                                                        'Annulée' => 'danger',
                                                        'Payée' => 'info',
                                                        default => 'secondary'
                                                    };
                                                @endphp
                                                <span class="badge badge-{{ $statusClass }} badge-status">
                                                    {{ $facture->statut }}
                                                </span>
                                            </td>
                                            <td>
                                                <div class="btn-group" role="group">
                                                    @if(empty($facture->demande->paiement))
                                                        <a href="{{ route('daf.edit', $facture) }}" 
                                                           class="btn btn-sm btn-primary"
                                                           data-toggle="tooltip" 
                                                           title="@lang('trans.edit_invoice')">
                                                            <i class="fas fa-edit"></i>
                                                        </a>
                                                        <form action="{{ route('daf.valider', $facture) }}" 
                                                              method="POST" class="d-inline">
                                                            @csrf
                                                            @method('PATCH')
                                                            <button type="submit" 
                                                                    class="btn btn-sm btn-success"
                                                                    data-toggle="tooltip" 
                                                                    title="@lang('trans.validate_invoice')"
                                                                    onclick="return confirm('@lang('trans.confirm_validation')')">
                                                                <i class="fas fa-check"></i>
                                                            </button>
                                                        </form>
                                                        <form action="{{ route('daf.destroy', $facture) }}" 
                                                              method="POST" class="d-inline">
                                                            @csrf
                                                            @method('DELETE')
                                                            <button type="submit" 
                                                                    class="btn btn-sm btn-danger"
                                                                    data-toggle="tooltip" 
                                                                    title="@lang('trans.delete_invoice')"
                                                                    onclick="return confirm('@lang('trans.confirm_deletion')')">
                                                                <i class="fas fa-trash"></i>
                                                            </button>
                                                        </form>
                                                    @endif
                                                    <a href="{{ route('daf.invoice', $facture->id) }}" 
                                                       class="btn btn-sm btn-warning" 
                                                       target="_blank"
                                                       data-toggle="tooltip" 
                                                       title="@lang('trans.print_invoice')">
                                                        <i class="fas fa-print"></i>
                                                    </a>
                                                </div>
                                            </td>
                                        </tr>
                                    @empty
                                        <tr>
                                            <td colspan="7" class="text-center text-muted py-4">
                                                <i class="fas fa-inbox fa-2x mb-2"></i><br>
                                                @lang('trans.no_invoices_found')
                                            </td>
                                        </tr>
                                    @endforelse
                                </tbody>
                            </table>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        {{-- Section : Paiements --}}
        <div class="row mt-4">
            <div class="col-md-12">
                <div class="card">
                    <div class="card-header">
                        <i class="fas fa-money-bill-wave mr-2"></i>@lang('trans.payments')
                    </div>
                    <div class="card-body">
                        <div class="table-responsive">
                            <table class="table table-bordered table-hover" id="paymentsTable">
                                <thead>
                                    <tr>
                                        <th>@lang('trans.reference')</th>
                                        <th>@lang('trans.applicant')</th>
                                        <th>@lang('trans.payment_date')</th>
                                        <th>@lang('trans.amount')</th>
                                        <th>@lang('trans.status')</th>
                                        <th>@lang('trans.actions')</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @forelse($paiements as $paiement)
                                        <tr>
                                            <td><span class="font-weight-bold">{{ $paiement->reference }}</span></td>
                                            <td>
                                                @if(empty($paiement->demande->demandeur->compagnie))
                                                    {{ $paiement->demande->demandeur->np }}
                                                @else
                                                    <div>
                                                        <strong>{{ $paiement->demande->demandeur->compagnie->nom_entreprise }}</strong>
                                                        <br>
                                                        <small class="text-muted">{{ $paiement->demande->demandeur->np }}</small>
                                                    </div>
                                                @endif
                                            </td>
                                            <td>{{ \Carbon\Carbon::parse($paiement->date_paiement)->format('d/m/Y') }}</td>
                                            <td class="text-right">
                                                <strong>{{ number_format($paiement->montant, 0, ',', ' ') }} MRU</strong>
                                            </td>
                                            <td>
                                                @php
                                                    $statusClass = match($paiement->statut) {
                                                        'Réglée' => 'success',
                                                        'En attente' => 'warning',
                                                        'Confirmé' => 'info',
                                                        default => 'secondary'
                                                    };
                                                @endphp
                                                <span class="badge badge-{{ $statusClass }} badge-status">
                                                    {{ $paiement->statut }}
                                                </span>
                                            </td>
                                            <td>
                                                <div class="btn-group" role="group">
                                                    <a href="{{ route('daf.show', $paiement) }}" 
                                                       class="btn btn-sm btn-info"
                                                       data-toggle="tooltip" 
                                                       title="@lang('trans.view_details')">
                                                        <i class="fas fa-eye"></i>
                                                    </a>
                                                    @if($paiement->statut === 'Réglée')
                                                        <form action="{{ route('daf.valider_paiement', $paiement) }}" 
                                                              method="POST" class="d-inline">
                                                            @csrf
                                                            @method('PATCH')
                                                            <button type="submit" 
                                                                    class="btn btn-sm btn-success"
                                                                    data-toggle="tooltip" 
                                                                    title="@lang('trans.confirm_payment')"
                                                                    onclick="return confirm('@lang('trans.confirm_payment_question')')">
                                                                <i class="fas fa-check-double"></i>
                                                            </button>
                                                        </form>
                                                        <a href="{{ route('daf.edit', $paiement) }}" 
                                                           class="btn btn-sm btn-primary"
                                                           data-toggle="tooltip" 
                                                           title="@lang('trans.edit_payment')">
                                                            <i class="fas fa-edit"></i>
                                                        </a>
                                                    @endif
                                                </div>
                                            </td>
                                        </tr>
                                    @empty
                                        <tr>
                                            <td colspan="6" class="text-center text-muted py-4">
                                                <i class="fas fa-inbox fa-2x mb-2"></i><br>
                                                @lang('trans.no_payments_found')
                                            </td>
                                        </tr>
                                    @endforelse
                                </tbody>
                            </table>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        {{-- Section : Autorisations de Paiement --}}
        <div class="row mt-4">
            <div class="col-md-12">
                <div class="card">
                    <div class="card-header">
                        <i class="fas fa-check-circle mr-2"></i>@lang('trans.payment_authorizations')
                    </div>
                    <div class="card-body">
                        <div class="table-responsive">
                            <table class="table table-bordered table-hover" id="authorizationsTable">
                                <thead>
                                    <tr>
                                        <th>@lang('trans.reference')</th>
                                        <th>@lang('trans.creation_date')</th>
                                        <th>@lang('trans.application_code')</th>
                                        <th>@lang('trans.application_type')</th>
                                        <th>@lang('trans.flight_type')</th>
                                        <th>@lang('trans.payment_method')</th>
                                        <th>@lang('trans.amount')</th>
                                        <th>@lang('trans.payment_date')</th>
                                        <th>@lang('trans.status')</th>
                                        <th>@lang('trans.invoice')</th>
                                        <th>@lang('trans.proof')</th>
                                        <th>@lang('trans.actions')</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @forelse($paiementAutorisations as $paiement)
                                        <tr>
                                            <td><span class="font-weight-bold">{{ strtoupper($paiement->reference) }}</span></td>
                                            <td>{{ \Carbon\Carbon::parse($paiement->created_at)->format('d/m/Y') }}</td>
                                            <td><span class="badge badge-info">{{ $paiement->demande->code ?? 'N/A' }}</span></td>
                                            <td>{{ $paiement->demande->type->libelle ?? 'N/A' }}</td>
                                            <td>{{ $paiement->demande->typeVol->nom ?? 'N/A' }}</td>
                                            <td>{{ strtoupper($paiement->methode) }}</td>
                                            <td class="text-right">
                                                <strong>{{ number_format($paiement->montant_total, 0, ',', ' ') }} MRU</strong>
                                            </td>
                                            <td>{{ \Carbon\Carbon::parse($paiement->date_paiement)->format('d/m/Y') }}</td>
                                            <td>
                                                @php
                                                    $statusClass = match($paiement->statut) {
                                                        'Validé' => 'success',
                                                        'En attente' => 'warning',
                                                        'Refusé' => 'danger',
                                                        default => 'secondary'
                                                    };
                                                @endphp
                                                <span class="badge badge-{{ $statusClass }} badge-status">
                                                    {{ strtoupper($paiement->statut) }}
                                                </span>
                                            </td>
                                            <td>
                                                <a href="{{ route('daf.invoiceAutorisation', $paiement->id) }}" 
                                                   class="btn btn-sm btn-outline-warning" 
                                                   target="_blank">
                                                    <i class="fas fa-print"></i>
                                                </a>
                                            </td>
                                            <td>
                                                @if($paiement->justificatif)
                                                    <a href="{{ asset('/uploads/' . $paiement->justificatif) }}" 
                                                       target="_blank" 
                                                       class="btn btn-sm btn-outline-primary">
                                                        <i class="fas fa-eye"></i>
                                                    </a>
                                                @else
                                                    <span class="text-muted">N/A</span>
                                                @endif
                                            </td>
                                            <td>
                                                @if(isset($paiement->demande->etatDemande) && 
                                                    $paiement->demande->etatDemande->compagnie_payer && 
                                                    !$paiement->demande->etatDemande->daf_confirme_pay)
                                                    <form action="{{ route('update-state', $paiement->demande->id) }}" 
                                                          method="POST">
                                                        @csrf
                                                        <input type="hidden" name="action" value="daf_confirme_pay">
                                                        <input type="hidden" name="is_approved" value="1">
                                                        <input type="hidden" name="type_autorisation" 
                                                               value="{{ $paiement->demande->type->libelle }}">
                                                        <input type="hidden" name="paiement_id" value="{{ $paiement->id }}">
                                                        <button type="submit" 
                                                                class="btn btn-sm btn-success"
                                                                onclick="return confirm('@lang('trans.confirm_payment_authorization')')">
                                                            <i class="fas fa-check mr-1"></i>@lang('trans.confirm')
                                                        </button>
                                                    </form>
                                                @endif
                                            </td>
                                        </tr>
                                    @empty
                                        <tr>
                                            <td colspan="12" class="text-center text-muted py-4">
                                                <i class="fas fa-inbox fa-2x mb-2"></i><br>
                                                @lang('trans.no_authorizations_found')
                                            </td>
                                        </tr>
                                    @endforelse
                                </tbody>
                            </table>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
@endsection

@push('script')
    <script src="{{ asset('assets/admin/plugins/datatables/jquery.dataTables.min.js') }}"></script>
    <script src="{{ asset('assets/admin/plugins/datatables-bs4/js/dataTables.bootstrap4.min.js') }}"></script>
    <script src="{{ asset('assets/admin/plugins/datatables-responsive/js/dataTables.responsive.min.js') }}"></script>
    <script src="{{ asset('assets/admin/plugins/datatables-responsive/js/responsive.bootstrap4.min.js') }}"></script>
    <script src="{{ asset('assets/admin/plugins/select2/js/select2.full.min.js') }}"></script>
    <script src="{{ asset('assets/admin/plugins/moment/moment.min.js') }}"></script>
    <script src="{{ asset('assets/admin/plugins/daterangepicker/daterangepicker.js') }}"></script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/toastr.js/latest/toastr.min.js" integrity="sha512-VEd+nq25CkR676O+pLBnDW09R7VQX9Mdiij052gVCp5yVH3jGtH70Ho/UUv4mJDsEdTvqRCFZg0NKGiojGnUCw==" crossorigin="anonymous" referrerpolicy="no-referrer"></script>
@endpush

@push('custom')
    <script>
        $(function() {
            // Initialisation des tooltips Bootstrap
            $('[data-toggle="tooltip"]').tooltip();
            
            // Initialisation de Select2 pour les filtres
            $('.select2-filter').select2({
                theme: 'bootstrap4',
                placeholder: '@lang("trans.select_option")',
                allowClear: true
            });
            
            // Configuration commune pour les DataTables
            const dataTableConfig = {
                paging: true,
                lengthChange: true,
                searching: true,
                ordering: true,
                info: true,
                autoWidth: false,
                responsive: true,
                pageLength: 25,
                lengthMenu: [[10, 25, 50, 100, -1], [10, 25, 50, 100, "Tous"]]
            };
            
            // Initialisation des DataTables
            $('#ordersTable').DataTable({
                ...dataTableConfig,
                columnDefs: [{
                    targets: [0, 6],
                    orderable: false
                }]
            });
            
            $('#invoicesTable').DataTable({
                ...dataTableConfig,
                columnDefs: [{
                    targets: 6,
                    orderable: false
                }]
            });
            
            $('#paymentsTable').DataTable({
                ...dataTableConfig,
                columnDefs: [{
                    targets: 5,
                    orderable: false
                }]
            });
            
            $('#authorizationsTable').DataTable({
                ...dataTableConfig,
                columnDefs: [{
                    targets: [9, 10, 11],
                    orderable: false
                }]
            });
            
            // Gestion de la sélection multiple pour les ordres de paiement
            const $selectAll = $('#selectAllOrders');
            const $orderCheckboxes = $('.order-checkbox');
            const $bulkActions = $('.bulk-actions');
            
            function updateBulkActions() {
                const checkedCount = $orderCheckboxes.filter(':checked').length;
                $bulkActions.toggle(checkedCount > 0);
            }
            
            $selectAll.on('change', function() {
                $orderCheckboxes.prop('checked', this.checked);
                updateBulkActions();
            });
            
            $orderCheckboxes.on('change', function() {
                $selectAll.prop('checked', $orderCheckboxes.length === $orderCheckboxes.filter(':checked').length);
                updateBulkActions();
            });
            
            // Création de factures en masse
            $('#bulkCreateInvoice').on('click', function() {
                const selectedOrders = $orderCheckboxes.filter(':checked').map(function() {
                    return this.value;
                }).get();
                
                if (selectedOrders.length === 0) {
                    alert('@lang("trans.select_at_least_one_order")');
                    return;
                }
                
                if (confirm('@lang("trans.confirm_bulk_invoice_creation")')) {
                    // Redirection vers la page de création avec les IDs sélectionnés
                    window.location.href = '{{ route("daf.bulkCreate") }}?orders=' + selectedOrders.join(',');
                }
            });
            
            // Application des filtres
            $('#applyFilters').on('click', function() {
                // Logique d'application des filtres
                toastr.info('@lang("trans.filters_applied")');
            });
            
            // Gestion des messages flash
            @if(session('success'))
                toastr.success('{{ session('success') }}');
            @endif
            
            @if(session('error'))
                toastr.error('{{ session('error') }}');
            @endif
        });
    </script>
@endpush