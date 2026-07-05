@extends('layouts.admin')
@section('title')
    @lang('trans.dashboard_admin')
@endsection
@section('contentheader')
    @lang('trans.dashboard_admin')
@endsection
@section('contentheaderlink')
    <a href="">
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

    <meta name="csrf-token" content="{{ csrf_token() }}">
    <style>
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
    </style>
@endpush

@section('content')
    <div class="container-fluid">
        <div class="row">
            <div class="col-md-12">
                <div class="card">
                    <div class="card-header">
                        <h3 class="card-title">@lang('trans.applications')</h3>
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
                            <table class="table table-bordered table-striped" id="demandes">
                                <thead>
                                    <tr>
                                        <th>@lang('trans.id')</th>
                                        <th>@lang('trans.date')</th>
                                        <th>@lang('trans.applicant')</th>
                                        <th>@lang('trans.type_application')</th>
                                        <th>@lang('trans.type_license')</th>
                                        <th>@lang('trans.status')</th>
                                        <th>@lang('trans.paiements')</th>
                                        <th>@lang('trans.signed')</th>
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
                                        <td>{{ $demande->date }}</td>
                                        <td>{{ $demande->demandeur->np }}</td>
                                        <td class="type-application-cell" data-demand-id="{{ $demande->id }}">
                                            <span class="type-application-text">
                                                {{ LaravelLocalization::getCurrentLocale() == 'fr' ? optional($demande->typeDemande)->nom_fr : optional($demande->typeDemande)->nom_en }}
                                            </span>
                                            @if(auth()->user()->hasRole('admin') || auth()->user()->isAdmin())
                                                <button type="button" 
                                                        class="btn btn-sm btn-warning edit-type-application" 
                                                        data-demand-id="{{ $demande->id }}"
                                                        data-current-type="{{ optional($demande->typeDemande)->id }}"
                                                        data-toggle="modal" 
                                                        data-target="#editTypeModal">
                                                    <i class="fas fa-edit"></i>
                                                </button>
                                            @endif
                                        </td>
                                        <td>{{ LaravelLocalization::getCurrentLocale() == 'fr' ? optional($demande->typeLicence)->fr : optional($demande->typeLicence)->en }}</td>
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
                                        <td>{{ optional($demande->ordre)->statut }}</td>
                                        <td>
                                            @if (empty(optional($demande->demandeur)->signature))
                                                <span class="badge badge-danger">@lang('trans.no')</span>
                                            @else
                                                <span class="badge badge-success">@lang('trans.yes')</span>
                                            @endif
                                         </td>
                                        <td>
                                            @if (optional($demande->etatDemande)->demandeur_cree_demande && empty($demande->licence) && !isset($demande->licence))
                                            <form action="{{ route('admin.destroy', $demande->id) }}"
                                                  method="POST"
                                                  class="d-inline"
                                                  onsubmit="return confirm('@lang('trans.confirm_delete')')">
                                                @csrf
                                                @method('DELETE')
                                                <button type="submit" class="btn btn-danger btn-sm">
                                                    <i class="fas fa-trash"></i> @lang('trans.delete')
                                                </button>
                                            </form>
                                            @endif
                                            @if (optional($demande->etatDemande)->demandeur_cree_demande)
                                                <a href="{{ route('demandes.show', $demande->id) }}"
                                                    class="btn btn-info btn-sm">@lang('trans.view')</a>
                                            @endif

                                            {{-- Annotate Button Logic --}}
                                            @php
                                                $isSMA = in_array($demande->typeDemande->id, [1, 3, 10 , 5, 7, 9]);
                                                $isSLA = !in_array($demande->typeDemande->id, [5]);
                                                $canAnnotate =
                                                    optional($demande->etatDemande)->demandeur_cree_demande &&
                                                    optional($demande->etatDemande)->dsv_annoter &&
                                                    !optional($demande->etatDemande)->pel_annoter;
                                            @endphp

                                            @if (($isSMA && $canAnnotate) || ($isSLA && $canAnnotate))
                                                {{-- PEL Annote --}}
                                                <form action="{{ route('update-state-licence', $demande->id) }}" method="POST" class="d-inline">
                                                    @csrf
                                                    <input type="hidden" name="action" value="pel_annoter">
                                                    <input type="hidden" name="is_approved" value="1">
                                                    <button type="submit" class="btn {{ $isSMA && $isSLA ? 'btn-info' : ($isSMA ? 'btn-success' : 'btn-primary') }} btn-sm"
                                                        onclick="return confirm('Confirmer l\'annotation par le PEL ?')">
                                                        <i class="fas fa-edit"></i> @lang('trans.annotate')
                                                    </button>
                                                </form>

                                            @endif

                                            {{-- Validate Button Logic --}}
                                            @php
                                                $shouldValidate = false;
                                                if (
                                                    $isSMA &&
                                                    optional($demande->etatDemande)->demandeur_cree_demande &&
                                                    optional($demande->etatDemande)->sm_valider &&
                                                    !optional($demande->etatDemande)->pel_valider
                                                ) {
                                                    $shouldValidate = true;
                                                }
                                                if (
                                                    $isSLA &&
                                                    optional($demande->etatDemande)->demandeur_cree_demande &&
                                                    optional($demande->etatDemande)->sl_valider &&
                                                    !optional($demande->etatDemande)->pel_valider
                                                ) {
                                                    $shouldValidate = true;
                                                }
                                            @endphp

                                            @if ($shouldValidate)
                                                <form action="{{ route('update-state-licence', $demande->id) }}" method="POST" class="d-inline">
                                                    @csrf
                                                    <input type="hidden" name="action" value="pel_valider">
                                                    <input type="hidden" name="is_approved" value="1">
                                                    <button type="submit" class="btn btn-success btn-sm mb-1"
                                                        onclick="return confirm('Confirmer la validation par le PEL ?')">
                                                        <i class="fas fa-check-circle"></i> @lang('trans.validate')
                                                    </button>
                                                </form>
                                            @endif
                                            
                                            @if (optional($demande->etatDemande)->pel_annoter && !optional($demande->etatDemande)->sm_valider)
                                                    {{-- Section Médecine Aéronautique Valider --}}
                                                    <form action="{{ route('update-state-licence', $demande->id) }}" method="POST" class="d-inline">
                                                        @csrf
                                                        <input type="hidden" name="action" value="sm_valider">
                                                        <input type="hidden" name="is_approved" value="1">
                                                        <button type="submit" class="btn btn-success btn-sm mb-1"
                                                            onclick="return confirm('Confirmer la validation par la section de médecine aéronautique ?')">
                                                            <i class="fas fa-check-circle"></i> @lang('trans.validate_sma')
                                                        </button>
                                                    </form>
                                            @endif
                                            
                                            @if (optional($demande->etatDemande)->pel_annoter &&  !optional($demande->etatDemande)->sl_valider)
                                                {{-- Service Licences Aéronautiques Valider --}}
                                                <form action="{{ route('update-state-licence', $demande->id) }}" method="POST" class="d-inline">
                                                    @csrf
                                                    <input type="hidden" name="action" value="sl_valider">
                                                    <input type="hidden" name="is_approved" value="1">
                                                    <button type="submit" class="btn btn-success btn-sm mb-1"
                                                        onclick="return confirm('Confirmer la validation par le service des licences aéronautiques ?')">
                                                        <i class="fas fa-check-circle"></i> @lang('trans.validate_sla')
                                                    </button>
                                                </form>
                                            @endif
                                            
                                            {{-- Generate/Update License Logic --}}
                                            @if ( optional($demande->ordre)->statut === 'Validé' && optional($demande->etatDemande)->dg_valider &&
                                                    in_array($demande->typeDemande->id, [1, 3, 10]) &&
                                                    !empty(optional($demande->demandeur)->signature))
                                                <form action="{{ route('admin.generer', $demande->id) }}"
                                                    method="POST" class="d-inline">
                                                    @csrf
                                                    @method('PATCH')
                                                    <button type="submit" class="btn btn-success btn-sm"
                                                        onclick="return confirm('@lang('trans.confirm_generate_license')')">
                                                        @lang('trans.generate_license')
                                                    </button>
                                                </form>
                                            @elseif(optional($demande->ordre)->statut === 'Validé' &&  (optional($demande->etatDemande)->dsv_valider || optional($demande->etatDemande)->pel_valider))
                                                @if (in_array($demande->typeDemande->id, [2, 4, 6]))
                                                    <form action="{{ route('admin.generer', $demande->id) }}"
                                                        method="POST" class="d-inline">
                                                        @csrf
                                                        @method('PATCH')
                                                        <button type="submit" class="btn btn-primary btn-sm"
                                                            onclick="return confirm('@lang('trans.confirm_update_license')')">
                                                            @lang('trans.update_license')
                                                        </button>
                                                    </form>
                                                @elseif (in_array($demande->typeDemande->id, [5]))
                                                    <form action="{{ route('admin.generer', $demande->id) }}"
                                                        method="POST" class="d-inline">
                                                        @csrf
                                                        @method('PATCH')
                                                        <button type="submit" class="btn btn-primary btn-sm"
                                                            onclick="return confirm('@lang('trans.update_auth')')">
                                                            @lang('trans.update_auth')
                                                        </button>
                                                    </form>
                                                @elseif (in_array($demande->typeDemande->id, [8]))
                                                    <form action="{{ route('admin.generer', $demande->id) }}"
                                                        method="POST" class="d-inline">
                                                        @csrf
                                                        @method('PATCH')
                                                        <button type="submit" class="btn btn-success btn-sm"
                                                            onclick="return confirm('@lang('trans.confirm_generate_trainee_card')')">
                                                            @lang('trans.generate_trainee_card')
                                                        </button>
                                                    </form>
                                                @elseif (in_array($demande->typeDemande->id, [7]))
                                                    @if (empty($demande->validation) && !isset($demande->validation))
                                                        <form action="{{ route('admin.generer', $demande->id) }}"
                                                            method="POST" class="d-inline">
                                                            @csrf
                                                            @method('PATCH')
                                                            <button type="submit" class="btn btn-success btn-sm"
                                                                onclick="return confirm('@lang('trans.confirm_validate_license')')">
                                                                @lang('trans.validate_license')
                                                            </button>
                                                        </form>
                                                    @endif
                                                @endif
                                            @endif
                                         </td>
                                    </tr>
                                    @endforeach
                                </tbody>
                            </table>
                                <!-- Modal pour éditer le type de demande -->
