<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Carbon\Carbon;

class VolApprobation extends Model
{
    use HasFactory;

    protected $fillable = [
        'numero_vol',
        'jours_operation',
        'aeroport_depart_id',
        'aeroport_arrivee_id',
        'heure_depart',
        'heure_arrivee',
        'date_debut',
        'date_fin',
        'demande_approbation_id',
        'motif',
        'valider'
    ];

    protected $casts = [
        'jours_operation' => 'array',
        'heure_depart' => 'datetime:H:i',
        'heure_arrivee' => 'datetime:H:i',
        'date_debut' => 'date',
        'date_fin' => 'date'
    ];

    public function getPeriodFormattedAttribute()
    {
        if (!$this->date_debut || !$this->date_fin) {
            return '';
        }

        return $this->date_debut->format('Y-m-d') . ' - ' . $this->date_fin->format('Y-m-d');
    }
    public function getJoursOperationDisplayAttribute()
    {
        if (empty($this->jours_operation)) {
            return '';
        }

        return implode(', ', json_decode($this->jours_operation, true));
    }

    public function aeroportDepart()
    {
        return $this->belongsTo(Aeroport::class, 'aeroport_depart_id');
    }

    public function aeroportArrivee()
    {
        return $this->belongsTo(Aeroport::class, 'aeroport_arrivee_id');
    }

    public function demandeApprobation()
    {
        return $this->belongsTo(DemandeApprobation::class);
    }


    public static function calculateSummerSeasonDates($year = null)
    {
        $year = $year ?? now()->year;

        // Last Sunday of March
        $summerStart = Carbon::create($year, 3, 31)
            ->startOfDay()
            ->previous(Carbon::SUNDAY);

        // Last Sunday of September
        $summerEnd = Carbon::create($year, 9, 30)
            ->startOfDay()
            ->previous(Carbon::SUNDAY);

        return [
            'date_debut' => $summerStart->format('Y-m-d'),
            'date_fin' => $summerEnd->format('Y-m-d'),
            'season' => 'summer'
        ];
    }
    public static function calculateWinterSeasonDates($year = null)
    {
        $year = $year ?? now()->year;
        $winterStart = Carbon::create($year, 9, 30)
            ->startOfDay()
            ->previous(Carbon::SUNDAY)
            ->addDay();

        $winterEnd = Carbon::create($year + 1, 3, 31)
            ->startOfDay()
            ->previous(Carbon::SUNDAY);

        return [
            'date_debut' => $winterStart->format('Y-m-d'),
            'date_fin' => $winterEnd->format('Y-m-d'),
            'season' => 'winter'
        ];
    }
}
