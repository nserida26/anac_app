{{-- resources/views/admin/partials/workflow-timeline.blade.php --}}
@php
    $etat = $demande->etat_workflow;
    $steps = [
        'submitted' => ['icon' => 'fa-paper-plane', 'label' => trans('trans.submitted')],
        'under_review' => ['icon' => 'fa-search', 'label' => trans('trans.under_review')],
        'service_approved' => ['icon' => 'fa-check-circle', 'label' => trans('trans.service_approved')],
        'paid' => ['icon' => 'fa-credit-card', 'label' => trans('trans.paid')],
        'payment_confirmed' => ['icon' => 'fa-check-double', 'label' => trans('trans.payment_confirmed')],
        'signed' => ['icon' => 'fa-signature', 'label' => trans('trans.signed')]
    ];

    $currentStepIndex = array_search($etat, array_keys($steps));
    if ($etat === 'rejected') {
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

@if($etat === 'rejected')
    <div class="alert alert-danger text-center mt-3">
        <i class="fas fa-ban"></i> @lang('trans.application_rejected')
        @if($demande->dg_motif)
            <br><small>DG: {{ $demande->dg_motif }}</small>
        @endif
        @if($demande->dta_motif)
            <br><small>DTA: {{ $demande->dta_motif }}</small>
        @endif
    </div>
@endif