<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class ExperienceDemandeur extends Model
{
    use HasFactory;
    use HasFactory;

    protected $fillable = [
        'nature',
        'total',
        'six_mois',
        'trois_mois',
        'document',
        'demande_id',
    ];

    public function demande()
    {
        return $this->belongsTo(Demande::class);
    }
}
