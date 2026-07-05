<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\DB;

class EtatDemande extends Model
{
    protected $fillable = [
        'demandeur_cree_demande',
        'dg_annoter',
        'dsv_dg_annoter',
        'dg_rejeter',
        'dsv_dg_rejeter',
        'dsv_annoter',
        'dsv_rejeter',
        'pel_annoter',
        'evaluateur_annoter',
        'evaluateur_valider',
        'sm_valider',
        'sl_valider',
        'pel_valider',
        'dsv_valider',
        'dg_valider',
        'dsv_dg_valider',
        'dsv_recette',
        'daf_demande_pay',
        'daf_confirme_pay',
        'demandeur_payer',
        'compagnie_payer',
        'agent_enroler',
        'pel_valider_enrol',
        'dg_signer',
        'dsv_dg_signer',
        'dsv_signer',
        'pel_dsv_signer',
        'pel_licence_valider',
        'agent_imprimer',
        'user_id',
        'demande_id'
    ];

    protected $casts = [
        'demandeur_cree_demande' => 'boolean',
        'dg_annoter' => 'boolean',
        'dsv_dg_annoter' => 'boolean',
        'dg_rejeter' => 'boolean',
        'dsv_dg_rejeter' => 'boolean',
        'dsv_annoter' => 'boolean',
        'dsv_rejeter' => 'boolean',
        'pel_annoter' => 'boolean',
        'evaluateur_annoter' => 'boolean',
        'evaluateur_valider' => 'boolean',
        'sm_valider' => 'boolean',
        'sl_valider' => 'boolean',
        'pel_valider' => 'boolean',
        'dsv_valider' => 'boolean',
        'dg_valider' => 'boolean',
        'dsv_dg_valider' => 'boolean',
        'dsv_recette' => 'boolean',
        'daf_demande_pay' => 'boolean',
        'daf_confirme_pay' => 'boolean',
        'demandeur_payer' => 'boolean',
        'compagnie_payer' => 'boolean',
        'agent_enroler' => 'boolean',
        'pel_valider_enrol' => 'boolean',
        'dg_signer' => 'boolean',
        'dsv_dg_signer' => 'boolean',
        'dsv_signer' => 'boolean',
        'pel_dsv_signer' => 'boolean',
        'pel_licence_valider' => 'boolean',
        'agent_imprimer' => 'boolean',
    ];

    public function demande()
    {
        return $this->belongsTo(Demande::class, 'demande_id');
    }

    public function user()
    {
        return $this->belongsTo(User::class, 'user_id');
    }

    public static function updateState($demandeId, $action, $userId, $isApproved = false, $isRejected = false)
    {
        try {
            DB::beginTransaction();

            $state = self::firstOrCreate(['demande_id' => $demandeId]);
            $state->resetRelatedStates($action);

            if ($isApproved) {
                $state->{$action} = true;
                $state->setValidationState($action);
            } elseif ($isRejected) {
                $state->setRejectionState($action);
            } else {
                $state->{$action} = true;
            }

            $state->user_id = $userId;
            $state->save();

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
            $this->{$validationAction} = true;
        }
    }

    protected function setRejectionState($action)
    {
        if (str_contains($action, '_annoter')) {
            $rejectionAction = str_replace('_annoter', '_rejeter', $action);
            $this->{$rejectionAction} = true;
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

        if ($state->dg_rejeter || $state->dsv_rejeter || $state->dsv_dg_rejeter) {
            $status = 'rejected';
        } elseif ($state->agent_imprimer) {
            $status = 'completed';
        } elseif ($state->daf_confirme_pay) {
            $status = 'payment_confirmed';
        } elseif ($state->compagnie_payer || $state->demandeur_payer) {
            $status = 'paid';
        } elseif ($state->dsv_valider || $state->dg_valider || $state->dsv_dg_valider) {
            $status = 'service_approved';
        } elseif ($state->dg_annoter || $state->dsv_annoter || $state->dsv_dg_annoter) {
            $status = 'under_review';
        } else {
            $status = 'draft';
        }

        $demande->update(['status' => $status]);
    }

    public function resetAllApprovalStates()
    {
        $this->update([
            'demandeur_cree_demande' => true,
            'dg_annoter' => true,
            'dsv_dg_annoter' => false,
            'dg_rejeter' => false,
            'dsv_dg_rejeter' => false,
            'dsv_annoter' => true,
            'dsv_rejeter' => false,
            'pel_annoter' => false,
            'evaluateur_annoter' => false,
            'evaluateur_valider' => false,
            'sm_valider' => false,
            'sl_valider' => false,
            'pel_valider' => false,
            'dsv_valider' => false,
            'dg_valider' => false,
            'dsv_dg_valider' => false,
            'dsv_recette' => false,
            'daf_demande_pay' => false,
            'compagnie_payer' => false,
            'daf_confirme_pay' => false,
            'demandeur_payer' => false,
            'agent_enroler' => false,
            'pel_valider_enrol' => false,
            'dg_signer' => false,
            'dsv_dg_signer' => false,
            'dsv_signer' => false,
            'pel_dsv_signer' => false,
            'pel_licence_valider' => false,
            'agent_imprimer' => false,
        ]);

        self::updateGlobalStatus($this->demande_id);
        return $this;
    }

    public function approveAllStates()
    {
        $this->update([
            'dg_annoter' => true,
            'dsv_dg_annoter' => true,
            'dg_rejeter' => false,
            'dsv_dg_rejeter' => false,
            'dsv_annoter' => true,
            'dsv_rejeter' => false,
            'pel_annoter' => true,
            'evaluateur_annoter' => true,
            'evaluateur_valider' => true,
            'sm_valider' => true,
            'sl_valider' => true,
            'pel_valider' => true,
            'dsv_valider' => true,
            'dg_valider' => true,
            'dsv_dg_valider' => true,
            'dsv_recette' => true,
            'daf_demande_pay' => true,
            'compagnie_payer' => true,
            'daf_confirme_pay' => true,
            'demandeur_payer' => true,
            'agent_enroler' => true,
            'pel_valider_enrol' => true,
            'dg_signer' => true,
            'dsv_dg_signer' => true,
            'dsv_signer' => true,
            'pel_dsv_signer' => true,
            'pel_licence_valider' => false,
            'agent_imprimer' => false,
        ]);

        self::updateGlobalStatus($this->demande_id);
        return $this;
    }
}
