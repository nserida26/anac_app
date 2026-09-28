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

    /** Un demandeur ne peut détenir qu'une seule licence par type. */
    public function detientLicenceDeType(string $typeLicence): bool
    {
        return $this->licences()->where('type_licence', $typeLicence)->exists();
    }

    /** Au moins une licence validée, non bloquée et non expirée. */
    public function detientLicenceValide(): bool
    {
        return $this->licences()->valid()->exists();
    }

    public function designationsExaminateur()
    {
        return $this->hasMany(DesignationExaminateur::class);
    }

    /**
     * Examinateur désigné par l'ANAC à la date donnée (aujourd'hui par défaut),
     * éventuellement pour un type de licence précis. Remplace l'ancienne case is_examinateur.
     */
    public function estExaminateurDesigne($date = null, ?int $typeLicenceId = null): bool
    {
        return $this->designationsExaminateur()->enVigueur($date)
            ->when($typeLicenceId, fn ($q) => $q->whereHas('typesLicence', fn ($t) => $t->where('type_licences.id', $typeLicenceId)))
            ->exists();
    }

    /** Peut enregistrer des formations depuis son compte : instructeur ou examinateur désigné. */
    public function estFormateur(): bool
    {
        return (bool) $this->is_instructeur || $this->estExaminateurDesigne();
    }
}
