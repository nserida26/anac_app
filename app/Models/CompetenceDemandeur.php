<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class CompetenceDemandeur extends Model
{
    use HasFactory;

    protected $fillable = [
        'type',
        'date',
        'validite',
        'centre_formation_id',
        'niveau',
        'demande_id',
        'document'
    ];

    public function demande()
    {
        return $this->belongsTo(Demande::class);
    }
}
