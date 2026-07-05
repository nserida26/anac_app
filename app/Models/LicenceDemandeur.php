<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class LicenceDemandeur extends Model
{
    use HasFactory;
    protected $fillable = [
        'demande_id',
        'date_licence',
        'lieu_delivrance',
        'autorite_id',
        'num_licence',
        'document'
    ];

    public function demande()
    {
        return $this->belongsTo(Demande::class, 'demande_id');
    }
    public function autorite()
    {
        return $this->belongsTo(Autorite::class, 'autorite_id');
    }
}
