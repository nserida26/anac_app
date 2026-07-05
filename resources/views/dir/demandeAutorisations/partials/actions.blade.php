{{-- Actions pour chaque rôle --}}
@php
    $etat = optional($demande->etatDemande);
    $user = auth()->user();
@endphp

{{-- Champ d'état de la demande --}}
<div class="alert alert-info mb-3">
    <strong>@lang('trans.current_position'):</strong>
    @php
        $position = 'demande_created';
        $positionText = __('trans.demande_created');
        
        if($etat->dg_rejeter) {
            $position = 'rejected_by_dg';
            $positionText = __('trans.rejected_by_dg');
        } elseif($etat->dg_valider || $etat->dta_dg_valider) {
            $position = 'validated_by_dg';
            $positionText = __('trans.validated_by_dg');
        } elseif($etat->dta_valider) {
            $position = 'validated_by_dta';
            $positionText = __('trans.validated_by_dta');
        } elseif($etat->service_valider || $etat->service_tout_valider) {
            $position = 'validated_by_service';
            $positionText = __('trans.validated_by_service');
        } elseif($demande->isValidatedByAll()) {
            $position = 'validated_by_direction';
            $positionText = __('trans.validated_by_direction');
        } elseif($etat->service_annoter) {
            $position = 'annotated_by_service';
            $positionText = __('trans.annotated_by_service');
        } elseif($etat->dta_annoter) {
            $position = 'annotated_by_dta';
            $positionText = __('trans.annotated_by_dta');
        } elseif($etat->dg_annoter || $etat->dta_dg_annoter) {
            $position = 'annotated_by_dg';
            $positionText = __('trans.annotated_by_dg');
        }
        
        // Barre de progression
        $steps = [
            'demande_created' => 10,
            'annotated_by_dg' => 20,
            'annotated_by_service' => 30,
            'annotated_by_dta' => 40,
            'validated_by_direction' => 50,
            'validated_by_service' => 60,
            'validated_by_dta' => 70,
            'validated_by_dg' => 80
        ];
        
        $progress = $steps[$position] ?? 0;
    @endphp
    
    <div class="d-flex align-items-center">
        <div class="flex-grow-1">
            <i class="fas fa-map-marker-alt"></i> {{ $positionText }}
            
            {{-- Barre de progression --}}
            <div class="progress mt-2" style="height: 8px;">
                <div class="progress-bar bg-info" role="progressbar" 
                     style="width: {{ $progress }}%;" 
                     aria-valuenow="{{ $progress }}" 
                     aria-valuemin="0" 
                     aria-valuemax="100">
                </div>
            </div>
            <small class="text-muted">{{ $progress }}% @lang('trans.completed')</small>
        </div>
        
        {{-- Icône selon l'état --}}
        <div class="ms-3">
            @if($position === 'validated_by_dg')
                <i class="fas fa-flag-checkered fa-2x text-success"></i>
            @elseif(strpos($position, 'rejected') !== false)
                <i class="fas fa-ban fa-2x text-danger"></i>
            @elseif(strpos($position, 'validated') !== false)
                <i class="fas fa-check-circle fa-2x text-success"></i>
            @elseif(strpos($position, 'annotated') !== false)
                <i class="fas fa-edit fa-2x text-primary"></i>
            @else
                <i class="fas fa-hourglass-half fa-2x text-warning"></i>
            @endif
        </div>
    </div>
</div>

@if($user->hasRole('dg'))
    <!-- Actions pour DG -->
    @if($etat->compagnie_cree_demande && !$etat->dg_annoter && !$etat->dta_dg_annoter && !$etat->dg_rejeter)
        <form action="{{ route('update-state', $demande->id) }}" method="POST" class="d-inline">
            @csrf
            <input type="hidden" name="action" value="dg_annoter">
            <input type="hidden" name="is_approved" value="1">
            <button type="submit" class="btn btn-success btn-sm mb-1"
                onclick="return confirm('Confirmer l\'annotation ?')">
                <i class="fas fa-check-circle"></i> @lang('trans.annotate')
            </button>
        </form>
        <button type="button" class="btn btn-danger btn-sm mb-1"
            onclick="openRejectionModal('demande_autorisations','{{ $demande->id }}')">
            <i class="fas fa-times-circle"></i> @lang('trans.reject')
        </button>
    @endif

    @if($etat->dta_valider && 
        !$etat->dg_valider && 
        !$etat->dta_dg_valider)
        
        @if($demande->vols->isNotEmpty() && $demande->avions->isNotEmpty())
            <form action="{{ route('update-state', $demande->id) }}" method="POST" class="d-inline">
                @csrf
                <input type="hidden" name="action" value="dg_valider">
                <input type="hidden" name="is_approved" value="1">

                <input type="hidden" name="type_autorisation" value="{{ $demande->type->libelle }}">
                <input type="hidden" name="type_autorisation_id" value="{{ $demande->type->id }}">
                <input type="hidden" name="type_vol_id" value="{{ $demande->typeVol->id }}">
                
                <button type="submit" class="btn btn-success btn-sm mb-1"
                    onclick="return confirm('Confirmer la validation ?')">
                    <i class="fas fa-check"></i> @lang('trans.validate')
                </button>
            </form>
        @endif
    @endif

@elseif($user->hasRole('dta'))
    <!-- Actions pour DTA -->
    @if($etat->compagnie_cree_demande && !$etat->dg_annoter && !$etat->dta_dg_annoter && !$etat->dg_rejeter)
        <form action="{{ route('update-state', $demande->id) }}" method="POST" class="d-inline">
            @csrf
            <input type="hidden" name="action" value="dta_dg_annoter">
            <input type="hidden" name="is_approved" value="1">
            <button type="submit" class="btn btn-success btn-sm mb-1"
                onclick="return confirm('Confirmer l\'annotation DG ?')">
                <i class="fas fa-check-circle"></i> @lang('trans.annotate_dg')
            </button>
        </form>
    @endif

    @if($etat->compagnie_cree_demande && 
        ($etat->dg_annoter || $etat->dta_dg_annoter) && 
        !$etat->dta_annoter && 
        !$etat->dg_rejeter && 
        !$etat->service_tout_valider)
        @if(!$demande->isFullyValidated())
        <form action="{{ route('update-state', $demande->id) }}" method="POST" class="d-inline">
            @csrf
            <input type="hidden" name="action" value="dta_annoter">
            <input type="hidden" name="is_approved" value="1">
            <button type="submit" class="btn btn-success btn-sm mb-1"
                onclick="return confirm('Confirmer l\'annotation ?')">
                <i class="fas fa-check-circle"></i> @lang('trans.annotate')
            </button>
        </form>
        @endif
        @if(!$etat->dta_valider)
        <button type="button" class="btn btn-danger btn-sm mb-1"
            onclick="openRejectionModal('demande_autorisations','{{ $demande->id }}')">
            <i class="fas fa-times-circle"></i> @lang('trans.reject')
        </button>
        @endif
    @endif
    @if(!$etat->dta_valider && ($demande?->hasRejectionReasons() ?? false))
        <form action="{{ route('update-state', $demande->id) }}" method="POST" class="d-inline">
            @csrf
            <input type="hidden" name="action" value="dta_notifier">
            <input type="hidden" name="is_approved" value="1">
            <button type="submit" class="btn btn-warning btn-sm mb-1"
                onclick="return confirm('Confirmer la notification ?')">
                <i class="fas fa-bell"></i> @lang('trans.notify')
            </button>
        </form>
    @endif

    @if($etat->compagnie_cree_demande && 
        ($etat->dg_annoter || $etat->dta_dg_annoter) && $etat->dta_annoter && !$etat->service_valider)
        <form action="{{ route('update-state', $demande->id) }}" method="POST" class="d-inline">
            @csrf
            <input type="hidden" name="action" value="service_valider">
            <input type="hidden" name="is_approved" value="1">
            <button type="submit" class="btn btn-success btn-sm mb-1"
                onclick="return confirm('Confirmer la validation service ?')">
                <i class="fas fa-check-double"></i> @lang('trans.validate_service')
            </button>
        </form>
    @endif

    @if($etat->compagnie_cree_demande && 
        ($etat->dg_annoter || $etat->dta_dg_annoter) && 
        !$etat->dta_annoter && 
        !$etat->service_tout_valider)
        {{-- !$etat->service_annoter &&  --}}
        <form action="{{ route('update-state', $demande->id) }}" method="POST" class="d-inline">
            @csrf
            <input type="hidden" name="action" value="service_tout_valider">
            <input type="hidden" name="is_approved" value="1">
            <button type="submit" class="btn btn-success btn-sm mb-1"
                onclick="return confirm('Valider à la place des directions ?')">
                <i class="fas fa-user-check"></i> @lang('trans.validate_all')
            </button>
        </form>
    @endif

    @if($etat->compagnie_cree_demande && 
        ($etat->dg_annoter || $etat->dta_dg_annoter) && $demande->isValidatedByAll() && $demande->isFullyValidated() && 
        !$etat->dta_valider)
        
        <form action="{{ route('update-state', $demande->id) }}" method="POST" class="d-inline">
            @csrf
            <input type="hidden" name="action" value="dta_valider">
            <input type="hidden" name="is_approved" value="1">
            <button type="submit" class="btn btn-success btn-sm mb-1"
                onclick="return confirm('Confirmer la validation DTA ?')">
                <i class="fas fa-check"></i> @lang('trans.validate')
            </button>
        </form>
    @endif

    @if(!$etat->dg_valider && !$etat->dta_dg_valider && $etat->dta_valider)
        @if($demande->vols->isNotEmpty() && $demande->avions->isNotEmpty())
            <form action="{{ route('update-state', $demande->id) }}" method="POST" class="d-inline">
                @csrf
                <input type="hidden" name="action" value="dta_dg_valider">
                <input type="hidden" name="is_approved" value="1">
                
                <input type="hidden" name="type_autorisation" value="{{ $demande->type->libelle }}">
                <input type="hidden" name="type_autorisation_id" value="{{ $demande->type->id }}">
                <input type="hidden" name="type_vol_id" value="{{ $demande->typeVol->id }}">
                
                <button type="submit" class="btn btn-success btn-sm mb-1"
                    onclick="return confirm('Confirmer la validation DG par DTA ?')">
                    <i class="fas fa-check"></i> @lang('trans.validate_dg')
                </button>
            </form>
        @endif
    @endif

    @if(!empty($demande->autorisation($demande->id)))
        <a target="_blank" href="{{ route('autorisations.print', $demande->autorisation($demande->id)) }}"
           class="btn btn-warning btn-sm mb-1">
            <i class="fas fa-print"></i> @lang('trans.print')
        </a>
    @endif

@elseif($user->hasRole(['dsv', 'dsna', 'dsad']))
    <!-- Actions pour les directions (DSV, DSNA, DSAD) -->
    @php
        $directionRole = $user->getRoleNames()->first();
        $validationAction = $directionRole . '_valider';
    @endphp
    
    @if($etat->compagnie_cree_demande && 
        ($etat->dg_annoter || $etat->dta_dg_annoter) && 
        $etat->service_annoter && 
        !$etat->{$validationAction} && 
        $demande->isAnnotedTo($directionRole))
        
        <form action="{{ route('update-state', $demande->id) }}" method="POST" class="d-inline">
            @csrf
            <input type="hidden" name="action" value="{{ $validationAction }}">
            <input type="hidden" name="is_approved" value="1">
            <button type="submit" class="btn btn-success btn-sm mb-1"
                onclick="return confirm('Confirmer la validation ?')">
                <i class="fas fa-check"></i> @lang('trans.validate')
            </button>
        </form>
        
        <button type="button" class="btn btn-danger btn-sm mb-1"
            onclick="openAchievementModal('demande_autorisations','{{ $demande->id }}')">
            <i class="fas fa-flag-checkered"></i> @lang('trans.achieve')
        </button>
    @endif
@endif