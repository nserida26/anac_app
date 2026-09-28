<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

/**
 * Désignation par l'ANAC d'un détenteur de licence (déjà instructeur) comme
 * examinateur, pour des types de licence et une période donnés.
 */
class DesignationExaminateur extends Model
{
    protected $table = 'designations_examinateur';

    protected $fillable = ['demandeur_id', 'date_debut', 'date_fin', 'designe_par', 'retiree_le', 'retiree_par'];

    protected $casts = [
        'date_debut' => 'date',
        'date_fin' => 'date',
        'retiree_le' => 'datetime',
    ];

    public function demandeur()
    {
        return $this->belongsTo(Demandeur::class);
    }

    public function typesLicence()
    {
        return $this->belongsToMany(TypeLicence::class, 'designation_examinateur_type_licence', 'designation_examinateur_id', 'type_licence_id');
    }

    public function designePar()
    {
        return $this->belongsTo(User::class, 'designe_par');
    }

    /** Non retirée et couvrant la date donnée (aujourd'hui par défaut). */
    public function scopeEnVigueur($query, $date = null)
    {
        $date = ($date ? \Carbon\Carbon::parse($date) : now())->toDateString();

        return $query->whereNull('retiree_le')
            ->whereDate('date_debut', '<=', $date)
            ->whereDate('date_fin', '>=', $date);
    }

    public function estEnVigueur(): bool
    {
        return !$this->retiree_le && $this->date_debut->lte(today()) && $this->date_fin->gte(today());
    }
}
