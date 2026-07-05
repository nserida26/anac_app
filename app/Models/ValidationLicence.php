<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class ValidationLicence extends Model
{
    use HasFactory;

    /**
     * The table associated with the model.
     *
     * @var string
     */
    protected $table = 'validation_licences';

    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = [
        'demande_id',
        'type_licence_id',
        'compagnie_id',
        'numero_validation',
        'num_licence',
        'date_delivrance_licence',
        'lieu_delivrance_licence',
        'type_appareil',
        'immatriculation_appareil',
        'date_debut_validite',
        'date_fin_validite',
        'date_emission',
        'restrictions',
        'is_active',
        'signataire_nom',
        'signataire_titre',
        'signature_path',
        'cachet_path'
    ];

    /**
     * The attributes that should be cast.
     *
     * @var array
     */
    protected $casts = [
        'date_delivrance_licence' => 'date',
        'date_debut_validite' => 'date',
        'date_fin_validite' => 'date',
        'date_emission' => 'date',
        'is_active' => 'boolean',
    ];

    /**
     * Get the demande associated with the validation licence.
     */
    public function demande()
    {
        return $this->belongsTo(Demande::class);
    }

    /**
     * Get the type of licence associated with the validation licence.
     */
    public function typeLicence()
    {
        return $this->belongsTo(TypeLicence::class);
    }

    /**
     * Get the compagnie (airline) associated with the validation licence.
     */
    public function compagnie()
    {
        return $this->belongsTo(Compagnie::class);
    }

    /**
     * Scope a query to only include active validations.
     *
     * @param  \Illuminate\Database\Eloquent\Builder  $query
     * @return \Illuminate\Database\Eloquent\Builder
     */
    public function scopeActive($query)
    {
        return $query->where('is_active', true);
    }

    /**
     * Scope a query to only include validations that are currently valid.
     *
     * @param  \Illuminate\Database\Eloquent\Builder  $query
     * @return \Illuminate\Database\Eloquent\Builder
     */
    public function scopeCurrentlyValid($query)
    {
        return $query->where('date_debut_validite', '<=', now())
            ->where('date_fin_validite', '>=', now());
    }

    /**
     * Get the validation period as a formatted string.
     *
     * @return string
     */
    public function getValidationPeriodAttribute()
    {
        return "Du {$this->date_debut_validite->format('d/m/Y')} au {$this->date_fin_validite->format('d/m/Y')}";
    }

    /**
     * Check if the validation is currently valid.
     *
     * @return bool
     */
    public function getIsCurrentlyValidAttribute()
    {
        return now()->between($this->date_debut_validite, $this->date_fin_validite);
    }
}
