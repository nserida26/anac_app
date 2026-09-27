<?php

namespace App\Services;

use App\Models\CentreMedical;
use App\Models\ExamenMedical;
use App\Models\Examinateur;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Storage;

/**
 * Enregistrement des rapports médicaux, commun à l'examinateur individuel et
 * au centre d'expertise médicale. Les fichiers vont sur le disque privé.
 */
class RapportMedicalService
{
    /** Règles communes du formulaire (fichiers obligatoires à la création). */
    public static function regles(bool $creation): array
    {
        $fichier = ($creation ? 'required' : 'nullable') . '|file|mimes:pdf,jpg,jpeg,png|max:10240';

        return [
            'date_examen' => 'required|date|before_or_equal:today',
            'validite' => 'required|integer|min:1',
            'aptitude' => 'required|in:Apte,Inapte',
            'rapport' => $fichier,
            'attestation' => $fichier,
        ];
    }

    public function creer(array $donnees, UploadedFile $rapport, UploadedFile $attestation, Examinateur $examinateur, ?CentreMedical $centre = null): ExamenMedical
    {
        return ExamenMedical::create([
            'demandeur_id' => $donnees['demandeur_id'],
            'examinateur_id' => $examinateur->id,
            'centre_medical_id' => optional($centre)->id,
            'soumis_par' => Auth::id(),
            'date_examen' => $donnees['date_examen'],
            'validite' => $donnees['validite'],
            'aptitude' => $donnees['aptitude'],
            'rapport' => $this->stocker($rapport, 'rapports'),
            'attestation' => $this->stocker($attestation, 'attestations'),
        ]);
    }

    /** Mise à jour par l'auteur ; un fichier non fourni est conservé. */
    public function modifier(ExamenMedical $examen, array $donnees, ?UploadedFile $rapport, ?UploadedFile $attestation): void
    {
        $examen->fill(collect($donnees)->only(['date_examen', 'validite', 'aptitude'])->all());
        if ($rapport) {
            $this->remplacer($examen, 'rapport', $rapport, 'rapports');
        }
        if ($attestation) {
            $this->remplacer($examen, 'attestation', $attestation, 'attestations');
        }
        $examen->save();
    }

    public function supprimer(ExamenMedical $examen): void
    {
        Storage::disk(ExamenMedical::DISQUE)->delete(array_filter([$examen->rapport, $examen->attestation, $examen->rapport_evaluateur]));
        $examen->delete();
    }

    public function stocker(UploadedFile $fichier, string $dossier): string
    {
        return $fichier->store('examens_medicaux/' . $dossier, ExamenMedical::DISQUE);
    }

    private function remplacer(ExamenMedical $examen, string $colonne, UploadedFile $fichier, string $dossier): void
    {
        if ($examen->{$colonne}) {
            Storage::disk(ExamenMedical::DISQUE)->delete($examen->{$colonne});
        }
        $examen->{$colonne} = $this->stocker($fichier, $dossier);
    }
}
