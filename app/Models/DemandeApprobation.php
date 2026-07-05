<?php

namespace App\Models;

use App\Enums\SaisonEnum;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Carbon\Carbon;

class DemandeApprobation extends Model
{
    use HasFactory;

    protected $table = 'demande_approbations';

    protected $fillable = [
        'reference',
        'saison',
        'user_id',
        'compagnie_id',
        'date_demande',
        'date_debut',
        'date_fin',
        'statut',
        'dg_motif',
        'dta_motif',
        'date_soumission',
        'dsna_motif',
        'dsad_motif',
        'dsv_motif',
        'amender'
    ];
    public $timestamps = false;
    protected $casts = [
        'date_demande' => 'date:Y-m-d',
        'date_debut' => 'date:Y-m-d',
        'date_fin' => 'date:Y-m-d'
    ];

    protected $attributes = [
        'statut' => 'EN_ATTENTE',
    ];

    public static function calculateSeasonalDates($year = null)
    {
        $year = $year ?? now()->year;

        // Calculate last Sunday of March
        $lastSundayMarch = Carbon::create($year, 3, 31) // Start at March 31
            ->startOfDay()
            ->previous(Carbon::SUNDAY); // Go back to previous Sunday

        // Calculate last Sunday of September
        $lastSundaySeptember = Carbon::create($year, 9, 30) // Start at September 30
            ->startOfDay()
            ->previous(Carbon::SUNDAY); // Go back to previous Sunday

        return [
            'date_debut' => $lastSundayMarch->format('Y-m-d'),
            'date_fin' => $lastSundaySeptember->format('Y-m-d')
        ];
    }

    public function documents()
    {
        return $this->hasMany(DocumentApprobation::class);
    }
    public function avions()
    {
        return $this->hasMany(Avion::class, 'demande_approbation_id');
    }
    protected static function booted()
    {
        static::creating(function ($model) {
            // Auto-set submission date if not provided
            if (empty($model->date_demande)) {
                $model->date_demande = now()->format('Y-m-d');
            }

            // Auto-set season dates if empty
            if (empty($model->date_debut)) {
                $seasonDates = SaisonEnum::getDates($model->saison, date('Y'));
                $model->date_debut = $seasonDates['date_debut'];
                $model->date_fin = $seasonDates['date_fin'];
            }
        });
    }

    // Relationships
    public function user()
    {
        return $this->belongsTo(User::class);
    }
    public function approbation()
    {
        return $this->hasOne(Approbation::class, 'demande_id');
    }

    public function compagnie()
    {
        return $this->belongsTo(Compagnie::class);
    }

    public function vols()
    {
        return $this->hasMany(VolApprobation::class, 'demande_approbation_id');
    }
    public function itineraires()
    {
        return $this->hasMany(ItineraireVol::class);
    }

    // Scopes
    public function scopeApprouvees($query)
    {
        return $query->where('statut', 'APPROUVEE');
    }

    public function scopePourSaison($query, string $saison)
    {
        return $query->where('saison', $saison);
    }

    // Helpers
    public function estValide(): bool
    {
        return $this->statut === 'APPROUVEE' &&
            $this->date_fin >= now()->format('Y-m-d');
    }

    public function getPeriodeAttribute(): string
    {
        return "Du {$this->date_debut->format('d/m/Y')} au {$this->date_fin->format('d/m/Y')}";
    }
    public function etatDemande()
    {
        return $this->hasOne(EtatDemandeApprobation::class, 'demande_id');
    }

    public function isFullyValidated(): bool
    {
        return !$this->avions()->where('valider', false)->exists()
            && !$this->vols()->where('valider', false)->exists()
            && !$this->itineraires()->where('valider', false)->exists()
            && !$this->documents()->where('valider', false)->exists();
    }

    public function validationProgress(): array
    {
        $components = [
            'avions' => $this->avions,
            'vols' => $this->vols,
            'itineraires' => $this->itineraires,
            'documents' => $this->documents
        ];

        $totalItems = 0;
        $validatedItems = 0;
        $details = [];

        foreach ($components as $key => $collection) {
            $count = $collection->count();
            $validated = $collection->where('valider', true)->count();

            $totalItems += $count;
            $validatedItems += $validated;

            $details[$key] = [
                'total' => $count,
                'validated' => $validated,
                'percentage' => $count > 0 ? round(($validated / $count) * 100) : 100,
            ];
        }

        return [
            'total' => $totalItems,
            'validated' => $validatedItems,
            'percentage' => $totalItems > 0 ? round(($validatedItems / $totalItems) * 100) : 0,
            'details' => $details,
        ];
    }

