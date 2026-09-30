{{-- resources/views/user/partials/autorisation-details-modal.blade.php --}}
<div class="modal fade" id="detailsModal-{{ $demande->id }}" tabindex="-1" role="dialog"
    aria-labelledby="detailsModalLabel-{{ $demande->id }}" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered" role="document">
        <div class="modal-content anac-modal">
            <div class="modal-header">
                <h5 class="modal-title" id="detailsModalLabel-{{ $demande->id }}">
                    <i class="fas fa-info-circle"></i>
                    @lang('trans.details') : <span class="anac-modal__code">{{ $demande->code }}</span>
                </h5>
                <button type="button" class="close" data-dismiss="modal" aria-label="@lang('trans.close')">
                    <span aria-hidden="true">&times;</span>
                </button>
            </div>
            <div class="modal-body">
                <div class="anac-detail-list">
                    <div class="anac-detail-row">
                        <span class="anac-detail-label"><i class="fas fa-hashtag"></i> @lang('trans.code')</span>
                        <span class="anac-detail-value">{{ $demande->code ?? 'N/A' }}</span>
                    </div>
                    <div class="anac-detail-row">
                        <span class="anac-detail-label"><i class="fas fa-building"></i> @lang('trans.operator')</span>
                        <span class="anac-detail-value">{{ optional($demande->compagnie)->nom_entreprise ?? 'N/A' }}</span>
                    </div>
                    <div class="anac-detail-row">
                        <span class="anac-detail-label"><i class="fas fa-calendar-plus"></i> @lang('trans.creation_date')</span>
                        <span class="anac-detail-value">
                            {{ $demande->created_at ? $demande->created_at->format('d/m/Y') : 'N/A' }}
                        </span>
                    </div>
                    <div class="anac-detail-row">
                        <span class="anac-detail-label"><i class="fas fa-paper-plane"></i> @lang('trans.submission_date')</span>
                        <span class="anac-detail-value">{{ $demande->date_soumission_formatted ?? 'N/A' }}</span>
                    </div>
                    <div class="anac-detail-row">
                        <span class="anac-detail-label"><i class="fas fa-plane-departure"></i> @lang('trans.start_date')</span>
                        <span class="anac-detail-value">
                            {{ $demande->date_debut ? \Carbon\Carbon::parse($demande->date_debut)->format('d/m/Y') : 'N/A' }}
                        </span>
                    </div>
                    <div class="anac-detail-row">
                        <span class="anac-detail-label"><i class="fas fa-plane-arrival"></i> @lang('trans.end_date')</span>
                        <span class="anac-detail-value">
                            {{ $demande->date_fin ? \Carbon\Carbon::parse($demande->date_fin)->format('d/m/Y') : 'N/A' }}
                        </span>
                    </div>
                </div>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-dismiss="modal">@lang('trans.close')</button>
            </div>
        </div>
    </div>
</div>
