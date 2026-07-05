<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class FormationDemandeur extends Model
{
    use HasFactory;
    protected $fillable = [
        'date_formation',
        'centre_formation_id',
        'demande_id',
        'lieu',
        'document'
    ];

    public function demande()
    {
        return $this->belongsTo(Demande::class);
    }
}
