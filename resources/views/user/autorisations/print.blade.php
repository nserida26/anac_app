<!DOCTYPE html>
<html lang="fr">
    @php
    $documentAvions = $autorisation->demande->documents()
        ->whereHas('typeDocument', function($query) {
            $query->where('nom_fr', 'Liste des avions');
        })
        ->first();
    $documentVols = $autorisation->demande->documents()
        ->whereHas('typeDocument', function($query) {
            $query->where('nom_fr', 'Liste des Vols');
        })
        ->first();
    @endphp
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Flight Authorization {{ $autorisation->id }}</title>
    <style>
        @font-face {
            font-family: 'Louguiya';
            src: url('/assets/admin/fonts/Louguiya.ttf') format('ttf');
            font-weight: normal;
            font-style: normal;
        }

        @font-face {
            font-family: 'LouguiyaFR';
            src: url('/assets/admin/fonts/LouguiyaFR.ttf') format('ttf');
            font-weight: normal;
            font-style: normal;
        }

        body {
            font-family: 'LouguiyaFR', Arial, sans-serif;
            line-height: 1.2;
            margin: 0;
            padding: 5px;
            position: relative;
            font-size: 13px;
        }

        .watermark {
            position: fixed;
            top: 50%;
            left: 50%;
            transform: translate(-50%, -50%);
            opacity: 0.1;
            z-index: -1;
            width: 50%;
            height: auto;
            display: block !important;
        }

        h4, h6 {
            color: #333;
            margin: 3px 0;
            font-size: 14px;
        }

        .dual-language {
            display: flex;
            justify-content: space-between;
            margin: 3px 0;
        }

        label {
            display: block;
            font-weight: bold;
            margin-bottom: 1px;
            font-size: 13px;
        }

        .submit-btn {
            background-color: #4CAF50;
            color: white;
            padding: 8px 12px;
            border: none;
            border-radius: 4px;
            cursor: pointer;
            font-size: 14px;
            margin-bottom: 8px;
        }

        .note {
            font-style: italic;
            color: #666;
            font-size: 11px;
            margin: 4px 0;
        }

        li {
            font-size: 11px;
            margin-bottom: 3px;
        }

        .arabic {
            font-family: 'Arial Arabic', 'Times New Roman', serif;
            font-size: 20px;
            font-weight: bold;
            margin-bottom: 3px;
        }

        .french {
            font-size: 16px;
            font-weight: bold;
            margin-top: 5px;
        }

        .logo {
            height: 70px;
            margin: 3px 0;
        }

        .dual-column {
            display: flex;
            justify-content: space-between;
            align-items: center;
        }

        .column {
            width: 50%;
        }

        table {
            width: 100%;
            margin-bottom: 0px;
            border-top: 0.5px solid #000;
            border-left: 1px solid #000;
            border-right: 1px solid #000;
            border-bottom: 1px solid #000;
            border-collapse: collapse;
        }

        table td, table th {
            border-left: none;
            border-right: none;
            border-top: none;
            border-bottom: 1px solid #ddd;
            padding: 4px;
            vertical-align: top;
        }

        table tr:last-child td {
            border-bottom: none;
        }

        .header-table {
            border: none;
        }

        .header-table td {
            border: none;
            padding: 0;
        }

        .signature-section {
            display: flex;
            justify-content: space-between;
            align-items: center;
            margin: 0px 0;
        }

        .signature-img {
            width: 120px;
            height: 80px;
        }

        .footer-section {
            display: flex;
            justify-content: space-between;
            align-items: flex-start;
            width: 100%;
            margin-top: 10px;
        }

        .recipients-list {
            width: 45%;
            font-size: 12px;
            font-weight: bold;
            line-height: 1;
        }

        .recipients-list ul {
            list-style-type: none;
            padding-left: 0;
            margin: 0;
        }

        .signature-block {
            width: 50%;
            text-align: left;
        }

        .signature-line {
            display: flex;
            justify-content: flex-start;
            align-items: center;
            margin-bottom: 2px;
        }

        .signature-images {
            display: flex;
            justify-content: space-between;
            align-items: center;
            margin-top: 4px;
        }

        /* Print-specific styles */
        @media print {
            .no-print {
                display: none !important;
            }

            @page {
                size: A4;
                margin: 5mm;
            }
        }
    </style>
