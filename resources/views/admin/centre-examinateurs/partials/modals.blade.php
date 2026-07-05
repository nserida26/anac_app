{{-- resources/views/admin/examinateurs/partials/modals.blade.php --}}
{{-- Modal de validation --}}
<div class="modal fade" id="validateModal" tabindex="-1">
    <div class="modal-dialog">
        <div class="modal-content">
            <form id="validateForm">
                @csrf
                <div class="modal-header bg-success text-white">
                    <h5 class="modal-title">
                        <i class="fas fa-check-circle"></i> @lang('trans.validate_examiner')
                    </h5>
                    <button type="button" class="close text-white" data-dismiss="modal">
                        <span>&times;</span>
                    </button>
                </div>
                <div class="modal-body">
                    <p>@lang('trans.validate_examiner_confirm'): <strong id="validateExaminerName"></strong></p>
                    
                    <div class="form-group">
                        <label>@lang('trans.validity_end_date')</label>
                        <input type="date" class="form-control" name="date_fin_validite" id="validate_date_fin">
                        <small class="form-text text-muted">
                            @lang('trans.leave_empty_to_keep_current_date')
                        </small>
                    </div>
                    
                    <div class="form-group">
                        <label>@lang('trans.comment') (@lang('trans.optional'))</label>
                        <textarea class="form-control" name="commentaire" rows="2"></textarea>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-dismiss="modal">
                        <i class="fas fa-times"></i> @lang('trans.cancel')
                    </button>
                    <button type="submit" class="btn btn-success">
                        <i class="fas fa-check"></i> @lang('trans.confirm_validation')
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>

{{-- Modal de rejet --}}
<div class="modal fade" id="rejectModal" tabindex="-1">
    <div class="modal-dialog">
        <div class="modal-content">
            <form id="rejectForm">
                @csrf
                <div class="modal-header bg-danger text-white">
                    <h5 class="modal-title">
                        <i class="fas fa-times-circle"></i> @lang('trans.reject_examiner')
                    </h5>
                    <button type="button" class="close text-white" data-dismiss="modal">
                        <span>&times;</span>
                    </button>
                </div>
                <div class="modal-body">
                    <p>@lang('trans.reject_examiner_confirm'): <strong id="rejectExaminerName"></strong></p>
                    
                    <div class="form-group">
                        <label>@lang('trans.rejection_reason') <span class="text-danger">*</span></label>
                        <textarea class="form-control" name="motif_refus" rows="3" required></textarea>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-dismiss="modal">
                        <i class="fas fa-times"></i> @lang('trans.cancel')
                    </button>
                    <button type="submit" class="btn btn-danger">
                        <i class="fas fa-times"></i> @lang('trans.confirm_rejection')
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>