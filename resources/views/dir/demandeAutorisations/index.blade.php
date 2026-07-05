@extends('dir.layouts.app')

@section('title')
    @lang('trans.dashboard_dir', ['role' => strtoupper(auth()->user()->getRoleNames()->first() ?? '')])
@endsection

@section('contentheader')
    @lang('trans.dashboard_dir', ['role' => strtoupper(auth()->user()->getRoleNames()->first() ?? '')])
@endsection

@section('contentheaderlink')
    @php
        $userRole = auth()->user()->getRoleNames()->first();
        $routes = [
            'dsv' => route('dsv'),
            'dg' => route('dg'),
            'dta' => route('dta'),
            'dsna' => route('dsna'),
            'dsad' => route('dsad')
        ];
    @endphp
    
    @if(isset($routes[$userRole]))
        <a href="{{ $routes[$userRole] }}">
            @lang('trans.dashboard_dir', ['role' => strtoupper($userRole)])
        </a>
    @endif
@endsection

@section('contentheaderactive')
    @lang('trans.dashboard_dir', ['role' => strtoupper(auth()->user()->getRoleNames()->first() ?? '')])
@endsection

@push('css')
    <link rel="stylesheet" href="{{ asset('assets/admin/plugins/datatables-bs4/css/dataTables.bootstrap4.min.css') }}">
    <link rel="stylesheet" href="{{ asset('assets/admin/plugins/datatables-responsive/css/responsive.bootstrap4.min.css') }}">
    <link rel="stylesheet" href="{{ asset('assets/admin/plugins/select2/css/select2.min.css') }}">
    <link rel="stylesheet" href="{{ asset('assets/admin/plugins/select2-bootstrap4-theme/select2-bootstrap4.min.css') }}">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/summernote/0.8.18/summernote-bs4.min.css">
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
    </style>
    <style>
/* Style pour les badges dans l'aperçu */
.badge-info, .badge-success {
    font-size: 0.85rem;
    padding: 5px 10px;
    border-radius: 3px;
}

/* Style pour les Select2 en mode tags */
.select2-container--bootstrap4 .select2-selection--multiple {
    min-height: 100px !important;
}

.select2-container--bootstrap4 .select2-selection--multiple .select2-selection__choice {
    background-color: #007bff;
    color: white;
    border-color: #006fe6;
}

.select2-container--bootstrap4 .select2-selection--multiple .select2-selection__choice__remove {
    color: white;
    margin-right: 5px;
}

.select2-container--bootstrap4 .select2-selection--multiple .select2-selection__choice__remove:hover {
    color: #ffc107;
    background: transparent;
}
</style>
@endpush

@section('content')
    <div class="container-fluid">