</head>

<body>
    <div id="authorization">
        <img src="{{ asset('assets/admin/imgs/logo.png') }}" alt="Watermark" class="watermark">
        
        <!-- Header with Arabic text and logo -->
        <table class="header-table">
            <tr>
                <td style="text-align: center;">
                    <img src="{{ asset('assets/admin/imgs/anac.png') }}" alt="Logo ANAC" style="display: block; margin: 5px auto; height: 100px; max-width: 100%;">
                </td>
            </tr>
        </table>

        <!-- Authorization header -->
        <table>
                <tr>
                    <td>
                                <div style="display: flex; justify-content: space-between; align-items: flex-start;">
            <h1 style="margin-bottom: 1px; margin-top: 0;">Autorisation / Authorization</h1>
            <div style="text-align: right;">
                <h4 style="margin: 0;">DATE/DATED: {{ date('d/m/Y') }}</h4>
            </div>
        </div>
                        <div style="align-items: baseline; margin-left: 20px; margin-top: 0;">
                            <h4 style="margin: 0;">Autorisation numéro / authorization number:</h4>
                            <h2 style="margin: 0; margin-left: 100px;">{{ $autorisation->code_autorisation }}</h2>
                        </div>
                
                        <div style="display: flex; align-items: baseline; margin-left: 20px; margin-top: 2px;">
                            <h4 style="margin: 0;">Autorisation type / Authorization:</h4>
                            <h2 style="margin: 0;">{{ $autorisation->demande->type->libelle }}</h2>
                        </div>
                    </td>
                </tr>
        </table>

        <!-- Issued by and To sections -->
        <table>
            <tr>
                <td style="width: 50%;">
                    <div style="margin: 2px 0;">
                        <div style="margin-bottom: 10px; font-size: 13px; font-weight: bold;">
                            Délivrée par : Agence Nationale de l'Aviation Civile :<br>
                            Delivered by : National Civil Aviation Authority :
                        </div>
                        <div style="margin-bottom: 10px; font-size: 13px; font-weight: bold;">
                            Tél/Tel: 00 222 45 24 40 05
                        </div>
                        <div style="margin-bottom: 10px; font-size: 13px; font-weight: bold;">
                            Télécopie/Fax: 00 222 45 25 35 78
                        </div>
                        <div style="margin-bottom: 10px; font-size: 13px; font-weight: bold;">
                            Email/Email: survol.dta@anac.mr
                        </div>
                        <div style="margin-bottom: 10px; font-size: 13px; font-weight: bold;">
                            Référence/Reference: {{ $autorisation->demande->code }}
                        </div>
                    </div>
                </td>

                <td style="width: 50%;">
                    <div style="margin: 10px 0;">
                        <div style="margin-bottom: 10px; font-size: 13px; font-weight: bold;">
                            À/To {{ strtoupper($autorisation->demande->user->demandeur->np) }}
                        </div>
                        <div style="margin-bottom: 8px; font-size: 13px; font-weight: bold;">
                            Tél/Tel: {{ $autorisation->demande->user->whatsapp }}
                        </div>
                        <div style="margin-bottom: 10px; font-size: 13px; font-weight: bold;">
                            Email/Email: {{ $autorisation->demande->user->email }}
                        </div>
                        <div style="margin-bottom: 10px; font-size: 13px; font-weight: bold;">
                            Date de réception de la demande / Date of receipt of request:
                            {{ date('d/m/Y', strtotime($autorisation->demande->date_soumission)) }}
                        </div>
                        <div style="margin-bottom: 10px; font-size: 13px; font-weight: bold;">
                            Référence/Reference: {{ $autorisation->demande->code }}
                        </div>
                    </div>
                </td>
            </tr>
        </table>

        <!-- Visa section -->
        <table>
            <tr>
                <td>
                    <div class="signature-section">
                        @php
                            $hasDta = !empty($autorisation->signature_dta) && file_exists(base_path('uploads/' . $autorisation->signature_dta));
                            $hasSrta = !empty($autorisation->signature_srta) && file_exists(base_path('uploads/' . $autorisation->signature_srta));
                        @endphp
                        

                        
                        <div style="display: flex; align-items: center; gap: 1px;">
                            <div style="font-weight: bold;">VISA/SRTA</div>
                            @if($hasSrta)
                                <img src="{{ asset('uploads/' . $autorisation->signature_srta) }}" 
                                     class="signature-img" alt="Signature SRTA">
                            @endif
                        </div>
                        <div style="display: flex; align-items: center; gap: 1px;">
                            <div style="font-weight: bold;">VISA/DTA</div>
                            @if($hasDta)
                                <img src="{{ asset('uploads/' . $autorisation->signature_dta) }}" 
                                     class="signature-img" alt="Signature DTA">
                            @endif
                        </div>
                    </div>

                    <div style="text-align: center; margin: 1px 0;">
                        <p style="font-size: 12px; margin: 0;">
                            HONNEUR DE VOUS NOTIFIER NOTRE ACCORD DE SURVOL DU TERRITOIRE MAURITANIEN
                            @if ($autorisation->demande->type->id === 2)
                                @php
                                    $output = [];
                                    $aeroportsUniques = []; // Tableau pour stocker les aéroports uniques
                                    
                                    if ($autorisation->demande->vols->isNotEmpty()){
                                        foreach($autorisation->demande->vols as $vol){
                                            // Aéroport d'arrivée
                                            if ($vol->aeroportArrivee->pays_id === 29) {
                                                $aeroportName = mb_strtoupper($vol->aeroportArrivee->nom, 'UTF-8');
                                                // Vérifier l'unicité
                                                if (!in_array($aeroportName, $aeroportsUniques)) {
                                                    $aeroportsUniques[] = $aeroportName;
                                                    $output[] = " {$aeroportName} ";
                                                }
                                            }
                                            // Aéroport de départ
                                            if ($vol->aeroportDepart->pays_id === 29) {
                                                $aeroportName = mb_strtoupper($vol->aeroportDepart->nom, 'UTF-8');
                                                // Vérifier l'unicité
                                                if (!in_array($aeroportName, $aeroportsUniques)) {
                                                    $aeroportsUniques[] = $aeroportName;
                                                    $output[] = " {$aeroportName} ";
                                                }
                                            }
                                            // Escales
                                            foreach ($vol->escales as $escale) {
                                                if ($escale->aeroport->pays_id === 29) {
                                                    $aeroportName = mb_strtoupper($escale->aeroport->nom, 'UTF-8');
                                                    // Vérifier l'unicité
                                                    if (!in_array($aeroportName, $aeroportsUniques)) {
                                                        $aeroportsUniques[] = $aeroportName;
                                                        $output[] = " {$aeroportName} ";
                                                    }
                                                }
                                            }
                                        }
                                    }
                                    
                                    $aeroportString = implode(' ', $output);
                                @endphp
                                ET L'ATTERRISSAGE SUR LE(S) AÉROPORT(S) {{ $aeroportString }}
                            @endif
                            EN FAVEUR DE(S) AÉRONEF(S) SELON LES INFORMATIONS CI-APRÈS #:
                        </p>
                    </div>
                    
                    @php
                        $output1 = [];
                        $output2 = [];
                        foreach ($autorisation->demande->avions as $avion) {
                            $output1[] = " {$avion->type->code} ";
                            $output2[] = " {$avion->immatriculation} ";
                        }
                        
                        $typeString1 = implode(' / ', array_unique($output1));
                        $typeString2 = implode(' / ', $output2);
                    @endphp

                    <div class="dual-column">
                        <div class="column">
                            <h4>Aéronef type / Aircraft type:</h4>
                            <label>{{ $typeString1 }} @if($documentAvions)  <span> OR SUB VOIR PIECE JOINTE </span> @endif</label>
                        </div>
                        <div class="column">
                            <h4>Immatriculation / Registration:</h4>
                            <label>{{ $typeString2 }} @if($documentAvions)  <span>  OR SUB VOIR PIECE JOINTE</span> @endif</label>
                        </div>
                    </div>
                </td>
            </tr>
        </table>

        <!-- Flight details -->
        <table>
            <tr>
                <td>
                    <div style="display: flex; flex-direction: column; gap: 12px; margin: 15px 0;">
                        <!-- Motif -->
                        <div style="display: flex;">
                            <span style="font-weight: bold; min-width: 200px;">Motif/Motif:</span>
                            <span style="font-weight: bold;">{{ strtoupper($autorisation->demande->objet) }}</span>
                        </div>

                        <!-- Opérateur -->
                        @if (!empty($autorisation->demande->avions))
                            <div style="display: flex;">
                                <span style="font-weight: bold; min-width: 200px;">Opérateur/Operator:</span>
                                <span style="font-weight: bold;">
                                    {{ $autorisation->demande->avions->first()->compagnie->nom_entreprise ?? '' }}
                                </span>
                            </div>
                        @endif

