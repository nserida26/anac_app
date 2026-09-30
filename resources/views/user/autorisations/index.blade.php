<x-app-user-layout :title="__('trans.autorization_applications')">

@push('css')
    <link rel="stylesheet" href="{{ asset('assets/admin/plugins/datatables-bs4/css/dataTables.bootstrap4.min.css') }}">
    <link rel="stylesheet" href="{{ asset('assets/admin/plugins/datatables-responsive/css/responsive.bootstrap4.min.css') }}">
    <link rel="stylesheet" href="{{ asset('assets/admin/plugins/toastr/toastr.min.css') }}">
    <link rel="stylesheet" href="{{ asset('assets/admin/plugins/select2/css/select2.min.css') }}">
    <link rel="stylesheet" href="{{ asset('assets/admin/plugins/select2-bootstrap4-theme/select2-bootstrap4.min.css') }}">
    <style>
        /* Couleurs des badges de statut : définies dans public/css/user-app.css */
        /* Styles du parcours (stepper) : définis dans public/css/user-app.css */

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
                                <table class="table" id="autorization_applications">
                                    <thead>
                                        <tr>
                                            <th>@lang('trans.code')</th>
                                            <th>@lang('trans.operator')</th>
                                            <th>@lang('trans.type_application')</th>
                                            <th>@lang('trans.type_flight')</th>
                                            <th>@lang('trans.status')</th>
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
                                                    $isIssued = (bool) $demande->autorisation($demande->id);
                                                    $workflowState = $isIssued ? 'issued' : ($demande->etat_workflow ?? 'draft');
                                                    $statusBadgeClass = match ($workflowState) {
                                                        'draft' => 'badge-draft',
                                                        'submitted' => 'badge-submitted',
                                                        'under_review' => 'badge-under_review',
                                                        'service_approved' => 'badge-service_approved',
                                                        'paid' => 'badge-paid',
                                                        'payment_confirmed' => 'badge-payment_confirmed',
                                                        'issued' => 'badge-issued',
                                                        'rejected' => 'badge-rejected',
                                                        default => 'badge-secondary',
                                                    };
                                                    $statusLabel = match ($workflowState) {
                                                        'draft' => __('trans.workflow_status_draft'),
                                                        'submitted' => __('trans.workflow_status_submitted'),
                                                        'under_review' => __('trans.workflow_status_under_review'),
                                                        'service_approved' => __('trans.workflow_status_service_approved'),
                                                        'paid' => __('trans.workflow_status_paid'),
                                                        'payment_confirmed' => __('trans.workflow_status_payment_confirmed'),
                                                        'issued' => __('trans.workflow_status_issued'),
                                                        'rejected' => __('trans.workflow_status_rejected'),
                                                        default => $workflowState,
                                                    };
                                                @endphp
                                                <tr>
                                                    <td>{{ $demande->code ?? 'N/A' }}</td>
                                                    <td>{{ optional($demande->compagnie)->nom_entreprise ?? 'N/A' }}</td>
                                                    <td>{{ $demande->type->libelle ?? 'N/A' }}</td>
                                                    <td>{{ $demande->typeVol->nom ?? 'N/A' }}</td>
                                                    <td>
                                                        <button type="button" class="badge {{ $statusBadgeClass }}"
                                                                style="border: none; cursor: pointer;"
                                                                data-toggle="modal" data-target="#statusModal-{{ $demande->id }}">
                                                            {{ $statusLabel }}
                                                        </button>
                                                    </td>
                                                    <td>
                                                        <div class="anac-actions">
                                                            {{-- Détails de la demande --}}
                                                            <button type="button" class="btn btn-sm anac-action anac-action--blue"
                                                                    data-toggle="modal" data-target="#detailsModal-{{ $demande->id }}"
                                                                    title="@lang('trans.action_details_title')" aria-label="@lang('trans.action_details_title')">
                                                                <i class="fas fa-info-circle"></i>
                                                            </button>

                                                            @if ($canEdit)
                                                                <a href="{{ route('user.autorisations.edit', $demande->id) }}"
                                                                   class="btn btn-sm anac-action anac-action--grey"
                                                                   title="@lang('trans.action_edit_title')" aria-label="@lang('trans.action_edit_title')">
                                                                    <i class="fas fa-pencil-alt"></i>
                                                                </a>
                                                                <button class="btn btn-sm btn-modify anac-action anac-action--grey"
                                                                    data-id="{{ $demande->id }}"
                                                                    data-type="{{ $typeId }}"
                                                                    data-type-vol="{{ $typeVolId }}"
                                                                    data-date-debut="{{ $dateDebut }}"
                                                                    data-date-fin="{{ $dateFin }}"
                                                                    data-sous-validite="{{ $sousValidite }}"
                                                                    data-objet="{{ $objet }}"
                                                                    data-compagnie-id="{{ $demande->compagnie_id }}"
                                                                    title="@lang('trans.action_modify_title')" aria-label="@lang('trans.action_modify_title')">
                                                                    <i class="fas fa-edit"></i>
                                                                </button>
                                                                <button type="button" class="btn btn-sm anac-action anac-action--red"
                                                                        data-toggle="modal" data-target="#deleteModal-{{ $demande->id }}"
                                                                        title="@lang('trans.action_delete_title')" aria-label="@lang('trans.action_delete_title')">
                                                                    <i class="fas fa-trash"></i>
                                                                </button>
                                                            @else
                                                                {{-- Demande déjà soumise : consultation en lecture seule uniquement --}}
                                                                <a href="{{ route('user.autorisations.edit', $demande->id) }}"
                                                                   class="btn btn-sm anac-action anac-action--black"
                                                                   title="@lang('trans.action_view_title')" aria-label="@lang('trans.action_view_title')">
                                                                    <i class="fas fa-eye"></i>
                                                                </a>
                                                            @endif

                                                            @if ($canSubmit)
                                                                <form action="{{ route('update-state', $demande->id) }}" method="POST" class="d-inline">
                                                                    @csrf
                                                                    <input type="hidden" name="action" value="compagnie_cree_demande">
                                                                    <input type="hidden" name="is_approved" value="1">
                                                                    <button type="submit" class="btn btn-success btn-sm"
                                                                            title="@lang('trans.action_send_title')" aria-label="@lang('trans.action_send_title')"
                                                                            onclick="return confirm('@lang('trans.confirm_submission')')">
                                                                        <i class="fas fa-paper-plane"></i>
                                                                        <span class="badge badge-light">{{ $documentCount }}</span>
                                                                    </button>
                                                                </form>
                                                            @elseif ($canEdit)
                                                                <button class="btn btn-secondary btn-sm" disabled
                                                                        title="@lang('trans.action_add_docs_title')"
                                                                        aria-label="@lang('trans.action_add_docs_title')">
                                                                    <i class="fas fa-paper-plane"></i>
                                                                </button>
                                                            @endif

                                                            @if ($hasIssues)
                                                                <button class="btn btn-sm anac-action anac-action--pink" data-toggle="modal"
                                                                        data-target="#issuesModal-{{ $demande->id }}"
                                                                        title="@lang('trans.action_issues_title')" aria-label="@lang('trans.action_issues_title')">
                                                                    <i class="fas fa-exclamation-circle"></i>
                                                                </button>
                                                            @endif

                                                            {{-- Autorisation délivrée : imprimable quel que soit le
                                                                 type de demande (les conditions précédentes, propres
                                                                 aux types 1/2, ne couvraient pas les autres). --}}
                                                            @if ($isIssued)
                                                                <a target="_blank"
                                                                   href="{{ route('user.print', $demande->autorisation($demande->id)) }}"
                                                                   class="btn btn-sm anac-action anac-action--yellow"
                                                                   title="@lang('trans.action_print_title')" aria-label="@lang('trans.action_print_title')">
                                                                    <i class="fas fa-print"></i>
                                                                </a>
                                                            @endif
                                                        </div>
                                                    </td>
                                                </tr>

                                                {{-- Modal des Erreurs/Issues pour Autorisations --}}
                                                @if ($hasIssues)
                                                    @include('dir.demandeAutorisations.modals.issues', ['demande' => $demande])
                                                @endif

                                                {{-- Modal du parcours de la demande --}}
                                                @include('user.partials.autorisation-status-timeline', ['demande' => $demande])

                                                {{-- Modal des détails de la demande --}}
                                                @include('user.partials.autorisation-details-modal', ['demande' => $demande])

                                                {{-- Modal de suppression de la demande --}}
                                                @include('user.partials.autorisation-delete-modal', ['demande' => $demande])
                                            @endforeach
                                        @else
                                            <tr>
                                                <td colspan="6" class="text-center">@lang('trans.no_data')</td>
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
                                    <table class="table" id="paiements">
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

