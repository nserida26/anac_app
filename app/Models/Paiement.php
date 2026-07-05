<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Paiement extends Model
{
    use HasFactory;

    protected $table = 'paiements';
    protected $fillable = ['demande_id', 'montant', 'statut', 'date_paiement', 'quittance', 'reference'];

    public function demande()
    {
        return $this->belongsTo(Demande::class, 'demande_id');
    }
}