@if ($autorisation->demande->vols->isNotEmpty())

    <div style="display:flex;">
        <span style="font-weight:bold; min-width:200px;">
            Itinéraire / Itinerary :
        </span>

        <div style="font-weight:bold; word-break:break-word;">

            @foreach ($autorisation->demande->vols as $vol)

                @php
                    $itineraireParts = [];

                    $departCode  = $vol->aeroportDepart->codeICAO;
                    $arriveeCode = $vol->aeroportArrivee->codeICAO;

                    $heureDepartVol  = date('Hi', strtotime($vol->date_depart));
                    $heureArriveeVol = date('Hi', strtotime($vol->date_arrivee));

                    // Départ
                    $itineraireParts[] = "{$departCode} {$heureDepartVol}";

                    // Escales
                    if ($vol->escales->isNotEmpty()) {
                        foreach ($vol->escales as $escale) {
                            $heureArriveeEscale = date('Hi', strtotime($escale->date_arrivee));
                            $heureDepartEscale  = date('Hi', strtotime($escale->date_depart));
                            $aeroportEscale     = $escale->aeroport->codeICAO;

                            $itineraireParts[] = "{$heureArriveeEscale} {$aeroportEscale} {$heureDepartEscale}";
                        }
                    }

                    // Arrivée
                    if ($departCode === $arriveeCode) {
                        $itineraireParts[] = "{$heureArriveeVol} {$arriveeCode}";
                    } else {
                        $itineraireParts[] = "{$heureArriveeVol} {$arriveeCode}";
                    }

                    $itineraireComplet = implode(' - ', $itineraireParts);
                @endphp

                <div>
                    {{ $vol->numero_vol }} {{ $itineraireComplet }}     @if($documentVols)  <span> OR SUB VOIR PIECE JOINTE </span> @endif
                </div>

            @endforeach

        </div>
    </div>

