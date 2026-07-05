<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Compagnie extends Model
{
    use HasFactory;

    // Définir les colonnes autorisées pour l'insertion
    protected $fillable = [
        'nom_entreprise',
        'panier',
        'email',
        'telephone',
        'user_id',
        'plafond',
        'code'
    ];

    /**
     * Relation entre l'Employeur et les Demandeurs
     * Un Employeur peut avoir plusieurs Demandeurs (employés)
     */
    public function demandeurs()
    {
        return $this->hasMany(Demandeur::class);
    }
public function user()
{
    return $this->belongsTo(User::class);
}
    /**
     * Relation entre l'Employeur et les Demandes
     * Un Employeur peut initier plusieurs Demandes
     */
    public function demandes()
    {
        return $this->hasMany(Demande::class);
    }
    public function demandeAutorisations()
    {
        return $this->hasMany(DemandeAutorisation::class, 'compagnie_aerienne_id');
    }
    public function demandeApprobations()
    {
        return $this->hasMany(DemandeApprobation::class, 'compagnie_id');
    }

    public function avions()
    {
        return $this->hasMany(Avion::class, 'compagnie_aerienne_id');
    }
}