@if($demandeAutorisations->isNotEmpty())
    <div class="row">
        <div class="col-md-12">
            <div class="card">
                <div class="card-header">
                    <h3 class="card-title">
                        <i class="fas fa-file-alt mr-2"></i>@lang('trans.applications')
                    </h3>
                    <div class="card-tools">
                        <!-- Filtre d'état personnalisé -->
                        <div class="input-group input-group-sm" style="width: 200px;">
                            <select id="etatDemandeFilter" class="form-control">
                                <option value="">@lang('trans.all_statuses')</option>
                                <option value="submitted">@lang('trans.submitted')</option>
                                <option value="under_review">@lang('trans.under_review')</option>
                                <option value="service_approved">@lang('trans.service_approved')</option>
                                <option value="paid">@lang('trans.paid')</option>
                                <option value="payment_confirmed">@lang('trans.payment_confirmed')</option>
                                
                                <option value="rejected">@lang('trans.rejected')</option>
                            </select>
                        </div>
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
                                        // Conditions de visibilité pour le bouton "View"
                                        $canView = false;
                                        $userRole = auth()->user()->getRoleNames()->first();
                                        $hasIssues = $demande->has_issues ?? false;
                                        $etatDemande = $demande->etat_workflow;
                                        
                                        if($userRole == 'dta') {
                                            $canView = optional($demande->etatDemande)->dg_annoter || 
                                                       optional($demande->etatDemande)->dta_dg_annoter ||
                                                       optional($demande->etatDemande)->dg_annoter_admin;
                                        } elseif(in_array($userRole, ['dsv', 'dsna', 'dsad', 'dsf'])) {
                                            $canView = optional($demande->etatDemande)->service_annoter && 
                                                       $demande->isAnnotedTo($userRole);
                                        } else {
                                            $canView = true;
                                        }
                                    @endphp
                                    
                                    <tr data-etat="{{ $etatDemande }}">
                                        <td data-order="{{ $demande->created_at ? strtotime($demande->created_at) : 0 }}">
                                            {{ $demande->created_at_formatted ?? 'N/A' }}
                                        </td>
                                        <td data-order="{{ $demande->date_soumission ? strtotime($demande->date_soumission) : 0 }}">
                                            {{ $demande->date_soumission_formatted ?? 'N/A' }}
                                        </td>
                                        <td><span class="badge badge-info">{{ $demande->code }}</span></td>
                                        <td>{{ $demande->type->libelle }}</td>
                                        <td>{{ $demande->typeVol->nom ?? 'N/A' }}</td>
                                        <td>{{ $demande->date_debut }}</td>
                                        <td>{{ $demande->date_fin }}</td>
                                        <td>
                                            @if($demande->user && $demande->user->demandeur)
                                                {{ strtoupper($demande->user->demandeur->np) }}
                                            @endif
                                        </td>
                                        <td>
                                            @foreach($demande->avions as $avion)
                                                <span class="badge badge-secondary">{{ $avion->immatriculation }}</span>
                                            @endforeach
                                        </td>
                                        <td>
                                            @php
                                                $badgeClass = match($etatDemande) {
                                                    'submitted' => 'badge-submitted',
                                                    'under_review' => 'badge-under_review',
                                                    'service_approved' => 'badge-service_approved',
                                                    'paid' => 'badge-paid',
                                                    'payment_confirmed' => 'badge-payment_confirmed',
                                                    
                                                    'rejected' => 'badge-rejected',
                                                    default => 'badge-secondary'
                                                };
                                            @endphp
                                            <span class="badge {{ $badgeClass }}">
                                                {{ $etatDemande }}
                                            </span>
                                        </td>
                                        <td>
                                            <div class="btn-group-vertical" role="group">
                                                @if($canView)
                                                    <a href="{{ route('dg.demandes.show', $demande->id) }}"
                                                       class="btn btn-info btn-sm mb-1">
                                                        <i class="fas fa-eye"></i> @lang('trans.view')
                                                    </a>
                                                @endif
                                                
                                                @if(!empty($demande->paiement) && isset($demande->paiement))
                                                    <a href="{{ route('daf.invoiceAutorisation', $demande->paiement->id) }}"
                                                            class="btn btn-warning btn-sm mb-1" target="_blank">
                                                        <i class="fas fa-file-invoice"></i> @lang('trans.tariffy')
                                                    </a>
                                                @endif
                                                
                                                <!-- Actions spécifiques au rôle -->
                                                @include('dir.demandeAutorisations.partials.role-actions', ['demande' => $demande])
                                                
                                                <!-- Bouton d'annotation pour DTA -->
                                                @if(auth()->user()->hasRole('dta'))
                                                    @if ($hasIssues)
                                                        <button class="btn btn-warning btn-sm mb-1" data-toggle="modal" data-target="#issuesModal-{{ $demande->id }}">
                                                            <i class="fas fa-exclamation-circle"></i> @lang('trans.issues')
                                                        </button>
                                                    @endif
                                                    @include('dir.demandeAutorisations.partials.annotation-button', ['demande' => $demande])
                                                @endif
                                            </div>
                                        </td>
                                    </tr>
                                    
                                    <!-- Modal des Erreurs/Issues -->
                                    @if ($hasIssues)
                                        @include('dir.demandeAutorisations.modals.issues', ['demande' => $demande])
                                    @endif
                                    @include('dir.demandeAutorisations.modals.notifications-modal', ['demande' => $demande])
                                @endforeach
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        </div>
    </div>
