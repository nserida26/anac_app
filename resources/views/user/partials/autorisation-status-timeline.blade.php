{{-- resources/views/user/partials/autorisation-status-timeline.blade.php --}}
@php
    $isPayable = (int) ($demande->type_demande_autorisation_id ?? 0) === 2
        && in_array((int) ($demande->type_vol_id ?? 0), [1, 2, 5, 8, 14], true);
    $isIssued = (bool) $demande->autorisation($demande->id);
    $isRejected = ($demande->etat_workflow ?? 'draft') === 'rejected';

    $timelineSteps = [
        'draft' => 'Brouillon',
        'submitted' => 'Déposé',
        'under_review' => "À l'étude",
        'service_approved' => 'Validé par les services',
    ];
    if ($isPayable) {
        $timelineSteps['paid'] = 'Payé';
        $timelineSteps['payment_confirmed'] = 'Paiement confirmé';
    }
    $timelineSteps['issued'] = 'Autorisation délivrée';

    $timelineKeys = array_keys($timelineSteps);
    $currentKey = $isIssued ? 'issued' : ($demande->etat_workflow ?? 'draft');
    $currentIndex = array_search($currentKey, $timelineKeys);
    if ($currentIndex === false) {
        $currentIndex = 0;
    }
@endphp
<div class="modal fade" id="statusModal-{{ $demande->id }}" tabindex="-1" role="dialog">
    <div class="modal-dialog modal-lg" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title">
                    <i class="fas fa-route"></i> Parcours de la demande : {{ $demande->code }}
                </h5>
                <button type="button" class="close" data-dismiss="modal"><span>&times;</span></button>
            </div>
            <div class="modal-body">
                @if ($isRejected)
                    @include('dir.demandeAutorisations.partials.rejected-by', ['demande' => $demande])
                    <p class="text-muted mb-0">
                        Si la DTA a rouvert le dossier, vous pouvez le corriger et le redéposer depuis votre tableau de bord ; le parcours reprendra alors depuis le début.
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
