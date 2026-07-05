<?php



namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;


class ItineraireVol extends Model
{
    use HasFactory;

    protected $fillable = [
        'demande_approbation_id',
        'vol_id',
        'aeroport_id',
        'heure_depart',
        'heure_arrivee',
        'valider',
        'motif'
    ];

    protected $casts = [
        'heure_depart' => 'datetime:H:i',
        'heure_arrivee' => 'datetime:H:i'
    ];

    public function demandeApprobation()
    {
        return $this->belongsTo(DemandeApprobation::class);
    }

    public function volApprobation()
    {
        return $this->belongsTo(VolApprobation::class, 'vol_id');
    }

    public function aeroport()
    {
        return $this->belongsTo(Aeroport::class);
    }
}
