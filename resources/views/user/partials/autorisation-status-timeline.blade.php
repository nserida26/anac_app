{{-- resources/views/user/partials/autorisation-status-timeline.blade.php --}}
@php
    $isPayable = (int) ($demande->type_demande_autorisation_id ?? 0) === 2
        && in_array((int) ($demande->type_vol_id ?? 0), [1, 2, 5, 8, 14], true);
    $isIssued = (bool) $demande->autorisation($demande->id);
    $isRejected = ($demande->etat_workflow ?? 'draft') === 'rejected';

    $timelineSteps = [
        'draft' => __('trans.workflow_status_draft'),
        'submitted' => __('trans.workflow_status_submitted'),
        'under_review' => __('trans.workflow_status_under_review'),
        'service_approved' => __('trans.workflow_status_service_approved'),
    ];
    if ($isPayable) {
        $timelineSteps['paid'] = __('trans.workflow_status_paid');
        $timelineSteps['payment_confirmed'] = __('trans.workflow_status_payment_confirmed');
    }
    $timelineSteps['issued'] = __('trans.workflow_status_issued');

    $timelineKeys = array_keys($timelineSteps);
    $currentKey = $isIssued ? 'issued' : ($demande->etat_workflow ?? 'draft');
    $currentIndex = array_search($currentKey, $timelineKeys);
    if ($currentIndex === false) {
        $currentIndex = 0;
    }
@endphp
<div class="modal fade" id="statusModal-{{ $demande->id }}" tabindex="-1" role="dialog">
    <div class="modal-dialog modal-lg modal-dialog-centered" role="document">
        <div class="modal-content anac-modal">
            <div class="modal-header">
                <h5 class="modal-title">
                    <i class="fas fa-route"></i>
                    @lang('trans.statut_circuit') : <span class="anac-modal__code">{{ $demande->code }}</span>
                </h5>
                <button type="button" class="close" data-dismiss="modal"><span>&times;</span></button>
            </div>
            <div class="modal-body">
                @if ($isRejected)
                    @include('dir.demandeAutorisations.partials.rejected-by', ['demande' => $demande])
                    <p class="text-muted mb-0">
                        @lang('trans.timeline_reopened_hint')
                    </p>
                @else
                    <ul class="autorisation-stepper">
                        @foreach ($timelineKeys as $index => $key)
                            <li class="{{ $index < $currentIndex ? 'completed' : ($index === $currentIndex ? 'current' : 'pending') }}"
                                data-step="{{ $index + 1 }}">
                                {{ $timelineSteps[$key] }}
                            </li>
                        @endforeach
                    </ul>
                @endif
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-dismiss="modal">@lang('trans.close')</button>
            </div>
        </div>
    </div>
</div>