{{-- MODAL APPLICATION --}}
    <!-- Modal -->
<div class="modal fade" id="applicationModal" tabindex="-1" role="dialog" aria-labelledby="applicationModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-xl modal-dialog-centered" role="document">
        <div class="modal-content anac-modal">
            <div class="modal-header">
                <h5 class="modal-title">
                    <i class="fas fa-file-signature"></i>
                    <span id="applicationModalLabel">@lang('trans.add_application')</span>
                </h5>
                <button type="button" class="close" data-dismiss="modal" aria-label="@lang('trans.close')">
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
                                <label for="type_vol_id">@lang('trans.type_flight') <span class="text-danger">*</span></label>
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
                                    <i class="fas fa-info-circle"></i> @lang('trans.type_vol_depouille_hint')
                                </small>
                                <small id="typeVolMultiInfo" class="form-text text-info" style="display: none;">
                                    <i class="fas fa-info-circle"></i> @lang('trans.type_vol_multi_hint')
                                </small>
                            </div>
                        </div>

                        <div class="col-md-4">
                            <div class="form-group">
                                <label for="date_debut">@lang('trans.start_date') <span class="text-danger">*</span></label>
                                <input type="date" class="form-control" id="date_debut" name="date_debut" required>
                                <div class="invalid-feedback" id="date_debut_error"></div>
                            </div>
                        </div>
                        <div class="col-md-4">
                            <div class="form-group">
                                <label for="date_fin">@lang('trans.end_date') <span class="text-danger">*</span></label>
                                <input type="date" class="form-control" id="date_fin" name="date_fin" required>
                                <div class="invalid-feedback" id="date_fin_error"></div>
                            </div>
                        </div>
                        <div class="col-md-4">
                            <div class="form-group">
                                <label for="sous_validite">@lang('trans.sub_validity_label')</label>
                                <input type="number" min="12" max="72" step="12"
                                    class="form-control" name="sous_validite" id="sous_validite"
                                    placeholder="@lang('trans.leave_empty_if_not_applicable')">
                                <div class="invalid-feedback" id="sous_validite_error"></div>
                            </div>
                        </div>
                    </div>

                    <!-- Opérateur représenté -->
                    <div class="row">
                        <div class="col-md-12 mb-3">
                            <div class="form-group">
                                <div class="d-flex justify-content-between align-items-center mb-1">
                                    <label for="compagnie_id" class="form-label">
                                        @lang('trans.operator') <span class="text-danger">*</span>
                                    </label>
                                    <button type="button" class="btn btn-sm btn-success" id="addCompanyBtnApplication">
                                        <i class="fas fa-plus"></i> @lang('trans.add_action')
                                    </button>
                                </div>
                                <select class="form-control select2" id="compagnie_id" name="compagnie_id" required>
                                    <option value="">@lang('trans.select_operator')</option>
                                    @foreach ($compagnies as $compagnie)
                                        <option value="{{ $compagnie->id }}">
                                            @if (!empty($compagnie->code))
                                                {{ $compagnie->code }} {{ $compagnie->nom_entreprise }}
                                            @else
                                                {{ $compagnie->nom_entreprise }}
                                            @endif
                                        </option>
                                    @endforeach
                                </select>
                                <small class="form-text text-muted">@lang('trans.operator_represented_hint')</small>
                                <div class="invalid-feedback" id="compagnie_id_error"></div>
                            </div>
                        </div>
                    </div>

                    <!-- Objet du vol -->
                    <div class="row">
                        <div class="col-md-12 mb-3">
                            <div class="form-group">
                                <label for="objet" class="form-label">@lang('trans.object')</label>
                                <textarea class="form-control" id="objet" name="objet" rows="2"
                                    placeholder="@lang('trans.describe_flight_purpose')"></textarea>
                                <div class="invalid-feedback" id="objet_error"></div>
                            </div>
                        </div>
                    </div>

                </div>

                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-dismiss="modal">
                        <i class="fas fa-times"></i> @lang('trans.close')
                    </button>
                    <button type="submit" class="btn btn-success" id="submitBtn">
                        <i class="fas fa-paper-plane"></i> @lang('trans.send')
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>

