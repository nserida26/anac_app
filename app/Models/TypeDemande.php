<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

/**
 * Class TypeDemande
 *
 * @property $id
 * @property $nom_en
 * @property $nom_fr
 *
 * @property Demande[] $demandes
 * @property TypeDocument[] $typeDocuments
 * @package App
 * @mixin \Illuminate\Database\Eloquent\Builder
 */
class TypeDemande extends Model
{

    static $rules = [
        'nom_en' => 'required',
        'nom_fr' => 'required',
    ];

    protected $perPage = 20;

    /**
     * Attributes that should be mass-assignable.
     *
     * @var array
     */
    protected $fillable = ['nom_en', 'nom_fr'];


    /**
     * @return \Illuminate\Database\Eloquent\Relations\HasMany
     */
    public function demandes()
    {
        return $this->hasMany('App\Models\Demande', 'type_demande_id', 'id');
    }

    /**
     * @return \Illuminate\Database\Eloquent\Relations\HasMany
     */
    public function typeDocuments()
    {
        return $this->hasMany('App\Models\TypeDocument', 'type_demande_id', 'id');
    }
}
