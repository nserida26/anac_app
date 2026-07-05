<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

/**
 * Class Autorite
 *
 * @property $id
 * @property $libelle
 * @property $created_at
 * @property $updated_at
 *
 * @property LicenceDemandeur[] $licenceDemandeurs
 * @package App
 * @mixin \Illuminate\Database\Eloquent\Builder
 */
class Autorite extends Model
{
    
    static $rules = [
		'libelle' => 'required',
    ];

    protected $perPage = 20;

    /**
     * Attributes that should be mass-assignable.
     *
     * @var array
     */
    protected $fillable = ['libelle'];


    /**
     * @return \Illuminate\Database\Eloquent\Relations\HasMany
     */
    public function licenceDemandeurs()
    {
        return $this->hasMany('App\Models\LicenceDemandeur', 'autorite_id', 'id');
    }
    

}
