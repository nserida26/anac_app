<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Autorisation extends Model
{
    use HasFactory;
    protected $fillable = [
        'demande_id',
        'vol_id',
        'code_autorisation',
        'date_delivrance',
        'date_expiration',
        'statut',
        'signature_dg',
        'signature_dta',
        'signature_srta',
        'cachet',
        'nom_signataire'
    ];

    public function demande()
    {
        return $this->belongsTo(DemandeAutorisation::class, 'demande_id');
    }

    public function vol()
    {
        return $this->belongsTo(Vol::class);
    }
}