<div class="modal fade" id="editTypeModal" tabindex="-1" role="dialog" aria-labelledby="editTypeModalLabel" aria-hidden="true">
    <div class="modal-dialog" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="editTypeModalLabel">
                    <i class="fas fa-edit"></i> @lang('trans.edit_type_demande')
                </h5>
                <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                    <span aria-hidden="true">&times;</span>
                </button>
            </div>
            
            <form id="editTypeForm" method="POST">
                @csrf
                
                <div class="modal-body">
                    <div class="form-group">
                        <label for="type_demande_id">
                            @lang('trans.select_type') <span class="text-danger">*</span>
                        </label>
                        <select name="type_demande_id" 
                                id="type_demande_id" 
                                class="form-control select2" 
                                required
                                style="width: 100%">
                            <option value="">@lang('trans.select_type')</option>
                            @foreach($typesDemandes as $type)
                                <option value="{{ $type->id }}">
                                    {{ LaravelLocalization::getCurrentLocale() == 'fr' ? optional($type)->nom_fr : optional($type)->nom_en }}
                                </option>
                            @endforeach
                        </select>
                        <small class="form-text text-muted">
                            @lang('trans.select_type_help')
                        </small>
                    </div>
                </div>
                
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-dismiss="modal">
                        <i class="fas fa-times"></i> @lang('trans.cancel')
                    </button>
                    <button type="submit" class="btn btn-primary">
                        <i class="fas fa-save"></i> @lang('trans.update')
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>

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
    {{-- Date Range Picker JS --}}
    <script type="text/javascript" src="{{ asset('assets/admin/plugins/moment/moment.min.js') }}"></script>
    <script type="text/javascript" src="{{ asset('assets/admin/plugins/daterangepicker/daterangepicker.js') }}"></script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/toastr.js/latest/toastr.min.js"></script>

