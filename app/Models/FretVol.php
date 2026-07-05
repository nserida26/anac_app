<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class FretVol extends Model
{
    use HasFactory;

    protected $table = 'fret_vols';

    protected $fillable = [
        'demande_autorisation_id',
        'nature',
        'poids',
        'instructions_speciales',
        'valider',
        'motif'
    ];

    protected $casts = [
        'poids' => 'decimal:2',
        'created_at' => 'datetime',
        'updated_at' => 'datetime'
    ];

    /**
     * Relation avec le demande
     */
    public function demandeAutorisation()
    {
        return $this->belongsTo(DemandeAutorisation::class);
    }

    /**
     * Scope pour les fret de plus d'un certain poids
     */
    public function scopePoidsSuperieurA($query, $poids)
    {
        return $query->where('poids', '>', $poids);
    }

    /**
     * Scope pour les fret dangereux
     */
    public function scopeDangereux($query)
    {
        return $query->where('nature', 'Dangereux');
    }

    /**
     * Vérifie si le fret est lourd
     */
    public function estLourd()
    {
        return $this->poids > 1000; // Exemple: > 1000 kg
    }
}
