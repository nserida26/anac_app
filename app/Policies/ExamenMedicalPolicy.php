<?php

namespace App\Policies;

use App\Models\ExamenMedical;
use App\Models\User;
use Illuminate\Auth\Access\HandlesAuthorization;

/**
 * Un rapport médical n'est visible que par son auteur (l'examinateur individuel,
 * ou le centre d'expertise médicale qui l'a envoyé), par l'évaluateur et par la
 * SMA ; chacun n'agit qu'à son étape du circuit.
 */
class ExamenMedicalPolicy
{
    use HandlesAuthorization;

    public function view(User $user, ExamenMedical $examen): bool
    {
        return $this->estAuteur($user, $examen)
            || ($examen->estTransmis() && $user->hasAnyRole(['evaluateur', 'sma']));
    }

    /** Modifier, supprimer ou transmettre : l'auteur, tant que le rapport n'est pas transmis. */
    public function update(User $user, ExamenMedical $examen): bool
    {
        return $this->estAuteur($user, $examen) && !$examen->estTransmis();
    }

    public function delete(User $user, ExamenMedical $examen): bool
    {
        return $this->update($user, $examen);
    }

    public function transmettre(User $user, ExamenMedical $examen): bool
    {
        return $this->update($user, $examen);
    }

    /** Avis de l'évaluateur : rapport transmis, pas encore validé par un évaluateur. */
    public function evaluer(User $user, ExamenMedical $examen): bool
    {
        return $user->hasRole('evaluateur') && $examen->estTransmis() && !$examen->valider_evaluateur;
    }

    /** La SMA relance tant que l'évaluateur n'a pas validé. */
    public function relancer(User $user, ExamenMedical $examen): bool
    {
        return $user->hasRole('sma') && $examen->estTransmis() && !$examen->valider_evaluateur;
    }

    public function validerSma(User $user, ExamenMedical $examen): bool
    {
        return $user->hasRole('sma') && $examen->valider_evaluateur && !$examen->valider_sma;
    }

    private function estAuteur(User $user, ExamenMedical $examen): bool
    {
        // Envoyé par un centre : seul le compte de ce centre en est l'auteur.
        if ($examen->centre_medical_id) {
            return $user->hasRole('centre_medical')
                && (int) optional($user->centreMedical)->id === (int) $examen->centre_medical_id;
        }

        return $user->hasRole('examinateur')
            && (int) optional($user->examinateur)->id === (int) $examen->examinateur_id;
    }
}
