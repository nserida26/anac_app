<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

/**
 * Centre médical cité dans les visites médicales des demandes de licence.
 * Un centre d'expertise médicale certifié (ex. CEMPA) y est rattaché à un
 * compte (user_id, rôle centre_medical).
 */
class CentreMedical extends Model
{
    use HasFactory;

    protected $fillable = [
        'libelle',
        'user_id',
    ];

    public $timestamps = false;

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function examinateurs()
    {
        return $this->hasMany(Examinateur::class, 'centre_medical_id');
    }

    /** Examinateurs médicaux déclarés par ce centre (soumis à la validation de l'ANAC). */
    public function examinateursDeclares()
    {
        return $this->examinateurs()->declares();
    }

    public function medecins()
    {
        return $this->hasMany(MedecinCentre::class, 'centre_medical_id');
    }
}
