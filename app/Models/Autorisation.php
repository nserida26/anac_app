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

        // Opérateur = celui explicitement choisi sur la demande (demande_autorisations
        // .compagnie_id, obligatoire à la création — voir DemandeAutorisation::compagnie()) ;
        // à défaut (anciennes demandes créées avant ce choix obligatoire), l'exploitant du
        // 1ᵉʳ aéronef (nom figé à sa création, voir Avion::booted) ; en tout dernier recours
        // une compagnie du compte demandeur — arbitraire s'il en représente plusieurs, voir
        // User::compagnies().
        return optional(optional($demande)->compagnie)->nom_entreprise
            ?: optional(optional($demande)->avions->first())->operateur_nom
            ?: optional(optional(optional($demande)->user)->compagnie)->nom_entreprise;
    }
}
