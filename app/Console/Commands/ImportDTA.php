<?php

namespace App\Console\Commands;

use App\Helpers\ReferenceGenerator;
use Illuminate\Console\Command;
use App\Models\Avion;
use App\Models\Aeroport;
use App\Models\Compagnie;
use App\Models\VolApprobation;
use App\Models\ItineraireVol;
use App\Models\CompagnieAerienne;
use App\Models\EtatDemandeApprobation;
use App\Models\TypeAvion;
use Carbon\Carbon;
use PhpOffice\PhpSpreadsheet\IOFactory;

class ImportDTA extends Command
{
    protected $signature = 'import:dta {file}';
    protected $description = 'Importer les données du fichier DTA.xlsx';

    // Mapping des codes IATA vers les noms complets des compagnies
    private $compagniesMapping = [
        'DAH' => 'Air Algérie',
        'AFR' => 'Air France',
        'SZN' => 'Air Sénégal',
        'IBB' => 'Binter',
        'MAI' => 'Mauritania Airlines International',
        'RAM' => 'Royal Air Maroc',
        'SIV' => 'Solenta Aviation',
        'SWT' => 'Swiftair',
        'TAI' => 'Tunisair',
        'THY' => 'Turkish Airlines'
    ];

    public function handle()
    {
        $filePath = $this->argument('file');

        if (!file_exists($filePath)) {
            $this->error("Le fichier spécifié n'existe pas.");
            return;
        }

        $spreadsheet = IOFactory::load($filePath);

        // Générer les demandes saisonnières
        $this->genererDemandesSaisonnieres();
        // Importer les avions (feuille A)
        //$this->importAvions($spreadsheet->getSheetByName('A'));

        // Importer les vols (feuille V)
        $this->importVols($spreadsheet->getSheetByName('V'));

        // Importer les itinéraires (feuille E)
        $this->importItineraires($spreadsheet->getSheetByName('E'));


        $this->info("Importation terminée avec succès!");
    }

    protected function genererDemandesSaisonnieres()
    {
        $compagnies = Compagnie::has('demandeApprobations', '=', 0)->get();

        foreach ($compagnies as $compagnie) {
            $annee = now()->year;

            // Période été (avril à octobre)
            $demandeEte = $compagnie->demandeApprobations()->create([
                'reference' => ReferenceGenerator::generateApprovalReference($compagnie->code, $annee, 'ETE'),
                'saison' => 'ETE',
                'date_demande' => now(),
                'date_debut' => Carbon::create($annee, 4, 1),
                'date_fin' => Carbon::create($annee, 10, 31),
                'statut' => 'EN_ATTENTE',
                'user_id' => $compagnie->user_id
            ]);

            $this->creerEtatDemande($demandeEte);

            // Période hiver (novembre à mars)
            $demandeHiver = $compagnie->demandeApprobations()->create([
                'reference' => ReferenceGenerator::generateApprovalReference($compagnie->code, $annee, 'HIVER'),
                'saison' => 'HIVER',
                'date_demande' => now(),
                'date_debut' => Carbon::create($annee, 11, 1),
                'date_fin' => Carbon::create($annee + 1, 3, 31),
                'statut' => 'EN_ATTENTE',
                'user_id' => $compagnie->user_id
            ]);

            $this->creerEtatDemande($demandeHiver);

            $this->info("Demandes saisonnières créées pour {$compagnie->nom_entreprise}");
        }
    }

