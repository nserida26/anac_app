<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\DB;

class DemandeAutorisation extends Model
{
    use HasFactory;

    protected $fillable = [
        'code','mise_a_jour', 'date_debut', 'type_demande_autorisation_id', 'type_vol_id',
        'objet', 'date_fin', 'statut', 'date_soumission', 'date_validation',
        'sous_validite', 'user_id', 'dsv_motif', 'dsna_motif', 'dsad_motif',
        'dg_motif', 'dta_motif', 'directions_annotees', 'points','type_vol_ids'
    ];

    protected $appends = ['has_issues', 'invalid_reasons', 'rejection_reasons_list','etat_workflow'];

    // --- Relations ---
 public function typeVols()
    {
        return $this->belongsToMany(TypeVol::class, null, null, 'type_vol_ids')
            ->wherePivotIn('id', function($query) {
                $query->selectRaw('json_each.value')->from('json_each(?)', [$this->type_vol_ids]);
            });
    }
    public function typeVol() { return $this->belongsTo(TypeVol::class, 'type_vol_id'); }
    public function user() { return $this->belongsTo(User::class, 'user_id'); }
    public function etatDemande() { return $this->hasOne(EtatDemandeAutorisation::class, 'demande_id'); }
    public function type() { return $this->belongsTo(TypeDemandeAutorisation::class, 'type_demande_autorisation_id'); }
    public function paiement() { return $this->hasOne(PaiementAutorisation::class, 'demande_autorisation_id'); }
    public function documents() { return $this->hasMany(DocumentAutorisation::class); }

    public function equipe() { return $this->hasMany(EquipeVol::class); }
    public function personnes() { return $this->hasMany(PersonneDeces::class, 'demande_autorisation_id'); }
    public function fret() { return $this->hasMany(FretVol::class); }
    public function receivingParties() { return $this->hasMany(ReceivingParty::class); }
    public function vols() { return $this->hasMany(Vol::class); }
    public function avions() { return $this->hasMany(Avion::class, 'demande_autorisation_id'); }
    public function mdns()
    {
        return $this->hasMany(Mdn::class, 'demande_autorisation_id');
    }
        public function isAnnotedTo(string $direction): bool
    {
        return in_array($direction, json_decode($this->directions_annotees) ?? []);
    }
    public function isAnnotedToDSF(): bool
{
    return $this->isAnnotedTo('dsf');
}
    public function isAnnoted(): bool
    {
        return !empty($this->directions_annotees);
    }
    public function hasDocuments()
    {
        return $this->documents()->exists();
    }
    public function autorisation($demandeId)
    {
        $autorisation = Autorisation::where('demande_id', $demandeId)->first();
        return $autorisation;
    }
    // --- Accessors (Attributes) ---

    public function getHasIssuesAttribute(): bool
    {
        return $this->hasRejectionReasons() || $this->hasInvalidComponents();
    }

    public function getInvalidReasonsAttribute(): array
    {
        return $this->getInvalidComponents();
    }

    public function getRejectionReasonsListAttribute(): array
    {
        $list = [];
        foreach ($this->getRejectionReasons() as $dept => $data) {
            $list[] = "[" . strtoupper($dept) . "] " . $data['motif'];
        }
        return $list;
    }