@endif



<!-- Dates avec alignement parfait -->
<div style="display: flex; margin: 5px 0;">
    <div style="font-weight: bold; width: 300px;">Date début de validité/Validity Start Date:</div>
    <div style="font-weight: bold; width: 150px; text-align: center;">
        {{ date('d/m/Y', strtotime($autorisation->demande->date_debut)) }}
    </div>
</div>

<div style="display: flex; margin: 5px 0;">
    <div style="font-weight: bold; width: 300px;">Date fin de validité/Validity End Date:</div>
    <div style="font-weight: bold; width: 150px; text-align: center;">
        {{ date('d/m/Y', strtotime($autorisation->demande->date_fin)) }}

    </div>
    <div style="font-weight: bold; width: 150px; text-align: center;">
        @if (!empty($autorisation->demande->sous_validite))
            <span style="margin-left: 10px;">+ {{ $autorisation->demande->sous_validite }} H</span>
        @endif
    </div>
</div>
                    </div>

                    <div style="margin: 10px 0;">
                        <p class="note">Cette autorisation est délivrée sous réserve que/ This permit is issued subject to :</p>
                        <ol style="margin: 10px 0; padding-left: 15px;">
                            <li>Tous les documents de bord de l'aéronef soient en cours de validité pendant l'opération de vol ci-dessus autorisé/All aircraft's onboard documents be valid for the operation of authorized above flight.</li>
                            <li>La réglementation aérienne mauritanienne soit scrupuleusement respectée/Mauritanian Aviation Regulation is scrupulously respected.</li>
                        </ol>
                    </div>

                    @if ($autorisation->demande->type->id === 2)
                        <div style="margin: 10px 0;">
                            <p class="note">NB. Le paiement des frais et taxes relatifs à la délivrance de la présente autorisation sont dus dès la signature de celle-ci par l'ANAC.</p>
                            <p class="note">NB. The payment of the fees and taxes relating to the issue of this authorization are due as soon as it is signed by the ANAC.</p>
                        </div>
                    @endif

                    <p class="note">Salutations distinguées Stop</p>
                    <p class="note">Nouakchott Mauritanie Stop et Fin</p>
                </td>
            </tr>
        </table>

        <!-- Footer with recipients and signature -->
        <table>
            <tr>
                <td>
                    <div class="footer-section">
                        <!-- LEFT SIDE: List of recipients -->
                        <div class="recipients-list">
                            <ul>
                                <li>• MDN / MET</li>
                                <li>• E.M.A.A</li>
                                <li>• ASECNA / ONAM / NKC</li>
                                <li>• CTA / Police / Douane</li>
                                <li>• Afroport NKC / C3I</li>
                                <li>• DTA / DSV / DAF / ANAC</li>
                            </ul>
                            <img src="{{ asset('/uploads/iso9001.jpg') }}" style="height: 40px; margin-top: 10px;">
                        </div>

                        <!-- RIGHT SIDE: Signature and Seal -->
                        <div class="signature-block">
                            <div class="signature-line">
                                <strong>Nom du signataire : </strong>
                                <div style="font-weight: bold; font-size: 16px; margin-left: 10px;">
                                    @if (isset($autorisation->nom_signataire))
                                        {{ $autorisation->nom_signataire }}
                                    @endif
                                </div>
                            </div>

                            <div class="signature-line">
                                <strong>Titre : </strong>
                                <div style="font-weight: bold; font-size: 16px; margin-left: 10px;">
                                    Directeur Général
                                </div>
                            </div>

                            <div class="signature-images">
                                <div style="width: 25%;">
                                    <div>Signature et cachet:</div>
                                </div>
                                <div style="width: 75%; text-align: center;">
                                    @if (isset($autorisation->signature_dg))
                                        <img src="{{ asset('/uploads/' . $autorisation->signature_dg) }}"
                                             style="height: 100px; margin-bottom: 1px;">
                                    @endif
                                    @if (isset($autorisation->cachet))
                                        <img src="{{ asset('/uploads/' . $autorisation->cachet) }}"
                                             style="height: 100px; opacity: 0.9;">
                                    @endif
                                </div>
                            </div>
                        </div>
                    </div>
                </td>
            </tr>
        </table>
    </div>