@else
    <div class="alert alert-info text-center">
        <i class="fas fa-info-circle fa-2x mb-3"></i>
        <h4>@lang('trans.no_applications_found')</h4>
        <p class="mb-0">@lang('trans.no_applications_available')</p>
    </div>
@endif

    </div>

    <!-- Modals -->
    @include('dir.demandeAutorisations.modals.annotation')
    @include('dir.demandeAutorisations.modals.rejection')
    @include('dir.demandeAutorisations.modals.achievement')
    @include('dir.demandeAutorisations.modals.dg-annotation')
    @include('dir.demandeAutorisations.modals.dta-annotation')
    @include('dir.demandeAutorisations.modals.retrait-directions')
    @include('dir.demandeAutorisations.modals.notifications-modal')
    
@endsection

@push('script')
    <script src="{{ asset('assets/admin/plugins/datatables/jquery.dataTables.min.js') }}"></script>
    <script src="{{ asset('assets/admin/plugins/datatables-bs4/js/dataTables.bootstrap4.min.js') }}"></script>
    <script src="{{ asset('assets/admin/plugins/datatables-responsive/js/dataTables.responsive.min.js') }}"></script>
    <script src="{{ asset('assets/admin/plugins/datatables-responsive/js/responsive.bootstrap4.min.js') }}"></script>
    <script src="{{ asset('assets/admin/plugins/select2/js/select2.full.min.js') }}"></script>
    
    <!-- Toastr -->
    <script src="https://cdnjs.cloudflare.com/ajax/libs/toastr.js/latest/toastr.min.js"></script>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/toastr.js/latest/toastr.min.css">
    <script src="https://cdn.jsdelivr.net/npm/summernote@0.8.20/dist/summernote-bs4.min.js"></script>
@endpush

