<!DOCTYPE html>
<html lang="fr">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Approbation Programme {{ $approbation->saison }}
        {{ date('Y', strtotime($approbation->date_approbation)) }}</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <style>
        body {
            font-family: 'Arial', sans-serif;
            background-color: #f8f9fa;
        }

        .document-container {
            background-color: white;
            padding: 30px;
            margin: 20px auto;
            max-width: 1000px;
            box-shadow: 0 0 10px rgba(0, 0, 0, 0.1);
        }

        .header {
            text-align: center;
            margin-bottom: 30px;
            border-bottom: 2px solid #dee2e6;
            padding-bottom: 15px;
        }

        .section-title {
            font-weight: bold;
            color: #0d6efd;
            margin: 20px 0 15px;
            border-bottom: 1px solid #dee2e6;
            padding-bottom: 5px;
        }

        .reference {
            font-style: italic;
            margin-bottom: 20px;
        }

        .flight-table {
            margin-bottom: 30px;
        }

        .flight-table th {
            background-color: #f8f9fa;
            font-weight: bold;
        }




        .distribution-list {
            margin-top: 30px;
        }

        .distribution-list li {
            margin-bottom: 5px;
        }

        .signature-img {
            height: 35px !important;
            width: auto !important;
        }

        .cachet-img {
            width: 80px !important;
        }

        .no-print {
            display: none !important;
        }
    </style>
</head>

