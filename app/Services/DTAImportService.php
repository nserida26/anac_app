<?php
// app/Services/DTAImportService.php

namespace App\Services;

use App\Models\Compagnie;
use App\Models\DemandeApprobation;
use PhpOffice\PhpSpreadsheet\IOFactory;
use App\Models\Aeroport;
use App\Models\VolApprobation;
use App\Models\ItineraireVol;
use Illuminate\Support\Facades\DB;

class DTAImportService
{
    public function importFromFile(Compagnie $compagnie, string $filePath, DemandeApprobation $demande)
    {
        if (!file_exists($filePath)) {
            throw new \Exception("Le fichier spécifié n'existe pas.");
        }

        $spreadsheet = IOFactory::load($filePath);

        return DB::transaction(function () use ($spreadsheet, $demande, $compagnie) {
            // Importer les données
            $this->importVols($compagnie, $demande, $spreadsheet->getSheetByName('V'));
            $this->importItineraires($spreadsheet->getSheetByName('E'));

            return $demande;
        });
    }

    private function importVols(Compagnie $compagnie, DemandeApprobation $demande, $sheet)
    {
        $rows = $sheet->toArray();

        $header = array_shift($rows);
        foreach ($rows as $row) {

            $numVol = $row[0] ?? null;
            $aeroportDepart = $row[1] ?? null;
            $aeroportArrivee = $row[2] ?? null;
            $jours = $row[3] ?? null;
            $heureDepart = $row[4] ?? null;
            $heureArrivee = $row[5] ?? null;

            if (!$numVol || !$aeroportDepart || !$aeroportArrivee) {
                continue;
            }

            // Trouver les aéroports
            $aeroportDepartModel = Aeroport::where('codeIATA', $aeroportDepart)->orWhere('codeICAO', $aeroportDepart)->first();
            $aeroportArriveeModel = Aeroport::where('codeIATA', $aeroportArrivee)->orWhere('codeICAO', $aeroportArrivee)->first();

            if (!$aeroportDepartModel || !$aeroportArriveeModel) {

                continue;
            }

            $heureDepart = $this->parseHeure($heureDepart);
            $heureArrivee = $this->parseHeure($heureArrivee);


            $codeCompagnieVol = strtoupper(substr($numVol, 0, 3));
            $codeCompagnie = $compagnie->code;


            if ($codeCompagnieVol !== $codeCompagnie) {
                continue;
            }


            $joursOperation = $this->parseJours($jours);


            $vol = VolApprobation::create([
                'numero_vol' => $numVol,
                'aeroport_depart_id' => $aeroportDepartModel->id,
                'aeroport_arrivee_id' => $aeroportArriveeModel->id,
                'heure_depart' => $heureDepart,
                'heure_arrivee' => $heureArrivee,
                'date_debut' => $demande->date_debut,
                'date_fin' => $demande->date_fin,
                'demande_approbation_id' => $demande->id,
                'jours_operation' => $joursOperation,
                'valider' => true
            ]);
        }
    }

    private function importItineraires($sheet)
    {
        $rows = $sheet->toArray();
        $header = array_shift($rows);
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

                continue;
            }

            // Trouver l'aéroport
            $aeroportModel = Aeroport::where('codeIATA', $aeroport)->orWhere('codeICAO', $aeroport)->first();
            if (!$aeroportModel) {
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
        }
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
}
