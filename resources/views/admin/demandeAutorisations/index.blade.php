@extends('layouts.admin')

@section('title')
    @lang('trans.dashboard_admin')
@endsection

@section('contentheader')
    @lang('trans.dashboard_admin')
@endsection

@section('contentheaderlink')
    <a href="{{ route('demandeAutorisations') }}">
        @lang('trans.dashboard_admin')
    </a>
@endsection

@section('contentheaderactive')
    @lang('trans.dashboard_admin')
@endsection

@push('css')
    <link rel="stylesheet" href="{{ asset('assets/admin/plugins/datatables-bs4/css/dataTables.bootstrap4.min.css') }}">
    <link rel="stylesheet" href="{{ asset('assets/admin/plugins/datatables-responsive/css/responsive.bootstrap4.min.css') }}">
    <link rel="stylesheet" href="{{ asset('assets/admin/plugins/select2/css/select2.min.css') }}">
    <link rel="stylesheet" href="{{ asset('assets/admin/plugins/select2-bootstrap4-theme/select2-bootstrap4.min.css') }}">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/toastr.js/latest/toastr.min.css">
    <style>
        .badge-submitted { background-color: #17a2b8; color: white; }
        .badge-under_review { background-color: #ffc107; color: black; }
        .badge-service_approved { background-color: #28a745; color: white; }
        .badge-paid { background-color: #007bff; color: white; }
        .badge-payment_confirmed { background-color: #20c997; color: white; }
        
        .badge-rejected { background-color: #dc3545; color: white; }
        .filter-section { margin-bottom: 20px; padding: 15px; background: #f8f9fa; border-radius: 5px; }
        .status-badge { cursor: pointer; transition: opacity 0.3s; }
        .status-badge:hover { opacity: 0.8; }
    </style>
@endpush

@section('content')
    <div class="container-fluid">
        @if($demandeAutorisations->isNotEmpty())
            <div class="row">
                <div class="col-md-12">
                    <!-- Filtres avancés -->
                    <div class="filter-section">
                        <div class="row">
                            <div class="col-md-3">
                                <label>@lang('trans.filter_by_status')</label>
                                <select id="etatFilter" class="form-control select2">
                                    <option value="">@lang('trans.all_statuses')</option>
                                    <option value="submitted">@lang('trans.submitted')</option>
                                    <option value="under_review">@lang('trans.under_review')</option>
                                    <option value="service_approved">@lang('trans.service_approved')</option>
                                    <option value="paid">@lang('trans.paid')</option>
                                    <option value="payment_confirmed">@lang('trans.payment_confirmed')</option>
                                    
                                    <option value="rejected">@lang('trans.rejected')</option>
                                </select>
                            </div>
                            <div class="col-md-6">
                                <label>@lang('trans.filter_by_date')</label>
                                <input type="text" id="dateRangeFilter" class="form-control" placeholder="Sélectionner une période">
                            </div>
                            
                            <div class="col-md-3">
                                <label>&nbsp;</label>
                                <button id="resetFilters" class="btn btn-secondary form-control">
                                    <i class="fas fa-undo"></i> @lang('trans.reset_filters')
                                </button>
                            </div>
                        </div>
                    </div>

                    <div class="card">
                        <div class="card-header">
                            <h3 class="card-title">
                                <i class="fas fa-file-alt mr-2"></i>@lang('trans.applications')
                                <span class="badge badge-primary ml-2">{{ $demandeAutorisations->count() }}</span>
                            </h3>
                            <div class="card-tools">
                                <button type="button" class="btn btn-tool" data-card-widget="collapse">
                                    <i class="fas fa-minus"></i>
                                </button>
                            </div>
                        </div>
                        <div class="card-body">
                            <div class="table-responsive">
                                <table class="table table-bordered table-striped table-hover" id="applicationsTable">
                                    <thead>
                                        <tr>
                                            <th>@lang('trans.submission_date')</th>
                                            <th>@lang('trans.creation_date')</th>
                                            <th>@lang('trans.code')</th>
                                            <th>@lang('trans.type_application')</th>
                                            <th>@lang('trans.type_flight')</th>
                                            <th>@lang('trans.start_date')</th>
                                            <th>@lang('trans.end_date')</th>
                                            <th>@lang('trans.applicant')</th>
                                            <th>@lang('trans.plane')</th>
                                            <th>@lang('trans.status')</th>
                                            <th>@lang('trans.actions')</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        @foreach($demandeAutorisations as $demande)
                                            @php
                                                $etat = $demande->etat_workflow;
                                                $canView = optional($demande->etatDemande)->compagnie_cree_demande ?? false;
                                            @endphp
                                            <tr data-etat="{{ $etat }}" data-type="{{ $demande->type->id }}">
                                                <td data-order="{{ $demande->created_at ? strtotime($demande->created_at) : 0 }}">
                                                    {{ $demande->created_at ? $demande->created_at->format('d-m-Y H:i') : 'N/A' }}
                                                </td>
                                                <td data-order="{{ $demande->date_soumission ? strtotime($demande->date_soumission) : 0 }}">
                                                    {{ $demande->date_soumission ? \Carbon\Carbon::parse($demande->date_soumission)->format('d-m-Y H:i') : 'N/A' }}
                                                </td>
                                                <td><span class="badge badge-info">{{ $demande->code }}</span></td>
                                                <td>{{ $demande->type->libelle }}</td>
                                                <td>{{ $demande->typeVol->nom ?? 'N/A' }}</td>
                                                <td>{{ \Carbon\Carbon::parse($demande->date_debut)->format('d-m-Y') }}</td>
                                                <td>{{ \Carbon\Carbon::parse($demande->date_fin)->format('d-m-Y') }}</td>
                                                <td>
                                                    @if($demande->user && $demande->user->demandeur)
                                                        <strong>{{ strtoupper($demande->user->demandeur->np) }}</strong><br>
                                                        <small class="text-muted">{{ $demande->user->email }}</small>
                                                    @endif
                                                </td>
                                                <td>
                                                    @foreach($demande->avions as $avion)
                                                        <span class="badge badge-secondary">{{ $avion->immatriculation }}</span>
                                                    @endforeach
                                                </td>
                                                <td>
                                                    @php
                                                        $badgeClass = match($etat) {
                                                            'submitted' => 'badge-submitted',
                                                            'under_review' => 'badge-under_review',
                                                            'service_approved' => 'badge-service_approved',
                                                            'paid' => 'badge-paid',
                                                            'payment_confirmed' => 'badge-payment_confirmed',
                                                            
                                                            'rejected' => 'badge-rejected',
                                                            default => 'badge-secondary'
                                                        };
                                                    @endphp
                                                    <span class="badge {{ $badgeClass }} status-badge" 
                                                          onclick="filterByStatus('{{ $etat }}')"
                                                          title="@lang('trans.click_to_filter')">
                                                        {{ __("trans.{$etat}") }}
                                                    </span>
                                                </td>
                                                <td>
                                                    <div class="btn-group" role="group">
                                                        @if($canView)
                                                            <a href="{{ route('demandeAutorisations.show', $demande->id) }}" 
                                                               class="btn btn-info btn-sm" 
                                                               title="@lang('trans.view_details')">
                                                                <i class="fas fa-eye"></i>
                                                            </a>
                                                        @endif

                                                        @if($demande->has_issues ?? false)
                                                            <button type="button" 
                                                                    class="btn btn-warning btn-sm" 
                                                                    onclick="showIssues({{ $demande->id }})"
                                                                    title="@lang('trans.view_issues')">
                                                                <i class="fas fa-exclamation-triangle"></i>
                                                            </button>
                                                        @endif
                                                    
                                                        @if(optional($demande->etatDemande)->dta_annoter &&
                                                            $demande->isValidatedByAll() &&
                                                            !optional($demande->etatDemande)->service_valider)
                                                            <form action="{{ route('update-state', $demande->id) }}" method="POST" class="d-inline">
                                                                @csrf
                                                                <input type="hidden" name="action" value="service_valider">
                                                                <input type="hidden" name="is_approved" value="1">
                                                                <button type="submit" class="btn btn-success btn-sm mb-1"
                                                                        onclick="return confirm('Confirmer la validation ?')">
                                                                    <i class="fas fa-check-double"></i> @lang('trans.validate')
                                                                </button>
                                                            </form>
                                                        @endif
                                                        @if(!empty($demande->autorisation($demande->id)))
                                                            <a target="_blank" 
                                                               href="{{ route('autorisations.print', $demande->autorisation($demande->id)) }}"
                                                               class="btn btn-primary btn-sm" 
                                                               title="@lang('trans.print_authorization')">
                                                                <i class="fas fa-print"></i>
                                                            </a>
                                                        @endif

                                                        @if($demande->paiement && $demande->paiement->statut == 'on_hold')
                                                            <a href="{{ route('daf.invoiceAutorisation', $demande->paiement->id) }}"
                                                               class="btn btn-warning btn-sm" 
                                                               target="_blank"
                                                               title="@lang('trans.view_invoice')">
                                                                <i class="fas fa-file-invoice"></i>
                                                            </a>
                                                        @endif
                                                    </div>
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
        @else
            <div class="row">
                <div class="col-md-12">
                    <div class="alert alert-info text-center">
                        <i class="fas fa-info-circle fa-3x mb-3"></i>
                        <h4>@lang('trans.no_applications_found')</h4>
                        <p>@lang('trans.no_applications_available')</p>
                    </div>
                </div>
            </div>
        @endif
    </div>

    <!-- Modal des issues -->
    <div class="modal fade" id="issuesModal" tabindex="-1" role="dialog">
        <div class="modal-dialog modal-lg" role="document">
            <div class="modal-content">
                <div class="modal-header bg-warning">
                    <h5 class="modal-title">
                        <i class="fas fa-exclamation-triangle"></i> @lang('trans.application_issues')
                    </h5>
                    <button type="button" class="close" data-dismiss="modal">
                        <span>&times;</span>
                    </button>
                </div>
                <div class="modal-body" id="issuesModalBody">
                    <!-- Contenu chargé dynamiquement -->
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-dismiss="modal">
                        @lang('trans.close')
                    </button>
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
    <script src="https://cdnjs.cloudflare.com/ajax/libs/moment.js/2.29.1/moment.min.js"></script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/toastr.js/latest/toastr.min.js"></script>
@endpush

@push('custom')
<script>
    $(document).ready(function() {
        // Initialisation des Select2
        $('.select2').select2({
            theme: 'bootstrap4',
            width: '100%'
        });

        // Configuration de DataTable
        const table = $('#applicationsTable').DataTable({
            order: [[0, "desc"]],
            pageLength: 25,
            responsive: true,
            language: {
                url: "//cdn.datatables.net/plug-ins/1.13.6/i18n/fr-FR.json",
                search: "@lang('trans.search')",
                lengthMenu: "@lang('trans.show_entries')",
                info: "@lang('trans.showing_entries')"
            },
            columnDefs: [
                {
                    targets: [0, 1, 5, 6],
                    type: "date-eu"
                },
                {
                    targets: -1,
                    orderable: false,
                    searchable: false,
                    width: "120px"
                }
            ],
            dom: '<"row"<"col-sm-12 col-md-6"l><"col-sm-12 col-md-6"f>>' +
                 '<"row"<"col-sm-12"tr>>' +
                 '<"row"<"col-sm-12 col-md-5"i><"col-sm-12 col-md-7"p>>',
            initComplete: function() {
                // Appliquer les filtres initiaux
                applyFilters();
            }
        });

        // Filtre par état
        $('#etatFilter').on('change', function() {
            const etat = $(this).val();
            if (etat) {
                table.column(9).search('^' + etat + '$', true, false).draw();
            } else {
                table.column(9).search('').draw();
            }
        });

        // Filtre par type
        $('#typeFilter').on('change', function() {
            const typeId = $(this).val();
            if (typeId) {
                table.column(3).search(typeId, false, false).draw();
            } else {
                table.column(3).search('').draw();
            }
        });

        // Réinitialiser les filtres
        $('#resetFilters').on('click', function() {
            $('#etatFilter').val('').trigger('change');
            $('#typeFilter').val('').trigger('change');
            $('#dateRangeFilter').val('');
            table.search('').columns().search('').draw();
        });

        // Fonction pour appliquer tous les filtres
        function applyFilters() {
            const urlParams = new URLSearchParams(window.location.search);
            const statusParam = urlParams.get('status');
            
            if (statusParam) {
                $('#etatFilter').val(statusParam).trigger('change');
            }
        }
    });

    // Fonction pour filtrer par statut
    window.filterByStatus = function(status) {
        $('#etatFilter').val(status).trigger('change');
        
        // Scroll jusqu'au tableau
        $('html, body').animate({
            scrollTop: $('#applicationsTable').offset().top - 100
        }, 500);
    };

    // Fonction pour afficher les issues
    window.showIssues = function(demandeId) {
        // Ici vous pouvez faire un appel AJAX pour récupérer les détails des issues
        // Ou utiliser les données déjà chargées
        $.ajax({
            url: `/demande-autorisations/${demandeId}/issues`,
            method: 'GET',
            success: function(response) {
                $('#issuesModalBody').html(response);
                $('#issuesModal').modal('show');
            },
            error: function() {
                toastr.error('@lang('trans.error_loading_issues')');
            }
        });
    };

    // Configuration de Toastr
    toastr.options = {
        closeButton: true,
        progressBar: true,
        positionClass: "toast-top-right",
        timeOut: 3000
    };
</script>
@endpush