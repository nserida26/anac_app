{{-- resources/views/dir/demandeAutorisations/modals/annotation.blade.php --}}
<div class="modal fade" id="annotationModal" tabindex="-1" aria-labelledby="annotationModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered modal-lg">
        <div class="modal-content">
            <div class="modal-header bg-primary text-white">
                <h5 class="modal-title" id="annotationModalLabel">
                    <i class="fas fa-share-alt mr-2"></i>@lang('trans.annotation_to_directions')
                </h5>
                <button type="button" class="close text-white" data-dismiss="modal" aria-label="Close">
                    <span aria-hidden="true">&times;</span>
                </button>
            </div>
            <form id="annotationForm" method="POST">
                @csrf
                <div class="modal-body">
                    <input type="hidden" name="id" id="annotationDemandeId">
                    <input type="hidden" name="action" value="service_annoter">
                    <input type="hidden" name="is_approved" value="1">
                    
                    <div class="form-group">
                        <label for="annotationDirections" class="form-label font-weight-bold">
                            <i class="fas fa-users mr-1"></i> @lang('trans.select_directions')
                        </label>
                        <select name="directions[]" id="annotationDirections" 
                                class="form-control select2-directions" multiple required 
                                style="width: 100%"
                                data-placeholder="@lang('trans.select_directions_placeholder')">
                            <option value="dsv">DSV</option>
                            <option value="dsad">DSAD</option>
                            <option value="dsna">DSNA</option>
                            <option value="dsf">DSF</option>
                        </select>
                        <small class="form-text text-muted">
                            @lang('trans.select_multiple_directions')
                        </small>
                    </div>
                    
                    <div class="form-group">
                        <label for="points" class="form-label font-weight-bold">
                            <i class="fas fa-list mr-1"></i> @lang('trans.points')
                        </label>
                        <textarea name="points" id="points" class="form-control summernote">{{ old('points') }}</textarea>
                    </div>
                    
                    <div class="alert alert-info mt-3">
                        <i class="fas fa-info-circle mr-2"></i>
                        @lang('trans.annotation_info')
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-dismiss="modal">
                        <i class="fas fa-times mr-1"></i> @lang('trans.cancel')
                    </button>
                    <button type="submit" class="btn btn-primary" id="submitAnnotationBtn">
                        <i class="fas fa-paper-plane mr-1"></i> @lang('trans.send')
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>