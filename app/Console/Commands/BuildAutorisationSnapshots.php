<?php

namespace App\Console\Commands;

use App\Models\Autorisation;
use App\Models\AutorisationSnapshot;
use Illuminate\Console\Command;

/**
 * (Re)construit la table figée `autorisation_snapshots`.
 *
 * Le snapshot est normalement créé automatiquement à la délivrance
 * (Autorisation::booted). Cette commande sert au rattrapage : autorisations
 * délivrées avant la mise en place du mécanisme, ou snapshot ayant échoué.
 *
 * Par défaut, ne touche qu'aux autorisations SANS snapshot (--missing).
 * --all reconstruit tous les snapshots à partir des données actuelles.
 */
class BuildAutorisationSnapshots extends Command
{
    protected $signature = 'autorisations:snapshot
        {--id= : Ne traiter que cette autorisation}
        {--all : Reconstruire aussi les snapshots existants (données actuelles)}
        {--force : Écrire réellement (sinon dry-run)}';

    protected $description = "Construit/reconstruit la table figée des informations demandeur & opérateur des autorisations.";

    public function handle(): int
    {
        $force = (bool) $this->option('force');
        $all   = (bool) $this->option('all');

        $query = Autorisation::query()->has('demande');

        if ($id = $this->option('id')) {
            $query->whereKey($id);
        } elseif (!$all) {
            $query->whereDoesntHave('snapshot');
        }

        $autorisations = $query->get();

        if ($autorisations->isEmpty()) {
            $this->info('Aucune autorisation à traiter.');
            return self::SUCCESS;
        }

        $this->info(($force ? '' : '[DRY-RUN] ') . $autorisations->count() . ' autorisation(s) à traiter.');

        $done = 0;
        foreach ($autorisations as $autorisation) {
            if (!$force) {
                $this->line(" - #{$autorisation->id} {$autorisation->code_autorisation}");
                continue;
            }

            try {
                $snap = AutorisationSnapshot::buildFor($autorisation);
                $done++;
                $this->line(" ✓ #{$autorisation->id} {$autorisation->code_autorisation} → {$snap->demandeur_np} / {$snap->operateur_nom}");
            } catch (\Throwable $e) {
                $this->error(" ✗ #{$autorisation->id} : {$e->getMessage()}");
            }
        }

        if ($force) {
            $this->info("Terminé : {$done} snapshot(s) écrit(s).");
        } else {
            $this->comment('Relancer avec --force pour écrire.');
        }

        return self::SUCCESS;
    }
}
