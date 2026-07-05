<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Facture extends Model
{
    protected $fillable = ['demande_id', 'montant', 'statut', 'date_facture', 'facture','reference','date_limite'];

    public function demande()
    {
        return $this->belongsTo(Demande::class, 'demande_id');
    }
}

