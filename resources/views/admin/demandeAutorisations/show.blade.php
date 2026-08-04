@extends('layouts.admin')

@section('title')
    @lang('trans.dashboard_admin') - {{ $demandeAutorisation->code }}
@endsection

@section('contentheader')
    @lang('trans.view_application')
@endsection

@section('contentheaderlink')
    <a href="{{ route('demandeAutorisations') }}">
        @lang('trans.dashboard_admin')
    </a>
@endsection

@section('contentheaderactive')
    @lang('trans.view_application')
@endsection

@push('css')
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/summernote/0.8.18/summernote-bs4.min.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/toastr.js/latest/toastr.min.css">
    <style>
        .validation-badge {
            font-size: 0.8rem;
            padding: 3px 8px;
            border-radius: 10px;
        }

        .validated {
            background-color: #28a745;
            color: white;
        }

        .not-validated {
            background-color: #dc3545;
            color: white;
        }

        .pending {
            background-color: #ffc107;
            color: black;
        }

        .validation-comments {
            font-size: 0.9rem;
            color: #6c757d;
            margin-top: 5px;
        }

        .workflow-timeline {
            position: relative;
            padding: 20px 0;
            margin-bottom: 30px;
        }

        .workflow-step {
            display: inline-block;
            width: 14%;
            text-align: center;
            position: relative;
            z-index: 1;
        }

        .workflow-step .step-icon {
            width: 40px;
            height: 40px;
            border-radius: 50%;
            background: #e9ecef;
            display: flex;
            align-items: center;
            justify-content: center;
            margin: 0 auto 10px;
            color: #6c757d;
        }

        .workflow-step.completed .step-icon {
            background: #28a745;
            color: white;
        }

        .workflow-step.active .step-icon {
            background: #007bff;
            color: white;
            animation: pulse 2s infinite;
        }

        .workflow-step .step-label {
            font-size: 0.75rem;
            color: #6c757d;
        }

        .workflow-step.completed .step-label {
            color: #28a745;
        }

        .workflow-step.active .step-label {
            color: #007bff;
            font-weight: bold;
        }

        .workflow-connector {
            position: absolute;
            top: 38px;
            left: 7%;
            right: 7%;
            height: 2px;
            background: #e9ecef;
            z-index: 0;
        }

        .workflow-connector.completed {
            background: #28a745;
        }

        @keyframes pulse {
            0% { transform: scale(1); }
            50% { transform: scale(1.1); }
            100% { transform: scale(1); }
        }

        .info-panel {
            background: #f8f9fa;
            border-radius: 8px;
            padding: 15px;
            margin-bottom: 20px;
            border-left: 4px solid #007bff;
        }

        .annotation-badge {
            background: #17a2b8;
            color: white;
            padding: 2px 8px;
            border-radius: 12px;
            font-size: 0.75rem;
            margin-left: 5px;
        }
    </style>
@endpush

