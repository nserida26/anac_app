<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

/**
 * Class CentreFormation
 *
 * @property $id
 * @property $libelle
 * @property $created_at
 * @property $updated_at
 * @property $user_id
 *
 * @property CompetenceDemandeur[] $competenceDemandeurs
 * @property Formation[] $formations
 * @property FormationDemandeur[] $formationDemandeurs
 * @property QualificationDemandeur[] $qualificationDemandeurs
 * @property SimulateurCentre[] $simulateurCentres
 * @property TrainingDemandeur[] $trainingDemandeurs
 * @property User $user
 * @package App
 * @mixin \Illuminate\Database\Eloquent\Builder
 */
class CentreFormation extends Model
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
    protected $fillable = ['libelle', 'user_id'];


    /**
     * @return \Illuminate\Database\Eloquent\Relations\HasMany
     */
    public function competenceDemandeurs()
    {
        return $this->hasMany('App\Models\CompetenceDemandeur', 'centre_formation_id', 'id');
    }

    /**
     * @return \Illuminate\Database\Eloquent\Relations\HasMany
     */
    public function formations()
    {
        return $this->hasMany('App\Models\Formation', 'centre_formation_id', 'id');
    }

    /**
     * @return \Illuminate\Database\Eloquent\Relations\HasMany
     */
    public function formationDemandeurs()
    {
        return $this->hasMany('App\Models\FormationDemandeur', 'centre_formation_id', 'id');
    }

    /**
     * @return \Illuminate\Database\Eloquent\Relations\HasMany
     */
    public function qualificationDemandeurs()
    {
        return $this->hasMany('App\Models\QualificationDemandeur', 'centre_formation_id', 'id');
    }

    /**
     * @return \Illuminate\Database\Eloquent\Relations\HasMany
     */
    public function simulateurs()
    {
        return $this->belongsToMany(Simulateur::class, 'centre_simulateur');
    }

    /**
     * @return \Illuminate\Database\Eloquent\Relations\HasMany
     */
    public function trainingDemandeurs()
    {
        return $this->hasMany('App\Models\TrainingDemandeur', 'centre_formation_id', 'id');
    }

    /**
     * @return \Illuminate\Database\Eloquent\Relations\HasOne
     */
    public function user()
    {
        return $this->hasOne('App\Models\User', 'id', 'user_id');
    }
}
