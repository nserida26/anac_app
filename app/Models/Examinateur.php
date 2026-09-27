<?php

namespace App\Models;

use App\Models\Concerns\ValidableParAnac;
use Illuminate\Database\Eloquent\Model;

/**
 * Examinateur médical.
 *
 * - Individuel : il a son propre compte (user_id) et agit lui-même.
 * - Rattaché à un centre d'expertise médicale : déclaré par le centre, sans
 *   compte (user_id vide), validé par l'ANAC ; c'est le centre qui agit pour lui.
 * Une même personne peut avoir les deux fiches.
 *
 * @property $id
 * @property $np
 * @property $user_id
 * @property $centre_medical_id
 *
 * @property CentreMedical $centreMedical
 * @property User $user
 * @mixin \Illuminate\Database\Eloquent\Builder
 */
class Examinateur extends Model
{
    use ValidableParAnac;

    public const COLONNE_CENTRE = 'centre_medical_id';

    /** Règles de création d'un examinateur individuel par l'ANAC. */
    static $rules = [
		'np' => 'required',
		'user_id' => 'required',
    ];

    protected $perPage = 20;

    protected $fillable = [
        'np', 'user_id', 'centre_medical_id',
        'nom', 'prenom', 'email', 'telephone', 'numero_licence_examinateur', 'date_naissance',
        'nationalite', 'adresse', 'document_justificatif', 'date_debut_validite', 'date_fin_validite',
        'statut_validation', 'motif_refus', 'valide_par', 'date_validation',
    ];

    protected $casts = [
        'date_naissance' => 'date',
        'date_debut_validite' => 'date',
        'date_fin_validite' => 'date',
        'date_validation' => 'datetime',
    ];

    protected static function booted()
    {
        // np (nom complet) reste la colonne affichée partout pour un examinateur médical.
        static::saving(function (Examinateur $examinateur) {
            if (empty($examinateur->np) && ($examinateur->nom || $examinateur->prenom)) {
                $examinateur->np = trim($examinateur->nom . ' ' . $examinateur->prenom);
            }
        });
    }

    /** Seuls les examinateurs déclarés par un centre (sans compte) passent par la validation ANAC. */
    public function scopeDeclares($query)
    {
        return $query->whereNull('user_id')->whereNotNull('centre_medical_id');
    }

    public function getNomCompletAttribute()
    {
        return $this->nom || $this->prenom ? trim($this->nom . ' ' . $this->prenom) : $this->np;
    }

    /**
     * @return \Illuminate\Database\Eloquent\Relations\HasOne
     */
    public function centreMedical()
    {
        return $this->hasOne('App\Models\CentreMedical', 'id', 'centre_medical_id');
    }

    /** Centre de rattachement (nom commun aux examinateurs déclarés). */
    public function centre()
    {
        return $this->belongsTo(CentreMedical::class, 'centre_medical_id');
    }

    /**
     * @return \Illuminate\Database\Eloquent\Relations\HasMany
     */
    public function examensMedicauxes()
    {
        return $this->hasMany('App\Models\ExamenMedical', 'examinateur_id', 'id');
    }

    /**
     * @return \Illuminate\Database\Eloquent\Relations\HasOne
     */
    public function user()
    {
        return $this->hasOne('App\Models\User', 'id', 'user_id');
    }
}