<!-- Modal pour ajouter un opérateur (utilisé par le sélecteur "Opérateur" ci-dessus) -->
<div class="modal fade" id="companyModalApplication" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content anac-modal">
            <div class="modal-header">
                <h5 class="modal-title">
                    <i class="fas fa-building"></i> @lang('trans.new_operator')
                </h5>
                <button type="button" class="close" data-dismiss="modal" aria-label="@lang('trans.close')">
                    <span aria-hidden="true">&times;</span>
                </button>
            </div>
            <div class="modal-body">
                <form id="companyFormApplication">
                    @csrf
                    <div class="mb-3">
                        <label class="form-label">@lang('trans.name_operator') <span class="text-danger">*</span></label>
                        <input type="text" class="form-control" name="nom_entreprise" required>
                    </div>
                    <div class="mb-3">
                        <label class="form-label">@lang('trans.code')</label>
                        <input type="text" class="form-control" name="code">
                    </div>
                    <div class="form-group">
                        <label>@lang('trans.email')</label>
                        <input type="email" class="form-control" name="email">
                    </div>
                    <div class="form-group">
                        <label>@lang('trans.phone')</label>
                        <input type="text" class="form-control" name="telephone">
                    </div>
                    <div class="form-group">
                        <label>@lang('trans.address')</label>
                        <input type="text" class="form-control" name="adresse">
                    </div>
                </form>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-dismiss="modal">@lang('trans.close')</button>
                <button type="button" class="btn btn-primary" id="saveCompanyBtnApplication">@lang('trans.save')</button>
            </div>
        </div>
    </div>
