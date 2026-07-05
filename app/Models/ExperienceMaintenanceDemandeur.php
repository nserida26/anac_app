<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class ExperienceMaintenanceDemandeur extends Model
{
    use HasFactory;
    protected $fillable = [
        'demande_id',
        'description_maintenance',
        'date_debut',
        'date_fin',
        'document'
    ];

    public function demande()
    {
        return $this->belongsTo(Demande::class, 'demande_id');
    }
}