    protected function creerEtatDemande($demande)
    {
        return EtatDemandeApprobation::create([
            'compagnie_cree_demande' => 0,
            'dg_annoter' => 0,
            'dta_dg_annoter' => 0,
            'dg_rejeter' => 0,
            'dta_annoter' => 0,
            'dta_rejeter' => 0,
            'service_annoter' => 0,
            'service_valider' => 0,
            'dta_valider' => 0,
            'dg_valider' => 0,
            'dta_dg_valider' => 0,
            'user_id' => $demande->user_id,
            'demande_id' => $demande->id,
            'service_tout_valider' => 0,
            'dsv_valider' => 0,
            'dsad_valider' => 0,
            'dsna_valider' => 0,
            'dta_notifier' => 0,
            'created_at' => now(),
            'updated_at' => now()
        ]);
    }
    private function importAvions($sheet)
    {
        $rows = $sheet->toArray();
        $header = array_shift($rows); // Enlever l'en-tête

        $bar = $this->output->createProgressBar(count($rows));

        foreach ($rows as $row) {
            $immatriculation = $row[0] ?? null;
            $typeAvion = $row[1] ?? null;
            $operateur = $row[2] ?? null;

            if (!$immatriculation || !$typeAvion || !$operateur) {
                continue;
            }

            // Trouver ou créer le type d'avion
            $typeAvionModel = TypeAvion::firstOrCreate(['nom' => $typeAvion]);

            // Trouver la compagnie aérienne
            $compagnie = CompagnieAerienne::where('nom', $operateur)->first();

            if (!$compagnie) {
                $this->warn("Compagnie non trouvée: $operateur");
                continue;
            }

            // Créer l'avion
            Avion::updateOrCreate(
                ['immatriculation' => $immatriculation],
                [
                    'compagnie_aerienne_id' => $compagnie->id,
                    'type_avion_id' => $typeAvionModel->id,
                    'valider' => true
                ]
            );

            $bar->advance();
        }

        $bar->finish();
        $this->line("\nImportation des avions terminée.");
    }

    private function importVols($sheet)
    {
        $rows = $sheet->toArray();
        $header = array_shift($rows);

        $bar = $this->output->createProgressBar(count($rows));

        foreach ($rows as $row) {
            $numVol = $row[0] ?? null;
            $aeroportDepart = $row[1] ?? null;
            $aeroportArrivee = $row[2] ?? null;
            $jours = $row[3] ?? null;
            $heureDepart = $row[4] ?? null;
            $heureArrivee = $row[5] ?? null;
            $periode = $row[6] ?? null;

            if (!$numVol || !$aeroportDepart || !$aeroportArrivee) {
                continue;
            }

            // Trouver les aéroports
            $aeroportDepartModel = Aeroport::where('codeIATA', $aeroportDepart)->orWhere('codeICAO', $aeroportDepart)->first();
            $aeroportArriveeModel = Aeroport::where('codeIATA', $aeroportArrivee)->orWhere('codeICAO', $aeroportArrivee)->first();

            if (!$aeroportDepartModel || !$aeroportArriveeModel) {
                $this->warn("Aéroport non trouvé pour le vol $numVol");
                continue;
            }

            // Extraire les dates de début et fin
            $dates = $this->parsePeriode($periode);
            if (!$dates) {
                $this->warn("Format de période invalide pour le vol $numVol: $periode");
                continue;
            }

            // Convertir les heures
            $heureDepart = $this->parseHeure($heureDepart);
            $heureArrivee = $this->parseHeure($heureArrivee);

            // Convertir les jours en format JSON
            $joursOperation = $this->parseJours($jours);

            // Identifier la compagnie aérienne
            $codeCompagnie = strtoupper(substr($numVol, 0, 3));
            $compagnieNom = $this->compagniesMapping[$codeCompagnie] ?? null;
            if (!$compagnieNom) {
                $this->warn("Compagnie non reconnue pour le vol $numVol");
                continue;
            }

            $compagnie = Compagnie::where('nom_entreprise', $compagnieNom)->first();
            if (!$compagnie) {
                $this->warn("Compagnie non trouvée en base: $compagnieNom");
                continue;
            }

            // Vérifier si une demande existe déjà pour cette période
            $demandeExistante = $compagnie->demandeApprobations()
                ->where('date_debut', $dates['debut'])
                ->where('date_fin', $dates['fin'])
                ->first();

            if (!$demandeExistante) {
                $currentYear = date('Y');
                $reference = ReferenceGenerator::generateApprovalReference(
                    $compagnie->code,
                    $currentYear
                );

                // Créer une nouvelle demande d'approbation
                $demandeExistante = $compagnie->demandeApprobations()->create([
                    'reference' => $reference,
                    'saison' => $this->determinerSaison($dates['debut']),
                    'date_demande' => now(),
                    'date_debut' => $dates['debut'],
                    'date_fin' => $dates['fin'],
                    'statut' => 'APPROUVEE',
                    'user_id' => $compagnie->user_id
                ]);

                // Créer l'état de la demande
                EtatDemandeApprobation::create([
                    'compagnie_cree_demande' => 0,
                    'dg_annoter' => 0,
                    'dta_dg_annoter' => 0,
                    'dg_rejeter' => 0,
                    'dta_annoter' => 0,
                    'dta_rejeter' => 0,
                    'service_annoter' => 0,
                    'service_valider' => 0,
                    'dta_valider' => 0,
                    'dg_valider' => 0,
                    'dta_dg_valider' => 0,
                    'user_id' => $compagnie->user_id,
                    'demande_id' => $demandeExistante->id,
                    'service_tout_valider' => 0,
                    'dsv_valider' => 0,
                    'dsad_valider' => 0,
                    'dsna_valider' => 0,
                    'dta_notifier' => 0,
                    'created_at' => now(),
                    'updated_at' => now()
                ]);
            }
            // Créer le vol
            VolApprobation::create([
                'numero_vol' => $numVol,
                'aeroport_depart_id' => $aeroportDepartModel->id,
                'aeroport_arrivee_id' => $aeroportArriveeModel->id,
                'heure_depart' => $heureDepart,
                'heure_arrivee' => $heureArrivee,
                'date_debut' => $dates['debut'],
                'date_fin' => $dates['fin'],
                'demande_approbation_id' => $demandeExistante->id,
                'jours_operation' => $joursOperation,
                'valider' => true
            ]);

            $bar->advance();
        }

        $bar->finish();
        $this->line("\nImportation des vols terminée.");
    }

