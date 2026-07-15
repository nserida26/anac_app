<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

/**
 * Class Vol
 *
 * @property $id
 * @property $numero_vol
 * @property $date_depart
 * @property $date_arrivee
 * @property $statut
 * @property $compagnie_aerienne_id
 * @property $nbr_passagers
 * @property $created_at
 * @property $updated_at
 *
 * @package App
 * @mixin \Illuminate\Database\Eloquent\Builder
 */
class Vol extends Model
{

  static $rules = [
    'numero_vol' => 'required',
    'date_depart' => 'required',
    'date_arrivee' => 'required',
    //'nbr_passagers' => 'required',
    'demande_autorisation_id' => 'required'
  ];

  protected $perPage = 20;

  /**
   * Attributes that should be mass-assignable.
   *
   * @var array
   */
  protected $fillable = [
    'numero_vol',
    'numero_piste_depart',
    'numero_piste_arrivee',
    'aeroport_depart_id',
    'aeroport_arrivee_id',
    'date_depart',
    'date_arrivee',
    'statut',
    'demande_autorisation_id',
    'nbr_passagers',
   
    'valider',
    'motif'
  ];
  public function demande()
  {
    return $this->belongsTo(DemandeAutorisation::class, 'demande_autorisation_id');
  }

  public function autorisations()
  {
    return $this->hasMany(Autorisation::class);
  }

  /**
   * Get the departure airport.
   */
  public function aeroportDepart()
  {
    return $this->belongsTo(Aeroport::class, 'aeroport_depart_id');
  }
  /**
   * Get the arrival airport.
   */
  public function aeroportArrivee()
  {
    return $this->belongsTo(Aeroport::class, 'aeroport_arrivee_id');
  }
  public function escales()
{
    return $this->hasMany(Escale::class)->orderBy('ordre');
}
}
