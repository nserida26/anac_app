<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Tarif extends Model
{
    protected $fillable = [
        'type_avion_id',
        'type_vol_id',
        'seuil_passagers',
        'prix_interieur',
        'prix_superieur'
    ];

    public function typeAvion()
    {
        return $this->belongsTo(TypeAvion::class);
    }

    public function typeVol()
    {
        return $this->belongsTo(TypeVol::class);
    }

    public function paiements()
    {
        return $this->hasMany(Paiement::class);
    }

    // Méthode pour calculer le montant selon le nombre de passagers
    public function calculerMontant($nbrPassagers)
    {
        return $nbrPassagers < $this->seuil_passagers
            ? $nbrPassagers * $this->prix_interieur
            : $nbrPassagers * $this->prix_superieur;
    }
}
