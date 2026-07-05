<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class TypeDemandeAutorisation extends Model
{
    use HasFactory;

    protected $table = 'type_demande_autorisations';

    protected $fillable = [
        'libelle',
        'description'
    ];


    public function typesDocuments()
    {
        return $this->hasMany(TypeDocumentAutorisation::class, 'type_demande_autorisation_id');
    }
    public function demandes()
    {
        return $this->hasMany(DemandeAutorisation::class, 'type_demande_id');
    }
}
