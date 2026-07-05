<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

/**
 * Class TypeAvion
 *
 * @property $id
 * @property $code
 *
 * @property QualificationDemandeur[] $qualificationDemandeurs
 * @package App
 * @mixin \Illuminate\Database\Eloquent\Builder
 */
class TypeAvion extends Model
{

  static $rules = [
    'code' => 'required',
    'capacite' => 'required',
    'charge_max' => 'required',
  ];

  protected $perPage = 20;

  /**
   * Attributes that should be mass-assignable.
   *
   * @var array
   */
  protected $fillable = ['code', 'capacite', 'charge_max'];


  /**
   * @return \Illuminate\Database\Eloquent\Relations\HasMany
   */
  public function qualificationDemandeurs()
  {
    return $this->hasMany('App\Models\QualificationDemandeur', 'type_avion_id', 'id');
  }
}