@push('custom')
<script>
$(document).ready(function() {
    // Initialiser les Select2 pour tous les modals de notification
    $('.email-recipients').each(function() {
        const modalId = $(this).attr('id');
        const demandeId = modalId.split('-').pop();
        
        $(this).select2({
            theme: 'bootstrap4',
            placeholder: "Tapez un email et appuyez sur Entrée",
            tags: true,
            tokenSeparators: [',', ' ', '\n'],
            allowClear: true,
            width: '100%',
            dropdownParent: $(`#sendNotificationModal-${demandeId}`),
            createTag: function(params) {
                var term = $.trim(params.term);
                if (term === '') {
                    return null;
                }
                
                var emailRegex = /^[^\s@]+@([^\s@]+\.)+[^\s@]+$/;
                if (!emailRegex.test(term)) {
                    return null;
                }
                
                return {
                    id: term,
                    text: term,
                    newTag: true
                };
            },
            insertTag: function(data, tag) {
                data.push(tag);
            },
            language: {
                noResults: function() {
                    return "Aucun résultat";
                },
                searching: function() {
                    return "Recherche...";
                }
            }
        }).on('change', function() {
            updateRecipientsPreview(demandeId);
        });
    });
    
    $('.whatsapp-recipients').each(function() {
        const modalId = $(this).attr('id');
        const demandeId = modalId.split('-').pop();
        
        $(this).select2({
            theme: 'bootstrap4',
            placeholder: "Tapez un numéro et appuyez sur Entrée (format: +1234567890)",
            tags: true,
            tokenSeparators: [',', ' ', '\n'],
            allowClear: true,
            width: '100%',
            dropdownParent: $(`#sendNotificationModal-${demandeId}`),
            createTag: function(params) {
                var term = $.trim(params.term);
                if (term === '') {
                    return null;
                }
                
                var phoneRegex = /^\+[0-9]{8,15}$/;
                if (!phoneRegex.test(term)) {
                    return null;
                }
                
                return {
                    id: term,
                    text: term,
                    newTag: true
                };
            },
            insertTag: function(data, tag) {
                data.push(tag);
            },
            language: {
                noResults: function() {
                    return "Aucun résultat";
                },
                searching: function() {
                    return "Recherche...";
                }
            }
        }).on('change', function() {
            updateRecipientsPreview(demandeId);
        });
    });
    
    // Fonction pour mettre à jour l'aperçu des destinataires
    function updateRecipientsPreview(demandeId) {
        const emails = $(`#email_recipients-${demandeId}`).val() || [];
        const whatsapps = $(`#whatsapp_recipients-${demandeId}`).val() || [];
        
        $(`#emailCount-${demandeId}`).text(emails.length);
        $(`#whatsappCount-${demandeId}`).text(whatsapps.length);
        
        let emailPreview = '';
        emails.forEach(function(email) {
            emailPreview += '<span class="badge badge-info mr-1 mb-1">' + email + '</span>';
        });
        $(`#emailPreviewList-${demandeId}`).html(emailPreview || 'Aucun');
        
        let whatsappPreview = '';
        whatsapps.forEach(function(phone) {
            whatsappPreview += '<span class="badge badge-success mr-1 mb-1">' + phone + '</span>';
        });
        $(`#whatsappPreviewList-${demandeId}`).html(whatsappPreview || 'Aucun');
        
        if (emails.length > 0 || whatsapps.length > 0) {
            $(`#recipientsPreview-${demandeId}`).fadeIn(300);
        } else {
            $(`#recipientsPreview-${demandeId}`).fadeOut(300);
        }
    }
    
    // Gestionnaire de soumission pour chaque formulaire
    $('[id^="notificationForm-"]').submit(function(e) {
        e.preventDefault();
        
        const formId = $(this).attr('id');
        const demandeId = formId.split('-').pop();
        
        const emails = $(`#email_recipients-${demandeId}`).val() || [];
        const whatsapps = $(`#whatsapp_recipients-${demandeId}`).val() || [];

        
        if (whatsapps.length === 0) {
            toastr.error('Veuillez sélectionner au moins un destinataire');
            return false;
        }
        
        const submitBtn = $(`#sendNotificationBtn-${demandeId}`);
        submitBtn.prop('disabled', true).html('<i class="fas fa-spinner fa-spin"></i> Envoi en cours...');
        
        const formData = new FormData(this);
        
        $.ajax({
            url: $(this).attr('action'),
            type: 'POST',
            data: formData,
            processData: false,
            contentType: false,
            success: function(response) {
                toastr.success(response.message || 'Notifications envoyées avec succès');
                
                $(`#sendNotificationModal-${demandeId}`).modal('hide');
            },
            error: function(xhr) {
                let errorMsg = 'Une erreur est survenue';
                if (xhr.responseJSON && xhr.responseJSON.message) {
                    errorMsg = xhr.responseJSON.message;
                }
                toastr.error(errorMsg);
            },
            complete: function() {
                submitBtn.prop('disabled', false).html('<i class="fas fa-paper-plane"></i> Envoyer les notifications');
            }
        });
    });
    
    // Réinitialiser les formulaires à la fermeture
    $('[id^="sendNotificationModal-"]').on('hidden.bs.modal', function() {
        const modalId = $(this).attr('id');
        const demandeId = modalId.split('-').pop();
        
        $(`#notificationForm-${demandeId}`)[0].reset();
        $(`#email_recipients-${demandeId}`).val(null).trigger('change');
        $(`#whatsapp_recipients-${demandeId}`).val(null).trigger('change');
        $(`#recipientsPreview-${demandeId}`).hide();
    });
});
</script>
<script>

    $(document).ready(function() {
        // Fonction pour obtenir les paramètres d'URL
        function getUrlParameter(name) {
            name = name.replace(/[\[]/, '\\[').replace(/[\]]/, '\\]');
            var regex = new RegExp('[\\?&]' + name + '=([^&#]*)');
            var results = regex.exec(location.search);
            return results === null ? '' : decodeURIComponent(results[1].replace(/\+/g, ' '));
        }

        // Initialiser DataTable
        var table = $('#applicationsTable').DataTable({
            order: [[0, "desc"]],
            pageLength: 25,
            responsive: true,
            language: {
                url: "//cdn.datatables.net/plug-ins/1.13.6/i18n/fr-FR.json"
            },
            columnDefs: [
                {
                    targets: [0, 1],
                    type: "date-eu"
                },
                {
                    targets: -1,
                    orderable: false,
                    searchable: false,
                    width: "220px"
                }
            ],
            dom: '<"row"<"col-sm-12 col-md-6"l><"col-sm-12 col-md-6"f>>' +
                 '<"row"<"col-sm-12"tr>>' +
                 '<"row"<"col-sm-12 col-md-5"i><"col-sm-12 col-md-7"p>>',
            initComplete: function() {
                initSelect2();
                initFlightTypeFilter(this.api());
                
                // Appliquer le filtre pending si présent dans l'URL
                var statusFilter = getUrlParameter('status_filter');
                if (statusFilter === 'pending') {
                    var api = this.api();
                    
                    // Mettre à jour le select de filtre
                    $('#etatDemandeFilter').val('pending');
                    
                    // Appliquer le filtre sur la colonne des statuts (index 9)
                    api.column(9).search('submitted|under_review', true, false).draw();
                    
                    // Mettre en évidence le filtre
                    highlightPendingFilter();
                    
                    // Afficher une notification
                    toastr.info('Affichage des demandes en attente (soumises et en cours)');
                }
            }
        });

        // Filtre personnalisé pour l'état de la demande
        $('#etatDemandeFilter').on('change', function() {
            var etat = $(this).val();
            
            if (etat === 'pending') {
                // Filtre spécial pour les demandes en attente
                table.column(9).search('submitted|under_review', true, false).draw();
                highlightPendingFilter();
            } else {
                // Filtre normal
                table.column(9).search(etat).draw();
                
                // Enlever la mise en évidence
                removePendingHighlight();
            }
        });

        // Fonction pour mettre en évidence le filtre "en attente"
        function highlightPendingFilter() {
            $('.filter-section').addClass('bg-warning-light');
            $('#etatDemandeFilter').addClass('border-warning');
            
            // Ajouter un badge d'information
            if ($('#pendingFilterBadge').length === 0) {
                $('.card-header .card-tools').append(
                    '<span id="pendingFilterBadge" class="badge badge-warning ml-2">' +
                    '<i class="fas fa-clock"></i> Filtre: Demandes en attente' +
                    '</span>'
                );
            }
        }

        function removePendingHighlight() {
            $('.filter-section').removeClass('bg-warning-light');
            $('#etatDemandeFilter').removeClass('border-warning');
            $('#pendingFilterBadge').remove();
        }

        // Initialiser tous les Select2
        function initSelect2() {
            $('.select2-directions, .select2-dg-options, .select2-dta-options').each(function() {
                if ($(this).length > 0) {
                    $(this).select2({
                        theme: 'bootstrap4',
                        placeholder: $(this).data('placeholder') || "Sélectionnez...",
                        allowClear: true,
                        width: '100%',
                        dropdownParent: $(this).closest('.modal')
                    });
                }
            });
        }

        // Initialiser le filtre par type de vol
        function initFlightTypeFilter(api) {
            var flightTypeFilter = $('<div class="flight-type-filter mb-2"><strong>@lang("trans.flight_type"):</strong> <div class="checkboxes-container"></div></div>');
            $('#applicationsTable_wrapper .dataTables_filter').after(flightTypeFilter);
            
            var column = api.column(4);
            var uniqueTypes = column.data().unique().sort().toArray();
            var checkboxesContainer = flightTypeFilter.find('.checkboxes-container');
            
            checkboxesContainer.append('<label class="checkbox-inline mr-3"><input type="checkbox" class="flight-type-checkbox" value="" checked> @lang("trans.all")</label>');
            
            uniqueTypes.forEach(function(type) {
                if (type && type.trim() !== '') {
                    checkboxesContainer.append(
                        `<label class="checkbox-inline mr-3"><input type="checkbox" class="flight-type-checkbox" value="${type}" checked> ${type}</label>`
                    );
                }
            });
            
            $('.flight-type-checkbox').on('change', function() {
                applyFlightTypeFilter(api);
            });
        }

        function applyFlightTypeFilter(api) {
            const column = api.column(4);
            const isAllChecked = $('.flight-type-checkbox[value=""]').is(':checked');
            const selectedValues = [];

            $('.flight-type-checkbox[value!=""]:checked').each(function () {
                selectedValues.push($(this).val());
            });

            if (isAllChecked) {
                column.search('').draw();
                return;
            }

            if (selectedValues.length === 0) {
                column.search('a^', true, false).draw();
                return;
            }

            const searchRegex = selectedValues
                .map(val => '^' + $.fn.dataTable.util.escapeRegex(val) + '$')
                .join('|');

            column.search(searchRegex, true, false).draw();
        }

        // Gestion des modals d'annotation
        setupAnnotationModals();
        
        // Gestion des soumissions de formulaires
        setupFormSubmissions();
    });

    function setupAnnotationModals() {
        // Modal d'annotation standard (DTA vers directions)
        $('#annotationModal').on('show.bs.modal', function(event) {
            const button = $(event.relatedTarget);
            const demandeId = button.data('demande-id');
            const demandeCode = button.data('demande-code');
            
            $('#annotationDemandeId').val(demandeId);
            if (demandeCode) {
                $('#annotationModalLabel').html(
                    `<i class="fas fa-share-alt mr-2"></i>@lang("trans.annotation") - @lang("trans.application") ${demandeCode}`
                );
            }
            
            initSummernote('#points');
            resetSelect2('#annotationDirections');
        });

        // Modal d'annotation DG
        $('#dgAnnotationModal').on('show.bs.modal', function(event) {
            const button = $(event.relatedTarget);
            const demandeId = button.data('demande-id');
            const demandeCode = button.data('demande-code');
            
            $('#dgAnnotationDemandeId').val(demandeId);
            if (demandeCode) {
                $('#dgAnnotationModalLabel').html(
                    `<i class="fas fa-user-tie mr-2"></i>@lang("trans.dg_annotation") - @lang("trans.application") ${demandeCode}`
                );
            }
            
            resetSelect2('#dgAnnotationOptions');
        });

        // Modal d'annotation DTA
        $('#dtaAnnotationModal').on('show.bs.modal', function(event) {
            const button = $(event.relatedTarget);
            const demandeId = button.data('demande-id');
            const demandeCode = button.data('demande-code');
            
            $('#dtaAnnotationDemandeId').val(demandeId);
            if (demandeCode) {
                $('#dtaAnnotationModalLabel').html(
                    `<i class="fas fa-user-tie mr-2"></i>@lang("trans.dta_annotation") - @lang("trans.application") ${demandeCode}`
                );
            }
            
            initSummernote('#dtaPoints');
        });

        // Nettoyage des modals à la fermeture
        $('.modal').on('hidden.bs.modal', function() {
            $(this).find('textarea.summernote').each(function() {
                if ($(this).data('summernote')) {
                    $(this).summernote('destroy');
                }
            });
            
            $(this).find('select.select2-directions, select.select2-dg-options, select.select2-dta-options')
                .val(null).trigger('change');
        });
    }

    function initSummernote(selector) {
        $(selector).summernote({
            height: 150,
            placeholder: '@lang("trans.enter_points")',
            toolbar: [
                ['style', ['bold', 'italic', 'underline']],
                ['para', ['ul', 'ol', 'paragraph']],
                ['insert', ['link']],
                ['view', ['fullscreen', 'codeview']]
            ],
            callbacks: {
                onInit: function() {
                    $(selector).data('summernote', true);
                }
            }
        });
    }

    function resetSelect2(selector) {
        setTimeout(() => {
            $(selector).select2({
                theme: 'bootstrap4',
                placeholder: $(selector).data('placeholder') || "@lang('trans.select_options')",
                allowClear: true,
                width: '100%',
                dropdownParent: $(selector).closest('.modal')
            });
        }, 100);
    }

    function setupFormSubmissions() {
        // Formulaire d'annotation standard
        $('#annotationForm').submit(function(e) {
            e.preventDefault();
            submitForm(this, '@lang("trans.annotation_success")');
        });

        // Formulaire d'annotation DG
        $('#dgAnnotationForm').submit(function(e) {
            e.preventDefault();
            submitForm(this, '@lang("trans.dg_annotation_success")');
        });

        // Formulaire d'annotation DTA
        $('#dtaAnnotationForm').submit(function(e) {
            e.preventDefault();
            submitForm(this, '@lang("trans.dta_annotation_success")');
        });
    }

    function submitForm(form, successMessage) {
        const $form = $(form);
        
        
        const selectedValues = $form.find('select').val();
        
        if (!selectedValues || selectedValues.length === 0) {
            toastr.error('@lang("trans.select_at_least_one_option")');
            return false;
        }
        
        const $submitBtn = $form.find('button[type="submit"]');
        $submitBtn.prop('disabled', true).html('<i class="fas fa-spinner fa-spin mr-1"></i> @lang("trans.sending")');
        
        const demandeId = $form.find('input[name="id"]').val();
        const url = "{{ route('update-state', ':id') }}".replace(':id', demandeId);
        const formData = new FormData($form[0]);
        
        $.ajax({
            url: url,
            type: 'POST',
            data: formData,
            processData: false,
            contentType: false,
            success: function(response) {
                toastr.success(successMessage);
                $form.closest('.modal').modal('hide');
                setTimeout(() => window.location.reload(), 1000);
            },
            error: function(xhr) {
                handleAjaxError(xhr);
                $submitBtn.prop('disabled', false).html($submitBtn.data('original-text'));
            }
        });
    }

    function handleAjaxError(xhr) {
        let errorMessage = '@lang("trans.error_occurred")';
        
        if (xhr.status === 419 || xhr.status === 401) {
            errorMessage = '@lang("trans.session_expired")';
            if (confirm('@lang("trans.refresh_page")')) {
                window.location.reload();
            }
        } else if (xhr.status === 403) {
            errorMessage = '@lang("trans.access_denied")';
        } else if (xhr.responseJSON && xhr.responseJSON.message) {
            errorMessage = xhr.responseJSON.message;
        }
        
        toastr.error(errorMessage);
    }

    // Fonctions pour les modals de rejet et achievement
    window.openAchievementModal = function(table, id) {
        $('#achievementId').val(id);
        $('#achievementTable').val(table);
        $('#achievementModal').modal('show');
    };

    window.openRejectionModal = function(table, id) {
        $('#rejectionId').val(id);
        $('#rejectionTable').val(table);
        $('#rejectionModal').modal('show');
    };
    window.openRetraitModalWithData = function(demandeId, code, directions) {
        $('#retraitDemandeId').val(demandeId);
        $('#retraitDemandeCode').text(code);
        
        let directionsHtml = '';
        directions.forEach(function(direction) {
            directionsHtml += `
                <div class="form-check mb-2">
                    <input class="form-check-input" 
                           type="checkbox" 
                           name="directions_to_remove[]" 
                           value="${direction}"
                           id="dir-${direction}">
                    <label class="form-check-label" for="dir-${direction}">
                        <span class="badge badge-primary p-2">
                            ${direction.toUpperCase()}
                        </span>
                    </label>
                </div>
            `;
        });
        
        $('#retraitDirectionsList').html(directionsHtml);
        $('#retraitDirectionsModal').modal('show');
    };
    
    
    // Gestion de la soumission du formulaire de retrait
