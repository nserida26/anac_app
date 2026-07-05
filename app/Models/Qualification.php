<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;


/**
 * Class Qualification
 *
 * @property $id
 * @property $libelle
 *
 * @property QualificationDemandeur[] $qualificationDemandeurs
 * @package App
 * @mixin \Illuminate\Database\Eloquent\Builder
 */
class Qualification extends Model
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

  public $timestamps = false;

  /**
   * @return \Illuminate\Database\Eloquent\Relations\HasMany
   */
  public function qualificationDemandeurs()
  {
    return $this->hasMany('App\Models\QualificationDemandeur', 'qualification_id', 'id');
  }
  public function typeLicences(): BelongsToMany
  {
    return $this->belongsToMany(TypeLicence::class, 'type_licence_qualification', 'qualification_id', 'type_licence_id');
  }
}
