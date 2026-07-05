<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class PaiementAutorisation extends Model
{
    protected $fillable = [
        'reference',
        'demande_autorisation_id',
        'user_id',
        'methode',
        'montant_total',
        'statut',
        'date_paiement',
        'justificatif',
        'signature_dg',
        'signature_daf',
        'cachet_dg',
        'cachet_daf',
        'dg_signataire',
        'daf_signataire'
    ];

    public function user()
    {
        return $this->belongsTo(User::class, 'user_id');
    }

    public function demande()
    {
        return $this->belongsTo(DemandeAutorisation::class, 'demande_autorisation_id');
    }
}
