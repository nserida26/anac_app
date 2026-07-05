<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

/**
 * Class Examinateur
 *
 * @property $id
 * @property $np
 * @property $created_at
 * @property $updated_at
 * @property $user_id
 * @property $centre_medical_id
 *
 * @property CentreMedical $centreMedical
 * @property ExamensMedicaux[] $examensMedicauxes
 * @property User $user
 * @package App
 * @mixin \Illuminate\Database\Eloquent\Builder
 */
class Examinateur extends Model
{
    
    static $rules = [
		'np' => 'required',
		'user_id' => 'required',
    ];

    protected $perPage = 20;

    /**
     * Attributes that should be mass-assignable.
     *
     * @var array
     */
    protected $fillable = ['np','user_id','centre_medical_id'];


    /**
     * @return \Illuminate\Database\Eloquent\Relations\HasOne
     */
    public function centreMedical()
    {
        return $this->hasOne('App\Models\CentreMedical', 'id', 'centre_medical_id');
    }
    
    /**
     * @return \Illuminate\Database\Eloquent\Relations\HasMany
     */
    public function examensMedicauxes()
    {
        return $this->hasMany('App\Models\ExamensMedicaux', 'examinateur_id', 'id');
    }
    
    /**
     * @return \Illuminate\Database\Eloquent\Relations\HasOne
     */
    public function user()
    {
        return $this->hasOne('App\Models\User', 'id', 'user_id');
    }
    

}
