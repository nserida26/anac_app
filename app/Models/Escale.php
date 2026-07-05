<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Escale extends Model
{
    protected $fillable = [
        'vol_id', 'aeroport_id', 'date_arrivee', 'date_depart', 'ordre'
    ];
    
    public function vol()
    {
        return $this->belongsTo(Vol::class);
    }
    
    public function aeroport()
    {
        return $this->belongsTo(Aeroport::class);
    }
}