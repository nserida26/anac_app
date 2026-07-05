<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>ID Card</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <style>
        @font-face {
            font-family: 'LouguiyaFR';
            src: url('/assets/admin/fonts/LouguiyaFR.ttf') format('ttf'),
                url('/assets/admin/fonts/LouguiyaFR.ttf') format('ttf');
            font-weight: bold;
            font-style: bold;
        }

        body {
            font-family: 'LouguiyaFR', sans-serif;
            background-color: #f8f9fa;
        }

        .a4-container {
            width: 210mm;
            min-height: 297mm;
            margin: 0 auto;
            padding: 20px;
            background: white;
            box-shadow: 0 0 10px rgba(0, 0, 0, 0.1);
        }

        .header-container {
            display: flex;
            flex-direction: column;
            align-items: center;
            border-bottom: 1px solid #000;
            padding-bottom: 5px;
            margin-bottom: 5px;
        }

        .logo {
            height: 80px;
            margin-bottom: 5px;
        }

        .header-english {
            text-align: center;
            font-size: 15px;
            font-weight: bold;
            line-height: 1.2;
        }

        .id-card-container {
            display: flex;
            flex-direction: column;
            align-items: center;
            gap: 30px;
            margin-top: 10px;
        }

        .id-card {
            width: 600px;
            height: 350px;
            padding: 15px;
            box-shadow: 3px 3px 10px rgba(0, 0, 0, 0.3);
            position: relative;
            font-size: 13px;
            color: #000;
            font-weight: bold;
            border: 2px solid #000;
            background: white;
        }

        .signature-demandeur {
            position: absolute;
            top: 270px;
            left: 20px;
            width: 40mm;
            text-align: center;
            font-weight: 10px;
        }

        .photo {
            width: 40mm;
            height: 50mm;
            position: absolute;
            top: 20px;
            left: 20px;
            right: 20px;
        }

        .id-details-front {
            margin-left: 175px;
        }

        .id-details-back {
            margin-left: 130px;
        }

        h6 {
            margin: 0 0;
            color: #000;
            font-weight: bold;
            line-height: 1.6;
        }

        p {
            font-size: 13px;
            margin: 0 0;
            color: #000;
            font-weight: bold;
        }

        .id-details-front p {
            line-height: 1.8;
        }

        li {
            font-size: 10px;
            color: #000;
            font-weight: bold;
        }

        .no-bullets {
            list-style-type: none;
            padding-left: 0;
        }

        .button-group {
            margin-bottom: 20px;
            display: flex;
            gap: 10px;
            justify-content: center;
        }

        .card-label {
            text-align: center;
            font-weight: bold;
            margin-bottom: 10px;
            font-size: 18px;
            text-decoration: underline;
        }

        @media print {
            body {
                background: none;
                padding: 0;
                margin: 0;
            }

            .button-group {
                display: none;
            }

            .a4-container {
                box-shadow: none;
                padding: 0;
                width: 100%;
                height: 100%;
            }

            .id-card {
                box-shadow: none;
                margin: 0 auto;
                page-break-inside: avoid;
            }

            .id-card-container {
                gap: 20px;
            }
        }

        @page {
            size: A4;
            margin: 0;
        }
    </style>
</head>

