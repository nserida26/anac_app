<?php

namespace App\Http\Controllers;

use App\Http\Controllers\Concerns\ServitDocumentPrive;
use App\Models\Formation;

/**
 * Seul accès au rapport d'examen d'une formation (disque privé) : les droits sont
 * vérifiés à chaque demande par FormationPolicy::voirRapport.
 */
class RapportFormationController extends Controller
{
    use ServitDocumentPrive;

    public function __invoke(Formation $formation)
    {
        abort_unless($formation->rapport, 404);
        $this->authorize('voirRapport', $formation);

        return $this->servirDocumentPrive(Formation::DISQUE_RAPPORT, $formation->rapport);
    }
}
