<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class InterruptionDemandeur extends Model
{
    use HasFactory;
    protected $fillable = [
        'demande_id',
        'date_debut',
        'date_fin',
        'raison',
        'document'
    ];

    public function demande()
    {
        return $this->belongsTo(Demande::class, 'demande_id');
    }
}