$('#retraitDirectionsForm').submit(function(e) {
    e.preventDefault();
    
    const checkedBoxes = $(this).find('input[name="directions_to_remove[]"]:checked');
    
    if (checkedBoxes.length === 0) {
        toastr.error('@lang("trans.select_at_least_one_direction")');
        return false;
    }
    
    if (!confirm('@lang("trans.confirm_directions_removal")')) {
        return false;
    }
    
    const $form = $(this);
    const $submitBtn = $('#submitRetraitBtn');
    const demandeId = $('#retraitDemandeId').val();
    const url = "{{ route('update-state', ':id') }}".replace(':id', demandeId);
    
    $submitBtn.prop('disabled', true).html('<i class="fas fa-spinner fa-spin mr-1"></i> @lang("trans.sending")');
    
    $.ajax({
        url: url,
        type: 'POST',
        data: $form.serialize(),
        success: function(response) {
            toastr.success('@lang("trans.directions_removed_success")');
            $('#retraitDirectionsModal').modal('hide');
            setTimeout(() => window.location.reload(), 1000);
        },
        error: function(xhr) {
            handleAjaxError(xhr);
            $submitBtn.prop('disabled', false).html('<i class="fas fa-undo"></i> @lang("trans.confirm_removal")');
        }
    });
});

