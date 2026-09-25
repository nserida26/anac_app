<?php

namespace App\Services;

use App\Models\Activity;
use App\Models\Demande;
use App\Models\TypeDemande;
use App\Models\TypeLicence;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Log;

/**
 * Règles de modification du type de demande et du type de licence d'une
 * demande de licence, communes à l'utilisateur et à l'administration.
 */
class ModificationDemandeService
{
    /** Types de demande réservés à l'administration : validation (7) et carte stagiaire (8). */
    public const TYPES_DEMANDE_RESERVES_ADMIN = [7, 8];

    /** Types de demande proposés à l'utilisateur. */
    public function typesDemandePourUtilisateur()
    {
        return TypeDemande::whereNotIn('id', self::TYPES_DEMANDE_RESERVES_ADMIN)->orderBy('id')->get();
    }

    /** L'utilisateur peut modifier sa demande tant qu'il ne l'a pas soumise. */
    public function modifiableParUtilisateur(Demande $demande): bool
    {
        return !optional($demande->etatDemande)->demandeur_cree_demande;
    }

    /**
     * Renvoie le motif du refus, ou null si le changement est autorisé.
     */
    public function verifierTypeDemande(Demande $demande, TypeDemande $nouveauType, bool $parAdmin): ?string
    {
        if ((int) $demande->type_demande_id === (int) $nouveauType->id) {
            return __('trans.type_demande_identique');
        }
        if (!$parAdmin && in_array($nouveauType->id, self::TYPES_DEMANDE_RESERVES_ADMIN)) {
            return __('trans.type_invalid');
        }

        return $this->verifierEtat($demande, $parAdmin)
            ?? $this->verifierUniciteLicence($demande, (int) $nouveauType->id, $demande->typeLicence);
    }

    /**
     * Renvoie le motif du refus, ou null si le changement est autorisé.
     */
    public function verifierTypeLicence(Demande $demande, TypeLicence $nouveauType, bool $parAdmin): ?string
    {
        if ((int) $demande->type_licence_id === (int) $nouveauType->id) {
            return __('trans.type_licence_identique');
        }

        $refus = $this->verifierEtat($demande, $parAdmin)
            ?? $this->verifierUniciteLicence($demande, (int) $demande->type_demande_id, $nouveauType);
        if ($refus) {
            return $refus;
        }

        $incompatibles = $this->qualificationsIncompatibles($demande, $nouveauType);
        if ($incompatibles->isNotEmpty()) {
            return __('trans.qualifications_incompatibles', [
                'type' => $nouveauType->nom,
                'qualifications' => $incompatibles->implode(', '),
            ]);
        }

        return null;
    }

    public function changerTypeDemande(Demande $demande, TypeDemande $nouveauType): void
    {
        $this->changer($demande, 'type_demande_id', $nouveauType->id, 'changer_type_demande');
    }

    public function changerTypeLicence(Demande $demande, TypeLicence $nouveauType): void
    {
        $this->changer($demande, 'type_licence_id', $nouveauType->id, 'changer_type_licence');
    }

    /** Libellés des qualifications saisies qui n'existent pas pour le type de licence donné. */
    public function qualificationsIncompatibles(Demande $demande, TypeLicence $typeLicence)
    {
        $autorisees = $typeLicence->qualifications()->pluck('qualifications.id');

        return $demande->qualifications()->with('qualification')->get()
            ->reject(fn ($qualification) => $autorisees->contains($qualification->qualification_id))
            ->map(fn ($qualification) => optional($qualification->qualification)->libelle ?? '#' . $qualification->qualification_id)
            ->unique()
            ->values();
    }

    /** Contrôles communs aux deux modifications. */
    private function verifierEtat(Demande $demande, bool $parAdmin): ?string
    {
        if (!$parAdmin && !$this->modifiableParUtilisateur($demande)) {
            return __('trans.type_demande_deja_soumise');
        }
        if ($demande->licence()->exists() || $demande->validation()->exists() || $demande->carteStagiare()->exists()) {
            return __('trans.type_demande_document_genere');
        }
        // Le tarif de l'ordre de recette dépend du type de demande et du type de licence.
        if ($demande->ordre()->exists() || $demande->facture()->exists() || $demande->paiement()->exists()) {
            return __('trans.type_demande_deja_facturee');
        }

        return null;
    }

    /** Une délivrance initiale (1) est impossible si le demandeur détient déjà une licence de ce type. */
    private function verifierUniciteLicence(Demande $demande, int $typeDemandeId, TypeLicence $typeLicence): ?string
    {
        if ($typeDemandeId === 1 && $demande->demandeur->detientLicenceDeType($typeLicence->nom)) {
            return __('trans.licence_deja_detenue', ['type' => $typeLicence->nom]);
        }

        return null;
    }

    private function changer(Demande $demande, string $champ, int $nouvelleValeur, string $action): void
    {
        $ancienneValeur = $demande->{$champ};

        $demande->{$champ} = $nouvelleValeur;
        $demande->save();

        Activity::log($action, $demande->id);
        Log::info('Demande de licence modifiée', [
            'demande_id' => $demande->id,
            'champ' => $champ,
            'ancienne_valeur' => $ancienneValeur,
            'nouvelle_valeur' => $nouvelleValeur,
            'user_id' => Auth::id(),
        ]);
    }
}
