<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class TypeDocumentAutorisation extends Model
{
    use HasFactory;

    protected $table = 'type_document_autorisations';

    // Formulaire : sélection multiple de type_vol_id/type_demande_autorisation_id,
    // une ligne est enregistrée par combinaison (voir TypeDocumentAutorisationController).
    static $rules = [
        'type_vol_id' => 'required|array|min:1',
        'type_vol_id.*' => 'exists:type_vols,id',
        'type_demande_autorisation_id' => 'required|array|min:1',
        'type_demande_autorisation_id.*' => 'exists:type_demande_autorisations,id',
        'nom_fr' => 'required|string|max:100',
        'nom_en' => 'required|string|max:100',
    ];

    protected $fillable = [
        'type_vol_id',
        'type_demande_autorisation_id',
        'nom_fr',
        'nom_en'
    ];


    /**
     * Get the flight type
     */
    public function typeVol()
    {
        return $this->belongsTo(TypeVol::class);
    }

    /**
     * Get the authorization type
     */
    public function typeDemande()
    {
        return $this->belongsTo(TypeDemandeAutorisation::class, 'type_demande_autorisation_id');
    }

    /**
     * Get all documents of this type
     */
    public function documents()
    {
        return $this->hasMany(DocumentAutorisation::class, 'type_document_id');
    }
}
