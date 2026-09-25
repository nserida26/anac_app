<?php

namespace App\Services;

use App\Models\CompetenceDemandeur;
use App\Models\Demande;
use Carbon\Carbon;

/**
 * Source unique de la règle de calcul de la date d'expiration d'une licence :
 * la plus proche entre l'expiration des qualifications et celle du contrôle
 * de compétence linguistique (ELP) le plus récent.
 */
class LicenceExpirationService
{
    /** Types de licence dont les qualifications sont valables 24 mois (12 sinon). */
    private const TYPES_LICENCE_QUALIFICATION_24_MOIS = [35, 36, 37, 38];

    public function calculer(Demande $demande): ?Carbon
    {
        $dates = array_filter([
            $this->expirationQualifications($demande),
            $this->expirationCompetenceLinguistique($demande),
        ]);

        return empty($dates) ? null : min($dates);
    }

    /** Expiration la plus lointaine parmi les qualifications de la demande. */
    public function expirationQualifications(Demande $demande): ?Carbon
    {
        $mois = in_array($demande->type_licence_id, self::TYPES_LICENCE_QUALIFICATION_24_MOIS) ? 24 : 12;

        return $demande->qualifications
            ->map(fn ($qualification) => Carbon::parse($qualification->date_examen)->addMonths($mois)->endOfMonth())
            ->max();
    }

    /**
     * Expiration de l'ELP le plus récent, celui affiché sur l'authentification.
     * Null si aucun ELP ou si le niveau le plus récent n'expire pas (niveau 6).
     */
    public function expirationCompetenceLinguistique(Demande $demande): ?Carbon
    {
        return optional($this->competenceLinguistiqueCourante($demande))->date_expiration;
    }

    public function competenceLinguistiqueCourante(Demande $demande): ?CompetenceDemandeur
    {
        return $demande->competences
            ->where('type', CompetenceDemandeur::TYPE_LINGUISTIQUE)
            ->sortByDesc(fn ($competence) => $competence->date . '|' . str_pad($competence->id, 10, '0', STR_PAD_LEFT))
            ->first();
    }
}
