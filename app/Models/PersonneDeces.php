<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class PersonneDeces extends Model
{
    use HasFactory;

    /**
     * The table associated with the model.
     *
     * @var string
     */
    protected $table = 'personne_deces';

    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = [
        'demande_autorisation_id',
        'nom_prenom',
        'numero_passport',
        'justificatif',
        'valider',
        'motif'
    ];

    /**
     * Get the demande autorisation that owns the deceased person.
     */
    public function demandeAutorisation()
    {
        return $this->belongsTo(DemandeAutorisation::class);
    }
}