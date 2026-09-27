<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

/**
 * Médecin déclaré par un centre d'expertise médicale sur son compte.
 */
class MedecinCentre extends Model
{
    protected $table = 'medecins_centre';

    protected $fillable = [
        'centre_medical_id',
        'nom',
        'prenom',
        'email',
        'telephone',
        'numero_ordre',
        'date_naissance',
        'nationalite',
        'adresse',
        'document_justificatif',
        'statut',
    ];

    protected $casts = [
        'date_naissance' => 'date',
    ];

    public function centreMedical()
    {
        return $this->belongsTo(CentreMedical::class);
    }

    public function getNomCompletAttribute()
    {
        return $this->nom . ' ' . $this->prenom;
    }
}
