<?php

namespace App\Services;

use App\Models\Demandeur;
use App\Models\DesignationExaminateur;
use App\Models\TypeLicence;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;

/**
 * Règles de l'examinateur désigné par l'ANAC : un détenteur de licence valide,
 * déjà instructeur, désigné pour des types de licence et une période donnés.
 */
class DesignationExaminateurService
{
    /**
     * Désigne le demandeur ; renvoie le motif du refus, ou null si la désignation est créée.
     */
    public function designer(Demandeur $demandeur, string $dateDebut, string $dateFin, array $typesLicenceIds): ?string
    {
        if (!$demandeur->is_instructeur) {
            return __('trans.designation_instructeur_requis');
        }
        if (!$demandeur->detientLicenceValide()) {
            return __('trans.designation_licence_valide_requise');
        }

        DB::transaction(function () use ($demandeur, $dateDebut, $dateFin, $typesLicenceIds) {
            $designation = DesignationExaminateur::create([
                'demandeur_id' => $demandeur->id,
                'date_debut' => $dateDebut,
                'date_fin' => $dateFin,
                'designe_par' => Auth::id(),
            ]);
            $designation->typesLicence()->sync($typesLicenceIds);
        });

        return null;
    }

    public function retirer(DesignationExaminateur $designation): void
    {
        $designation->update(['retiree_le' => now(), 'retiree_par' => Auth::id()]);
    }

    /**
     * Un détenteur peut-il enregistrer cet examen / cette formation en cette qualité ?
     * Renvoie le motif du refus, ou null si c'est permis.
     */
    public function verifierFormation(Demandeur $formateur, string $qualite, string $date, ?int $typeLicenceId): ?string
    {
        if ($qualite === 'instructeur') {
            return $formateur->is_instructeur ? null : __('trans.pas_instructeur');
        }

        if (!$typeLicenceId) {
            return __('trans.type_licence_requis_examen');
        }
        if (!$formateur->estExaminateurDesigne($date, $typeLicenceId)) {
            return __('trans.examen_hors_designation', ['type' => optional(TypeLicence::find($typeLicenceId))->nom]);
        }

        return null;
    }
}
