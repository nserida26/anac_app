<?php

namespace App\Console\Commands;

use App\Models\DemandeAutorisation;
use App\Models\User;
use App\Services\DtaAutorisationNotificationService;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\Log;

class RelanceDemandesEnAttente extends Command
{
    protected $signature = 'demandes-autorisations:relance-en-attente {--jours=3 : Nombre de jours sans activité avant relance}';

    protected $description = "Relance l'acteur responsable pour chaque demande d'autorisation bloquée depuis plusieurs jours à une étape du workflow (Chemin 11 du parcours).";

    public function handle(DtaAutorisationNotificationService $notificationService): int
    {
        $seuilJours = (int) $this->option('jours');

        $demandes = DemandeAutorisation::with(['etatDemande', 'type', 'user'])
            ->whereHas('etatDemande', function ($q) {
                $q->where('compagnie_cree_demande', true)
                    ->where('dg_rejeter', false)
                    ->where('dta_rejeter', false);
            })
            ->get()
            ->filter(fn (DemandeAutorisation $demande) => !$demande->autorisation($demande->id));

        if ($demandes->isEmpty()) {
            $this->info('Aucune demande en attente à relancer.');
            return self::SUCCESS;
        }

        $relances = 0;

        foreach ($demandes as $demande) {
            $lastActivity = optional($demande->etatDemande)->updated_at ?? $demande->updated_at;

            if (!$lastActivity || $lastActivity->diffInDays(now()) < $seuilJours) {
                continue;
            }

            if ($demande->last_relance_at && $demande->last_relance_at->diffInDays(now()) < $seuilJours) {
                continue;
            }

            $pendingActors = $this->resolvePendingActors($demande);

            if (empty($pendingActors)) {
                continue;
            }

            foreach ($pendingActors as $actor) {
                /** @var User|null $user */
                $user = $actor['user'];
                if (!$user || empty($user->whatsapp)) {
                    continue;
                }

                $notificationService->sendApplicationActionRequired(
                    demandeNumber: $demande->code,
                    demandeType: $demande->type->libelle ?? '',
                    recipientRole: $actor['role'],
                    recipientPhone: $user->whatsapp,
                    actionType: 'reminder',
                    applicantName: optional(optional($demande->user)->demandeur)->np ?? '',
                );
            }

            $demande->update(['last_relance_at' => now()]);
            $relances++;

            $this->line(sprintf(
                '  Relance envoyée — %s (bloqué depuis %d j chez %s)',
                $demande->code,
                $lastActivity->diffInDays(now()),
                implode(', ', array_column($pendingActors, 'role'))
            ));

            Log::info('Relance demande autorisation en attente', [
                'demande_id' => $demande->id,
                'code' => $demande->code,
                'jours_bloques' => $lastActivity->diffInDays(now()),
                'acteurs' => array_column($pendingActors, 'role'),
            ]);
        }

        $this->info("{$relances} demande(s) relancée(s) sur {$demandes->count()} en attente.");

        return self::SUCCESS;
    }

    /**
     * Détermine qui doit agir sur le dossier à l'étape actuelle du workflow,
     * en suivant le même enchaînement que updateDemandeState()/role-actions.blade.php.
     *
     * @return array<int, array{role: string, user: ?User}>
     */
    protected function resolvePendingActors(DemandeAutorisation $demande): array
    {
        $etat = $demande->etatDemande;
        if (!$etat) {
            return [];
        }

        $dg = User::role('dg')->whereHas('signature', fn ($q) => $q->whereNotNull('signature'))->latest()->first();
        $dta = User::role('dta')->whereHas('signature', fn ($q) => $q->whereNotNull('signature'))->latest()->first();
        $srta = User::role('admin')->whereHas('permissions', fn ($q) => $q->where('name', 'menage-vi'))
            ->whereHas('signature', fn ($q) => $q->whereNotNull('signature'))->latest()->first();
        $daf = User::role('daf')->latest()->first();

        // Le DG n'a pas encore pris connaissance du dossier
        if (!$etat->dg_annoter && !$etat->dg_annoter_admin && !$etat->dta_dg_annoter) {
            return [['role' => 'DG', 'user' => $dg]];
        }

        // La DTA n'a pas encore transmis au service (et n'a pas utilisé le raccourci "valider tout")
        if (!$etat->dta_annoter && !$etat->service_tout_valider) {
            return [['role' => 'DTA', 'user' => $dta]];
        }

        // Des directions ont été saisies mais toutes n'ont pas encore validé
        if ($demande->isAnnoted() && !$demande->isValidatedByAll()) {
            $directions = json_decode($demande->directions_annotees ?? '[]') ?? [];
            $directionUsers = [
                'dsv' => User::role('dsv')->latest()->first(),
                'dsna' => User::role('dsna')->latest()->first(),
                'dsad' => User::role('dsad')->latest()->first(),
                'dsf' => User::role('dsf')->latest()->first(),
            ];

            $pending = [];
            foreach ($directions as $direction) {
                $isValide = match ($direction) {
                    'dsv' => (bool) $etat->dsv_valider,
                    'dsna' => (bool) $etat->dsna_valider,
                    'dsad' => (bool) $etat->dsad_valider,
                    'dsf' => (bool) ($etat->dsf_valider ?? false),
                    default => true,
                };
                if (!$isValide && isset($directionUsers[$direction])) {
                    $pending[] = ['role' => strtoupper($direction), 'user' => $directionUsers[$direction]];
                }
            }

            if (!empty($pending)) {
                return $pending;
            }
        }

        // Le service (SRTA) n'a pas encore donné sa validation finale
        if (!$etat->service_valider && !$etat->srta_valider && !$etat->service_tout_valider) {
            return [['role' => 'SRTA', 'user' => $srta]];
        }

        // La DTA n'a pas encore validé avant signature du DG
        if (!$etat->dta_valider) {
            return [['role' => 'DTA', 'user' => $dta]];
        }

        // Le DG n'a pas encore donné son accord final
        if (!$etat->dg_valider && !$etat->dta_dg_valider) {
            return [['role' => 'DG', 'user' => $dg]];
        }

        // Cas paiement (atterrissage) : facture en attente de paiement ou de confirmation
        $isPayable = (int) $demande->type_demande_autorisation_id === 2
            && in_array((int) $demande->type_vol_id, [1, 2, 5, 8, 14], true);

        if ($isPayable) {
            if (!$etat->compagnie_payer) {
                return [['role' => 'COMPAGNIE', 'user' => $demande->user]];
            }
            if (!$etat->daf_confirme_pay) {
                return [['role' => 'DAF', 'user' => $daf]];
            }
        }

        return [];
    }
}
