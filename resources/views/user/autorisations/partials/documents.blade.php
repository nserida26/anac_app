@push('css')
    <style>
        /* ── Section Documents : alignée sur le design du layout (navy & or) ── */
        .doc-card .card-header {
            gap: 0.75rem;
        }

        .doc-head {
            display: inline-flex;
            align-items: center;
            gap: 0.65rem;
            min-width: 0;
        }

        .doc-head__icon {
            display: inline-flex;
            align-items: center;
            justify-content: center;
            width: 34px;
            height: 34px;
            flex: 0 0 auto;
            border-radius: 10px;
            background: linear-gradient(135deg, var(--anac-primary) 0%, var(--anac-primary-light) 100%);
            color: var(--anac-accent);
            font-size: 0.9rem;
            box-shadow: var(--anac-shadow-sm);
        }

        .doc-head__title {
            margin: 0;
            font-size: 1rem;
            font-weight: 700;
            color: var(--anac-primary);
        }

        .doc-head__count {
            display: inline-flex;
            align-items: center;
            gap: 0.35rem;
            padding: 0.3em 0.75em;
            border-radius: 999px;
            background: var(--anac-gray-100);
            color: var(--anac-gray-600);
            font-size: 0.78rem;
            font-weight: 700;
        }

        .doc-intro {
            margin: 0 0 1rem;
            font-size: 0.9rem;
            color: var(--anac-gray-600);
        }

        /* Liste des pièces à fournir */
        .doc-list {
            list-style: none;
            margin: 0;
            padding: 0;
            display: flex;
            flex-direction: column;
            gap: 0.75rem;
        }

        .doc-item {
            padding: 0.9rem 1rem;
            border: 1px solid var(--anac-gray-200);
            border-radius: var(--anac-radius-sm);
            background: var(--anac-white);
            transition: var(--anac-transition);
        }

        .doc-item:hover {
            border-color: var(--anac-gray-400);
            box-shadow: var(--anac-shadow-sm);
        }

        .doc-item.is-done {
            border-color: #cde8d6;
            background: linear-gradient(180deg, #f6fbf7 0%, var(--anac-white) 60%);
        }

        .doc-item__top {
            display: flex;
            align-items: center;
            flex-wrap: wrap;
            gap: 0.5rem;
            margin-bottom: 0.6rem;
        }

        .doc-item__name {
            font-weight: 600;
            color: var(--anac-gray-800);
        }

        .doc-pill {
            display: inline-flex;
            align-items: center;
            gap: 0.3rem;
            padding: 0.25em 0.7em;
            border-radius: 999px;
            font-size: 0.74rem;
            font-weight: 700;
            line-height: 1.2;
        }

        .doc-pill--ok {
            background: #e7f6ed;
            color: #1e7a47;
        }

        .doc-pill--todo {
            background: var(--anac-gray-100);
            color: var(--anac-gray-600);
        }

        /* Ligne de contrôle : fichier / bouton, puis actions alignées à droite */
        .doc-item__control {
            display: flex;
            align-items: center;
            gap: 0.5rem;
        }

        /* Fichier renseigné : icône + nom du fichier */
        .doc-item__file {
            display: inline-flex;
            align-items: center;
            gap: 0.55rem;
            min-width: 0;
            flex: 1 1 auto;
            color: var(--anac-gray-800);
            font-size: 0.88rem;
        }

        .doc-item__file > i {
            flex: 0 0 auto;
            font-size: 1.05rem;
            color: #c0392b;
        }

        .doc-item__filename {
            font-weight: 600;
            overflow: hidden;
            text-overflow: ellipsis;
            white-space: nowrap;
        }

        /* Pièce non renseignée */
        .doc-item__hint {
            flex: 1 1 auto;
            min-width: 0;
            font-size: 0.85rem;
            color: var(--anac-gray-600);
        }

        .doc-item__actions {
            display: flex;
            align-items: center;
            gap: 0.4rem;
            flex: 0 0 auto;
        }

        /* Boutons d'action carrés (icônes) */
        .doc-icon-btn {
            display: inline-flex;
            align-items: center;
            justify-content: center;
            width: 40px;
            height: 40px;
            flex: 0 0 auto;
            border: none;
            border-radius: var(--anac-radius-sm);
            font-size: 0.9rem;
            cursor: pointer;
            text-decoration: none;
            transition: var(--anac-transition);
        }

        .doc-icon-btn:hover {
            filter: brightness(0.95);
            transform: translateY(-1px);
            text-decoration: none;
        }

        .doc-icon-btn:disabled {
            opacity: 0.6;
            cursor: not-allowed;
            transform: none;
        }

        .doc-icon-btn--blue {
            background: #e8f1fb;
            color: #2563a8;
        }

        .doc-icon-btn--yellow {
            background: #fdf4dd;
            color: #9c7c1a;
        }

        .doc-icon-btn--red {
            background: #fdecec;
            color: #c0392b;
        }

        /* Bouton d'upload (pièces manquantes) */
        .doc-upload-btn {
            display: inline-flex;
            align-items: center;
            justify-content: center;
            gap: 0.45rem;
            height: 40px;
            padding: 0 1.1rem;
            flex: 0 0 auto;
            border: none;
            border-radius: var(--anac-radius-sm);
            background: linear-gradient(135deg, var(--anac-primary) 0%, var(--anac-primary-light) 100%);
            color: var(--anac-white);
            font-weight: 600;
            letter-spacing: 0.2px;
            white-space: nowrap;
            box-shadow: var(--anac-shadow-sm);
            transition: var(--anac-transition);
        }

        .doc-upload-btn:hover:not(:disabled) {
            color: var(--anac-white);
            transform: translateY(-1px);
            box-shadow: var(--anac-shadow-md);
        }

        .doc-upload-btn:disabled {
            opacity: 0.65;
            cursor: not-allowed;
        }

        /* Zone de dépôt dans le modal (ajout / remplacement) */
        .doc-drop {
            display: flex;
            flex-direction: column;
            align-items: center;
            justify-content: center;
            gap: 0.5rem;
            width: 100%;
            padding: 1.25rem 1rem;
            cursor: pointer;
            text-align: center;
            border: 1.5px dashed var(--anac-gray-400);
            border-radius: var(--anac-radius-sm);
            background: var(--anac-gray-50);
            color: var(--anac-gray-600);
            font-size: 0.88rem;
            transition: var(--anac-transition);
        }

        .doc-drop:hover {
            border-color: var(--anac-primary);
            background: var(--anac-white);
        }

        .doc-drop > i {
            font-size: 1.4rem;
            color: var(--anac-primary);
        }

        .doc-drop__input {
            display: none;
        }

        .doc-drop__file {
            display: flex;
            align-items: center;
            gap: 0.5rem;
            margin-top: 0.6rem;
            padding: 0.55rem 0.75rem;
            border-radius: var(--anac-radius-sm);
            background: #fdecec;
            color: #c0392b;
            font-weight: 600;
            font-size: 0.85rem;
        }

        .doc-drop__file > i {
            flex: 0 0 auto;
        }

        .doc-drop__filename {
            overflow: hidden;
            text-overflow: ellipsis;
            white-space: nowrap;
        }

        .doc-modal-progress {
            display: none;
            height: 10px;
            margin-top: 0.85rem;
            border-radius: 999px;
            background: var(--anac-gray-200);
            overflow: hidden;
        }

        .doc-modal-progress .progress-bar {
            background: linear-gradient(90deg, var(--anac-primary) 0%, var(--anac-accent) 100%);
            font-size: 0;
        }

        /* État vide global (aucune pièce requise) */
        .doc-empty {
            padding: 2rem 1rem;
            text-align: center;
            border: 1.5px dashed var(--anac-gray-200);
            border-radius: var(--anac-radius-sm);
            background: var(--anac-gray-50);
            color: var(--anac-gray-600);
        }

        .doc-empty i {
            display: block;
            margin-bottom: 0.5rem;
            font-size: 1.6rem;
            color: var(--anac-gray-400);
        }

        @media (max-width: 575.98px) {
            .doc-head__count {
                display: none;
            }

            .doc-item__control {
                flex-wrap: wrap;
            }

            .doc-item__hint,
            .doc-item__file {
                flex: 1 1 100%;
            }

            .doc-item__actions {
                width: 100%;
                justify-content: flex-end;
            }
        }
    </style>
@endpush

<!-- Documents Section -->
<div class="card doc-card">
    <div class="card-header">
        <div class="doc-head">
            <span class="doc-head__icon"><i class="fas fa-folder-open"></i></span>
            <h3 class="doc-head__title">@lang('trans.documents')</h3>
        </div>
        <span class="doc-head__count">
            <i class="fas fa-paperclip"></i>
            {{ $demandeAutorisation->documents->count() }}
        </span>
    </div>

    <div class="card-body">
        <p class="doc-intro">@lang('trans.pdf_only_max_size')</p>

        @if (count($requiredDocs) === 0)
            <div class="doc-empty">
                <i class="fas fa-file-pdf"></i>
                @lang('trans.no_documents_uploaded')
            </div>
        @else
            <ol class="doc-list">
                @foreach ($requiredDocs as $index => $requiredDoc)
                    @php
                        $existingDoc = $demandeAutorisation->documents
                            ->where('type_document_id', $requiredDoc->id)
                            ->first();
                        $typeName = LaravelLocalization::getCurrentLocale() == 'fr'
                            ? $requiredDoc->nom_fr
                            : $requiredDoc->nom_en;
                        $fileName = $existingDoc ? basename($existingDoc->url) : null;
                    @endphp

                    <li class="doc-item{{ $existingDoc ? ' is-done' : '' }}">
                        <div class="doc-item__top">
                            <span class="doc-item__name">{{ $typeName }}</span>

                            @if ($existingDoc)
                                <span class="doc-pill doc-pill--ok">
                                    <i class="fas fa-check-circle"></i> @lang('trans.existing_document')
                                </span>
                            @else
                                <span class="doc-pill doc-pill--todo">
                                    <i class="fas fa-clock"></i> @lang('trans.pending')
                                </span>
                            @endif
                        </div>

                        <div class="doc-item__control">
                            @if ($existingDoc)
                                {{-- Pièce fournie : icône + nom du fichier, puis les actions --}}
                                <div class="doc-item__file">
                                    <i class="fas fa-file-pdf"></i>
                                    <span class="doc-item__filename" title="{{ $fileName }}">{{ $fileName }}</span>
                                </div>

                                <div class="doc-item__actions">
                                    <a href="{{ $existingDoc->file_url }}" target="_blank"
                                        class="doc-icon-btn doc-icon-btn--blue view-document"
                                        title="@lang('trans.view')">
                                        <i class="fas fa-eye"></i>
                                    </a>
                                    <button type="button" class="doc-icon-btn doc-icon-btn--yellow replace-document"
                                        data-id="{{ $existingDoc->id }}" data-type-id="{{ $requiredDoc->id }}"
                                        data-type-name="{{ $typeName }}" title="@lang('trans.replace')">
                                        <i class="fas fa-sync"></i>
                                    </button>
                                    <button type="button" class="doc-icon-btn doc-icon-btn--red delete-document"
                                        data-id="{{ $existingDoc->id }}" title="@lang('trans.delete')">
                                        <i class="fas fa-trash"></i>
                                    </button>
                                </div>
                            @else
                                {{-- Pièce manquante : pas d'input, un simple bouton d'upload --}}
                                <span class="doc-item__hint">@lang('trans.no_documents_uploaded')</span>

                                <div class="doc-item__actions">
                                    <button type="button" class="doc-upload-btn upload-document"
                                        data-type-id="{{ $requiredDoc->id }}" data-type-name="{{ $typeName }}">
                                        <i class="fas fa-upload"></i> <span>@lang('trans.upload')</span>
                                    </button>
                                </div>
                            @endif
                        </div>
                    </li>
                @endforeach
            </ol>
        @endif
    </div>
</div>

@push('custom')
    <script>
        $(document).ready(function() {
            const uploadRoute = "{{ url('/user/documents') }}";
            const demandeId = '{{ $demandeAutorisation->id }}';
            const TXT = {
                upload: @json(__('trans.upload')),
                replace: @json(__('trans.replace')),
                replaceDocument: @json(__('trans.replace_document')),
                selectFile: 'Veuillez sélectionner un document (PDF)'
            };

            let docMode = 'upload';
            let docId = null;

            // Ouverture du modal (ajout OU remplacement selon le bouton cliqué)
            function openDocModal(mode, typeId, typeName, id) {
                docMode = mode;
                docId = id || null;

                $('#replace_piece').val('');
                $('#replace-file-name').hide().find('.doc-drop__filename').text('');
                $('#replace-doc-type').text(typeName || '—');
                $('#replaceDocumentModal').data('type-id', typeId);
                $('#replaceDocumentModal').data('type-name', typeName);

                if (mode === 'replace') {
                    $('#docModalTitle').html('<i class="fas fa-sync"></i> <span>' + TXT.replaceDocument +
                        '</span>');
                    $('#docModalSubmitLabel').text(TXT.replace);
                } else {
                    $('#docModalTitle').html('<i class="fas fa-file-upload"></i> <span>' + TXT.upload +
                        '</span>');
                    $('#docModalSubmitLabel').text(TXT.upload);
                }

                $('#replaceDocumentModal').modal('show');
            }

            $(document).on('click', '.upload-document', function() {
                openDocModal('upload', $(this).data('type-id'), $(this).data('type-name'));
            });

            $(document).on('click', '.replace-document', function() {
                openDocModal('replace', $(this).data('type-id'), $(this).data('type-name'), $(this).data('id'));
            });

            // Aperçu du nom du fichier choisi
            $(document).on('change', '#replace_piece', function() {
                if (this.files && this.files.length) {
                    $('#replace-file-name').show().find('.doc-drop__filename').text(this.files[0].name);
                } else {
                    $('#replace-file-name').hide();
                }
            });

            // Validation du modal : envoi (POST) ou remplacement (PUT)
            $(document).on('click', '#confirmReplace', function() {
                const input = document.getElementById('replace_piece');
                if (!input || !input.files.length) {
                    toastr.warning(TXT.selectFile);
                    return;
                }

                const $modal = $('#replaceDocumentModal');
                const typeId = $modal.data('type-id');
                const file = input.files[0];

                let formData = new FormData();
                formData.append('_token', '{{ csrf_token() }}');

                let url = uploadRoute;
                if (docMode === 'replace') {
                    url = uploadRoute + '/' + docId;
                    formData.append('_method', 'PUT');
                    formData.append('piece', file);
                } else {
                    formData.append('demande_autorisation_id', demandeId);
                    formData.append('type_document_id[]', typeId);
                    formData.append('pieces[]', file);
                }

                const $btn = $(this);
                const originalHtml = $btn.html();
                $btn.prop('disabled', true).html('<i class="fas fa-spinner fa-spin"></i>');
                $('#docModalProgress').show().find('.progress-bar').css('width', '0%');

                $.ajax({
                    url: url,
                    type: 'POST',
                    data: formData,
                    processData: false,
                    contentType: false,
                    xhr: function() {
                        let xhr = new window.XMLHttpRequest();
                        xhr.upload.addEventListener('progress', function(evt) {
                            if (evt.lengthComputable) {
                                let percent = (evt.loaded / evt.total) * 100;
                                $('#docModalProgress .progress-bar').css('width', percent + '%');
                            }
                        }, false);
                        return xhr;
                    },
                    success: function(response) {
                        toastr.success(response.message || TXT.upload);
                        $modal.modal('hide');
                        setTimeout(function() {
                            location.reload();
                        }, 1000);
                    },
                    error: function(xhr) {
                        let message = TXT.selectFile;
                        if (xhr.responseJSON && xhr.responseJSON.message) {
                            message = xhr.responseJSON.message;
                        }
                        toastr.error(message);
                        $btn.prop('disabled', false).html(originalHtml);
                        $('#docModalProgress').hide();
                    }
                });
            });

            // Suppression d'un document
            $(document).on('click', '.delete-document', function() {
                const id = $(this).data('id');

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
                            url: uploadRoute + '/' + id,
                            type: 'DELETE',
                            data: {
                                _token: '{{ csrf_token() }}'
                            },
                            success: function(response) {
                                toastr.success(response.message || 'Document supprimé avec succès');
                                setTimeout(function() {
                                    location.reload();
                                }, 900);
                            },
                            error: function() {
                                toastr.error('Erreur lors de la suppression du document');
                            }
                        });
                    }
                });
            });
        });
    </script>
@endpush