<body>
    <div class="container">
        <div class="document-container">
            <!-- Header Section -->
            <div
                style="display: flex; align-items: center; border-bottom: 1px solid #000; padding-bottom: 5px; margin-bottom: 10px; direction: rtl;">
                <div style="flex: 1; text-align: right; font-family: 'Arial Arabic', 'Traditional Arabic', sans-serif; font-size: 20px; font-weight: bold; padding-left: 10px; word-spacing: 2px; line-height: 1.2;"
                    class="arabic">
                    الجمهورية الإسلامية الموريتانية<br>
                    وزارة التجهيز والنقل<br>
                    الوكالة الوطنية للطيران المدني
                </div>
                <div style="flex: 0 0 auto; margin: 0 8px;">
                    <img src="{{ asset('assets/admin/imgs/logo.png') }}" alt="Logo ANAC" class="logo">
                </div>
                <div style="flex: 1; text-align: left; font-size: 16px; font-weight: bold; padding-right: 10px; direction: ltr; line-height: 1.2;"
                    class="french">
                    République Islamique de Mauritanie<br>
                    Ministère de l'Equipement et des Transports<br>
                    AGENCE NATIONALE DE L'AVIATION CIVILE
                </div>

            </div>

            <!-- Definition Section -->
            <div>
                <h5 class="section-title"></h5>
                <p><strong><u>Objet :</u> Approbation Programme {{ $approbation->saison }}
                        {{ date('Y', strtotime($approbation->date_approbation)) }}</strong></p>
                <p class="reference"><strong><u>Réf. :</u> {{ $approbation->reference }} du
                        {{ $approbation->date_approbation }}</strong></p>
            </div>

            <!-- Revision Section -->
            <div>
                <h5 class="section-title"></h5>
                @php
                    $dateDebut = \Carbon\Carbon::parse($approbation->date_debut)->locale('fr');
                    $dayDebut = $dateDebut->day;
                    $monthDebut = strtoupper($dateDebut->monthName);
                    $dateFin = \Carbon\Carbon::parse($approbation->date_fin)->locale('fr');
                    $dayFin = $dateFin->day;
                    $monthFin = strtoupper($dateFin->monthName);
                    $yearFin = $dateFin->year;
                @endphp
                <p><strong>Suite à votre lettre citée en référence, je vous notifie par la présente notre accord pour
                        l'exécution de votre programme {{ $approbation->saison }}
                        {{ date('Y', strtotime($approbation->date_approbation)) }}, du
                        {{ "$dayDebut $monthDebut" }}
                        au
                        {{ "$dayFin $monthFin" }}
                        {{ $yearFin }}, selon
                        les jours,
                        horaires et parcours suivants :</strong></p>

                <p> <strong><u>Type avion :</u> {{ $demande->avions->pluck('type.code')->implode(', ') }}
                    </strong></p>
                <p> <strong><u>Immatriculation :</u>
                        {{ $demande->avions->pluck('immatriculation')->implode(', ') }}
                    </strong></p>

                <!-- Flight Schedule Table -->
                <div class="table-responsive flight-table">
                    <table class="table table-bordered">
                        <thead>
                            <tr>
                                <th>Vol</th>
                                <th>Jours d'opération</th>
                                <th>Routing</th>
                                <th>Période</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach ($demande->vols as $vol)
                                <tr>
                                    <td>{{ $vol->numero_vol }}</td>
                                    <td>{{ $vol->jours_operation_display }}</td>
                                    @php
                                        // Regrouper les itinéraires par vol
                                        $routeString = null;
                                        $groupedItineraires = $demande->itineraires->groupBy('vol_id');
                                    @endphp

                                    @foreach ($groupedItineraires as $volId => $volItineraires)
                                        @php
                                            $vol = $volItineraires->first()->volApprobation;
                                            $output = [];
                                            foreach ($volItineraires as $itineraire) {
                                                $aeroportCode = $itineraire->aeroport->codeICAO;
                                                $heureArrivee = date('Hi', strtotime($itineraire->heure_arrivee));
                                                $heureDepart = date('Hi', strtotime($itineraire->heure_depart));
                                                if (empty($output)) {
                                                    $output[] = "{$heureArrivee} {$aeroportCode} {$heureDepart}";
                                                } else {
                                                    $output[] = " - {$heureArrivee} {$aeroportCode} {$heureDepart}";
                                                }
                                            }
                                            $routeString = implode(' ', $output);
                                        @endphp
                                    @endforeach
                                    <td>{{ $vol->aeroportDepart->codeICAO }}
                                        {{ date('Hi', strtotime($vol->heure_depart)) }}
                                        @if (!empty($routeString))
                                            -
                                            {{ $routeString }}
                                        @endif
                                        -
                                        {{ date('Hi', strtotime($vol->heure_depart)) }}
                                        {{ $vol->aeroportArrivee->codeICAO }}
                                    </td>
                                    @if ($vol->date_debut !== $demande->date_debut || $vol->date_fin !== $demande->date_fin)
                                        <td>{{ date('d-m-Y', strtotime($vol->date_debut)) }} au
                                            {{ date('d-m-Y', strtotime($vol->date_fin)) }}</td>
                                    @else
                                        <td></td>
                                    @endif

                                </tr>
                            @endforeach

                        </tbody>
                    </table>
                </div>
                @if ($demande->amender)
                    @php
                        $previousApproval = $approbation->getPreviousApproval();
                    @endphp
                    <p>Cette approbation annule et remplace l'approbation n° {{ $previousApproval->reference }} du
                        {{ date('d/m/Y', strtotime($previousApproval->date_approbation)) }} .</p>
                @endif

                <p>Veuillez agréer, Monsieur le Délégué, l'expression de mes salutations les meilleures.</p>
            </div>
            <!-- Signature Block -->

            <div style="position: relative; width: 100%; margin-top: 20px;">
                <div style="display: flex; justify-content: flex-end; position: relative;">
                    <div style="position: absolute; left: 15%; bottom: 0;">
                        <img src="{{ asset('/uploads/' . $dg->cachet->cachet) }}" style="width: 110px; opacity: 0.9;"
                            class="cachet-img">
                    </div>
                    <div style="text-align: right; padding-right: 15px;">
                        <div style="margin-bottom: 3px; font-size: 12px;"><strong>Nom du signataire:</strong></div>
                        <div style="margin-bottom: 3px; font-weight: bold; font-size: 14px;">N'GADE ABDOULAYE ABASSE
                        </div>
                        <div style="margin-bottom: 5px; font-size: 12px;">Directeur général</div>
                        <img src="{{ asset('/uploads/' . $dg->signature->signature) }}"
                            style="width: 100px; height: 110px;" class="signature-img">
                    </div>
                </div>
            </div>

        </div>
    </div>


    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
    <script>
        document.addEventListener('DOMContentLoaded', function() {
            window.print();
        });
    </script>
</body>

</html>