<body>
    <div class="button-group">
        <button id="printID" class="btn btn-secondary">Print ID</button>
    </div>
    <div class="a4-container">
        <div class="header-container">
            <img src="{{ asset('assets/admin/imgs/logo.png') }}" alt="ANAC Logo" class="logo">
            <div class="header-english">
                ISLAMIC REPUBLIC OF MAURITANIA<br>
                MINISTRY OF EQUIPEMENT AND TRANSPORT<br>
                NATIONAL CIVIL AVIATION AGENCY
            </div>
        </div>

        <div class="id-card-container">
            <div class="card-label">AUTHENTIFICATION OF LICENSE</div>
            <div class="id-card" id="frontSide">
                <h6 style="text-align: center;"> {{ $licence->categorie_licence }}</h6>
                <img style="" src="{{ asset('/uploads/' . $licence->photo) }}" alt="Profile Photo" class="photo">
                <p class="signature-demandeur">Signature of holder :<img
                        src="{{ asset('/uploads/' . $licence->signature) }}" width="150" height="30"></p>

                <div class="id-details-front">
                    <p>Title of license : {{ $licence->type_licence }}</p>
                    <p>Serial number of the license : {{ $licence->numero_licence }}</p>
                    <p> Full name : {{ $licence->np }}</p>
                    @php
                        use Carbon\Carbon;
                        $date_naissance = Carbon::parse($licence->date_naissance);
                        $date_naissance = $date_naissance->format('d-M-Y');

                        $date_deliverance = Carbon::parse($licence->date_deliverance);
                        $date_deliverance = $date_deliverance->format('d-M-Y');

                        $date_expiration = Carbon::parse($licence->date_expiration);
                        $date_expiration = $date_expiration->format('d-M-Y');

                        $date_mise_a_jour = Carbon::parse($licence->date_mise_a_jour);
                        $date_mise_a_jour = $date_mise_a_jour->format('d-M-Y');

                    @endphp
                    <p>Date of birth : {{ $date_naissance }}</p>
                    <p>Address : {{ $licence->adresse }}</p>
                    <p>Nationality : {{ strtoupper($licence->nationalite) }}</p>

                    <p>Issued in accordance with Mauritanian Regulation RTA1-PEL and compliant with applicable
                        ICAO
                        Standards</p>
                    <p>HAS BEEN FOUND TO BE QUALIFIED TO EXERCISE THE PRILEGES OF THIS LICENSE</p>
                </div>
            </div>


            <div class="id-card" id="backSide">
                <div class="id-details-back">
                    <p>License issuer signature & date :
                        {{ $date_deliverance }} : <img src="{{ asset('/uploads/' . $licence->signature_dg) }}"
                            width="100" height="50">
                    </p>
                    <p>Seal of authority : <img src="{{ asset('/uploads/' . $licence->cachet) }}" width="100"
                            height="110" style="margin-right: 40px">

                        {!! QrCode::size(100)->errorCorrection('H')->margin(0)->encoding('UTF-8')->generate(
                                json_encode([
                                    $licence->categorie_licence,
                                    $licence->type_licence,
                                    $licence->numero_licence,
                                    $licence->np,
                                    $date_expiration,
                                ]),
                            ) !!}

                    </p>
                    <p>
                        Ratings :
                        @php
                            $typeDetails = [];
                            $currentDate = Carbon::now();
                            $groupedQualifications = [];
                        @endphp
                        
                        @if (!empty($qualification_types) && $qualification_types->isNotEmpty())
                            @foreach ($qualification_types as $qualification_type)
                                @php
                                    $typeStartDate = Carbon::parse($qualification_type->date_examen);
                                    $codeAirCraft = $qualification_type->code;
                                    
                                    // Calcul de la date d'expiration selon le type de licence
                                    if (in_array($demande->typeLicence->id, [36, 39])) {
                                        $typeExpiryDate = $typeStartDate->copy()->addMonths(
                                            $demande->typeLicence->id == 39 ? 12 : 24
                                        )->endOfMonth();
                                    } else {
                                        $typeExpiryDate = $typeStartDate->copy()->addMonths(12)->endOfMonth();
                                    }
                                    
                                    // Vérifier si la qualification est encore valide
                                    if ($currentDate->lte($typeExpiryDate)) {
                                        $expiryFormatted = $typeExpiryDate->format('d-m-Y');
                                        
                                        // Grouper par date d'expiration
                                        if (!isset($groupedQualifications[$expiryFormatted])) {
                                            $groupedQualifications[$expiryFormatted] = [];
                                        }
                                        $groupedQualifications[$expiryFormatted][] = $codeAirCraft;
                                    }
                                @endphp
                            @endforeach
                        
                            @php
                                $resultStrings = [];
                                
                                // Pour les types de licence 36 et 39, on affiche tous les codes avec une date commune
                                if (in_array($demande->typeLicence->id, [36, 39])) {
                                    foreach ($groupedQualifications as $expiryDate => $codes) {
                                        $resultStrings[] = implode(', ', $codes) . " [{$expiryDate}]";
                                    }
                                    $typeString = implode('; ', $resultStrings);
                                } else {
                                    // Pour les autres types, on conserve le format original
                                    foreach ($groupedQualifications as $expiryDate => $codes) {
                                        foreach ($codes as $code) {
                                            $resultStrings[] = "{$code} [{$expiryDate}]";
                                        }
                                    }
                                    $typeString = implode('; ', $resultStrings);
                                }
                            @endphp
                        
                            <span>{{ $typeString }}</span>
                        @endif
                        @php
                            $amtDetails = [];
                        @endphp
                        @if (!empty($qualification_amts) && $qualification_amts->isNotEmpty())
                            @foreach ($qualification_amts as $qualification_amt)
                                @php
                                    $amtStartDate = $qualification_amt->date_examen;
                                    $amtStartDate = Carbon::parse($amtStartDate);
                                    $amtExpiryDate = $amtStartDate->copy()->addMonths(24)->endOfMonth();
                                    $amt = $qualification_amt->amt_display;
                                    $code = $qualification_amt->code;
                                    if ($currentDate->lte($amtExpiryDate)) {
                                        $amtExpiryDate = $amtExpiryDate->format('d-m-Y');
                                        $amtDetails[] = "{$amt} {$code} [{$amtExpiryDate}]";
                                    }
                                @endphp
                            @endforeach
                            @php
                                $amtString = implode('; ', $amtDetails);
                            @endphp
                            <span> {{ $amtString }}</span>
                        @endif
                        @php
                            $atcDetails = [];
                        @endphp
                        @if (!empty($qualification_atcs) && $qualification_atcs->isNotEmpty())
                            @foreach ($qualification_atcs as $qualification_atc)
                                @php
                                    $atcStartDate = $qualification_atc->date_examen;
                                    $atcStartDate = Carbon::parse($atcStartDate);
                                    $atcExpiryDate = $atcStartDate->copy()->addMonths(24)->endOfMonth();

                                    $atc = $qualification_atc->atc_display;

                                    if ($currentDate->lte($atcExpiryDate)) {
                                        $atcExpiryDate = $atcExpiryDate->format('d-m-Y');
                                        $atcDetails[] = "{$atc} [{$atcExpiryDate}]";
                                    }
                                @endphp
                            @endforeach
                            @php
                                $atcString = implode('; ', $atcDetails);
                            @endphp
                            <span> {{ $atcString }}</span>
                        @endif
                        @php
                            $rpaDetails = [];
                        @endphp
                        @if (!empty($qualification_rpas) && $qualification_rpas->isNotEmpty())
                            @foreach ($qualification_rpas as $qualification_rpa)
                                @php
                                    $rpaStartDate = $qualification_rpa->date_examen;
                                    $rpaStartDate = Carbon::parse($rpaStartDate);
                                    $rpaExpiryDate = $rpaStartDate->copy()->addMonths(12);
                                    $rpaExpiryDate = $rpaExpiryDate->format('d-M-Y');
                                    $rpa = $qualification_rpa->rpa;

                                    $rpaDetails[] = "{$rpa} [{$rpaExpiryDate}]";
                                @endphp
                            @endforeach
                            @php
                                $rpaString = implode('; ', $rpaDetails);
                            @endphp
                            <span> {{ $rpaString }}</span>
                        @endif
                                                @if (!empty($qualification_ifr) || !empty($qualification_classe))
                            @php
                                $ifrExpiryDate = null;

                                $typeMoteur = '';

                                if (!empty($qualification_ifr)) {
                                    $ifrStartDate = Carbon::parse($qualification_ifr->date_examen);
                                    $ifrExpiryDate = $ifrStartDate->copy()->addMonths(12)->endOfMonth();
                                    if ($currentDate->gt($ifrExpiryDate)) {
                                        $ifrExpiryDate = null; // Ne pas afficher si expirée
                                    } else {
                                        $ifrExpiryDate = $ifrExpiryDate->format('d-m-Y');
                                    }
                                }
                                if (!empty($qualification_classe)) {
                                    $typeMoteur = $qualification_classe->type_moteur;
                                }

                            @endphp
                            
                            <p>
                                @if (!empty($qualification_ifr) && !empty($qualification_classe))
                                    IR({{ $typeMoteur }}) @if ($ifrExpiryDate)
                                        [{{ $ifrExpiryDate }}]
                                    @endif
                                @else
                                    @if (!empty($qualification_ifr))
                                        IR @if ($ifrExpiryDate)
                                            [{{ $ifrExpiryDate }}]
                                        @endif
                                    @endif
                                    @if (!empty($qualification_classe))
                                        {{ $typeMoteur }}
                                    @endif
                                @endif
                            </p>
                        @endif
                                                
                        @if (!empty($qualification_instructeur))
                            @php
                                $instructeurDetails = [];
                                $groupedByExpiry = [];
                            @endphp
                            
                            @foreach ($qualification_instructeur as $qualification_instructeur)
                                @php
                                    $instructeurStartDate = $qualification_instructeur->date_examen;
                                    $instructeurStartDate = Carbon::parse($instructeurStartDate);
                                    $instExpiryDate = $instructeurStartDate->copy()->addMonths(24)->endOfMonth();
                        
                                    if ($currentDate->lte($instExpiryDate)) {
                                        $instExpiryDateFormatted = $instExpiryDate->format('d-m-Y');
                                        
                                        // Créer une clé unique pour cette date d'expiration
                                        if (!isset($groupedByExpiry[$instExpiryDateFormatted])) {
                                            $groupedByExpiry[$instExpiryDateFormatted] = [
                                                'types' => [],
                                                'machines' => [],
                                                'codes' => []
                                            ];
                                        }
                                        
                                        // Ajouter les détails uniquement s'ils existent et ne sont pas déjà présents
                                        if (!empty($qualification_instructeur->type_privilege) && 
                                            !in_array($qualification_instructeur->type_privilege, $groupedByExpiry[$instExpiryDateFormatted]['types'])) {
                                            $groupedByExpiry[$instExpiryDateFormatted]['types'][] = $qualification_instructeur->type_privilege;
                                        }
                                        
                                        if (!empty($qualification_instructeur->machine) && 
                                            !in_array($qualification_instructeur->machine, $groupedByExpiry[$instExpiryDateFormatted]['machines'])) {
                                            $groupedByExpiry[$instExpiryDateFormatted]['machines'][] = $qualification_instructeur->machine;
                                        }
                                        
                                        if (!empty($qualification_instructeur->code) && 
                                            !in_array($qualification_instructeur->code, $groupedByExpiry[$instExpiryDateFormatted]['codes'])) {
                                            $groupedByExpiry[$instExpiryDateFormatted]['codes'][] = $qualification_instructeur->code;
                                        }
                                    }
                                @endphp
                            @endforeach
                            
                            @php
                                // Construire la chaîne finale groupée par date d'expiration
                                foreach ($groupedByExpiry as $expiryDate => $details) {
                                    $parts = [];
                                    
                                    if (!empty($details['types'])) {
                                        $parts[] = implode('/', $details['types']);
                                    }
                                    
                                    if (!empty($details['codes'])) {
                                        $parts[] = "(" . implode('/', $details['codes']) . ")";
                                    }
                                    
                                    if (!empty($details['machines'])) {
                                        $parts[] = "(" . implode('/', $details['machines']) . ")";
                                    }
                                    
                                    if (!empty($parts)) {
                                        $instructeurDetails[] = implode(' ', $parts) . " [{$expiryDate}]";
                                    }
                                }
                                
                                $instructeurString = implode('; ', $instructeurDetails);
                            @endphp
                            
                            @if (!empty($instructeurString))
                                <p>{{ $instructeurString }}</p>
                            @endif
                        @endif

                        @if (!empty($qualification_examinateur))
                            @php
                                $examinateurDetails = [];
                            @endphp
                            @foreach ($qualification_examinateur as $qualification_examinateur)
                                @php
                                    $examinateurStartDate = $qualification_examinateur->date_examen;
                                    $examinateurStartDate = Carbon::parse($examinateurStartDate);
                                    
                                    if(in_array($demande->typeLicence->id, [35])){
                                        $examExpiryDate = $examinateurStartDate->copy()->addMonths(24)->endOfMonth();
                                    }else{
                                    
                                        $examExpiryDate = $examinateurStartDate->copy()->addMonths(12)->endOfMonth();
                                    }
                                    if ($currentDate->lte($examExpiryDate)) {
                                        $examExpiryDateFormatted = $examExpiryDate->format('d-m-Y');
                                        if(!empty($qualification_examinateur->machine) && !empty($qualification_examinateur->code)){
                                        $examinateurDetails[] =
                                            "{$qualification_examinateur->type_privilege} " .
                                            "({$qualification_examinateur->machine}) " .
                                            "({$qualification_examinateur->code}) [{$examExpiryDateFormatted}]";
                                        }else{
                                            $examinateurDetails[] =
                                                    "{$qualification_examinateur->type_privilege} " .
                                                    "[{$examExpiryDateFormatted}]";
                                        }
                                    }
                                @endphp
                            @endforeach
                            @php
                                $examinateurString = implode('; ', $examinateurDetails);
                            @endphp
                            @if (!empty($examinateurString))
                                <p>{{ $examinateurString }}</p>
                            @endif
                        @endif
                    <ul class="no-bullets">
                        
                        @if (!empty($qualification_ulm))
                            @php
                                $ulmStartDate = $qualification_ulm->date_examen;
                                $ulmStartDate = Carbon::parse($ulmStartDate);
                                $ulmExpiryDate = $ulmStartDate->copy()->addMonths(12);
                                $ulmExpiryDate = $ulmExpiryDate->format('d-M-Y');

                            @endphp
                            <li>{{ $qualification_ulm->ulm }}
                                [{{ $ulmExpiryDate }}]</li>
                        @endif
                    </ul>
                    </p>
                    @if (!empty($competence_demandeur))
                        @php
                            $langStartDate = null;
                            $langExpiryDate = null;
                            $showLang = false;

                            if (in_array($competence_demandeur->niveau, [4, 5])) {
                                $langStartDate = $competence_demandeur->date;
                                $langStartDate = Carbon::parse($langStartDate);
                                $langExpiryDate = $langStartDate->copy()->addMonths($competence_demandeur->validite);

                                if ($currentDate->lte($langExpiryDate)) {
                                    $showLang = true;
                                    $langExpiryDateFormatted = $langExpiryDate->format('d-m-Y');
                                }
                            } else {
                                $showLang = true;
                            }
                        @endphp

                        @if ($showLang)
                            <p>XIII E L P Level {{ $competence_demandeur->niveau }}
                                @if (!empty($langStartDate) && in_array($competence_demandeur->niveau, [4, 5]))
                                    [{{ $langExpiryDateFormatted }}]
                                @endif
                            </p>
                        @endif
                    @endif

                    {{--@if (!empty($entrainement_demandeurs) && $entrainement_demandeurs->isNotEmpty())
                        @php
                            $entrainementDetails = [];
                        @endphp

                        @foreach ($entrainement_demandeurs as $entrainement)
                            @php
                                $entrStartDate = Carbon::parse($entrainement->date);
                                $entrExpiryDate = $entrStartDate->copy()->addMonths($entrainement->validite);
                                $entrExpiryDateFormatted = $entrExpiryDate->format('d-M-Y');
                                $entrainementDetails[] = "{$entrainement->libelle} [{$entrExpiryDateFormatted}]";
                            @endphp
                        @endforeach

                        @php
                            $entrainementString = implode('; ', $entrainementDetails);
                        @endphp
                        @if (!empty($entrainementString))
                            <p>Recurent training
                                ({{ $entrainementString }})
                            </p>
                        @endif
                    @endif
                    --}}
                    @if (!empty($medical_certificat))
                        @php
                            $medicalStartDate = $medical_certificat->date_examen;
                            $medicalStartDate = Carbon::parse($medicalStartDate);
                            $medicalExpiryDate = $medicalStartDate->copy()->addMonths($medical_certificat->validite);
                            $medicalExpiryDate = $medicalExpiryDate->format('d-M-Y');

                            $class = '';
                            $class1 = [27, 28, 29, 30];
                            $class2 = [31, 32, 39];
                            $class3 = [35, 36, 37, 38];

                            if (in_array($licence->demande->typeLicence->id, $class1)) {
                                $class = 'Class 1';
                            } elseif (in_array($licence->demande->typeLicence->id, $class2)) {
                                $class = 'Class 2';
                            } elseif (in_array($licence->demande->typeLicence->id, $class3)) {
                                $class = 'Class 3';
                            }
                        @endphp
                        <p>Medical certificat ({{ $class }} [{{ $medicalExpiryDate }}])</p>
                    @endif

                    @if (!in_array($licence->demande->typeDemande->id, [1, 3]))
                        <p>License updated by: DSV ANAC at {{ $date_mise_a_jour }}</p>
                    @endif

                    <p>License expiry date:{{ $date_expiration }}</p>
                </div>
            </div>

        </div>
    </div>

    <script src="https://cdnjs.cloudflare.com/ajax/libs/jquery/3.6.0/jquery.min.js"></script>
    <script>
        $(document).ready(function() {
            $("#printID").click(function() {
                window.print();
            });
        });
    </script>
</body>

</html>
