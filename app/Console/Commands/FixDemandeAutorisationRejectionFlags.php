<?php

namespace App\Console\Commands;

use App\Models\DemandeAutorisation;
use Illuminate\Console\Command;

class FixDemandeAutorisationRejectionFlags extends Command
{
    protected $signature = 'demande-autorisations:fix-rejection-flags {--force : Actually write the changes instead of a dry run}';
    protected $description = 'Backfill etat_demande_autorisations.dta_rejeter/dg_rejeter for demandes whose dta_motif/dg_motif is set but the flag is not, so they keep showing as "rejected" in the dir/admin lists.';

    public function handle(): int
    {
        $force = $this->option('force');

        $demandes = DemandeAutorisation::where(function ($q) {
            $q->whereNotNull('dta_motif')->where('dta_motif', '!=', '')
              ->orWhere(function ($q2) {
                  $q2->whereNotNull('dg_motif')->where('dg_motif', '!=', '');
              });
        })->get();

        $toFix = [];
        foreach ($demandes as $demande) {
            $state = $demande->etatDemande;
            $needsDta = !empty($demande->dta_motif) && (!$state || !$state->dta_rejeter);
            $needsDg = !empty($demande->dg_motif) && (!$state || !$state->dg_rejeter);

            if ($needsDta || $needsDg) {
                $toFix[] = [$demande, $needsDta, $needsDg];
            }
        }

        if (empty($toFix)) {
            $this->info('Nothing to fix — all rejected demandes already have consistent flags.');
            return self::SUCCESS;
        }

        $this->info(count($toFix) . ' demande(s) with a rejection motif but a missing/false reject flag:');
        foreach ($toFix as [$demande, $needsDta, $needsDg]) {
            $this->line(sprintf(
                '  #%d %s — needs_dta_rejeter=%s needs_dg_rejeter=%s',
                $demande->id,
                $demande->code,
                $needsDta ? 'yes' : 'no',
                $needsDg ? 'yes' : 'no'
            ));
        }

        if (!$force) {
            $this->warn('Dry run only — re-run with --force to apply these changes.');
            return self::SUCCESS;
        }

        foreach ($toFix as [$demande, $needsDta, $needsDg]) {
            $state = $demande->etatDemande()->firstOrCreate([]);
            $update = [];
            if ($needsDta) {
                $update['dta_rejeter'] = true;
            }
            if ($needsDg) {
                $update['dg_rejeter'] = true;
            }
            $state->update($update);
        }

        $this->info('Done — flags backfilled for ' . count($toFix) . ' demande(s).');
        return self::SUCCESS;
    }
}
