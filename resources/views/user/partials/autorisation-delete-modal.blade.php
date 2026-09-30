{{-- resources/views/user/partials/autorisation-delete-modal.blade.php --}}
<div class="modal fade" id="deleteModal-{{ $demande->id }}" tabindex="-1" role="dialog"
    aria-labelledby="deleteModalLabel-{{ $demande->id }}" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered" role="document">
        <div class="modal-content anac-modal anac-modal--danger">
            <div class="modal-header">
                <h5 class="modal-title" id="deleteModalLabel-{{ $demande->id }}">
                    <i class="fas fa-trash"></i> @lang('trans.action_delete_title')
                </h5>
                <button type="button" class="close" data-dismiss="modal" aria-label="@lang('trans.close')">
                    <span aria-hidden="true">&times;</span>
                </button>
            </div>
            <div class="modal-body">
                <p class="mb-2">@lang('trans.delete_confirmation_message')</p>
                <p class="mb-0 text-muted">
                    @lang('trans.code') : <strong>{{ $demande->code }}</strong>
                </p>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-dismiss="modal">
                    <i class="fas fa-times"></i> @lang('trans.cancel')
                </button>
                <form action="{{ route('user.autorisations.destroy', $demande->id) }}" method="POST" class="d-inline">
                    @csrf
                    @method('DELETE')
                    <button type="submit" class="btn btn-danger">
                        <i class="fas fa-trash"></i> @lang('trans.destroy')
                    </button>
                </form>
            </div>
        </div>
    </div>
</div>
