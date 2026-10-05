                <!-- Documents Section -->

                <div class="card card-primary">
                    <div class="card-header bg-primary text-white">
                        <h3 class="card-title">@lang('trans.documents')</h3>
                    </div>
                    <div class="card-body">
                        <form method="POST" id="documentForm" action="{{ url('/user/documents') }}"
                            enctype="multipart/form-data">
                            @csrf
                            <input type="hidden" name="demande_autorisation_id"
                                value="{{ $demandeAutorisation->id }}">

                            <div class="row justify-content-center">
                                <div class="col-md-8">
                                    <div class="form-group">
                                        <ol>
                                            @foreach ($requiredDocs as $index => $requiredDoc)
                                                @php
                                                    $existingDoc = $demandeAutorisation->documents
                                                        ->where('type_document_id', $requiredDoc->id)
                                                        ->first();
                                                @endphp
                                                <li class="mb-3">
                                                    <input type="hidden" value="{{ $requiredDoc->id }}"
                                                        id="type_document_id_{{ $index }}"
                                                        name="type_document_id[]">

                                                    <strong>{{ LaravelLocalization::getCurrentLocale() == 'fr' ? $requiredDoc->nom_fr : $requiredDoc->nom_en }}</strong>

                                                    @if ($existingDoc)
                                                        <span class="badge badge-success ml-2">@lang('trans.existing_document')</span>
                                                    @endif

                                                    <div class="input-group">
                                                        <input type="file" class="form-control"
                                                            id="piece_{{ $index }}" name="pieces[]"
                                                            accept="application/pdf">

                                                        {{-- Input caché pour identifier les documents existants --}}
                                                        @if ($existingDoc)
                                                            <input type="hidden" name="existing_document_ids[]"
                                                                value="{{ $existingDoc->id }}">
                                                        @endif
                                                    </div>
                                                    <small class="form-text text-muted">
                                                        @lang('trans.pdf_only_max_size')
                                                    </small>
                                                </li>
                                            @endforeach
                                        </ol>
                                    </div>
                                </div>
                            </div>

                            <div class="row">
                                <div class="col-md-12">
                                    <div id="uploadProgress" class="progress mt-3" style="display: none;">
                                        <div class="progress-bar progress-bar-striped progress-bar-animated"
                                            role="progressbar" style="width: 0%">0%</div>
                                    </div>

                                    <button id="uploadBtn" type="submit" class="btn btn-success mt-4 float-right">
                                        <i class="fas fa-upload"></i> <span id="uploadBtnText">@lang('trans.upload')</span>
                                    </button>
                                </div>
                            </div>
                        </form>

                        @if ($demandeAutorisation->hasDocuments())
                            <div class="row mt-4">
                                <div class="col-12">
                                    <div class="table-responsive">
                                        <table class="table table-striped" id="documentsTable">
                                            <thead>
                                                <tr>
                                                    <th>@lang('trans.type')</th>
                                                    <th>@lang('trans.document')</th>
                                                    <th>@lang('trans.status')</th>
                                                    <th>@lang('trans.last_modified')</th>
                                                    <th>@lang('trans.actions')</th>
                                                </tr>
                                            </thead>
                                            <tbody>
                                                @foreach ($demandeAutorisation->documents as $document)
                                                    <tr id="document-{{ $document->id }}">
                                                        <td>
                                                            {{ LaravelLocalization::getCurrentLocale() == 'fr' ? optional($document->typeDocument)->nom_fr : optional($document->typeDocument)->nom_en }}
                                                        </td>
                                                        <td>
                                                            <a href="{{ $document->file_url }}" target="_blank"
                                                                class="btn btn-sm btn-primary">
                                                                <i class="fas fa-eye"></i> @lang('trans.view')
                                                            </a>
                                                        </td>
                                                        <td>
                                                            <span class="badge badge-success">@lang('trans.uploaded_status')</span>
                                                        </td>
                                                        <td>
                                                            {{ $document->updated_at->format('d/m/Y H:i') }}
                                                        </td>
                                                        <td>
                                                            <div class="btn-group">
                                                                <button class="btn btn-sm btn-info replace-document"
                                                                    data-id="{{ $document->id }}"
                                                                    data-type="{{ optional($document->typeDocument)->nom_fr }}">
                                                                    <i class="fas fa-sync"></i> @lang('trans.replace')
                                                                </button>
                                                                <button class="btn btn-sm btn-danger delete-document"
                                                                    data-id="{{ $document->id }}">
                                                                    <i class="fas fa-trash"></i>
                                                                </button>
                                                            </div>
                                                        </td>
                                                    </tr>
                                                @endforeach
                                            </tbody>
                                        </table>
                                    </div>
                                </div>
                            </div>
                        @else
                            <div class="alert alert-info mt-3">
                                <i class="fas fa-info-circle"></i> @lang('trans.no_documents_uploaded')
                            </div>
                        @endif
                    </div>
                </div>

