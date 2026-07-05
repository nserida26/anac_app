<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class ExamenMedical extends Model
{
    use HasFactory;

    protected $table = 'examens_medicaux';

    protected $fillable = ['demandeur_id', 'evaluateur_id', 'examinateur_id', 'date_examen', 'aptitude', 'rapport', 'rapport_evaluateur', 'valider_examinateur', 'valider_evaluateur', 'valider_sma', 'validite_evaluateur', 'validite', 'attestation'];

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
}
