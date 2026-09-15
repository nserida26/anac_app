{{-- resources/views/admin/partials/workflow-timeline.blade.php --}}
@php
    // etat_workflow (DemandeAutorisation::getEtatWorkflowAttribute) ne va jamais
    // au-delà de 'payment_confirmed' : il ne sait pas qu'une autorisation a été
    // délivrée. « Signé » n'est donc atteint qu'en le déduisant explicitement de
    // l'existence de l'Autorisation, comme le fait déjà le parcours côté demandeur
    // (voir user/partials/autorisation-status-timeline.blade.php).
    $isIssued = (bool) $demande->autorisation($demande->id);
    $etat = $isIssued ? 'signed' : ($demande->etat_workflow ?? 'draft');

    $steps = [
        'submitted' => ['icon' => 'fa-paper-plane', 'label' => trans('trans.submitted')],
        'under_review' => ['icon' => 'fa-search', 'label' => trans('trans.under_review')],
        'service_approved' => ['icon' => 'fa-check-circle', 'label' => trans('trans.service_approved')],
        'paid' => ['icon' => 'fa-credit-card', 'label' => trans('trans.paid')],
        'payment_confirmed' => ['icon' => 'fa-check-double', 'label' => trans('trans.payment_confirmed')],
        'signed' => ['icon' => 'fa-signature', 'label' => trans('trans.signed')]
    ];

    $currentStepIndex = array_search($etat, array_keys($steps));
    if ($demande->etat_workflow === 'rejected') {
        $currentStepIndex = -1;
    }
@endphp

<div class="workflow-timeline">
    <div class="workflow-connector {{ $currentStepIndex >= 0 ? 'completed' : '' }}"></div>
    
    @foreach($steps as $key => $step)
        @php
            $stepIndex = array_search($key, array_keys($steps));
            $isCompleted = $currentStepIndex >= $stepIndex;
            $isActive = $key === $etat;
        @endphp
        
        <div class="workflow-step {{ $isCompleted ? 'completed' : '' }} {{ $isActive ? 'active' : '' }}">
            <div class="step-icon">
                <i class="fas {{ $step['icon'] }}"></i>
            </div>
            <div class="step-label">{{ $step['label'] }}</div>
        </div>
    @endforeach
</div>

<div class="mt-3">
    @include('dir.demandeAutorisations.partials.rejected-by', ['demande' => $demande])
    {{-- Filet de sécurité : affiche le motif même si le flag dta_rejeter/dg_rejeter
         n'est pas synchronisé avec le motif enregistré. --}}
    @include('dir.demandeAutorisations.partials.rejection-reasons-list', ['demande' => $demande])
    @include('dir.demandeAutorisations.partials.invalid-components', ['demande' => $demande])
</div>