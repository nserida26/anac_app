<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class MedicalExamination extends Model
{
    use HasFactory;

    protected $fillable = [
        'date_examen',
        'centre_medical_id',
        'validite',
        'demande_id',
        'document'
    ];

    public function demande()
    {
        return $this->belongsTo(Demande::class);
    }
}
