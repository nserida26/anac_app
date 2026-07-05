<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class ReceivingParty extends Model
{
    use HasFactory;

    protected $table = 'receiving_parties';

    protected $fillable = [
        'nom_contact',
        'telephone_contact',
        'email_contact',
        'fonction_contact',
        'autres_renseignements',
        'piece_identite_path',
        'demande_autorisation_id',
        'valider',
        'motif'
    ];

    public function demandeAutorisation()
    {
        return $this->belongsTo(DemandeAutorisation::class);
    }
}
