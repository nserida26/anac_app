{{-- resources/views/dir/demandeAutorisations/modals/dg-annotation.blade.php --}}
<div class="modal fade" id="dgAnnotationModal" tabindex="-1" aria-labelledby="dgAnnotationModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content">
            <div class="modal-header bg-success text-white">
                <h5 class="modal-title" id="dgAnnotationModalLabel">
                    <i class="fas fa-user-tie mr-2"></i>@lang('trans.dg_annotation')
                </h5>
                <button type="button" class="close text-white" data-dismiss="modal" aria-label="Close">
                    <span aria-hidden="true">&times;</span>
                </button>
            </div>
            <form id="dgAnnotationForm" method="POST">
                @csrf
                <div class="modal-body">
                    <input type="hidden" name="id" id="dgAnnotationDemandeId">
                    <input type="hidden" name="is_approved" value="1">
                    
                    <div class="form-group">
                        <label for="dgAnnotationOptions" class="form-label font-weight-bold">
                            <i class="fas fa-arrow-right mr-1"></i> @lang('trans.annotate_to')
                        </label>
                        <select name="action" id="dgAnnotationOptions" 
                                class="form-control select2-dg-options" required 
                                style="width: 100%"
                                data-placeholder="@lang('trans.select_recipient')">
                            <option value="dg_annoter">@lang('trans.to_dta')</option>
                            <option value="dg_annoter_admin">@lang('trans.to_admin_srta')</option>
                        </select>
                    </div>
                    
                    <div class="form-group">
                        <label for="dgMotif" class="form-label font-weight-bold">
                            <i class="fas fa-comment mr-1"></i> @lang('trans.comment') (Optional)
                        </label>
                        <textarea name="motif" id="dgMotif" class="form-control" rows="3"></textarea>
                    </div>
                    
                    <div class="alert alert-info">
                        <i class="fas fa-info-circle mr-2"></i>
                        @lang('trans.dg_annotation_info')
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-dismiss="modal">
                        <i class="fas fa-times mr-1"></i> @lang('trans.cancel')
                    </button>
                    <button type="submit" class="btn btn-success">
                        <i class="fas fa-paper-plane mr-1"></i> @lang('trans.send')
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>