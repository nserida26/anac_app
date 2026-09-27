<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

/**
 * Rapport médical d'une visite (rapport + attestation), envoyé par un
 * examinateur médical individuel ou par un centre d'expertise médicale pour
 * l'un de ses examinateurs. Circuit : examinateur (transmission) → évaluateur
 * (avis) → SMA (validation). Visible seulement par ces acteurs
 * (voir App\Policies\ExamenMedicalPolicy) ; fichiers sur le disque « prive ».
 */
class ExamenMedical extends Model
{
    use HasFactory;

    public const DISQUE = 'prive';

    /** Documents téléchargeables, par nom de colonne. */
    public const DOCUMENTS = ['rapport', 'attestation', 'rapport_evaluateur'];

    public const AVIS = ['valide', 'reserve', 'suggestion'];

    protected $table = 'examens_medicaux';

    protected $fillable = [
        'demandeur_id', 'evaluateur_id', 'examinateur_id', 'centre_medical_id', 'soumis_par',
        'date_examen', 'aptitude', 'rapport', 'rapport_evaluateur', 'attestation',
        'valider_examinateur', 'valider_evaluateur', 'valider_sma',
        'validite', 'validite_evaluateur', 'avis_evaluateur', 'observations_evaluateur',
    ];

    protected $casts = [
        'valider_examinateur' => 'boolean',
        'valider_evaluateur' => 'boolean',
        'valider_sma' => 'boolean',
    ];

    public function demandeur()
    {
        return $this->belongsTo(Demandeur::class, 'demandeur_id');
    }

    public function examinateur()
    {
        return $this->belongsTo(Examinateur::class, 'examinateur_id');
    }

    public function evaluateur()
    {
        return $this->belongsTo(Evaluateur::class, 'evaluateur_id');
    }

    /** Centre d'expertise médicale ayant envoyé le rapport (vide si examinateur individuel). */
    public function centreMedical()
    {
        return $this->belongsTo(CentreMedical::class, 'centre_medical_id');
    }

    public function soumisPar()
    {
        return $this->belongsTo(User::class, 'soumis_par');
    }

    /** Transmis à l'ANAC : plus modifiable par son auteur. */
    public function estTransmis(): bool
    {
        return (bool) $this->valider_examinateur;
    }

    public function scopeTransmis($query)
    {
        return $query->where('valider_examinateur', true);
    }

    /** Rapports d'un examinateur individuel (pas ceux envoyés par un centre pour lui). */
    public function scopeDeLExaminateurIndividuel($query, Examinateur $examinateur)
    {
        return $query->where('examinateur_id', $examinateur->id)->whereNull('centre_medical_id');
    }

    public function scopeDuCentre($query, CentreMedical $centre)
    {
        return $query->where('centre_medical_id', $centre->id);
    }
}
