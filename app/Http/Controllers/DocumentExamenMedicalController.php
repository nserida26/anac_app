<?php

namespace App\Http\Controllers;

use App\Models\ExamenMedical;
use Illuminate\Support\Facades\Storage;

/**
 * Seul accès aux fichiers d'un rapport médical (disque privé) : les droits sont
 * vérifiés à chaque demande par ExamenMedicalPolicy::view.
 */
class DocumentExamenMedicalController extends Controller
{
    public function __invoke(ExamenMedical $examen, string $document)
    {
        abort_unless(in_array($document, ExamenMedical::DOCUMENTS, true) && $examen->{$document}, 404);
        $this->authorize('view', $examen);

        $disque = Storage::disk(ExamenMedical::DISQUE);
        abort_unless($disque->exists($examen->{$document}), 404);

        // Affiché dans le navigateur (visionneuse PDF / image), jamais mis en cache public.
        return $disque->response($examen->{$document}, null, [
            'Cache-Control' => 'private, no-store',
        ]);
    }
}
