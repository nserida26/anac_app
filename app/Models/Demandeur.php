<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Demandeur extends Model
{
    use HasFactory;


    protected $fillable = [

        'np',
        'date_naissance',
        'lieu_naissance',
        'adresse',
        'adresse_employeur',
        'signature',
        'photo',
        'user_id',
        'compagnie_id',
        'nationalite',
        'valider_compagnie',
        'dossier',
        'is_examinateur',
        'is_instructeur'
    ];
    public function userAccount()
    {
        return $this->belongsTo(User::class, 'user_id');
    }

    public function compagnie()
    {
        return $this->belongsTo(Compagnie::class, 'compagnie_id');
    }

    public function user()
    {
        return $this->belongsTo(User::class, 'user_id');
    }
    public function examens()
    {
        return $this->hasMany(ExamenMedical::class, 'demandeur_id');
    }


    public function demandes()
    {
        return $this->hasMany(Demande::class);
    }
    public function formations()
    {
        return $this->hasMany(Formation::class, 'demandeur_id');
    }
    /**
     * Un demandeur peut détenir plusieurs licences (une par type, voir la règle
     * d'unicité dans licences()). Cette relation hasOne reste utilisée par de
     * nombreux écrans qui n'affichent qu'« une » licence : ofMany() choisit de
     * façon déterministe celle dont la date d'expiration est la plus tardive
     * (typiquement la licence la plus récente/la plus élevée dans la carrière),
     * plutôt qu'une ligne arbitraire comme le faisait le hasOne simple.
     */
    public function licence()
    {
        return $this->hasOne(Licence::class, 'demandeur_id')->ofMany('date_expiration', 'max');
    }

    /**
     * Toutes les licences détenues par le demandeur (une par type de licence).
     */
    public function licences()
    {
        return $this->hasMany(Licence::class, 'demandeur_id');
    }
}
