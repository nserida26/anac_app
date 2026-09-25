<?php

namespace App\Console\Commands;

use App\Models\Licence;
use App\Services\LicenceExpirationService;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\Log;

class RecalculerExpirationLicences extends Command
{
    protected $signature = 'licences:recalculer-expiration {--force : Enregistrer les changements au lieu d\'une simple simulation}';
    protected $description = "Ramène la date d'expiration des licences enregistrées sur la règle actuelle (qualifications + compétence linguistique). Ne fait qu'avancer une date trop tardive, jamais la repousser.";

    public function handle(LicenceExpirationService $expirationService): int
    {
        $aCorriger = [];
        $licences = Licence::with(['demande.qualifications', 'demande.competences'])->get();

        foreach ($licences as $licence) {
            if (empty($licence->demande) || empty($licence->date_expiration)) {
                continue;
            }
            $nouvelleDate = $expirationService->calculer($licence->demande);
            if ($nouvelleDate && $nouvelleDate->lt($licence->date_expiration->copy()->startOfDay())) {
                $aCorriger[] = [$licence, $nouvelleDate];
            }
        }

        if (empty($aCorriger)) {
            $this->info("Rien à corriger : toutes les dates d'expiration respectent la règle.");
            return self::SUCCESS;
        }

        $this->table(
            ['Licence', 'Titulaire', 'Date actuelle', 'Nouvelle date', 'Statut après correction'],
            array_map(fn ($ligne) => [
                $ligne[0]->numero_licence,
                $ligne[0]->np,
                $ligne[0]->date_expiration->format('d/m/Y'),
                $ligne[1]->format('d/m/Y'),
                $ligne[1]->isPast() ? 'EXPIRÉE' : 'valide',
            ], $aCorriger)
        );
        $this->info(count($aCorriger) . ' licence(s) à corriger.');

        if (!$this->option('force')) {
            $this->warn('Simulation uniquement : relancer avec --force pour enregistrer.');
            return self::SUCCESS;
        }

        foreach ($aCorriger as [$licence, $nouvelleDate]) {
            $ancienneDate = $licence->date_expiration->format('Y-m-d');
            $licence->update(['date_expiration' => $nouvelleDate->format('Y-m-d')]);
            Log::info("Date d'expiration de licence recalculée", [
                'licence_id' => $licence->id,
                'numero_licence' => $licence->numero_licence,
                'ancienne_date' => $ancienneDate,
                'nouvelle_date' => $nouvelleDate->format('Y-m-d'),
            ]);
        }

        $this->info('Terminé : ' . count($aCorriger) . ' licence(s) mise(s) à jour.');
        return self::SUCCESS;
    }
}
