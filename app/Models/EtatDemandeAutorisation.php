<?php
// App\Models\EtatDemandeAutorisation.php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\DB;

class EtatDemandeAutorisation extends Model
{
    protected $table = 'etat_demande_autorisations';

    protected $fillable = [
        'compagnie_cree_demande',
        'compagnie_rectifie_demande', // Nouveau
        'dg_annoter',
        'dg_annoter_admin', // Nouveau
        'dta_dg_annoter',
        'dg_rejeter',
        'dta_annoter',
        'dta_rejeter',
        'service_annoter',
        'service_raturer',
        'dsv_valider',
        'dsna_valider',
        'dsad_valider',
        'service_valider',
        'service_tout_valider',
        'dta_valider',
        'dg_valider',
        'dta_dg_valider',
        'daf_demande_pay',
        'compagnie_payer',
        'daf_confirme_pay',
        
        'service_envoyer',
        'user_id',
        'demande_id',
        'dta_notifier'
    ];

    protected $casts = [
        'compagnie_cree_demande' => 'boolean',
        'compagnie_rectifie_demande' => 'boolean', // Nouveau
        'dg_annoter'             => 'boolean',
        'dg_annoter_admin'       => 'boolean', // Nouveau
        'dta_dg_annoter'         => 'boolean',
        'dg_rejeter'             => 'boolean',
        'dta_annoter'            => 'boolean',
        'dta_rejeter'            => 'boolean',
        'service_annoter'        => 'boolean',
        'service_raturer'        => 'boolean',
        'dsv_valider'            => 'boolean',
        'dsna_valider'           => 'boolean',
        'dsad_valider'           => 'boolean',
        'service_valider'        => 'boolean',
        'service_tout_valider'   => 'boolean',
        'dta_valider'            => 'boolean',
        'dg_valider'             => 'boolean',
        'dta_dg_valider'         => 'boolean',
        'daf_demande_pay'        => 'boolean',
        'compagnie_payer'        => 'boolean',
        'daf_confirme_pay'       => 'boolean',
        'service_envoyer'        => 'boolean',
        'dta_notifier'           => 'boolean',
    ];

    public function demande()
    {
        return $this->belongsTo(DemandeAutorisation::class, 'demande_id');
    }

    public function user()
    {
        return $this->belongsTo(User::class, 'user_id');
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

    protected function resetRelatedStates($action)
    {
        $relatedActions = $this->getRelatedActions($action);
        foreach ($relatedActions as $relatedAction) {
            $this->{$relatedAction} = false;
        }
    }

    protected function setValidationState($action)
    {
        if (str_contains($action, '_annoter')) {
            $validationAction = str_replace('_annoter', '_valider', $action);
            if (in_array($validationAction, $this->fillable)) {
                $this->{$validationAction} = true;
            }
        }
    }

    protected function setRejectionState($action)
    {
        if (str_contains($action, '_annoter')) {
            $rejectionAction = str_replace('_annoter', '_rejeter', $action);
            if (in_array($rejectionAction, $this->fillable)) {
                $this->{$rejectionAction} = true;
            }
        } else {
            $this->{$action} = false;
        }
    }

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

    public static function updateGlobalStatus($demandeId)
    {
        $state = self::where('demande_id', $demandeId)->first();
        if (!$state || !$state->demande) return;

        $demande = $state->demande;

        if ($state->dg_rejeter || $state->dta_rejeter) {
            $status = 'rejected';
        } elseif ($state->daf_confirme_pay) {
            $status = 'payment_confirmed';
        } elseif ($state->compagnie_payer) {
            $status = 'paid';
        } elseif (
            $state->service_valider || $state->dsv_valider || 
            $state->dsna_valider || $state->dsad_valider || 
            $state->dta_valider || $state->dg_valider || $state->dta_dg_valider
        ) {
            $status = 'service_approved';
        } elseif ($state->dg_annoter || $state->dg_annoter_admin || $state->dta_annoter || $state->service_annoter) {
            $status = 'under_review';
        } elseif ($state->compagnie_cree_demande || $state->compagnie_rectifie_demande) {
            $status = 'submitted';
        } else {
            $status = 'draft';
        }

        $demande->update(['status' => $status]);
    }

    public function resetAllApprovalStates()
    {
        $this->update([
            'compagnie_cree_demande' => true,
            'compagnie_rectifie_demande' => false, // Nouveau
            'dg_annoter'             => false,
            'dg_annoter_admin'       => false, // Nouveau
            'dta_dg_annoter'         => false,
            //'dg_rejeter'             => false,
            'dta_annoter'            => false,
            //'dta_rejeter'            => false,
            'service_annoter'        => false,
            'service_valider'        => false,
            'dta_valider'            => false,
            'dg_valider'             => false,
            'dta_dg_valider'         => false,
            'dsv_valider'            => false,
            'dsad_valider'           => false,
            'dsna_valider'           => false,
            'dsf_valider'            => false, // Nouveau
            'service_tout_valider'   => false,
            'daf_demande_pay'        => false,
            'compagnie_payer'        => false,
            'daf_confirme_pay'       => false,
            'service_envoyer'        => false,
            'dta_notifier'           => false,
        ]);

        self::updateGlobalStatus($this->demande_id);
        return $this;
    }
}