<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class AssistanceEscalePea extends Model
{
    use HasFactory;

    protected $table = 'assistance_escale_pea';

    protected $fillable = [
        'demande_autorisation_id',
        'structure_assistance',
        'etat_pea',
        'renseignements_divers'
    ];

    public function demandeAutorisation()
    {
        return $this->belongsTo(DemandeAutorisation::class);
    }
}
