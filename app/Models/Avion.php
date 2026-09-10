<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

/**
 * Class Avion
 *
 * @property $id
 * @property $immatriculation
 * @property $capacite
 * @property $charge_max
 * @property $compagnie_aerienne_id
 * @property $type_avion_id
 * @property $created_at
 * @property $updated_at
 *
 * @package App
 * @mixin \Illuminate\Database\Eloquent\Builder
 */
class Avion extends Model
{

  static $rules = [
    'immatriculation' => 'required',
    'compagnie_aerienne_id' => 'required',
    'type_avion_id' => 'required',
    'proprietaire_id' => 'required'
  ];

  protected $perPage = 20;

  /**
   * Attributes that should be mass-assignable.
   *
   * @var array
   */
  protected $fillable = ['immatriculation', 'capacite', 'charge_max', 'compagnie_aerienne_id', 'nom_entreprise_snapshot', 'type_avion_id', 'proprietaire_id', 'demande_autorisation_id', 'demande_approbation_id', 'valider', 'motif'];

  protected static function booted()
  {
      // Fige le nom de la compagnie au moment où l'aéronef est enregistré, pour
      // qu'un renommage ultérieur de la compagnie n'altère pas l'historique des
      // demandes déjà déposées (l'ancienne demande garde l'ancien nom).
      static::creating(function (Avion $avion) {
          if (empty($avion->nom_entreprise_snapshot) && $avion->compagnie_aerienne_id) {
              $avion->nom_entreprise_snapshot = optional(
                  Compagnie::find($avion->compagnie_aerienne_id)
              )->nom_entreprise;
          }
      });
  }

  public function compagnie()
  {
    return $this->belongsTo(Compagnie::class, 'compagnie_aerienne_id');
  }

  public function type()
  {
    return $this->belongsTo(TypeAvion::class, 'type_avion_id');
  }

  public function vols()
  {
    return $this->hasMany(Vol::class);
  }
  public function proprietaire()
  {
    return $this->belongsTo(Proprietaire::class, 'proprietaire_id');
  }

  /**
   * Nom de l'opérateur (compagnie) : valeur figée à la création de l'aéronef,
   * repli sur la compagnie actuelle si l'aéronef est antérieur à ce mécanisme.
   */
  public function getOperateurNomAttribute(): ?string
  {
      return $this->nom_entreprise_snapshot
          ?: optional($this->compagnie)->nom_entreprise;
  }
}
