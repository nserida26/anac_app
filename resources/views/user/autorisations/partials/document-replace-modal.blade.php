                <!-- Modal pour remplacer un document -->
                <div class="modal fade" id="replaceDocumentModal" tabindex="-1" role="dialog">
                    <div class="modal-dialog" role="document">
                        <div class="modal-content">
                            <div class="modal-header">
                                <h5 class="modal-title">@lang('trans.replace_document')</h5>
                                <button type="button" class="close" data-dismiss="modal">
                                    <span>&times;</span>
                                </button>
                            </div>
                            <div class="modal-body">
                                <p>@lang('trans.document_type'): <strong id="replace-doc-type"></strong></p>
                                <form id="replaceDocumentForm" enctype="multipart/form-data">
                                    @csrf
                                    @method('PUT')
                                    <div class="form-group">
                                        <label>@lang('trans.new_document')</label>
                                        <input type="file" class="form-control" id="replace_piece" name="piece"
                                            accept="application/pdf" required>
                                    </div>
                                </form>
                            </div>
                            <div class="modal-footer">
                                <button type="button" class="btn btn-secondary"
                                    data-dismiss="modal">@lang('trans.cancel')</button>
                                <button type="button" class="btn btn-primary" id="confirmReplace">
                                    <i class="fas fa-sync"></i> @lang('trans.replace')
                                </button>
                            </div>
                        </div>
                    </div>
                </div>