    /**
     * Get list of invalid components
     */
    public function getInvalidComponents(): array
    {
        $invalid = [];

        // Check avions
        foreach ($this->avions as $avion) {
            if (!$avion->valider) {
                $invalid[] = [
                    'type' => 'avion',
                    'id' => $avion->id,
                    'identifier' => $avion->immatriculation,
                    'motif' => $avion->motif ?? 'Non spécifié',
                ];
            }
        }

        // Check vols
        foreach ($this->vols as $vol) {
            if (!$vol->valider) {
                $invalid[] = [
                    'type' => 'vol',
                    'id' => $vol->id,
                    'identifier' => $vol->numero_vol,
                    'motif' => $vol->motif ?? 'Non spécifié',
                ];
            }
        }

        // Check itinerary
        foreach ($this->itineraires as $itineraire) {
            if (!$itineraire->valider) {
                $invalid[] = [
                    'type' => 'itineraire',
                    'id' => $itineraire->id,
                    'identifier' => optional($itineraire->aeroport)->codeICAO ?? 'N/A',
                    'motif' => $itineraire->motif ?? 'Non spécifié',
                ];
            }
        }

        // Check documents
        foreach ($this->documents as $document) {
            if (!$document->valider) {
                $invalid[] = [
                    'type' => 'document',
                    'id' => $document->id,
                    'identifier' => optional($document->typeDocument)->nom_fr ?? 'Document inconnu',
                    'motif' => $document->motif ?? 'Non spécifié',
                ];
            }
        }

        return $invalid;
    }

    /**
     * Check if any rejection reason exists
     */
    public function hasRejectionReasons(): bool
    {
        return !empty($this->dg_motif) ||
            !empty($this->dta_motif) ||
            !empty($this->dsna_motif) ||
            !empty($this->dsad_motif) ||
            !empty($this->dsv_motif);
    }

    /**
     * Get all non-empty rejection reasons
     */
    public function getRejectionReasons(): array
    {
        $reasons = [];

        if (!empty($this->dg_motif)) {
            $reasons['dg'] = [
                'motif' => $this->dg_motif,
                'date' => $this->date_validation
            ];
        }

        if (!empty($this->dta_motif)) {
            $reasons['dta'] = [
                'motif' => $this->dta_motif,
                'date' => $this->date_validation
            ];
        }

        if (!empty($this->dsna_motif)) {
            $reasons['dsna'] = [
                'motif' => $this->dsna_motif,
                'date' => $this->updated_at
            ];
        }

        if (!empty($this->dsad_motif)) {
            $reasons['dsad'] = [
                'motif' => $this->dsad_motif,
                'date' => $this->updated_at
            ];
        }

        if (!empty($this->dsv_motif)) {
            $reasons['dsv'] = [
                'motif' => $this->dsv_motif,
                'date' => $this->updated_at
            ];
        }

        return $reasons;
    }

    /**
     * Get validation status for display
     */
    public function getValidationStatus(): string
    {
        if ($this->statut === 'rejected') {
            return 'Rejected';
        }

        if ($this->isFullyValidated()) {
            return 'Fully Validated';
        }

        $progress = $this->validationProgress();

        if ($progress['percentage'] === 0) {
            return 'Not Started';
        }

        if ($progress['percentage'] === 100) {
            return 'Pending Final Approval';
        }

        return 'Partially Validated (' . $progress['percentage'] . '%)';
    }

    /**
     * Check if specific department has validated
     */
    public function isDepartmentValidated(string $department): bool
    {
        switch ($department) {
            case 'dta':
                return !empty($this->dta_motif);
            case 'dsv':
                return !empty($this->dsv_motif);
            case 'dsna':
                return !empty($this->dsna_motif);
            case 'dsad':
                return !empty($this->dsad_motif);
            case 'dg':
                return $this->statut === 'APPROUVEE' && !empty($this->dg_motif);
            default:
                return false;
        }
    }
}
