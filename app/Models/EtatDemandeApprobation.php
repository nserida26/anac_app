<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\DB;

class EtatDemandeApprobation extends Model
{
    use HasFactory;

    /**
     * The table associated with the model.
     *
     * @var string
     */
    protected $table = 'etat_demande_approbations';

    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = [
        'compagnie_cree_demande',
        'dg_annoter',
        'dta_dg_annoter',
        'dg_rejeter',
        'dta_annoter',
        'dta_rejeter',
        'service_annoter',
        'service_valider',
        'dta_valider',
        'dg_valider',
        'dta_dg_valider',
        'dsv_valider',
        'dsad_valider',
        'dsna_valider',
        'service_tout_valider',
        'user_id',
        'demande_id',
        'dta_notifier'
    ];

    /**
     * The attributes that should be cast.
     *
     * @var array
     */
    protected $casts = [
        'compagnie_cree_demande' => 'boolean',
        'dg_annoter' => 'boolean',
        'dta_dg_annoter' => 'boolean',
        'dg_rejeter' => 'boolean',
        'dta_annoter' => 'boolean',
        'dta_rejeter' => 'boolean',
        'service_annoter' => 'boolean',
        'service_valider' => 'boolean',
        'dta_valider' => 'boolean',
        'dg_valider' => 'boolean',
        'dta_dg_valider' => 'boolean',
        'daf_demande_pay' => 'boolean',
        'service_tout_valider' => 'boolean',
        'created_at' => 'datetime',
        'updated_at' => 'datetime'
    ];

    /**
     * Get the user associated with this state.
     */
    public function user()
    {
        return $this->belongsTo(User::class);
    }

    /**
     * Get the demande associated with this state.
     */
    public function demande()
    {
        return $this->belongsTo(DemandeApprobation::class, 'demande_id');
    }

    /**
     * Update the authorization request state
     */
    public static function updateState($demandeId, $action, $userId, $isApproved = false, $isRejected = false)
    {
        try {
            DB::beginTransaction();

            $state = self::firstOrCreate(['demande_id' => $demandeId]);

            // Reset conflicting states
            $state->resetRelatedStates($action);

            // Set the current action
            if ($isApproved) {
                $state->{$action} = true;
                $state->setValidationState($action);
            } elseif ($isRejected) {
                $state->setRejectionState($action);
            } else {
                $state->{$action} = true;
            }


            $state = self::updateOrCreate(
                ['demande_id' => $demandeId],
                [
                    $action => 1,
                    'user_id' => $userId,
                ]
            );

            // Update global status
            self::updateGlobalStatus($demandeId);

            DB::commit();

            return $state;
        } catch (\Exception $e) {
            DB::rollBack();
            throw $e;
        }
    }

    /**
     * Reset related states to avoid conflicts
     */
    protected function resetRelatedStates($action)
    {
        $relatedActions = $this->getRelatedActions($action);

        foreach ($relatedActions as $relatedAction) {
            $this->{$relatedAction} = false;
        }
    }

    /**
     * Set validation state when approving
     */
    protected function setValidationState($action)
    {
        if (str_contains($action, '_annoter')) {
            $validationAction = str_replace('_annoter', '_valider', $action);
            $this->{$validationAction} = true;
        }
    }

    /**
     * Set rejection state
     */
    protected function setRejectionState($action)
    {
        if (str_contains($action, '_annoter')) {
            $rejectionAction = str_replace('_annoter', '_rejeter', $action);
            $this->{$rejectionAction} = true;
        } else {
            $this->{$action} = false;
        }
    }

    /**
     * Get all related actions that should be reset
     */
    protected function getRelatedActions($action)
    {
        $related = [];

        if (str_contains($action, 'annoter')) {
            $related[] = str_replace('_annoter', '_rejeter', $action);
            $related[] = str_replace('_annoter', '_valider', $action);
        }

        if (str_contains($action, 'valider')) {
            $related[] = str_replace('_valider', '_annoter', $action);
            $related[] = str_replace('_valider', '_rejeter', $action);
        }

        return array_filter($related);
    }

    /**
     * Update the global status of the demande
     */
    public static function updateGlobalStatus($demandeId)
    {
        $state = self::where('demande_id', $demandeId)->first();

        if (!$state || !$state->demande) return;

        $demande = $state->demande;

        if ($state->dg_rejeter || $state->dta_rejeter) {
            $status = 'rejected';
        } //elseif ($state->dg_signer && $state->dta_signer) {
        //$status = 'completed';
        //} 
        elseif ($state->daf_confirme_pay) {
            $status = 'payment_confirmed';
        } elseif ($state->compagnie_payer) {
            $status = 'paid';
        } elseif (
            $state->service_valider
            || $state->dsv_valider
            || $state->dsna_valider
            || $state->dsad_valider
            || $state->dta_valider
            || $state->dg_valider
        ) {
            $status = 'service_approved';
        } elseif ($state->dg_annoter || $state->dta_annoter || $state->service_annoter) {
            $status = 'under_review';
        } else {
            $status = 'draft';
        }

        $demande->update(['status' => $status]);
    }
    /**
     * Reset all approval state flags to false
     */
    public function resetAllApprovalStates()
    {
        $this->update([
            'compagnie_cree_demande' => false,
            'dg_annoter' => false,
            'dta_dg_annoter' => false,
            'dg_rejeter' => false,
            'dta_annoter' => false,
            'dta_rejeter' => false,
            'service_annoter' => false,
            'service_valider' => false,
            'dta_valider' => false,
            'dg_valider' => false,
            'dta_dg_valider' => false,
            'dsv_valider' => false,
            'dsad_valider' => false,
            'dsna_valider' => false,
            'service_tout_valider' => false,
        ]);

        // Update global status after reset
        self::updateGlobalStatus($this->demande_id);

        return $this;
    }
}
