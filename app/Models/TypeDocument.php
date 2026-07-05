<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

/**
 * Class TypeDocument
 *
 * @property $id
 * @property $type_licence_id
 * @property $type_demande_id
 * @property $nom_fr
 * @property $nom_en
 *
 * @property Document[] $documents
 * @property TypeDemande $typeDemande
 * @property TypeLicence $typeLicence
 * @package App
 * @mixin \Illuminate\Database\Eloquent\Builder
 */
class TypeDocument extends Model
{

    static $rules = [
        'nom_fr' => 'required',
        'nom_en' => 'required',
    ];

    protected $perPage = 20;

    /**
     * Attributes that should be mass-assignable.
     *
     * @var array
     */
    protected $fillable = ['type_licence_id', 'type_demande_id', 'nom_fr', 'nom_en'];


    /**
     * @return \Illuminate\Database\Eloquent\Relations\HasMany
     */
    public function documents()
    {
        return $this->hasMany('App\Models\Document', 'type_document_id', 'id');
    }

    /**
     * @return \Illuminate\Database\Eloquent\Relations\HasOne
     */
    public function typeDemande()
    {
        return $this->hasOne('App\Models\TypeDemande', 'id', 'type_demande_id');
    }

    /**
     * @return \Illuminate\Database\Eloquent\Relations\HasOne
     */
    public function typeLicence()
    {
        return $this->hasOne('App\Models\TypeLicence', 'id', 'type_licence_id');
    }
}
