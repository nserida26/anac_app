<?php

namespace App\Policies;

use App\Models\Formation;
use App\Models\User;
use Illuminate\Auth\Access\HandlesAuthorization;

/**
 * Le rapport d'examen d'une formation n'est visible que par son auteur (le centre
 * de formation, ou le détenteur de licence qui l'a enregistrée), par la personne
 * examinée (un détenteur qui renouvelle ses compétences ou qualifications) et par l'ANAC.
 */
class FormationPolicy
{
    use HandlesAuthorization;

    /** Rôles ANAC qui consultent les formations d'un demandeur (pages demande admin, direction, SMA/SLA). */
    public const ROLES_ANAC = ['admin', 'dg', 'dsv', 'sma', 'sla'];

    public function voirRapport(User $user, Formation $formation): bool
    {
        if ($user->hasAnyRole(self::ROLES_ANAC)) {
            return true;
        }

        // Centre de formation de la formation (y compris celui choisi par un détenteur)
        if ($formation->centre_formation_id && (int) optional($user->centreFormation)->id === (int) $formation->centre_formation_id) {
            return true;
        }

        $demandeurId = (int) optional($user->demandeur)->id;

        // Détenteur formateur (auteur) ou personne examinée
        return $demandeurId !== 0 && in_array($demandeurId, [(int) $formation->formateur_demandeur_id, (int) $formation->demandeur_id], true);
    }
}