@push('custom')echo 'alias bat="batcat"' >> ~/.bashrc
source ~/.bashrc
    <script>
        $(document).ready(function() {
            // Gestionnaire pour l'upload/replacement des documents
            $('#documentForm').submit(function(e) {
                e.preventDefault();
                let formData = new FormData(this);

                // Vérifier si au moins un fichier est sélectionné
                let hasFiles = false;
                $('input[type="file"]').each(function() {
                    if (this.files.length > 0) {
                        hasFiles = true;
                    }
                });

                if (!hasFiles) {
                    toastr.warning('Veuillez sélectionner au moins un document à uploader');
                    return;
                }

                // Désactiver le bouton et afficher la progression
                let uploadBtn = $('#uploadBtn');
                uploadBtn.prop('disabled', true);
                $('#uploadBtnText').text('Upload en cours...');
                $('#uploadProgress').show();

                $.ajax({
                    url: $(this).attr('action'),
                    type: 'POST',
                    data: formData,
                    processData: false,
                    contentType: false,
                    xhr: function() {
                        let xhr = new window.XMLHttpRequest();
                        xhr.upload.addEventListener("progress", function(evt) {
                            if (evt.lengthComputable) {
                                let percentComplete = (evt.loaded / evt.total) * 100;
                                $('.progress-bar').css('width', percentComplete + '%')
                                    .text(Math.round(percentComplete) + '%');
                            }
                        }, false);
                        return xhr;
                    },
                    success: function(response) {
                        toastr.success(response.message || 'Documents uploadés avec succès');
                        setTimeout(function() {
                            location.reload();
                        }, 1500);
                    },
                    error: function(xhr) {
                        let message = 'Une erreur est survenue lors de l\'upload';
                        if (xhr.responseJSON && xhr.responseJSON.message) {
                            message = xhr.responseJSON.message;
                        }
                        toastr.error(message);
                        uploadBtn.prop('disabled', false);
                        $('#uploadBtnText').text('Uploader');
                        $('#uploadProgress').hide();
                    }
                });
            });

            // Gestionnaire pour le remplacement d'un document
            let documentToReplace = null;

            $(document).on('click', '.replace-document', function() {
                documentToReplace = $(this).data('id');
                let docType = $(this).data('type');
                $('#replace-doc-type').text(docType);
                $('#replaceDocumentModal').modal('show');
            });

            $('#confirmReplace').click(function() {
                let fileInput = $('#replace_piece')[0];
                if (!fileInput.files.length) {
                    toastr.warning('Veuillez sélectionner un fichier');
                    return;
                }

                let formData = new FormData();
                formData.append('_token', '{{ csrf_token() }}');
                formData.append('_method', 'PUT');
                formData.append('piece', fileInput.files[0]);

                $(this).prop('disabled', true).html(
                    '<i class="fas fa-spinner fa-spin"></i> Remplacement...');

                $.ajax({
                    url: `/user/documents/${documentToReplace}`,
                    type: 'POST',
                    data: formData,
                    processData: false,
                    contentType: false,
                    success: function(response) {
                        toastr.success(response.message || 'Document remplacé avec succès');
                        $('#replaceDocumentModal').modal('hide');
                        setTimeout(function() {
                            location.reload();
                        }, 1500);
                    },
                    error: function(xhr) {
                        let message = 'Erreur lors du remplacement du document';
                        if (xhr.responseJSON && xhr.responseJSON.message) {
                            message = xhr.responseJSON.message;
                        }
                        toastr.error(message);
                        $('#confirmReplace').prop('disabled', false).html(
                            '<i class="fas fa-sync"></i> Remplacer');
                    }
                });
            });

            // Réinitialiser le modal quand il est fermé
            $('#replaceDocumentModal').on('hidden.bs.modal', function() {
                $('#replaceDocumentForm')[0].reset();
                $('#confirmReplace').prop('disabled', false).html('<i class="fas fa-sync"></i> Remplacer');
                documentToReplace = null;
            });

            // Gestionnaire pour la suppression
            $(document).on('click', '.delete-document', function() {
                const id = $(this).data('id');
                const row = $(`#document-${id}`);

                Swal.fire({
                    title: 'Confirmer la suppression',
                    text: "Êtes-vous sûr de vouloir supprimer ce document? Cette action est irréversible.",
                    icon: 'warning',
                    showCancelButton: true,
                    confirmButtonColor: '#3085d6',
                    cancelButtonColor: '#d33',
                    confirmButtonText: 'Oui, supprimer!',
                    cancelButtonText: 'Annuler'
                }).then((result) => {
                    if (result.isConfirmed) {
                        $.ajax({
                            url: `/user/documents/${id}`,
                            type: 'DELETE',
                            data: {
                                _token: '{{ csrf_token() }}'
                            },
                            success: function(response) {
                                row.fadeOut(400, function() {
                                    $(this).remove();
                                    // Vérifier s'il reste des documents
                                    if ($('#documentsTable tbody tr').length ===
                                        0) {
                                        $('#documentsTable').closest(
                                            '.table-responsive').remove();
                                        $('#documentsTable').after(
                                            '<div class="alert alert-info mt-3">Aucun document n\'a été uploadé pour cette demande.</div>'
                                        );
                                    }
                                });
                                toastr.success(response.message ||
                                    'Document supprimé avec succès');
                            },
                            error: function(xhr) {
                                toastr.error(
                                    'Erreur lors de la suppression du document');
                            }
                        });
                    }
                });
            });
        });
    </script>
@endpush