@endpush
@push('custom')
    <script>
        function approveAll(id) {
            const lang = document.documentElement.lang || 'en';
            const messages = {
                en: {
                    confirm: 'Are you sure you want to approve all states? This action cannot be undone.',
                    processing: 'Processing...',
                    success: 'All states approved successfully!',
                    error: 'Error: ',
                    ajaxError: 'An error occurred while approving states.',
                    buttonText: 'Approve All States'
                },
                fr: {
                    confirm: 'Êtes-vous sûr de vouloir approuver tous les états? Cette action ne peut pas être annulée.',
                    processing: 'Traitement en cours...',
                    success: 'Tous les états ont été approuvés avec succès!',
                    error: 'Erreur: ',
                    ajaxError: 'Une erreur s\'est produite lors de l\'approbation des états.',
                    buttonText: 'Approuver Tous les États'
                }
            };

            const msg = messages[lang] || messages.en;

            if (confirm(msg.confirm)) {
                const btn = event.target;
                const originalText = btn.innerHTML;

                btn.innerHTML = `<i class="fas fa-spinner fa-spin"></i> ${msg.processing}`;
                btn.disabled = true;

                $.ajax({
                    url: `/admin/approve-all/${id}`,
                    method: 'POST',
                    headers: {
                        'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content'),
                        'Accept': 'application/json'
                    },
                    success: function(response) {
                        if (response.success) {
                            alert(msg.success);
                            location.reload();
                        } else {
                            alert(msg.error + (response.message || ''));
                        }
                    },
                    error: function(xhr) {
                        if (xhr.status === 419) {
                            alert('Session expired. Please refresh the page and try again.');
                        } else {
                            console.error('Error:', xhr.responseText);
                            alert(msg.ajaxError);
                        }
                    },
                    complete: function() {
                        btn.innerHTML = originalText;
                        btn.disabled = false;
                    }
                });
            }
        }

        $(function() {
            // Initialize DataTable
            var table = $('#demandes').DataTable({
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

            // Date Range Picker Configuration
            let startDate = null;
            let endDate = null;
            
            // Initialize the date range picker
            $('#dateRangePicker').daterangepicker({
                autoUpdateInput: false,
                locale: {
                    cancelLabel: 'Clear',
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
            });

            // Handle date range selection
            $('#dateRangePicker').on('apply.daterangepicker', function(ev, picker) {
                startDate = picker.startDate.format('YYYY-MM-DD');
                endDate = picker.endDate.format('YYYY-MM-DD');
                $('#dateRangeLabel').text(picker.startDate.format('YYYY-MM-DD') + ' - ' + picker.endDate.format('YYYY-MM-DD'));
            });

            $('#dateRangePicker').on('cancel.daterangepicker', function(ev, picker) {
                startDate = null;
                endDate = null;
                $('#dateRangeLabel').text('@lang("trans.select_date_range")');
            });

            // Apply filter button click
            $('#applyDateFilter').on('click', function() {
                if (startDate && endDate) {
                    // Apply custom filtering on the date column (index 1)
                    $.fn.dataTable.ext.search.push(
                        function(settings, data, dataIndex) {
                            var dateColumn = data[1]; // Date column index
                            if (!dateColumn) return true;
                            
                            var rowDate = moment(dateColumn, 'YYYY-MM-DD');
                            if (!rowDate.isValid()) return true;
                            
                            var start = moment(startDate, 'YYYY-MM-DD');
                            var end = moment(endDate, 'YYYY-MM-DD');
                            
                            if (rowDate.isBetween(start, end, null, '[]')) {
                                return true;
                            }
                            return false;
                        }
                    );
                    table.draw();
                    // Remove the filter function after draw to avoid stacking filters
                    $.fn.dataTable.ext.search.pop();
                } else {
                    alert("@lang('trans.select_date_range_first')");
                }
            });

            // Clear filter button click
            $('#clearDateFilter').on('click', function() {
                startDate = null;
                endDate = null;
                $('#dateRangeLabel').text('@lang("trans.select_date_range")');
                $('#dateRangePicker').data('daterangepicker').setStartDate(null);
                $('#dateRangePicker').data('daterangepicker').setEndDate(null);
                
                // Clear any active search filters
                $.fn.dataTable.ext.search.pop();
                table.search('').draw();
            });

            // Add simple search inputs for other columns
            table.columns().every(function(index) {
                if (index !== 7 && index !== 1) { // Skip actions and date column (handled separately)
                    var column = this;
                    var $input = $('<input type="text" placeholder="@lang("trans.search")" class="form-control form-control-sm">')
                        .appendTo($(column.header()))
                        .on('keyup change', function() {
                            column.search(this.value).draw();
                        });
                }
            });
            
            // Add a custom search input for date column header
            var dateHeader = $(table.column(1).header());
            dateHeader.html(`@lang('trans.date')<br><small class="text-muted">(@lang('trans.use_filter_above'))</small>`);
        });
    </script>
    <script>
$(document).ready(function() {
    // Store the current demande ID when edit button is clicked
    let currentDemandeId = null;
    // Handle form submission
$('#editTypeForm').on('submit', function(e) {
    e.preventDefault();
    
    const form = $(this);
    const url = form.attr('action');
    const formData = form.serialize();
    
    // Show loading state
    const submitBtn = form.find('button[type="submit"]');
    const originalText = submitBtn.html();
    submitBtn.html('<i class="fas fa-spinner fa-spin"></i> @lang("trans.saving")...').prop('disabled', true);
    
    $.ajax({
        url: url,
        type: 'POST',
        data: formData,
        headers: {
            'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
        },
        success: function(response) {
            if (response.success) {
                // Fermer le modal d'abord
                $('#editTypeModal').modal('hide');
                
                // Afficher le message de succès avec Toastr
                toastr.success(response.message);
                
                // Recharger la page après un court délai pour voir les changements
                setTimeout(function() {
                    window.location.reload();
                }, 1500);
                
            } else {
                toastr.error(response.message);
                submitBtn.html(originalText).prop('disabled', false);
            }
        },
        error: function(xhr) {
            let errorMessage = '@lang("trans.error_occurred")';
            
            if (xhr.status === 422) {
                // Erreurs de validation
                const errors = xhr.responseJSON.errors;
                errorMessage = Object.values(errors).flat().join('<br>');
            } else if (xhr.responseJSON && xhr.responseJSON.message) {
                errorMessage = xhr.responseJSON.message;
            } else if (xhr.status === 405) {
                errorMessage = 'Méthode non autorisée. Vérifiez l\'URL: ' + url;
            }
            
            toastr.error(errorMessage);
            submitBtn.html(originalText).prop('disabled', false);
            
            // Log pour debug
            console.error('Erreur AJAX:', {
                status: xhr.status,
                url: url,
                response: xhr.responseJSON
            });
        }
    });
});

// Nettoyer le modal à sa fermeture
$('#editTypeModal').on('hidden.bs.modal', function() {
    const form = $('#editTypeForm');
    const submitBtn = form.find('button[type="submit"]');
    
    // Réinitialiser le formulaire
    form[0].reset();
    
    // Réactiver le bouton et restaurer le texte
    submitBtn.html('@lang("trans.update")').prop('disabled', false);
    
    // Réinitialiser currentDemandeId
    currentDemandeId = null;
});

// Fonction pour ouvrir le modal d'édition
function openEditTypeModal(demandeId, currentTypeId) {
    // Stocker l'ID de la demande courante
    currentDemandeId = demandeId;
    
    // Mettre à jour l'URL du formulaire
    const form = $('#editTypeForm');
    form.attr('action', `/admin/demandes/${demandeId}/update-type`);
    
    // Sélectionner le type actuel dans le dropdown
    $('#type_demande_id').val(currentTypeId);
    
    // Ouvrir le modal
    $('#editTypeModal').modal('show');
}

// Gestionnaire pour le bouton d'édition
$(document).on('click', '.edit-type-application', function() {
    const demandeId = $(this).data('demand-id');
    const currentType = $(this).data('current-type');
    
    openEditTypeModal(demandeId, currentType);
});
});
</script>


@endpush