{{-- resources/views/dir/demandeAutorisations/modals/retrait-directions.blade.php --}}
<div class="modal fade" id="retraitDirectionsModal" tabindex="-1" role="dialog">
    <div class="modal-dialog" role="document">
        <div class="modal-content">
            <div class="modal-header bg-warning">
                <h5 class="modal-title">
                    <i class="fas fa-undo"></i> @lang('trans.backward_from_directions')
                    <small class="d-block" id="retraitDemandeCode"></small>
                </h5>
                <button type="button" class="close" data-dismiss="modal">
                    <span>&times;</span>
                </button>
            </div>
            <form id="retraitDirectionsForm" method="POST">
                @csrf
                <input type="hidden" name="id" id="retraitDemandeId">
                <input type="hidden" name="action" value="service_raturer">
                <input type="hidden" name="is_approved" value="1">
                
                <div class="modal-body">
                    <div class="alert alert-info">
                        <i class="fas fa-info-circle"></i>
                        @lang('trans.select_directions_to_remove')
                    </div>
                    
                    <div class="form-group">
                        <label>@lang('trans.annotated_directions')</label>
                        <div class="mt-2" id="retraitDirectionsList">
                            <!-- Les directions seront chargées dynamiquement -->
                        </div>
                        <small class="text-muted">
                            @lang('trans.select_directions_hint')
                        </small>
                    </div>
                    
                    <div class="form-group mt-3">
                        <label for="motif_retrait">
                            <i class="fas fa-comment"></i> @lang('trans.reason_for_removal')
                        </label>
                        <textarea name="motif_retrait" 
                                  id="motif_retrait" 
                                  class="form-control" 
                                  rows="3"
                                  placeholder="@lang('trans.removal_reason_placeholder')"></textarea>
                    </div>
                </div>
                
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-dismiss="modal">
                        @lang('trans.cancel')
                    </button>
                    <button type="submit" class="btn btn-warning" id="submitRetraitBtn">
                        <i class="fas fa-undo"></i> @lang('trans.confirm_removal')
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>