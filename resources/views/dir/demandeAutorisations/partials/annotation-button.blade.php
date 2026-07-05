{{-- resources/views/dir/demandeAutorisations/partials/annotation-button.blade.php --}}
@php
    $etat = optional($demande->etatDemande);
    $showAnnotationButton = $etat->compagnie_cree_demande && 
                          ($etat->dg_annoter || $etat->dta_dg_annoter || $etat->dg_annoter_admin) && 
                          !$etat->dta_valider && 
                          !$etat->service_tout_valider &&
                          !$etat->dg_rejeter;
    
    // Récupérer les directions actuellement annotées
    $annotatedDirections = json_decode($demande->directions_annotees) ?? [];
@endphp

@if($showAnnotationButton)
    <button type="button" class="btn btn-primary btn-sm mb-1 annotation-btn"
            data-toggle="modal" 
            data-target="#annotationModal"
            data-demande-id="{{ $demande->id }}"
            data-demande-code="{{ $demande->code }}">
        <i class="fas fa-share-alt"></i> @lang('trans.forward_to_directions')
    </button>
@endif

@if(!$demande->isValidatedByAll() && $etat->service_annoter && !empty($annotatedDirections))
    <button type="button" 
            class="btn btn-warning btn-sm mb-1 btn-retrait"
            onclick="openRetraitModalWithData({{ $demande->id }}, '{{ $demande->code }}', {{ json_encode($annotatedDirections) }})">
        <i class="fas fa-undo"></i> @lang('trans.backward')
    </button>
@endif

