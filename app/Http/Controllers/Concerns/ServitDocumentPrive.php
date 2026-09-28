<?php

namespace App\Http\Controllers\Concerns;

use Illuminate\Support\Facades\Storage;

/**
 * Délivre un fichier d'un disque privé (après vérification des droits par l'appelant) :
 * affiché dans le navigateur, jamais mis en cache public.
 */
trait ServitDocumentPrive
{
    protected function servirDocumentPrive(string $disque, ?string $chemin)
    {
        abort_unless($chemin && Storage::disk($disque)->exists($chemin), 404);

        return Storage::disk($disque)->response($chemin, null, [
            'Cache-Control' => 'private, no-store',
        ]);
    }
}
