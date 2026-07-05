<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class EquipeVol extends Model
{
    use HasFactory;

    /**
     * The table associated with the model.
     *
     * @var string
     */
    protected $table = 'equipe_vols';

    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = [
        'demande_autorisation_id',
        //'nom',
        //'prenom',
        //'age',
        //'email',
        'fonction',
        'licence_numero',
        'licence_expiration',
        'justificatif',
        'valider',
        'motif'
    ];

    /**
     * The attributes that should be cast.
     *
     * @var array
     */
    protected $casts = [
        'licence_expiration' => 'date',
        'age' => 'integer'
    ];

    /**
     * Get the flight that owns the crew member.
     */
    public function demandeAutorisation()
    {
        return $this->belongsTo(DemandeAutorisation::class);
    }

    /**
     * Get the crew member's full name.
     *
     * @return string
     */
    public function getNomCompletAttribute()
    {
        return "{$this->prenom} {$this->nom}";
    }

    /**
     * Scope a query to only include pilots.
     */
    public function scopePilotes($query)
    {
        return $query->where('fonction', 'Pilote');
    }

    /**
     * Scope a query to only include active licenses.
     */
    public function scopeLicenceValide($query)
    {
        return $query->whereDate('licence_expiration', '>', now());
    }

    /**
     * Check if the crew member's license is valid.
     *
     * @return bool
     */
    public function licenceEstValide()
    {
        return $this->licence_expiration && $this->licence_expiration->isFuture();
    }
}
