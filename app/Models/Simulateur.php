<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

/**
 * Class Simulateur
 *
 * @property $id
 * @property $libelle
 *
 * @property SimulateurCentre[] $simulateurCentres
 * @property TrainingDemandeur[] $trainingDemandeurs
 * @package App
 * @mixin \Illuminate\Database\Eloquent\Builder
 */
class Simulateur extends Model
{

    use HasFactory;

    protected $fillable = [
        'libelle',
        'type_avion_id',
        'date_delivrance_initiale',
        'date_renouvellement',
        'date_expiration',
        'compagnie'
    ];

    protected $casts = [
        'date_delivrance_initiale' => 'date',
        'date_renouvellement' => 'date',
        'date_expiration' => 'date',
    ];

    public function typeAvion()
    {
        return $this->belongsTo(TypeAvion::class, 'type_avion_id');
    }
    public function centres()
    {
        return $this->belongsToMany(CentreFormation::class, 'centre_simulateur');
    }

    /**
     * @return \Illuminate\Database\Eloquent\Relations\HasMany
     */
    public function trainingDemandeurs()
    {
        return $this->hasMany('App\Models\TrainingDemandeur', 'simulateur_id', 'id');
    }
}
