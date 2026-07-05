<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Itineraire extends Model
{
    use HasFactory;

    /**
     * The table associated with the model.
     *
     * @var string
     */
    protected $table = 'itineraires';

    /**
     * The primary key for the model.
     *
     * @var string
     */
    protected $primaryKey = 'id';

    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = [
        'demande_autorisation_id',
        'aeroport_id',
        'date_arrivee',
        'date_depart',
        'valider',
        'motif'
    ];

    /**
     * The attributes that should be cast.
     *
     * @var array
     */
    protected $casts = [
        'date_arrivee' => 'datetime',
        'date_depart' => 'datetime',
        'created_at' => 'datetime',
        'updated_at' => 'datetime'
    ];

    /**
     * Get the flight associated with the itinerary.
     */
    public function demandeAutorisation()
    {
        return $this->belongsTo(DemandeAutorisation::class, 'demande_autorisation_id');
    }

    /**
     * Get the departure airport.
     */
    public function aeroport()
    {
        return $this->belongsTo(Aeroport::class, 'aeroport_id');
    }
}
