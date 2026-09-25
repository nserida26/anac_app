<?php

namespace App\Services;

use App\Models\Activity;
use App\Models\Demande;
use App\Models\TypeDemande;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Log;

/**
 * Règles de changement du type d'une demande de licence, communes à
 * l'utilisateur et à l'administration.
 */
class ChangementTypeDemandeService
{
    /** Types réservés à l'administration : validation (7) et carte stagiaire (8). */
    public const TYPES_RESERVES_ADMIN = [7, 8];

    /** Types proposés à l'utilisateur. */
    public function typesDisponiblesPourUtilisateur()
    {
        return TypeDemande::whereNotIn('id', self::TYPES_RESERVES_ADMIN)->orderBy('id')->get();
    }

    /** L'utilisateur peut modifier le type tant qu'il n'a pas soumis sa demande. */
    public function modifiableParUtilisateur(Demande $demande): bool
    {
        return !optional($demande->etatDemande)->demandeur_cree_demande;
    }

    /**
     * Renvoie le motif du refus, ou null si le changement est autorisé.
     */
    public function verifier(Demande $demande, TypeDemande $nouveauType, bool $parAdmin): ?string
    {
        if ((int) $demande->type_demande_id === (int) $nouveauType->id) {
            return __('trans.type_demande_identique');
        }

        if (!$parAdmin) {
            if (!$this->modifiableParUtilisateur($demande)) {
                return __('trans.type_demande_deja_soumise');
            }
            if (in_array($nouveauType->id, self::TYPES_RESERVES_ADMIN)) {
                return __('trans.type_invalid');
            }
        }

        if ($demande->licence()->exists() || $demande->validation()->exists() || $demande->carteStagiare()->exists()) {
            return __('trans.type_demande_document_genere');
        }

        // Le tarif de l'ordre de recette dépend du type de demande.
        if ($demande->ordre()->exists() || $demande->facture()->exists() || $demande->paiement()->exists()) {
            return __('trans.type_demande_deja_facturee');
        }

        if ((int) $nouveauType->id === 1 && $demande->demandeur->detientLicenceDeType($demande->typeLicence->nom)) {
            return __('trans.licence_deja_detenue', ['type' => $demande->typeLicence->nom]);
        }

        return null;
    }

    public function changer(Demande $demande, TypeDemande $nouveauType): void
    {
        $ancienTypeId = $demande->type_demande_id;

        $demande->type_demande_id = $nouveauType->id;
        $demande->save();

        Activity::log('changer_type_demande', $demande->id);
        Log::info('Type de demande mis à jour', [
            'demande_id' => $demande->id,
            'ancien_type_id' => $ancienTypeId,
            'nouveau_type_id' => $nouveauType->id,
            'user_id' => Auth::id(),
        ]);
    }
}
