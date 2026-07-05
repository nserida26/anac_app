{{-- resources/views/dir/demandeAutorisations/modals/dta-annotation.blade.php --}}
<div class="modal fade" id="dtaAnnotationModal" tabindex="-1" aria-labelledby="dtaAnnotationModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content">
            <div class="modal-header bg-info text-white">
                <h5 class="modal-title" id="dtaAnnotationModalLabel">
                    <i class="fas fa-user-tie mr-2"></i>@lang('trans.dta_annotation')
                </h5>
                <button type="button" class="close text-white" data-dismiss="modal" aria-label="Close">
                    <span aria-hidden="true">&times;</span>
                </button>
            </div>
            <form id="dtaAnnotationForm" method="POST">
                @csrf
                <div class="modal-body">
                    <input type="hidden" name="id" id="dtaAnnotationDemandeId">
                    <input type="hidden" name="is_approved" value="1">
                    
                    <div class="form-group">
                        <label for="dtaAnnotationOptions" class="form-label font-weight-bold">
                            <i class="fas fa-arrow-right mr-1"></i> @lang('trans.annotate_to')
                        </label>
                        <select name="action" id="dtaAnnotationOptions" 
                                class="form-control select2-dta-options" required 
                                style="width: 100%"
                                data-placeholder="@lang('trans.select_recipient')">
                            <option value="dta_annoter">@lang('trans.to_srta')</option>
                            {{--<option value="dta_annoter_admin">@lang('trans.to_admin_srta')</option>--}}
                        </select>
                    </div>
                    
                    <div class="form-group">
                        <label for="dtaPoints" class="form-label font-weight-bold">
                            <i class="fas fa-list mr-1"></i> @lang('trans.points')
                        </label>
                        <textarea name="points" id="dtaPoints" class="form-control summernote">{{ old('points') }}</textarea>
                    </div>
                    
                    <div class="alert alert-info">
                        <i class="fas fa-info-circle mr-2"></i>
                        @lang('trans.dta_annotation_info')
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-dismiss="modal">
                        <i class="fas fa-times mr-1"></i> @lang('trans.cancel')
                    </button>
                    <button type="submit" class="btn btn-info">
                        <i class="fas fa-paper-plane mr-1"></i> @lang('trans.send')
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>