@section('content')
    <div class="container-fluid">
        <!-- En-tête de la demande -->
        <div class="info-panel">
            <div class="row">
                <div class="col-md-8">
                    <h4>
                        {{ $demandeAutorisation->type->libelle }} - 
                        {{ $demandeAutorisation->typeVol->nom ?? 'N/A' }} - 
                        {{ \Carbon\Carbon::parse($demandeAutorisation->date_debut)->format('d/m/Y') }} -
                        {{ \Carbon\Carbon::parse($demandeAutorisation->date_fin)->format('d/m/Y') }}
                    </h4>
                    <p>
                        <strong>@lang('trans.applicant'):</strong> 
                        {{ $demandeAutorisation->user->demandeur->np ?? 'N/A' }}
                        @if (!empty($demandeAutorisation->objet))
                            <br><strong>@lang('trans.object'):</strong> {{ strtoupper($demandeAutorisation->objet) }}
                        @endif
                        @if (!empty($demandeAutorisation->sous_validite))
                            <br><strong>@lang('trans.validity'):</strong> +{{ $demandeAutorisation->sous_validite }} H
                        @endif
                    </p>
                </div>
                <div class="col-md-4 text-right">
                    @php
                        $etat = $demandeAutorisation->etat_workflow;
                        $badgeClass = match($etat) {
                            'submitted' => 'badge-info',
                            'under_review' => 'badge-warning',
                            'service_approved' => 'badge-success',
                            'paid' => 'badge-primary',
                            'payment_confirmed' => 'badge-success',
                           
                            'rejected' => 'badge-danger',
                            default => 'badge-secondary'
                        };
                    @endphp
                    <span class="badge {{ $badgeClass }} p-2" style="font-size: 1rem;">
                        {{ __("trans.{$etat}") }}
                    </span>
                    
                    @if($demandeAutorisation->directions_annotees)
                        <div class="mt-2">
                            @foreach(json_decode($demandeAutorisation->directions_annotees) as $direction)
                                <span class="annotation-badge">{{ strtoupper($direction) }}</span>
                            @endforeach
                        </div>
                    @endif
                </div>
            </div>
        </div>

        <!-- Workflow Timeline -->
        @include('admin.partials.workflow-timeline', ['demande' => $demandeAutorisation])

        @php
            $hasRejectedItem = $demandeAutorisation->hasInvalidComponents();
        @endphp

        <!-- BOUTON TOUT VALIDER -->
        @if($hasRejectedItem)
            <div class="row mb-4">
                <div class="col-md-12 text-center">
                    <div class="alert alert-warning d-inline-block">
                        <i class="fas fa-exclamation-triangle"></i> @lang('trans.validation_blocked_by_rejection')
                    </div>
                </div>
            </div>
        @elseif(!$demandeAutorisation->isFullyValidated())
            <div class="row mb-4">
                <div class="col-md-12 text-center">
                    <button type="button" class="btn btn-success btn-lg" onclick="validateAllItems()">
                        <i class="fas fa-check-double"></i> @lang('trans.validate_all')
                    </button>
                </div>
            </div>
        @endif

        <!-- Modal pour Tout Valider -->
        <div class="modal fade" id="validateAllModal" tabindex="-1" role="dialog">
            <div class="modal-dialog" role="document">
                <div class="modal-content">
                    <div class="modal-header bg-success text-white">
                        <h5 class="modal-title">
                            <i class="fas fa-check-double"></i> @lang('trans.validate_all')
                        </h5>
                        <button type="button" class="close text-white" data-dismiss="modal">
                            <span>&times;</span>
                        </button>
                    </div>
                    <form id="validateAllForm" method="POST" action="{{ route('validate.all.items') }}">
                        @csrf
                        <div class="modal-body">
                            <input type="hidden" name="demande_id" value="{{ $demandeAutorisation->id }}">
                            
                            <div class="alert alert-info">
                                <i class="fas fa-info-circle"></i> 
                                @lang('trans.validate_all_confirmation')
                            </div>
                            

                            
                            <!-- Liste des éléments à valider -->
                            <div class="mt-3">
                                <h6>@lang('trans.items_to_validate'):</h6>
                                <ul class="list-group">
                                    @if(isset($avions) && $avions->isNotEmpty())
                                        <li class="list-group-item d-flex justify-content-between align-items-center">
                                            @lang('trans.aircraft')
                                            <span class="badge badge-primary badge-pill">{{ $avions->count() }}</span>
                                        </li>
                                    @endif
                                    
                                    @if(isset($vols) && $vols->isNotEmpty())
                                        <li class="list-group-item d-flex justify-content-between align-items-center">
                                            @lang('trans.flights')
                                            <span class="badge badge-primary badge-pill">{{ $vols->count() }}</span>
                                        </li>
                                    @endif
                                    
                                    @if($equipe_vols->isNotEmpty())
                                        <li class="list-group-item d-flex justify-content-between align-items-center">
                                            @lang('trans.crew')
                                            <span class="badge badge-primary badge-pill">{{ $equipe_vols->count() }}</span>
                                        </li>
                                    @endif
                                    
                                    @if($fretVols->isNotEmpty())
                                        <li class="list-group-item d-flex justify-content-between align-items-center">
                                            @lang('trans.freight')
                                            <span class="badge badge-primary badge-pill">{{ $fretVols->count() }}</span>
                                        </li>
                                    @endif
                                    
                                    @if($receivingParties->isNotEmpty())
                                        <li class="list-group-item d-flex justify-content-between align-items-center">
                                            @lang('trans.receiving_parties')
                                            <span class="badge badge-primary badge-pill">{{ $receivingParties->count() }}</span>
                                        </li>
                                    @endif
                                    
                                    @if($demandeAutorisation->hasDocuments())
                                        <li class="list-group-item d-flex justify-content-between align-items-center">
                                            @lang('trans.documents')
                                            <span class="badge badge-primary badge-pill">{{ $demandeAutorisation->documents->count() }}</span>
                                        </li>
                                    @endif
                                </ul>
                            </div>
                        </div>
                        <div class="modal-footer">
                            <button type="button" class="btn btn-secondary" data-dismiss="modal">
                                @lang('trans.cancel')
                            </button>
                            <button type="submit" class="btn btn-success">
                                <i class="fas fa-check-double"></i> @lang('trans.validate_all')
                            </button>
                        </div>
                    </form>
                </div>
            </div>
        </div>

        <!-- Sections de validation -->
        @include('admin.partials.validation-section', [
            'title' => trans('trans.aircraft_information'),
            'items' => $avions ?? collect(),
            'type' => 'avions',
            'demandeId' => $demandeAutorisation->id,
            'hasRejection' => $hasRejectedItem,
            'columns' => [
                'immatriculation' => trans('trans.registration'),
                'type' => trans('trans.type'),
                'operateur' => trans('trans.operator')
            ]
        ])

        @include('admin.partials.validation-section', [
            'title' => trans('trans.flight_information'),
            'items' => $vols ?? collect(),
            'type' => 'vols',
            'demandeId' => $demandeAutorisation->id,
            'hasRejection' => $hasRejectedItem,
            'columns' => [
                'numero_vol' => trans('trans.flight_number'),
                'depart' => trans('trans.departure'),
                'arrivee' => trans('trans.arrival'),
                'itineraire' => trans('trans.route')
            ]
        ])

        @include('admin.partials.validation-section', [
            'title' => trans('trans.flight_crew'),
            'items' => $equipe_vols ?? collect(),
            'type' => 'equipe_vols',
            'demandeId' => $demandeAutorisation->id,
            'hasRejection' => $hasRejectedItem,
            'columns' => [
                'fonction' => trans('trans.role'),
                'licence' => trans('trans.license'),
                'justificatif' => trans('trans.proof')
            ]
        ])

        @include('admin.partials.validation-section', [
            'title' => trans('trans.freight'),
            'items' => $fretVols ?? collect(),
            'type' => 'fret_vols',
            'demandeId' => $demandeAutorisation->id,
            'hasRejection' => $hasRejectedItem,
            'columns' => [
                'nature' => trans('trans.nature'),
                'poids' => trans('trans.weight_kg'),
                'description' => trans('trans.description')
            ]
        ])
        
        @include('admin.partials.validation-section', [
            'title' => trans('trans.deceased_persons'),
            'items' => $personnesDeces ?? collect(),
            'type' => 'personne_deces',
            'demandeId' => $demandeAutorisation->id,
            'hasRejection' => $hasRejectedItem,
            'columns' => [
                'nom_prenom' => trans('trans.full_name'),
                'numero_passport' => trans('trans.passport_number'),
                'justificatif' => trans('trans.proof')
            ]
        ])

        @include('admin.partials.validation-section', [
            'title' => trans('trans.receiving_parties'),
            'items' => $receivingParties ?? collect(),
            'type' => 'receiving_parties',
            'demandeId' => $demandeAutorisation->id,
            'hasRejection' => $hasRejectedItem,
            'columns' => [
                'nom_contact' => trans('trans.contact'),
                'telephone' => trans('trans.phone'),
                'email' => trans('trans.email'),
                'fonction' => trans('trans.function'),
                'piece_identite' => trans('trans.id_card')
            ]
        ])

        @include('admin.partials.validation-section', [
            'title' => trans('trans.documents'),
            'items' => $demandeAutorisation->documents ?? collect(),
            'type' => 'document_autorisations',
            'demandeId' => $demandeAutorisation->id,
            'hasRejection' => $hasRejectedItem,
            'columns' => [
                'type' => trans('trans.type'),
                'document' => trans('trans.document')
            ]
        ])
    </div>

    <!-- Modal de décision unique -->
    <div class="modal fade" id="decisionModal" tabindex="-1" role="dialog">
        <div class="modal-dialog">
            <div class="modal-content">
                <div class="modal-header" id="decisionModalHeader">
                    <h5 class="modal-title" id="decisionModalLabel"></h5>
                    <button type="button" class="close" data-dismiss="modal">
                        <span>&times;</span>
                    </button>
                </div>
                <div class="modal-body">
                    <form id="decisionForm" method="POST" action="{{ route('vr.handle_approval') }}">
                        @csrf
                        <input type="hidden" name="action_type" id="modalActionType">
                        <input type="hidden" name="table" id="modalTable">
                        <input type="hidden" name="id" id="modalId">
                        <input type="hidden" name="demande_id" id="modalDemandeId">

                        <div class="form-group" id="reasonFieldGroup">
                            <label for="modalMotif">@lang('trans.rejection_reason')</label>
                            <textarea name="motif" id="modalMotif" class="form-control" rows="3" required></textarea>
                        </div>
                        
                        <div class="alert alert-info" id="successAlert" style="display: none;">
                            <i class="fas fa-info-circle"></i> @lang('trans.approval_success')
                        </div>
                    </form>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-dismiss="modal">
                        @lang('trans.close')
                    </button>
                    <button type="button" class="btn" id="modalSubmitBtn"></button>
                </div>
            </div>
        </div>
    </div>