    private function importItineraires($sheet)
    {
        $rows = $sheet->toArray();
        $header = array_shift($rows);

        $bar = $this->output->createProgressBar(count($rows));

        foreach ($rows as $row) {
            $numVol = $row[0] ?? null;
            $heureArrivee = $row[1] ?? null;
            $heureDepart = $row[2] ?? null;
            $aeroport = $row[3] ?? null;

            if (!$numVol || !$aeroport) {
                continue;
            }

            // Trouver le vol
            $vol = VolApprobation::where('numero_vol', $numVol)->first();
            if (!$vol) {
                $this->warn("Vol non trouvé pour l'itinéraire: $numVol");
                continue;
            }

            // Trouver l'aéroport
            $aeroportModel = Aeroport::where('codeIATA', $aeroport)->orWhere('codeICAO', $aeroport)->first();
            if (!$aeroportModel) {
                $this->warn("Aéroport non trouvé pour l'itinéraire: $aeroport");
                continue;
            }

            // Convertir les heures
            $heureArrivee = $this->parseHeure($heureArrivee);
            $heureDepart = $this->parseHeure($heureDepart);

            // Créer l'itinéraire
            ItineraireVol::create([
                'demande_approbation_id' => $vol->demande_approbation_id,
                'vol_id' => $vol->id,
                'aeroport_id' => $aeroportModel->id,
                'heure_depart' => $heureDepart,
                'heure_arrivee' => $heureArrivee,
                'valider' => true
            ]);

            $bar->advance();
        }

        $bar->finish();
        $this->line("\nImportation des itinéraires terminée.");
    }

