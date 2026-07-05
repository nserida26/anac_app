<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class OrdreRecette extends Model
{
    protected $table = 'ordres_recette';
    protected $fillable = ['demande_id', 'montant', 'date_ordre', 'statut', 'ordre', 'reference'];

    public function demande()
    {
        return $this->belongsTo(Demande::class, 'demande_id');
    }
}
