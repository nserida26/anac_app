<!-- Modal d'ajout / remplacement d'un document (piloté depuis documents.blade.php) -->
<div class="modal fade anac-modal" id="replaceDocumentModal" tabindex="-1" role="dialog"
    aria-labelledby="docModalTitle" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="docModalTitle">
                    <i class="fas fa-file-upload"></i> <span>@lang('trans.upload')</span>
                </h5>
                <button type="button" class="close" data-dismiss="modal" aria-label="@lang('trans.close')">
                    <span>&times;</span>
                </button>
            </div>
            <div class="modal-body">
                <div class="anac-detail-list mb-3">
                    <div class="anac-detail-row">
                        <span class="anac-detail-label">
                            <i class="fas fa-tag"></i> @lang('trans.document_type')
                        </span>
                        <span class="anac-detail-value" id="replace-doc-type">—</span>
                    </div>
                </div>

                <label class="doc-drop" for="replace_piece">
                    <i class="fas fa-cloud-upload-alt"></i>
                    <span>@lang('trans.new_document')</span>
                </label>
                <input type="file" class="doc-drop__input" id="replace_piece" name="piece" accept="application/pdf">

                <div class="doc-drop__file" id="replace-file-name" style="display: none;">
                    <i class="fas fa-file-pdf"></i>
                    <span class="doc-drop__filename"></span>
                </div>

                <div class="progress doc-modal-progress" id="docModalProgress">
                    <div class="progress-bar" role="progressbar" style="width: 0%"></div>
                </div>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn anac-action anac-action--grey" data-dismiss="modal">
                    @lang('trans.cancel')
                </button>
                <button type="button" class="btn anac-action anac-action--blue" id="confirmReplace">
                    <i class="fas fa-upload"></i> <span id="docModalSubmitLabel">@lang('trans.upload')</span>
                </button>
            </div>
        </div>
    </div>
</div>