    // --- Validation & State Management ---

public function isFullyValidated(): bool
{
    return $this->avions()
            ->where(function ($q) {
                $q->where('valider', false)->orWhereNull('valider');
            })->doesntExist()

        && $this->vols()
            ->where(function ($q) {
                $q->where('valider', false)->orWhereNull('valider');
            })->doesntExist()

        && $this->equipe()
            ->where(function ($q) {
                $q->where('valider', false)->orWhereNull('valider');
            })->doesntExist()

        && $this->fret()
            ->where(function ($q) {
                $q->where('valider', false)->orWhereNull('valider');
            })->doesntExist()

        && $this->receivingParties()
            ->where(function ($q) {
                $q->where('valider', false)->orWhereNull('valider');
            })->doesntExist()

        && $this->documents()
            ->where(function ($q) {
                $q->where('valider', false)->orWhereNull('valider');
            })->doesntExist();
}


public function isValidatedByAll(): bool
{
    $annotatedDirections = json_decode($this->directions_annotees) ?? [];
    if (empty($annotatedDirections)) return true;

    $state = $this->etatDemande;
    if (!$state) return false;

    foreach ($annotatedDirections as $direction) {
        $isValid = match ($direction) {
            'dsv'  => (bool) ($state->dsv_valider),
            'dsna' => (bool) ($state->dsna_valider),
            'dsad' => (bool) ($state->dsad_valider),
            'dsf'  => (bool) ($state->dsf_valider ?? false), // Si vous ajoutez dsf_valider
            'dg'   => (bool) ($state->dg_valider),
            default => false,
        };
        if (!$isValid) return false;
    }
    return true;
}
    public function validationProgress(): array
    {
        $components = [
            'avions' => $this->avions,
            'vols' => $this->vols,
            'equipage' => $this->equipe,
            'personne' => $this->personnes,
            'mdn' => $this->mdns,
            'fret' => $this->fret,
            'receiving_parties' => $this->receivingParties,
            'documents' => $this->documents,
        ];

        $totalItems = 0; $validatedItems = 0; $details = [];

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

    public function getInvalidComponents(): array
    {
        $invalid = [];
        
        $checks = [
            'avion' => ['rel' => $this->avions, 'id' => 'immatriculation'],
            'vol' => ['rel' => $this->vols, 'id' => 'numero_vol'],
            'equipage' => ['rel' => $this->equipe, 'id' => 'fonction'],
            'fret' => ['rel' => $this->fret, 'id' => 'nature'],
            'personne' => ['rel' => $this->mdns, 'id' => 'numero_mdn'],
            'receiving_party' => ['rel' => $this->receivingParties, 'id' => 'nom_contact'],
            'document' => ['rel' => $this->documents, 'id' => 'id'],
        ];

        foreach ($checks as $type => $config) {
            foreach ($config['rel'] as $item) {
                if ($item->valider !== true && !empty($item->motif)) {
                    $invalid[] = [
                        'type' => $type,
                        'id' => $item->id,
                        'identifier' => $item->{$config['id']},
                        'motif' => $item->motif,
                    ];
                }
            }
        }
        return $invalid;
    }
    public function hasInvalidComponents(): bool
{
    $relations = [
        'avions' => 'immatriculation',
        'vols' => 'numero_vol',
        'equipe' => 'fonction',
        'fret' => 'nature',
        'personnes' => 'nom_prenom',
        'mdns' => 'numero_mdn',
        
        'receivingParties' => 'nom_contact',
        'documents' => 'id',
    ];

    foreach ($relations as $rel => $identifier) {
        foreach ($this->$rel as $item) {
            if ($item->valider !== true && !empty($item->motif)) {
                return true;
            }
        }
    }

    return false;
}


    public function hasRejectionReasons(): bool
    {
        return !empty($this->dg_motif) || !empty($this->dta_motif) || 
               !empty($this->dsv_motif) || !empty($this->dsna_motif) || !empty($this->dsad_motif);
    }

    public function getRejectionReasons(): array
    {
        $reasons = [];
        $fields = ['dg_motif' => 'dg', 'dta_motif' => 'dta', 'dsv_motif' => 'dsv', 'dsna_motif' => 'dsna', 'dsad_motif' => 'dsad'];
        
        foreach ($fields as $field => $label) {
            if (!empty($this->$field)) {
                $reasons[$label] = [
                    'motif' => $this->$field,
                    'date' => $this->updated_at
                ];
            }
        }
        return $reasons;
    }

    public function getValidationStatus(): string
    {
        if ($this->statut === 'rejected') return 'Rejected';
        if ($this->isFullyValidated()) return 'Fully Validated';

        $progress = $this->validationProgress();
        if ($progress['percentage'] === 0) return 'Not Started';
        if ($progress['percentage'] === 100) return 'Pending Final Approval';

        return 'Partially Validated (' . $progress['percentage'] . '%)';
    }

    // --- Reset Methods ---

    public function resetAllMotifs(): void
    {
        $this->update([
            'dg_motif' => null, 'dta_motif' => null, 'dsv_motif' => null,
            'dsna_motif' => null, 'dsad_motif' => null,
        ]);

        $relations = ['avions', 'vols', 'equipe', 'fret','personnes','mdns', 'receivingParties', 'documents'];
        foreach ($relations as $rel) {
            $this->$rel()->update(['motif' => null]);
        }
    }

    public function resetAllValidations(): void
    {
        $this->update(['statut' => 'pending']);

        $relations = ['avions', 'vols', 'equipe', 'fret','personnes', 'receivingParties', 'documents'];
        foreach ($relations as $rel) {
            $this->$rel()->update(['valider' => null, 'motif' => null]);
        }

        if ($this->etatDemande) {
            $this->etatDemande->update([
                'dsv_valider' => false, 'dsna_valider' => false,
                'dsad_valider' => false, 'dg_valider' => false,
            ]);
        }
    }
    public function getEtatWorkflowAttribute()
{
    $state = $this->etatDemande; // relation OK maintenant

    if (!$state) {
        return 'draft';
    }

    if ($state->dg_rejeter || $state->dta_rejeter) {
        return 'rejected';
    } elseif ($state->daf_confirme_pay) {
        return 'payment_confirmed';
    } elseif ($state->compagnie_payer) {
        return 'paid';
    } elseif (
        $state->service_valider || $state->dsv_valider || 
        $state->dsna_valider || $state->dsad_valider || 
        $state->dta_valider || $state->dg_valider || $state->dta_dg_valider
    ) {
        return 'service_approved';
    } elseif ($state->dg_annoter || $state->dta_annoter || $state->service_annoter) {
        return 'under_review';
    } elseif ($state->compagnie_cree_demande) {
        return 'submitted';
    }

    return 'draft';
}
    
    /**
     * Accesseur pour typeVol (simule la relation pour la compatibilité)
     */
    public function getTypeVolAttribute()
    {
        // Si c'est une demande de type 3 avec plusieurs types de vol
        if ($this->type_demande_autorisation_id == 3 && $this->type_vol_ids) {
            $ids = is_array($this->type_vol_ids) ? $this->type_vol_ids : json_decode($this->type_vol_ids, true);
            if ($ids && !empty($ids)) {
                // Retourner le premier type de vol pour la compatibilité
                return TypeVol::find($ids[0]);
            }
        }
        
        // Pour les autres types, retourner la relation existante
        return $this->belongsTo(TypeVol::class, 'type_vol_id')->first();
    }
    
    /**
     * Accesseur pour type_vols_list
     */
    public function getTypeVolsListAttribute()
    {
        if ($this->type_demande_autorisation_id == 3 && $this->type_vol_ids) {
            $ids = is_array($this->type_vol_ids) ? $this->type_vol_ids : json_decode($this->type_vol_ids, true);
            if ($ids && !empty($ids)) {
                return TypeVol::whereIn('id', $ids)->get();
            }
        }
        return collect();
    }
    
    /**
     * Accesseur pour le premier type de vol ID
     */
    public function getFirstTypeVolIdAttribute()
    {
        if ($this->type_vol_id) {
            return $this->type_vol_id;
        }
        
        if ($this->type_demande_autorisation_id == 3 && $this->type_vol_ids) {
            $ids = is_array($this->type_vol_ids) ? $this->type_vol_ids : json_decode($this->type_vol_ids, true);
            if ($ids && !empty($ids)) {
                return $ids[0];
            }
        }
        
        return null;
    }
    
    /**
     * Accesseur pour les noms des types de vol (formatés)
     */
    public function getTypeVolNamesAttribute()
    {
        return $this->type_vols_list->pluck('nom')->implode(', ');
    }
}