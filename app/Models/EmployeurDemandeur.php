<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class EmployeurDemandeur extends Model
{
    use HasFactory;

    
    protected $fillable = [
        'demande_id',
        'employeur',
        'periode_du',
        'periode_au',
        'fonction',
        'document'
    ];
    
    public function demande()
    {
        return $this->belongsTo(Demande::class);
    }
}
