<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

/**
 * Class Compagny
 *
 * @property $id
 * @property $nom_entreprise
 * @property $adresse
 * @property $panier
 * @property $user_id
 * @property $created_at
 * @property $updated_at
 *
 * @property Demandeur[] $demandeurs
 * @property User $user
 * @package App
 * @mixin \Illuminate\Database\Eloquent\Builder
 */
class Compagny extends Model
{

    static $rules = [
        'nom_entreprise' => 'required',
        'adresse' => 'required',
        'panier' => 'required',
        'user_id' => 'required'
    ];

    protected $perPage = 20;

    /**
     * Attributes that should be mass-assignable.
     *
     * @var array
     */
    protected $fillable = ['nom_entreprise', 'adresse', 'panier', 'user_id', 'plafond'];


    /**
     * @return \Illuminate\Database\Eloquent\Relations\HasMany
     */
    public function demandeurs()
    {
        return $this->hasMany('App\Models\Demandeur', 'compagnie_id', 'id');
    }

    /**
     * @return \Illuminate\Database\Eloquent\Relations\HasOne
     */
    public function user()
    {
        return $this->hasOne('App\Models\User', 'id', 'user_id');
    }

    /**
     * @return \Illuminate\Database\Eloquent\Relations\HasMany
     */

    public function demandeAutorisations()
    {
        return $this->hasMany(DemandeAutorisation::class, 'compagnie_aerienne_id');
    }
}