    private function parsePeriode($periode)
    {
        // Traduire les mois français en anglais pour le parsing
        $frenchMonths = [
            'janvier' => 'January',
            'février' => 'February',
            'mars' => 'March',
            'avril' => 'April',
            'mai' => 'May',
            'juin' => 'June',
            'juillet' => 'July',
            'août' => 'August',
            'septembre' => 'September',
            'octobre' => 'October',
            'novembre' => 'November',
            'décembre' => 'December'
        ];

        // Format "30 Mars au 25 Octobre 2025"
        if (preg_match('/(\d{1,2})\s+(\w+)\s+au\s+(\d{1,2})\s+(\w+)\s+(\d{4})/', $periode, $matches)) {
            $startDay = $matches[1];
            $startMonth = strtolower($matches[2]);
            $endDay = $matches[3];
            $endMonth = strtolower($matches[4]);
            $year = $matches[5];

            // Traduire les mois si nécessaire
            $startMonthEn = $frenchMonths[$startMonth] ?? $startMonth;
            $endMonthEn = $frenchMonths[$endMonth] ?? $endMonth;

            try {
                $startDate = \Carbon\Carbon::createFromFormat('d F Y', "{$startDay} {$startMonthEn} {$year}");
                $endDate = \Carbon\Carbon::createFromFormat('d F Y', "{$endDay} {$endMonthEn} {$year}");

                return [
                    'debut' => $startDate->format('Y-m-d'),
                    'fin' => $endDate->format('Y-m-d')
                ];
            } catch (\Exception $e) {
                $this->warn("Erreur de parsing de date: {$periode} - " . $e->getMessage());
                return null;
            }
        }
        // Format "30-03-2025 au 25-10-2025"
        elseif (preg_match('/(\d{2}-\d{2}-\d{4})\s*au\s*(\d{2}-\d{2}-\d{4})/', $periode, $matches)) {
            try {
                $startDate = \Carbon\Carbon::createFromFormat('d-m-Y', $matches[1]);
                $endDate = \Carbon\Carbon::createFromFormat('d-m-Y', $matches[2]);

                return [
                    'debut' => $startDate->format('Y-m-d'),
                    'fin' => $endDate->format('Y-m-d')
                ];
            } catch (\Exception $e) {
                $this->warn("Erreur de parsing de date: {$periode} - " . $e->getMessage());
                return null;
            }
        }
        // Format "30/03/2025 au 25/10/2025"
        elseif (preg_match('/(\d{2}\/\d{2}\/\d{4})\s*au\s*(\d{2}\/\d{2}\/\d{4})/', $periode, $matches)) {
            try {
                $startDate = \Carbon\Carbon::createFromFormat('d/m/Y', $matches[1]);
                $endDate = \Carbon\Carbon::createFromFormat('d/m/Y', $matches[2]);

                return [
                    'debut' => $startDate->format('Y-m-d'),
                    'fin' => $endDate->format('Y-m-d')
                ];
            } catch (\Exception $e) {
                $this->warn("Erreur de parsing de date: {$periode} - " . $e->getMessage());
                return null;
            }
        }

        $this->warn("Format de période non reconnu: {$periode}");
        return null;
    }

    private function parseHeure($heure)
    {
        if (preg_match('/(\d{1,2})H(\d{0,2})/', $heure, $matches)) {
            // Format "18H00" ou "8H"
            $h = $matches[1];
            $m = $matches[2] ?? '00';
            return "$h:$m:00";
        } elseif (preg_match('/(\d{1,2}):(\d{2}):(\d{2})/', $heure)) {
            // Format déjà en "HH:MM:SS"
            return $heure;
        } elseif (preg_match('/(\d{1,2}):(\d{2})/', $heure)) {
            // Format "HH:MM"
            return "$heure:00";
        }

        return null;
    }

    private function parseJours($jours)
    {
        $joursMap = [
            'J1' => 'J1',
            'Lundi' => 'J1',
            'J2' => 'J2',
            'Mardi' => 'J2',
            'J3' => 'J3',
            'Mercredi' => 'J3',
            'J4' => 'J4',
            'Jeudi' => 'J4',
            'J5' => 'J5',
            'Vendredi' => 'J5',
            'J6' => 'J6',
            'Samedi' => 'J6',
            'J7' => 'J7',
            'Dimanche' => 'J7'
        ];

        $joursArray = [];
        foreach ($joursMap as $code => $jour) {
            if (strpos($jours, $code) !== false) {
                $joursArray[] = $jour;
            }
        }

        return json_encode($joursArray);
    }
    private function determinerSaison($date)
    {
        $mois = \Carbon\Carbon::parse($date)->month;
        return ($mois >= 4 && $mois <= 10) ? 'ETE' : 'HIVER';
    }
}
