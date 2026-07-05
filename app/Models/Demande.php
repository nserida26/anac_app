<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Demande extends Model
{
    use HasFactory;

    protected $fillable = [
        'code',
        'date',
        'description',
        'signature',
        'nom_responsable',
        'type_demande_id',
        'type_licence_id',
        'status',
        'demandeur_id',
        'checklist_admin',
        'checklist_sla',
        'checklist_sma',
        'evaluateur_id',
        'motif_dg',
        'motif_dsv'
    ];
 protected $appends = ['etat_workflow'];
    // Relationships
    public function demandeur()
    {
        return $this->belongsTo(Demandeur::class, 'demandeur_id');
    }
    
    public function evaluateur()
    {
        return $this->belongsTo(Evaluateur::class, 'evaluateur_id');
    }

    public function etatDemande()
    {
        return $this->hasOne(EtatDemande::class, 'demande_id');
    }

    public function licence()
    {
        return $this->hasOne(Licence::class, 'demande_id');
    }
    public function carteStagiare()
    {
        return $this->hasOne(CarteStagiare::class, 'demande_id');
    }

    public function validation()
    {
        return $this->hasOne(ValidationLicence::class, 'demande_id');
    }
public function pieces()
{
    return $this->hasMany(DemandePiece::class);
}
    public function paiement()
    {
        return $this->hasOne(Paiement::class, 'demande_id');
    }

    public function ordre()
    {
        return $this->hasOne(OrdreRecette::class, 'demande_id');
    }

    public function facture()
    {
        return $this->hasOne(Facture::class, 'demande_id');
    }

    public function typeDemande()
    {
        return $this->belongsTo(TypeDemande::class, 'type_demande_id');
    }

    public function typeLicence()
    {
        return $this->belongsTo(TypeLicence::class, 'type_licence_id');
    }

    public function licences()
    {
        return $this->hasMany(LicenceDemandeur::class);
    }

    public function documents()
    {
        return $this->hasMany(Document::class);
    }

    public function medicalExaminations()
    {
        return $this->hasMany(MedicalExamination::class, 'demande_id');
    }

    public function experiences()
    {
        return $this->hasMany(ExperienceDemandeur::class);
    }

    public function competences()
    {
        return $this->hasMany(CompetenceDemandeur::class);
    }

    public function qualifications()
    {
        return $this->hasMany(QualificationDemandeur::class, 'demande_id');
    }

    public function trainings()
    {
        return $this->hasMany(TrainingDemandeur::class, 'demande_id');
    }

    // New methods for state management
    public function isFullyValidated(): bool
    {
        return $this->licences()->where('valider', false)->doesntExist()
            && $this->documents()->where('valider', false)->doesntExist()
            && $this->medicalExaminations()->where('valider', false)->doesntExist()
            && $this->qualifications()->where('valider', false)->doesntExist()
            && $this->trainings()->where('valider', false)->doesntExist()
            && $this->experiences()->where('valider', false)->doesntExist()
            && $this->competences()->where('valider', false)->doesntExist()
            && $this->status === 'validated';
    }

    public function isValidatedByAll(): bool
    {
        $state = $this->etatDemande;
        if (!$state) {
            return false;
        }

        return $state->evaluateur_valider
            && $state->sm_valider
            && $state->sl_valider
            && $state->pel_valider
            && $state->dsv_valider
            && $state->dg_valider;
    }

    public function validationProgress(): array
    {
        $components = [
            'licences' => $this->licences,
            'documents' => $this->documents,
            'medical_examinations' => $this->medicalExaminations,
            'qualifications' => $this->qualifications,
            'trainings' => $this->trainings,
            'experiences' => $this->experiences,
            'competences' => $this->competences,
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

    public function getInvalidComponents(): array
    {
        $invalid = [];

        // Check licences
        foreach ($this->licences as $licence) {
            if ($licence->valider === false) {
                $invalid[] = [
                    'type' => 'licence',
                    'id' => $licence->id,
                    'identifier' => $licence->num_licence,
                    'motif' => $licence->motif,
                ];
            }
        }

        // Check documents
        foreach ($this->documents as $document) {
            if ($document->valider === false) {
                $invalid[] = [
                    'type' => 'document',
                    'id' => $document->id,
                    'identifier' => $document->nom_fr,
                    'motif' => $document->motif,
                ];
            }
        }

        // Check medical examinations
        foreach ($this->medicalExaminations as $exam) {
            if ($exam->valider === false) {
                $invalid[] = [
                    'type' => 'medical_examination',
                    'id' => $exam->id,
                    'identifier' => $exam->date_examen,
                    'motif' => $exam->motif,
                ];
            }
        }

        // Check qualifications
        foreach ($this->qualifications as $qualification) {
            if ($qualification->valider === false) {
                $invalid[] = [
                    'type' => 'qualification',
                    'id' => $qualification->id,
                    'identifier' => $qualification->qualification,
                    'motif' => $qualification->motif,
                ];
            }
        }

        // Check trainings
        foreach ($this->trainings as $training) {
            if ($training->valider === false) {
                $invalid[] = [
                    'type' => 'training',
                    'id' => $training->id,
                    'identifier' => $training->id,
                    'motif' => $training->motif,
                ];
            }
        }

        // Check experiences
        foreach ($this->experiences as $experience) {
            if ($experience->valider === false) {
                $invalid[] = [
                    'type' => 'experience',
                    'id' => $experience->id,
                    'identifier' => $experience->nature,
                    'motif' => $experience->motif,
                ];
            }
        }

        // Check competences
        foreach ($this->competences as $competence) {
            if ($competence->valider === false) {
                $invalid[] = [
                    'type' => 'competence',
                    'id' => $competence->id,
                    'identifier' => $competence->type,
                    'motif' => $competence->motif,
                ];
            }
        }

        return $invalid;
    }

    public function hasRejectionReasons(): bool
    {
        return !empty($this->motif_dg) || !empty($this->motif_dsv);
    }

    public function getRejectionReasons(): array
    {
        $reasons = [];

        if (!empty($this->motif_dg)) {
            $reasons['dg'] = [
                'motif' => $this->motif_dg,
                'date' => $this->updated_at
            ];
        }

        if (!empty($this->motif_dsv)) {
            $reasons['dsv'] = [
                'motif' => $this->motif_dsv,
                'date' => $this->updated_at
            ];
        }

        return $reasons;
    }

    public function getValidationStatus(): string
    {
        if ($this->status === 'rejected') {
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

    public function isDepartmentValidated(string $department): bool
    {
        $state = $this->etatDemande;
        if (!$state) return false;

        return match ($department) {
            'evaluateur' => $state->evaluateur_valider,
            'sm' => $state->sm_valider,
            'sl' => $state->sl_valider,
            'pel' => $state->pel_valider,
            'dsv' => $state->dsv_valider,
            'dg' => $state->dg_valider,
            default => false,
        };
    }

    public function getHasIssuesAttribute(): bool
    {
        return $this->hasRejectionReasons() || count($this->getInvalidComponents()) > 0;
    }

    public function getInvalidReasonsAttribute(): array
    {
        return $this->getInvalidComponents();
    }

    public function getRejectionReasonsListAttribute(): array
    {
        $reasons = [];
        foreach ($this->getRejectionReasons() as $dept => $reason) {
            $reasons[] = "[" . strtoupper($dept) . "] " . $reason['motif'];
        }
        return $reasons;
    }

    /**
     * Reset all rejection motifs to null
     */
    public function resetAllMotifs(): void
    {
        $this->update([
            'motif_dg' => null,
            'motif_dsv' => null,
        ]);

        // Reset all related model motifs
        $this->licences()->update(['motif' => null]);
        $this->documents()->update(['motif' => null]);
        $this->medicalExaminations()->update(['motif' => null]);
        $this->qualifications()->update(['motif' => null]);
        $this->trainings()->update(['motif' => null]);
        $this->experiences()->update(['motif' => null]);
        $this->competences()->update(['motif' => null]);
    }

    /**
     * Reset specific department rejection motifs
     */
    public function resetDepartmentMotifs(string $department): void
    {
        switch ($department) {
            case 'dg':
                $this->update(['motif_dg' => null]);
                break;
            case 'dsv':
                $this->update(['motif_dsv' => null]);
                break;
            case 'all':
            default:
                $this->resetAllMotifs();
                break;
        }
    }

    /**
     * Reset validation status for all components
     */
    public function resetAllValidations(): void
    {
        $this->update(['status' => 'pending']);

        $this->licences()->update(['valider' => true, 'motif' => null]);
        $this->documents()->update(['valider' => true, 'motif' => null]);
        $this->medicalExaminations()->update(['valider' => true, 'motif' => null]);
        $this->qualifications()->update(['valider' => true, 'motif' => null]);
        $this->trainings()->update(['valider' => true, 'motif' => null]);
        $this->experiences()->update(['valider' => true, 'motif' => null]);
        $this->competences()->update(['valider' => true, 'motif' => null]);

        // Reset state if exists
        if ($this->etatDemande) {
            $this->etatDemande->update([
                'evaluateur_valider' => false,
                'sm_valider' => false,
                'sl_valider' => false,
                'pel_valider' => false,
                'dsv_valider' => false,
                'dg_valider' => false,
            ]);
        }
    }
    
public function checklistReponses(): HasMany
    {
        return $this->hasMany(ChecklistDemande::class);
    }
public function getEtatWorkflowAttribute()
{
    $state = $this->etatDemande;

    if (!$state) {
        return 'draft';
    }

    // ordre du plus avancé au moins avancé
    if ($state->pel_licence_valider) {
        return 'printed';
    }

    if ($state->pel_valider) {
        return 'service_approved';
    }

    if ($state->daf_confirme_pay) {
        return 'payment_confirmed';
    }

    if ($state->demandeur_payer) {
        return 'paid';
    }

    if ($state->dg_rejeter || $state->dsv_rejeter) {
        return 'rejected';
    }

    if ($state->dg_annoter) {
        return 'under_review';
    }

    if ($state->demandeur_cree_demande) {
        return 'submitted';
    }

    return 'draft';
}

}