@endsection

@push('script')
    <script src="https://cdnjs.cloudflare.com/ajax/libs/summernote/0.8.18/summernote-bs4.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/toastr.js/latest/toastr.min.js"></script>
    <script>
        // Configuration de Toastr
        toastr.options = {
            closeButton: true,
            progressBar: true,
            positionClass: "toast-top-right",
            timeOut: 3000
        };

        // Configuration commune du modal (titre, couleurs, bouton) selon l'action
        function configureDecisionModal(action) {
            document.getElementById('modalActionType').value = action;
            document.getElementById('modalMotif').value = '';

            const modalHeader = document.getElementById('decisionModalHeader');
            const modalLabel = document.getElementById('decisionModalLabel');
            const submitBtn = document.getElementById('modalSubmitBtn');
            const reasonField = document.getElementById('reasonFieldGroup');

            if (action === 'approve') {
                modalHeader.className = 'modal-header bg-success text-white';
                modalLabel.textContent = '@lang('trans.confirm_approval')';
                submitBtn.textContent = '@lang('trans.approve')';
                submitBtn.className = 'btn btn-success';
                reasonField.style.display = 'none';
            } else {
                modalHeader.className = 'modal-header bg-danger text-white';
                modalLabel.textContent = '@lang('trans.confirm_rejection')';
                submitBtn.textContent = '@lang('trans.reject')';
                submitBtn.className = 'btn btn-danger';
                reasonField.style.display = 'block';
            }

            submitBtn.onclick = function() {
                submitDecisionForm(action);
            };
        }

        // Fonction pour ouvrir le modal de décision (un seul élément)
        window.openDecisionModal = function(table, id, demande, action) {
            document.getElementById('modalTable').value = table;
            document.getElementById('modalId').value = id;
            document.getElementById('modalDemandeId').value = demande;
            window.currentBulkIds = null;

            configureDecisionModal(action);
            $('#decisionModal').modal('show');
        };

        // Fonction pour ouvrir le modal de décision (plusieurs éléments sélectionnés)
        window.openBulkDecisionModal = function(table, ids, demande, action) {
            if (!ids || ids.length === 0) {
                toastr.warning('@lang('trans.select_items_first')');
                return;
            }

            document.getElementById('modalTable').value = table;
            document.getElementById('modalId').value = '';
            document.getElementById('modalDemandeId').value = demande;
            window.currentBulkIds = ids;

            configureDecisionModal(action);
            $('#decisionModal').modal('show');
        };

        // Fonction pour soumettre la décision
        function submitDecisionForm(action) {
            const form = document.getElementById('decisionForm');
            const motif = document.getElementById('modalMotif').value;

            if (action === 'reject' && !motif.trim()) {
                toastr.error('@lang('trans.rejection_reason_required')');
                return;
            }

            Swal.fire({
                title: action === 'approve' ? '@lang('trans.confirm_approval')' : '@lang('trans.confirm_rejection')',
                text: action === 'approve' ? '@lang('trans.confirm_approval_question')' : '@lang('trans.confirm_rejection_question')',
                icon: action === 'approve' ? 'question' : 'warning',
                showCancelButton: true,
                confirmButtonColor: action === 'approve' ? '#28a745' : '#dc3545',
                cancelButtonColor: '#6c757d',
                confirmButtonText: action === 'approve' ? '@lang('trans.approve')' : '@lang('trans.reject')',
                cancelButtonText: '@lang('trans.cancel')'
            }).then((result) => {
                if (result.isConfirmed) {
                    // Désactiver le bouton
                    document.getElementById('modalSubmitBtn').disabled = true;

                    const formData = new FormData(form);
                    if (window.currentBulkIds && window.currentBulkIds.length) {
                        formData.delete('id');
                        window.currentBulkIds.forEach(id => formData.append('ids[]', id));
                    }

                    fetch(form.action, {
                        method: 'POST',
                        body: formData,
                        headers: {
                            'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content
                        }
                    })
                    .then(response => response.json())
                    .then(data => {
                        if (data.success) {
                            $('#decisionModal').modal('hide');
                            Swal.fire({
                                icon: 'success',
                                title: '@lang('trans.success')',
                                text: data.message,
                                confirmButtonText: '@lang('trans.ok')'
                            }).then(() => {
                                window.location.reload();
                            });
                        } else {
                            toastr.error(data.message || '@lang('trans.error_occurred')');
                            document.getElementById('modalSubmitBtn').disabled = false;
                        }
                    })
                    .catch(error => {
                        console.error('Error:', error);
                        toastr.error('@lang('trans.request_failed')');
                        document.getElementById('modalSubmitBtn').disabled = false;
                    });
                }
            });
        }

        // Sélection multiple des lignes par section (avions, vols, équipage, ...)
        function getCheckedIds(type) {
            return $('.item-checkbox[data-type="' + type + '"]:checked')
                .map(function() { return this.value; })
                .get();
        }

        function refreshBulkButtons(type) {
            const hasSelection = getCheckedIds(type).length > 0;
            $('.bulk-approve-btn[data-type="' + type + '"], .bulk-reject-btn[data-type="' + type + '"]')
                .prop('disabled', !hasSelection);
        }

        $(document).on('change', '.select-all-checkbox', function() {
            const type = $(this).data('type');
            const checked = $(this).prop('checked');
            $('.item-checkbox[data-type="' + type + '"]').prop('checked', checked);
            refreshBulkButtons(type);
        });

        $(document).on('change', '.item-checkbox', function() {
            const type = $(this).data('type');
            const allChecked = $('.item-checkbox[data-type="' + type + '"]').length ===
                $('.item-checkbox[data-type="' + type + '"]:checked').length;
            $('.select-all-checkbox[data-type="' + type + '"]').prop('checked', allChecked);
            refreshBulkButtons(type);
        });

        $(document).on('click', '.bulk-approve-btn', function() {
            const type = $(this).data('type');
            const demandeId = $(this).data('demande-id');
            window.openBulkDecisionModal(type, getCheckedIds(type), demandeId, 'approve');
        });

        $(document).on('click', '.bulk-reject-btn', function() {
            const type = $(this).data('type');
            const demandeId = $(this).data('demande-id');
            window.openBulkDecisionModal(type, getCheckedIds(type), demandeId, 'reject');
        });

        // Fonction pour valider tous les items
        window.validateAllItems = function() {
            $('#validateAllModal').modal('show');
        };

        // Gestion de la soumission du formulaire Tout Valider
        $('#validateAllForm').on('submit', function(e) {
            e.preventDefault();

            $.ajax({
                url: $(this).attr('action'),
                method: 'POST',
                data: $(this).serialize(),
                beforeSend: function() {
                    $('#validateAllModal').modal('hide');
                    Swal.fire({
                        title: '@lang('trans.validation_in_progress')',
                        html: '@lang('trans.please_wait')',
                        allowOutsideClick: false,
                        didOpen: () => {
                            Swal.showLoading();
                        }
                    });
                },
                success: function(response) {
                    if (response.success) {
                        Swal.fire({
                            icon: 'success',
                            title: '@lang('trans.success')',
                            text: response.message,
                            confirmButtonText: '@lang('trans.ok')'
                        }).then(() => {
                            window.location.reload();
                        });
                    } else {
                        Swal.fire({
                            icon: 'error',
                            title: '@lang('trans.error')',
                            text: response.message || '@lang('trans.error_occurred')',
                            confirmButtonText: '@lang('trans.ok')'
                        });
                    }
                },
                error: function() {
                    Swal.fire({
                        icon: 'error',
                        title: '@lang('trans.error')',
                        text: '@lang('trans.error_occurred')',
                        confirmButtonText: '@lang('trans.ok')'
                    });
                }
            });
        });

        

        // Rafraîchir la page après 5 minutes d'inactivité
        let inactivityTimer;
        function resetTimer() {
            clearTimeout(inactivityTimer);
            inactivityTimer = setTimeout(() => {
                window.location.reload();
            }, 300000); // 5 minutes
        }

        $(document).on('mousemove keydown click', resetTimer);
        resetTimer();
    </script>
@endpush