<div style="text-align: center; margin-top: 20px;" class="no-print">
    <button onclick="window.print()" class="submit-btn">Imprimer PDF</button>
    

    
    @if($documentAvions)
        <a href="{{ asset('/uploads/' . $documentAvions->url) }}" 
           class="submit-btn"
           target="_blank"
           onclick="setTimeout(() => { window.print(); }, 1000);"
           style="margin-left: 10px; background-color: #4CAF50; text-decoration: none; display: inline-block; padding: 10px 20px; color: white; border-radius: 5px;">
            Imprimer la liste des avions
        </a>
    @else

        <button disabled 
                class="submit-btn" 
                style="margin-left: 10px; background-color: #cccccc; cursor: not-allowed;"
                title="Document non disponible">
            Liste des avions non disponible
        </button>
    @endif
    
    @if($documentVols)
        <a href="{{ asset('/uploads/' . $documentVols->url) }}" 
           class="submit-btn"
           target="_blank"
           style="margin-left: 10px; background-color: #4CAF50; text-decoration: none; display: inline-block; padding: 10px 20px; color: white; border-radius: 5px;">
            Imprimer la liste des vols
        </a>
    @else

        <button disabled 
                class="submit-btn" 
                style="margin-left: 10px; background-color: #cccccc; cursor: not-allowed;"
                title="Document non disponible">
            Liste des vols non disponible
        </button>
    @endif
</div>
    <script>
        document.addEventListener('DOMContentLoaded', function() {
            window.print();
        });
    </script>
</body>
</html>