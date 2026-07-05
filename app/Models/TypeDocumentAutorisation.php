<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class TypeDocumentAutorisation extends Model
{
    use HasFactory;

    protected $table = 'type_document_autorisations';

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
