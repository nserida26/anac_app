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
    <link rel="stylesheet" href="{{ asset('assets/admin/plugins/select2/css/select2.min.css') }}">
    <link rel="stylesheet" href="{{ asset('assets/admin/plugins/select2-bootstrap4-theme/select2-bootstrap4.min.css') }}">
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
        <div class="row">
            <div class="col-md-12">
                <div class="card">
                    <div class="card-header">@lang('trans.autorisations')</div>
                    <div class="card-body">
                        <div class="table-responsive">
                            <table class="table table-bordered table-striped" id="autorisations">
                                <thead>
                                    <tr>
                                        <th>@lang('trans.code')</th>
                                        <th>@lang('trans.start_date')</th>
                                        <th>@lang('trans.end_date')</th>
                                        <th>@lang('trans.actions')</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @foreach ($autorisations as $autorisation)
                                        <tr>
                                            <td>{{ $autorisation->code_autorisation }}</td>
                                            <td>{{ $autorisation->date_delivrance }}</td>
                                            <td>{{ $autorisation->date_expiration }}</td>
                                            <td>
                                                <a href="{{ route('autorisations.show', $autorisation) }}"
                                                    class="btn btn-info btn-sm">View</a>
                                                <a target="_blank" href="{{ route('autorisations.print', $autorisation) }}"
                                                    class="btn btn-warning btn-sm">@lang('trans.print')</a>
                                                <button type="button" class="btn btn-primary btn-sm" data-toggle="modal" 
                                                        data-target="#sendNotificationModal-{{ $autorisation->id }}"
                                                        data-autorisation-id="{{ $autorisation->id }}"
                                                        data-autorisation-code="{{ $autorisation->code_autorisation }}"
                                                        data-demande-id="{{ $autorisation->demande->id ?? '' }}"
                                                        data-demande-type="{{ $autorisation->demande->type->libelle ?? '' }}"
                                                        data-demande-date-debut="{{ $autorisation->demande->date_debut ?? '' }}"
                                                        data-demande-date-fin="{{ $autorisation->demande->date_fin ?? '' }}">
                                                    <i class="fas fa-bell"></i> @lang('trans.send_notifications')
                                                </button>
                                            </td>
                                        </tr>
                                        
                                        <!-- Modal pour chaque autorisation -->
                                        
                                        @include('admin.autorisations.notification-modal', ['autorisation' => $autorisation])
                                    @endforeach
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
        
    <!-- Toastr -->
    <script src="https://cdnjs.cloudflare.com/ajax/libs/toastr.js/latest/toastr.min.js"></script>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/toastr.js/latest/toastr.min.css">
@endpush