</div>

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
// Libellés DataTables communs (i18n) réutilisés par les deux tableaux
var anacDataTablesLang = {
    "processing": "@lang('trans.dt_processing')",
    "search": "@lang('trans.dt_search')",
    "lengthMenu": "@lang('trans.dt_length_menu')",
    "info": "@lang('trans.dt_info')",
    "infoEmpty": "@lang('trans.dt_info_empty')",
    "infoFiltered": "@lang('trans.dt_info_filtered')",
    "infoPostFix": "",
    "loadingRecords": "@lang('trans.dt_loading_records')",
    "zeroRecords": "@lang('trans.dt_zero_records')",
    "emptyTable": "@lang('trans.dt_empty_table')",
    "paginate": {
        "first": "@lang('trans.dt_first')",
        "previous": "@lang('trans.dt_previous')",
        "next": "@lang('trans.dt_next')",
        "last": "@lang('trans.dt_last')"
    },
    "aria": {
        "sortAscending": "@lang('trans.dt_sort_ascending')",
        "sortDescending": "@lang('trans.dt_sort_descending')"
    }
};

$(document).ready(function() {
    // Initialisation du DataTable
    var table = $('#autorization_applications').DataTable({
        "order": [], // Conserve l'ordre renvoyé par le serveur
        "columnDefs": [
            {
                "targets": 5, // Colonne actions
                "orderable": false,
                "searchable": false
            }
        ],
        "pageLength": 25,
        "autoWidth": false, // La table s'adapte à la largeur du conteneur
        "responsive": true,
        "language": anacDataTablesLang,
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
    $('#paiements').DataTable({
        "paging": true,
        "lengthChange": false,
        "searching": true,
        "ordering": true,
        "info": true,
        "autoWidth": false,
        "responsive": true,
        "order": [[1, "desc"]],
        "language": anacDataTablesLang
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

    // Initialisation du select2 pour l'opérateur représenté
    $('#compagnie_id').select2({
        dropdownParent: $('#applicationModal'),
        placeholder: "@lang('trans.select_operator')",
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

    // Filtrer : uniquement VOL CARGO (id=1), VOL CHARTER (id=2) et VOL COMMERCIAL (id=14)
    typeVolSelect.find('option').each(function() {
        const val = $(this).val();
        if (val === '' || ![1, 2, 14].includes(parseInt(val))) {
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
        objet: button.data('objet'),
        compagnieId: button.data('compagnie-id')
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
    $('#compagnie_id').val(demandeData.compagnieId || '').trigger('change');

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
    // Note: la route user.autorisations.update est un POST (pas de route PUT dédiée) ;
    // la distinction création/modification se fait via edit_mode + demande_id, pas via le verbe HTTP.
    $('#applicationForm').attr('action', "{{ route('user.autorisations.update', ':id') }}".replace(':id', demandeId));
    $('#applicationForm input[name="_method"]').remove();

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

    // Réinitialiser l'opérateur représenté
    $('#compagnie_id').val('').trigger('change');

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
        $('#date_debut_error').text("@lang('trans.start_date_required')");
        isValid = false;
    } else {
        const today = new Date();
        today.setHours(0, 0, 0, 0);
        const debutDate = new Date(dateDebut);

        if (debutDate < today) {
            $('#date_debut').addClass('is-invalid');
            $('#date_debut_error').text("@lang('trans.start_date_past')");
            isValid = false;
        }
    }

    if (!dateFin) {
        $('#date_fin').addClass('is-invalid');
        $('#date_fin_error').text("@lang('trans.end_date_required')");
        isValid = false;
    } else if (dateDebut && dateFin < dateDebut) {
        $('#date_fin').addClass('is-invalid');
        $('#date_fin_error').text("@lang('trans.end_date_after_start')");
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
    submitBtn.html('<i class="fas fa-spinner fa-spin"></i> @lang("trans.sending")');

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
                toastr.error("@lang('trans.server_error_retry')");
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
        errorDiv.text("@lang('trans.sub_validity_range')");
    } else if ($(this).val() && val % 12 !== 0) {
        $(this).addClass('is-invalid');
        errorDiv.text("@lang('trans.sub_validity_multiple')");
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
        errorDiv.text("@lang('trans.object_max_length')".replace(':max', maxLength));
    }
});

// ============================================
// OPÉRATEUR REPRÉSENTÉ (ajout à la volée d'une nouvelle compagnie)
// ============================================
$('#addCompanyBtnApplication').on('click', function() {
    $('#companyModalApplication').modal('show');
});

$('#saveCompanyBtnApplication').on('click', function() {
    const $btn = $(this);
    $btn.prop('disabled', true);

    $.ajax({
        url: "{{ route('user.store_compagnies') }}",
        type: 'POST',
        data: $('#companyFormApplication').serialize(),
        success: function(response) {
            const nouvelleCompagnie = response.data;
            const texteOption = nouvelleCompagnie.code ?
                nouvelleCompagnie.code + ' ' + nouvelleCompagnie.nom_entreprise :
                nouvelleCompagnie.nom_entreprise;

            $('#compagnie_id').append($('<option>', {
                value: nouvelleCompagnie.id,
                text: texteOption,
                selected: true
            })).trigger('change');

            $('#companyModalApplication').modal('hide');
            $('#companyFormApplication')[0].reset();

            toastr.success("@lang('trans.info_saved_success')");
        },
        error: function(xhr) {
            const errors = xhr.responseJSON?.errors;
            let errorMsg = xhr.responseJSON?.message || "@lang('trans.error_occurred')";
            if (errors) {
                errorMsg = Object.values(errors).map(v => v.join(' ')).join('<br>');
            }
            toastr.error(errorMsg);
        },
        complete: function() {
            $btn.prop('disabled', false);
        }
    });
});
</script>
@endpush

</x-app-user-layout>