// Nettoyer le modal à la fermeture
$('#retraitDirectionsModal').on('hidden.bs.modal', function() {
    $(this).find('input[name="directions_to_remove[]"]').prop('checked', false);
    $(this).find('#motif_retrait').val('');
});

    window.submitAchievementForm = function() {
        const motif = $('#achievementMotif').val();
        
        if(!motif.trim()) {
            toastr.error('@lang("trans.enter_motif")');
            return;
        }

        if(confirm('@lang("trans.confirm_achievement")')) {
            const formData = $('#achievementForm').serialize();
            const demandeId = $('#achievementId').val();
            const url = "{{ route('dir.achiever', ':id') }}".replace(':id', demandeId);

            $.ajax({
                url: url,
                type: 'POST',
                data: formData,
                success: function(response) {
                    toastr.success('@lang("trans.achievement_success")');
                    $('#achievementModal').modal('hide');
                    setTimeout(() => window.location.reload(), 1000);
                },
                error: function(xhr) {
                    handleAjaxError(xhr);
                }
            });
        }
    };

    window.submitRejectionForm = function() {
        const motif = $('#rejectionMotif').val();
        
        if(!motif.trim()) {
            toastr.error('@lang("trans.enter_rejection_motif")');
            return;
        }

        if(confirm('@lang("trans.confirm_rejection")')) {
            const formData = $('#rejectionForm').serialize();
            const demandeId = $('#rejectionId').val();
            const url = "{{ route('dir.rejeter', ':id') }}".replace(':id', demandeId);

            $.ajax({
                url: url,
                type: 'POST',
                data: formData,
                success: function(response) {
                    toastr.success('@lang("trans.rejection_success")');
                    $('#rejectionModal').modal('hide');
                    setTimeout(() => window.location.reload(), 1000);
                },
                error: function(xhr) {
                    handleAjaxError(xhr);
                }
            });
        }
    };
</script>
<script>
$(document).ready(function() {
    // Validation du formulaire de retrait
    $('#retraitDirectionsModal-{{ $demande->id }} form').on('submit', function(e) {
        const checkedBoxes = $(this).find('input[name="directions_to_remove[]"]:checked');
        
        if (checkedBoxes.length === 0) {
            e.preventDefault();
            toastr.error('@lang("trans.select_at_least_one_direction")');
            return false;
        }
        
        return confirm('@lang("trans.confirm_directions_removal")');
    });
});
</script>
@endpush