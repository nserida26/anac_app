<?php

namespace App\Http\Controllers;

use App\Http\Controllers\Concerns\ServitDocumentPrive;
use App\Models\ExamenMedical;

/**
 * Seul accès aux fichiers d'un rapport médical (disque privé) : les droits sont
 * vérifiés à chaque demande par ExamenMedicalPolicy::view.
 */
class DocumentExamenMedicalController extends Controller
{
    use ServitDocumentPrive;

    public function __invoke(ExamenMedical $examen, string $document)
    {
        abort_unless(in_array($document, ExamenMedical::DOCUMENTS, true) && $examen->{$document}, 404);
        $this->authorize('view', $examen);

        return $this->servirDocumentPrive(ExamenMedical::DISQUE, $examen->{$document});
    }
}
