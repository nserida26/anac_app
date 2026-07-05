{{-- resources/views/dir/demandeAutorisations/partials/role-actions.blade.php --}}
@php
    $etat = optional($demande->etatDemande);
    $user = auth()->user();
    $role = $user->getRoleNames()->first();
@endphp

@if($role == 'dg')
    {{-- Actions pour DG --}}
    @if($etat->compagnie_cree_demande && !$etat->dg_annoter && !$etat->dg_annoter_admin && !$etat->dta_dg_annoter  && !$etat->dg_rejeter )
        <button type="button" class="btn btn-primary btn-sm mb-1 dg-annotation-btn"
                data-toggle="modal" 
                data-target="#dgAnnotationModal"
                data-demande-id="{{ $demande->id }}"
                data-demande-code="{{ $demande->code }}">
            <i class="fas fa-user-tie"></i> @lang('trans.dg_annotate')
        </button>
        
        <button type="button" class="btn btn-danger btn-sm mb-1"
                onclick="openRejectionModal('demande_autorisations','{{ $demande->id }}')">
            <i class="fas fa-times-circle"></i> @lang('trans.reject')
        </button>
    @endif

    @if($etat->dta_valider && !$etat->dg_valider && !$etat->dta_dg_valider)
        @if($demande->vols->isNotEmpty() && $demande->avions->isNotEmpty())
            <form action="{{ route('update-state', $demande->id) }}" method="POST" class="d-inline">
                @csrf
                <input type="hidden" name="action" value="dg_valider">
                <input type="hidden" name="is_approved" value="1">
                <input type="hidden" name="type_autorisation" value="{{ $demande->type->libelle }}">
                <input type="hidden" name="type_autorisation_id" value="{{ $demande->type->id }}">
                <input type="hidden" name="type_vol_id" value="{{ $demande->typeVol->id }}">
                
                <button type="submit" class="btn btn-success btn-sm mb-1"
                        onclick="return confirm('@lang("trans.confirm_validation")')">
                    <i class="fas fa-check"></i> @lang('trans.validate')
                </button>
            </form>
        @endif
    @endif


@elseif($role == 'dta')
    {{-- Actions pour DTA --}}
    @if($etat->compagnie_cree_demande && !$etat->dg_annoter && !$etat->dta_dg_annoter && !$etat->dg_rejeter)
        <form action="{{ route('update-state', $demande->id) }}" method="POST" class="d-inline">
            @csrf
            <input type="hidden" name="action" value="dta_dg_annoter">
            <input type="hidden" name="is_approved" value="1">
            <button type="submit" class="btn btn-success btn-sm mb-1"
                    onclick="return confirm('@lang("trans.confirm_dg_annotation")')">
                <i class="fas fa-check-circle"></i> @lang('trans.annotate_dg')
            </button>
        </form>
    @endif
    

    @if($etat->compagnie_cree_demande && ($etat->dg_annoter || $etat->dta_dg_annoter) && !$etat->dta_annoter && !$etat->dg_rejeter && !$etat->dta_valider)
        <button type="button" class="btn btn-primary btn-sm mb-1 dta-annotation-btn"
                data-toggle="modal" 
                data-target="#dtaAnnotationModal"
                data-demande-id="{{ $demande->id }}"
                data-demande-code="{{ $demande->code }}">
            <i class="fas fa-share-alt"></i> @lang('trans.dta_annotate')
        </button>
        <button type="button" class="btn btn-danger btn-sm mb-1"
                    onclick="openRejectionModal('demande_autorisations','{{ $demande->id }}')">
                <i class="fas fa-times-circle"></i> @lang('trans.reject')
        </button>
    @endif

    @if(!$etat->dta_valider && ($demande?->hasRejectionReasons() ?? false))
        <form action="{{ route('update-state', $demande->id) }}" method="POST" class="d-inline">
            @csrf
            <input type="hidden" name="action" value="dta_notifier">
            <input type="hidden" name="is_approved" value="1">
            <button type="submit" class="btn btn-warning btn-sm mb-1"
                    onclick="return confirm('@lang("trans.confirm_notification")')">
                <i class="fas fa-bell"></i> @lang('trans.notify')
            </button>
        </form>
    @endif

    @if($etat->compagnie_cree_demande && ($etat->dg_annoter || $etat->dta_dg_annoter) && $etat->dta_annoter && !$etat->service_valider && !$etat->dta_valider)
        <form action="{{ route('update-state', $demande->id) }}" method="POST" class="d-inline">
            @csrf
            <input type="hidden" name="action" value="service_valider">
            <input type="hidden" name="is_approved" value="1">
            <button type="submit" class="btn btn-success btn-sm mb-1"
                    onclick="return confirm('@lang("trans.confirm_service_validation")')">
                <i class="fas fa-check-double"></i> @lang('trans.validate_service')
            </button>
        </form>
    @endif

    @if($etat->compagnie_cree_demande && ($etat->dg_annoter || $etat->dta_dg_annoter) && !$etat->dta_annoter && !$etat->service_tout_valider)
        <form action="{{ route('update-state', $demande->id) }}" method="POST" class="d-inline">
            @csrf
            <input type="hidden" name="action" value="service_tout_valider">
            <input type="hidden" name="is_approved" value="1">
            <button type="submit" class="btn btn-success btn-sm mb-1"
                    onclick="return confirm('@lang("trans.confirm_validate_all")')">
                <i class="fas fa-user-check"></i> @lang('trans.validate_all')
            </button>
        </form>
    @endif

    @if($etat->compagnie_cree_demande && ($etat->dg_annoter || $etat->dta_dg_annoter) && $demande->isValidatedByAll() && $demande->isFullyValidated() && !$etat->dta_valider)
        <form action="{{ route('update-state', $demande->id) }}" method="POST" class="d-inline">
            @csrf
            <input type="hidden" name="action" value="dta_valider">
            <input type="hidden" name="is_approved" value="1">
            <button type="submit" class="btn btn-success btn-sm mb-1"
                    onclick="return confirm('@lang("trans.confirm_dta_validation")')">
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
                        onclick="return confirm('@lang("trans.confirm_dg_validation")')">
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
<button type="button" class="btn btn-success btn-sm mb-1" 
                data-toggle="modal" 
                data-target="#sendNotificationModal-{{ $demande->id }}">
            <i class="fas fa-bell"></i> @lang('trans.send_notification')
        </button>
    @endif

@elseif(in_array($role, ['dsv', 'dsna', 'dsad', 'dsf']))
    {{-- Actions pour les directions --}}
    @php
        $validationAction = $role . '_valider';
    @endphp
    
    @if($etat->compagnie_cree_demande && 
        ($etat->dg_annoter || $etat->dta_dg_annoter || $etat->dg_annoter_admin) && 
        $etat->service_annoter && 
        !$etat->{$validationAction} && 
        $demande->isAnnotedTo($role))
        
        <form action="{{ route('update-state', $demande->id) }}" method="POST" class="d-inline">
            @csrf
            <input type="hidden" name="action" value="{{ $validationAction }}">
            <input type="hidden" name="is_approved" value="1">
            <button type="submit" class="btn btn-success btn-sm mb-1"
                    onclick="return confirm('@lang("trans.confirm_validation")')">
                <i class="fas fa-check"></i> @lang('trans.validate')
            </button>
        </form>
        
        <button type="button" class="btn btn-danger btn-sm mb-1"
                onclick="openAchievementModal('demande_autorisations','{{ $demande->id }}')">
            <i class="fas fa-flag-checkered"></i> @lang('trans.achieve')
        </button>
    @endif
@endif