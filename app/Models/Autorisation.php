<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Autorisation extends Model
{
    use HasFactory;
    protected $fillable = [
        'demande_id',
        'vol_id',
        'code_autorisation',
        'date_delivrance',
        'date_expiration',
        'statut',
        'signature_dg',
        'signature_dta',
        'signature_srta',
        'cachet',
        'nom_signataire'
    ];

    protected static function booted()
    {
        // À la délivrance, on fige les informations du demandeur et de l'opérateur.
        // Un échec de snapshot ne doit jamais empêcher la délivrance : on journalise
        // et le snapshot pourra être reconstruit (AutorisationSnapshot::buildFor).
        static::created(function (Autorisation $autorisation) {
            try {
                AutorisationSnapshot::buildFor($autorisation);
            } catch (\Throwable $e) {
                \Illuminate\Support\Facades\Log::error(
                    'Échec de création du snapshot pour autorisation ' . $autorisation->id . ' : ' . $e->getMessage()
                );
            }
        });
    }

    public function demande()
    {
        return $this->belongsTo(DemandeAutorisation::class, 'demande_id');
    }

    public function vol()
    {
        return $this->belongsTo(Vol::class);
    }

    public function snapshot()
    {
        return $this->hasOne(AutorisationSnapshot::class, 'autorisation_id');
    }

    // --- Accesseurs : privilégient le snapshot figé, repli sur les données en direct ---

    public function getDemandeurNpAttribute(): ?string
    {
        return optional($this->snapshot)->demandeur_np
            ?: optional(optional(optional($this->demande)->user)->demandeur)->np;
    }

    public function getDemandeurEmailAttribute(): ?string
    {
        return optional($this->snapshot)->demandeur_email
            ?: optional(optional($this->demande)->user)->email;
    }

    public function getDemandeurTelephoneAttribute(): ?string
    {
        return optional($this->snapshot)->demandeur_telephone
            ?: optional(optional($this->demande)->user)->whatsapp;
    }

    public function getOperateurNomAttribute(): ?string
    {
        if ($nom = optional($this->snapshot)->operateur_nom) {
            return $nom;
        }

        $demande = $this->demande;

        // Opérateur = exploitant du 1ᵉʳ aéronef ; à défaut (ex. dépouille mortelle,
        // sans aéronef) la compagnie du compte demandeur.
        return optional(optional(optional($demande)->avions->first())->compagnie)->nom_entreprise
            ?: optional(optional(optional($demande)->user)->compagnie)->nom_entreprise;
    }
}
