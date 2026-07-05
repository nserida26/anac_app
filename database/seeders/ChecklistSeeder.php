<?php
// database/seeders/ChecklistSeeder.php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\Checklist;

class ChecklistSeeder extends Seeder
{
    public function run()
    {
        $checklists = [
            // Section FORM-PEL-272
            [
                'numero' => 1,
                'index' => 'FORM-PEL-272',
                'type' => 'Licence',
                'libelle' => 'FORM-PEL-272 dûment renseigné/ou ledit formulaire en ligne',
                'section' => 'Documents de base',
                'ordre' => 1
            ],
            [
                'numero' => 2,
                'index' => '',
                'type' => 'Licence',
                'libelle' => 'Copie de la licence et qualifications associées',
                'section' => 'Documents de base',
                'ordre' => 2
            ],
            [
                'numero' => 3,
                'index' => '§4.9.1.5',
                'type' => 'Licence',
                'libelle' => 'Copie du certificat médical valide',
                'section' => 'Documents de base',
                'ordre' => 3
            ],
            
            // Section Qualification de type
            [
                'numero' => 4,
                'index' => '',
                'type' => 'Qualification de type',
                'libelle' => 'Effectuer avec succès la formation périodique des MEC',
                'section' => 'Qualification de type',
                'ordre' => 4
            ],
            [
                'numero' => 5,
                'index' => '',
                'type' => 'Qualification de type',
                'libelle' => 'Justificatif dernier contrôle de compétence satisfaisant effectué dans les 3 mois précédent l\'expiration de la qualification',
                'section' => 'Qualification de type',
                'ordre' => 5
            ],
            
            // Section Prorogation
            [
                'numero' => 6,
                'index' => '',
                'type' => 'Prorogation',
                'libelle' => 'Qualification d\'instructeur MEC',
                'section' => 'Prorogation',
                'ordre' => 6
            ],
            [
                'numero' => 7,
                'index' => '',
                'type' => 'Prorogation',
                'libelle' => 'Licence et qualifications détenues en cours de validité',
                'section' => 'Prorogation',
                'ordre' => 7
            ],
            [
                'numero' => 8,
                'index' => '',
                'type' => 'Prorogation',
                'libelle' => 'Justificatif de réalisation d\'un moins deux instructions au sol ou en vol durant la dernière année de validité de la qualification d\'instructeur détenue',
                'section' => 'Prorogation',
                'ordre' => 8
            ],
            [
                'numero' => 9,
                'index' => '',
                'type' => 'Prorogation',
                'libelle' => 'Justificatifs des contrôles de compétences règlementaires à jour',
                'section' => 'Prorogation',
                'ordre' => 9
            ],
            [
                'numero' => 10,
                'index' => '',
                'type' => 'Prorogation',
                'libelle' => 'Justificatif de réussite au stage/séminaire de recyclage accepté par l\'ANAC au cours de la période de validité de la qualification d\'instructeur',
                'section' => 'Prorogation',
                'ordre' => 10
            ],
            
            // Section Renouvellement - Délai 0-6 mois
            [
                'numero' => 11,
                'index' => 'Appendice §4.9.3',
                'type' => 'Renouvellement',
                'libelle' => 'Délai d\'expiration : 0 à 06 mois - Qualification de type',
                'section' => 'Renouvellement 0-6 mois',
                'ordre' => 11
            ],
            [
                'numero' => 12,
                'index' => '',
                'type' => 'Renouvellement',
                'libelle' => 'Détenir une attestation médicale de classe 2 valide',
                'section' => 'Renouvellement 0-6 mois',
                'ordre' => 12
            ],
            [
                'numero' => 13,
                'index' => '',
                'type' => 'Renouvellement',
                'libelle' => 'Justificatif du suivi avec succès d\'un stage de remise à niveau approuvé par l\'ANAC',
                'section' => 'Renouvellement 0-6 mois',
                'ordre' => 13
            ],
            [
                'numero' => 14,
                'index' => '',
                'type' => 'Renouvellement',
                'libelle' => 'Être autorisé par l\'ANAC',
                'section' => 'Renouvellement 0-6 mois',
                'ordre' => 14
            ],
            [
                'numero' => 15,
                'index' => '',
                'type' => 'Renouvellement',
                'libelle' => 'Justificatif de réussite à un contrôle de compétences sous supervision d\'un EMEC',
                'section' => 'Renouvellement 0-6 mois',
                'ordre' => 15
            ],
            
            // Section Renouvellement - Délai 6 mois à 5 ans
            [
                'numero' => 16,
                'index' => 'Appendice §4.9.3',
                'type' => 'Renouvellement',
                'libelle' => 'Délai d\'expiration : 06 mois à 5 ans - Qualification de type',
                'section' => 'Renouvellement 6 mois-5 ans',
                'ordre' => 16
            ],
            [
                'numero' => 17,
                'index' => '',
                'type' => 'Renouvellement',
                'libelle' => 'Détenir une attestation médicale de classe 2 valide',
                'section' => 'Renouvellement 6 mois-5 ans',
                'ordre' => 17
            ],
            [
                'numero' => 18,
                'index' => '',
                'type' => 'Renouvellement',
                'libelle' => 'Justificatif du suivi avec succès d\'un stage de remise à niveau approuvé par l\'ANAC',
                'section' => 'Renouvellement 6 mois-5 ans',
                'ordre' => 18
            ],
            [
                'numero' => 19,
                'index' => '',
                'type' => 'Renouvellement',
                'libelle' => 'Être autorisé par l\'ANAC',
                'section' => 'Renouvellement 6 mois-5 ans',
                'ordre' => 19
            ],
            [
                'numero' => 20,
                'index' => '',
                'type' => 'Renouvellement',
                'libelle' => 'Justificatif d\'au moins 20h d\'instruction en vol',
                'section' => 'Renouvellement 6 mois-5 ans',
                'ordre' => 20
            ],
            [
                'numero' => 21,
                'index' => '',
                'type' => 'Renouvellement',
                'libelle' => 'Justificatif de réussite à un contrôle de compétences sous supervision d\'un EMEC',
                'section' => 'Renouvellement 6 mois-5 ans',
                'ordre' => 21
            ],
            
            // Section Renouvellement - Plus de 5 ans
            [
                'numero' => 22,
                'index' => 'Appendice §4.9.3',
                'type' => 'Renouvellement',
                'libelle' => 'Délai d\'expiration : plus de 5 ans',
                'section' => 'Renouvellement 5+ ans',
                'ordre' => 22
            ],
            [
                'numero' => 23,
                'index' => '',
                'type' => 'Renouvellement',
                'libelle' => 'Détenir une attestation médicale de classe 2 valide',
                'section' => 'Renouvellement 5+ ans',
                'ordre' => 23
            ],
            [
                'numero' => 24,
                'index' => '',
                'type' => 'Renouvellement',
                'libelle' => 'Justificatif du suivi avec succès d\'un stage de remise à niveau approuvé par l\'ANAC',
                'section' => 'Renouvellement 5+ ans',
                'ordre' => 24
            ],
            [
                'numero' => 25,
                'index' => '',
                'type' => 'Renouvellement',
                'libelle' => 'Être autorisé par l\'ANAC',
                'section' => 'Renouvellement 5+ ans',
                'ordre' => 25
            ],
            [
                'numero' => 26,
                'index' => '',
                'type' => 'Renouvellement',
                'libelle' => 'Justificatif de réussite à un contrôle de compétences sous supervision d\'un EMEC',
                'section' => 'Renouvellement 5+ ans',
                'ordre' => 26
            ],
            
            // Section Qualification instructeur MEC
            [
                'numero' => 27,
                'index' => '§ 4.9.5.1 (d)',
                'type' => 'Qualification instructeur',
                'libelle' => 'Qualification d\'instructeur MEC - Justificatif de réussite à un contrôle de compétences sous supervision d\'un EMEC',
                'section' => 'Qualification instructeur',
                'ordre' => 27
            ],
            [
                'numero' => 28,
                'index' => '',
                'type' => 'Qualification instructeur',
                'libelle' => 'Justificatif de réussite au stage/séminaire de recyclage accepté par l\'ANAC au cours de la période de validité de la qualification d\'instructeur',
                'section' => 'Qualification instructeur',
                'ordre' => 28
            ],
            [
                'numero' => 29,
                'index' => '',
                'type' => 'Qualification instructeur',
                'libelle' => 'Licence et qualifications détenues en cours de validité',
                'section' => 'Qualification instructeur',
                'ordre' => 29
            ],
            
            // Section Documents finaux
            [
                'numero' => 30,
                'index' => '',
                'type' => 'Documents',
                'libelle' => 'Justificatif de proposition de candidature par l\'employeur',
                'section' => 'Documents finaux',
                'ordre' => 30
            ],
            [
                'numero' => 31,
                'index' => 'S/O',
                'type' => 'Documents',
                'libelle' => 'Justificatif de paiement de l\'acte',
                'section' => 'Documents finaux',
                'ordre' => 31
            ],
        ];

        foreach ($checklists as $checklist) {
            Checklist::create($checklist);
        }
    }
}