@push('custom')
<script>
$(document).ready(function() {
    // Initialisation de DataTable
    $('#autorisations').DataTable();
    
    // Initialiser les Select2 pour tous les modals
    $('[id^="sendNotificationModal-"]').each(function() {
        const modalId = $(this).attr('id');
        const autorisationId = modalId.split('-').pop();
        
        // Initialisation du Select2 pour les emails
        $(`#email_recipients-${autorisationId}`).select2({
            theme: 'bootstrap4',
            tags: true,
            tokenSeparators: [',', ' ', '\n'],
            allowClear: true,
            width: '100%',
            dropdownParent: $(`#${modalId}`),
            createTag: function(params) {
                var term = $.trim(params.term);
                if (term === '') return null;
                var emailRegex = /^[^\s@]+@([^\s@]+\.)+[^\s@]+$/;
                if (!emailRegex.test(term)) return null;
                return { id: term, text: term, newTag: true };
            }
        }).on('change', function() {
            updateRecipientsPreview(autorisationId);
        });
        
        // Initialisation du Select2 pour les numéros WhatsApp
        $(`#whatsapp_recipients-${autorisationId}`).select2({
            theme: 'bootstrap4',
            tags: true,
            tokenSeparators: [',', ' ', '\n'],
            allowClear: true,
            width: '100%',
            dropdownParent: $(`#${modalId}`),
            createTag: function(params) {
                var term = $.trim(params.term);
                if (term === '') return null;
                var phoneRegex = /^\+[0-9]{8,15}$/;
                if (!phoneRegex.test(term)) return null;
                return { id: term, text: term, newTag: true };
            }
        }).on('change', function() {
            updateRecipientsPreview(autorisationId);
        });
        
        // Initialiser le sujet avec les informations de l'autorisation
        const autorisationCode = $(`#sendNotificationModal-${autorisationId}`).data('autorisation-code');
        const demandeType = $(`#sendNotificationModal-${autorisationId}`).data('demande-type');
        
        if (autorisationCode) {
            $(`#notification_subject-${autorisationId}`).val(`Notification - Autorisation ${autorisationCode}`);
        }
    });
    
    // Fonction pour mettre à jour l'aperçu des destinataires
    function updateRecipientsPreview(autorisationId) {
        const emails = $(`#email_recipients-${autorisationId}`).val() || [];
        const whatsapps = $(`#whatsapp_recipients-${autorisationId}`).val() || [];
        
        $(`#emailCount-${autorisationId}`).text(emails.length);
        $(`#whatsappCount-${autorisationId}`).text(whatsapps.length);
        
        let emailPreview = '';
        emails.forEach(function(email) {
            emailPreview += '<span class="badge badge-info mr-1 mb-1">' + email + '</span>';
        });
        $(`#emailPreviewList-${autorisationId}`).html(emailPreview || 'Aucun');
        
        let whatsappPreview = '';
        whatsapps.forEach(function(phone) {
            whatsappPreview += '<span class="badge badge-success mr-1 mb-1">' + phone + '</span>';
        });
        $(`#whatsappPreviewList-${autorisationId}`).html(whatsappPreview || 'Aucun');
        
        if (emails.length > 0 || whatsapps.length > 0) {
            $(`#recipientsPreview-${autorisationId}`).fadeIn(300);
        } else {
            $(`#recipientsPreview-${autorisationId}`).fadeOut(300);
        }
    }
    
    // Gestionnaire de soumission du formulaire
    $(document).on('submit', '[id^="notificationForm-"]', function(e) {
        e.preventDefault();
        
        const form = $(this);
        const formId = form.attr('id');
        const autorisationId = formId.split('-').pop();
        
        const emails = $(`#email_recipients-${autorisationId}`).val() || [];
        const whatsapps = $(`#whatsapp_recipients-${autorisationId}`).val() || [];
        
        if (emails.length === 0 && whatsapps.length === 0) {
            toastr.error('Veuillez sélectionner au moins un destinataire');
            return false;
        }
        
        const submitBtn = $(`#sendNotificationBtn-${autorisationId}`);
        submitBtn.prop('disabled', true).html('<i class="fas fa-spinner fa-spin"></i> Envoi en cours...');
        
        const formData = new FormData(this);
        
        $.ajax({
            url: form.attr('action'),
            type: 'POST',
            data: formData,
            processData: false,
            contentType: false,
            success: function(response) {
                toastr.success(response.message || 'Notifications envoyées avec succès');
                $(`#sendNotificationModal-${autorisationId}`).modal('hide');
            },
            error: function(xhr) {
                let errorMsg = 'Une erreur est survenue';
                if (xhr.responseJSON && xhr.responseJSON.message) {
                    errorMsg = xhr.responseJSON.message;
                }
                toastr.error(errorMsg);
            },
            complete: function() {
                submitBtn.prop('disabled', false).html('<i class="fas fa-paper-plane"></i> Envoyer');
            }
        });
    });
    
    // Réinitialiser le formulaire à la fermeture du modal
    $(document).on('hidden.bs.modal', '[id^="sendNotificationModal-"]', function() {
        const modalId = $(this).attr('id');
        const autorisationId = modalId.split('-').pop();
        
        $(`#notificationForm-${autorisationId}`)[0].reset();
        $(`#email_recipients-${autorisationId}`).val(null).trigger('change');
        $(`#whatsapp_recipients-${autorisationId}`).val(null).trigger('change');
        $(`#recipientsPreview-${autorisationId}`).hide();
    });
});
</script>
@endpush