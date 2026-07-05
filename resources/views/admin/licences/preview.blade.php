<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>ID Card</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <style>
        /* Define the font */
        @font-face {
            font-family: 'LouguiyaFR';
            src: url('/assets/admin/fonts/Louguiya/LouguiyaFR.ttf') format('ttf'),
                url('/assets/admin/fonts/Louguiya/LouguiyaFR.ttf') format('ttf');
            font-weight: normal;
            font-style: normal;
            font-display: swap;
        }

        @font-face {
            font-family: 'LouguiyaFR';
            src: url('/assets/admin/fonts/Louguiya/LouguiyaFR Bold.ttf') format('ttf'),
                url('/assets/admin/fonts/Louguiya/LouguiyaFR Bold.ttf') format('ttf');
            font-weight: bold;
            font-style: normal;
            font-display: swap;
        }

        body {
            font-family: 'LouguiyaFR', sans-serif;
        }

        .id-card {
            width: 600px;
            height: 350px;
            padding: 15px;
            box-shadow: 3px 3px 10px rgba(0, 0, 0, 0.3);
            position: relative;
            margin-bottom: 20px;
            font-size: 13px;
            color: #000;
            /* Set text color to black */
            font-weight: bold;
            /* Make text bold */
        }

        .signature-demandeur {
            position: absolute;
            top: 270px;
            /* Adjusted upward to be closer to other elements */
            left: 30px;
            width: 40mm;
            /* Slightly wider container */
            text-align: left;
            /* Align text and image to left */
            display: flex;
            align-items: center;
            gap: 5px;
            /* Space between "VII" and the signature */
            font-size: 13px;
            font-weight: bold;
        }

        .photo {
            width: 40mm;
            height: 50mm;
            position: absolute;
            top: 30px;
            left: 30px;
            right: 20px;
        }

        .id-details-front {
            margin-left: 175px;
        }

        .id-details-back {
            margin-left: 130px;
        }

        h5 {
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
            line-height: 2.3;
        }

        li {
            font-size: 10px;
            color: #000;
            /* Set text color to black */
            font-weight: bold;
            /* Make text bold */
        }

        .no-bullets {
            list-style-type: none;
        }

        .no-bullets li {
            white-space: nowrap;
            display: block;
            overflow: hidden;
            text-overflow: ellipsis;
        }

        .button-group {
            margin-bottom: 20px;
            display: flex;
            gap: 10px;
        }

        @media print {
            .button-group {
                display: none;
            }
        }

        /* Styles pour le back side avec ajustement dynamique */
        .id-details-back.dynamic-font {
            font-size: 15px;
        }

        .id-details-back.dynamic-font p {
            font-size: 15px;
        }

        .id-details-back.dynamic-font li {
            font-size: 12px;
        }

        .id-details-back.dynamic-font .id-details-front p {
            line-height: 1.7;
        }

        .id-details-back.dynamic-font h5 {
            line-height: 1.2;
        }
    </style>
</head>

<body>
    <div class="container mt-3 d-flex justify-content-center flex-column align-items-center">
        <div class="button-group">
            <button id="printID" class="btn btn-secondary">Print ID</button>
        </div>
        <div id="idCardContainer">
            <!-- Front Side -->
            <div class="id-card" id="frontSide">
                <h5 style="text-align: center;margin-top: 50px;margin-bottom: 15px;font-size: 15px;">
                    {{ strtoupper($licence->categorie_licence) }}
                </h5>
                <img style="margin-top: 60px;" src="{{ asset('/uploads/' . $licence->photo) }}" alt="Profile Photo"
                    class="photo">
                <p class="signature-demandeur">
                    VII <img src="{{ asset('/uploads/' . $licence->signature) }}" width="120" height="80">
                </p>

                <!-- Placeholder for profile photo -->
                <div class="id-details-front">
                    <div style="display: flex; gap: 100px;">
                        <p>II {{ $licence->type_licence }}</p>
                        <p>III {{ $licence->numero_licence }}</p>
                    </div>
                    <p>IV {{ $licence->np }}</p>
                    @php
                        use Carbon\Carbon;
                        $date_naissance = Carbon::parse($licence->date_naissance);
                        $date_naissance = $date_naissance->format('d-m-Y');

                        $date_deliverance = Carbon::parse($licence->date_deliverance);
                        $date_deliverance = $date_deliverance->format('d-m-Y');

                        $date_expiration = Carbon::parse($licence->date_expiration);
                        $date_expiration = $date_expiration->format('d-m-Y');

                        $date_mise_a_jour = Carbon::parse($licence->date_mise_a_jour);
                        $date_mise_a_jour = $date_mise_a_jour->format('d-m-Y');
                    @endphp
                    <div style="display: flex; gap: 45px;">
                        <p>IVa {{ $date_naissance }}</p>
                        <p>VI {{ strtoupper($licence->nationalite) }}</p>
                    </div>
                    <p>V {{ $licence->adresse }}</p>
                    <p>VIII Issued in accordance with Mauritanian Regulation RTA1-PEL and compliant with applicable ICAO
                        Standards</p>
                    <p>IX This licence is valid only with a valid medical for <br> the holder's privileges
                    </p>
                </div>
            </div>

            <!-- Back Side -->
            <div class="id-card" id="backSide">
                <div class="id-details-back" id="dynamicBackContent">
                    <p>X First issue date {{ $date_deliverance }} </p>
                    
                    <p>X Reissue date {{ date('d-m-Y') }}<img src="{{ asset('/uploads/' . $licence->signature_dg) }}"
                            width="100" height="50"></p>
                    
                    <p>XI <img src="{{ asset('/uploads/' . $licence->cachet) }}" width="100" height="110"
                            style="margin-right: 40px">

                        XIVc {!! QrCode::size(100)->errorCorrection('H')->margin(0)->encoding('UTF-8')->generate(
                                json_encode([
                                    $licence->categorie_licence,
                                    $licence->type_licence,
                                    $licence->numero_licence,
                                    $licence->np,
                                    $date_expiration,
                                ]),
                            ) !!}
                    </p>
                    <p style="margin-top: 5px">
XII
@php
    $currentDate = Carbon::now();
    $typeDetails = [];
@endphp

@if (!empty($qualification_types) && $qualification_types->isNotEmpty())
    @php
        $groupedQualifications = [];
        foreach ($qualification_types as $qualification_type) {
            $typeStartDate = Carbon::parse($qualification_type->date_examen);
            $codeAirCraft = $qualification_type->code;
            
            // Calcul de la date d'expiration selon le type de licence
            if (in_array($demande->typeLicence->id, [31 ,36, 39])) {
                $typeExpiryDate = $typeStartDate->copy()->addMonths(
                    $demande->typeLicence->id == 39 ? 12 : 24
                )->endOfMonth();
            } else {
                $typeExpiryDate = $typeStartDate->copy()->addMonths(12)->endOfMonth();
            }
            
            // Vérifier si la qualification est encore valide
            if ($currentDate->lte($typeExpiryDate)) {
                // Grouper par codeAirCraft et garder la date d'expiration maximale
                if (!isset($groupedQualifications[$codeAirCraft]) || 
                    $typeExpiryDate->gt($groupedQualifications[$codeAirCraft]['expiry_date'])) {
                    $groupedQualifications[$codeAirCraft] = [
                        'expiry_date' => $typeExpiryDate,
                        'formatted_date' => $typeExpiryDate->format('d-m-Y')
                    ];
                }
            }
        }
        
        $resultStrings = [];
        
        // Pour les types de licence 36 et 39, on regroupe tous les codes avec leurs dates
        if (in_array($demande->typeLicence->id, [36, 39])) {
            // Pour le type 36 et 39, on peut avoir plusieurs codes avec différentes dates
            // On les regroupe par date d'expiration
            $groupedByExpiry = [];
            foreach ($groupedQualifications as $code => $data) {
                $expiryDate = $data['formatted_date'];
                if (!isset($groupedByExpiry[$expiryDate])) {
                    $groupedByExpiry[$expiryDate] = [];
                }
                $groupedByExpiry[$expiryDate][] = $code;
            }
            
            foreach ($groupedByExpiry as $expiryDate => $codes) {
                $resultStrings[] = implode(', ', $codes) . " [{$expiryDate}]";
            }
        } else {
            // Pour les autres types, format "code [date]"
            foreach ($groupedQualifications as $code => $data) {
                $resultStrings[] = "{$code} [{$data['formatted_date']}]";
            }
        }
        
        $typeString = implode('; ', $resultStrings);
    @endphp

    <span>{{ $typeString }}</span>
@endif

@if (!empty($qualification_amts) && $qualification_amts->isNotEmpty())
    @php
        $groupedAmt = [];
        foreach ($qualification_amts as $qualification_amt) {
            $amtStartDate = Carbon::parse($qualification_amt->date_examen);
            $amtExpiryDate = $amtStartDate->copy()->addMonths(24)->endOfMonth();
            
            if ($currentDate->lte($amtExpiryDate)) {
                $code = $qualification_amt->code;
                if (!isset($groupedAmt[$code]) || $amtExpiryDate->gt($groupedAmt[$code]['expiry_date'])) {
                    $groupedAmt[$code] = [
                        'expiry_date' => $amtExpiryDate,
                        'formatted_date' => $amtExpiryDate->format('d-m-Y'),
                        'display' => $qualification_amt->amt_display
                    ];
                }
            }
        }
        
        $amtDetails = [];
        foreach ($groupedAmt as $code => $data) {
            $amtDetails[] = "{$data['display']} {$code} [{$data['formatted_date']}]";
        }
        
        $amtString = implode('; ', $amtDetails);
    @endphp
    <span> {{ $amtString }}</span>
@endif

@if (!empty($qualification_atcs) && $qualification_atcs->isNotEmpty())
    @php
        $groupedAtc = [];
        foreach ($qualification_atcs as $qualification_atc) {
            $atcStartDate = Carbon::parse($qualification_atc->date_examen);
            $atcExpiryDate = $atcStartDate->copy()->addMonths(24)->endOfMonth();

            if ($currentDate->lte($atcExpiryDate)) {
                $atc = $qualification_atc->atc_display;
                if (!isset($groupedAtc[$atc]) || $atcExpiryDate->gt($groupedAtc[$atc]['expiry_date'])) {
                    $groupedAtc[$atc] = [
                        'expiry_date' => $atcExpiryDate,
                        'formatted_date' => $atcExpiryDate->format('d-m-Y'),
                        'display' => $atc
                    ];
                }
            }
        }
        
        $atcDetails = [];
        foreach ($groupedAtc as $atc => $data) {
            $atcDetails[] = "{$data['display']} [{$data['formatted_date']}]";
        }
        
        $atcString = implode('; ', $atcDetails);
    @endphp
    <span> {{ $atcString }}</span>
@endif

@if (!empty($qualification_rpas) && $qualification_rpas->isNotEmpty())
    @php
        $groupedRpa = [];
        foreach ($qualification_rpas as $qualification_rpa) {
            $rpaStartDate = Carbon::parse($qualification_rpa->date_examen);
            $rpaExpiryDate = $rpaStartDate->copy()->addMonths(12)->endOfMonth();
            
            if ($currentDate->lte($rpaExpiryDate)) {
                $rpa = $qualification_rpa->rpa;
                if (!isset($groupedRpa[$rpa]) || $rpaExpiryDate->gt($groupedRpa[$rpa]['expiry_date'])) {
                    $groupedRpa[$rpa] = [
                        'expiry_date' => $rpaExpiryDate,
                        'formatted_date' => $rpaExpiryDate->format('d-m-Y'),
                        'display' => $rpa
                    ];
                }
            }
        }
        
        $rpaDetails = [];
        foreach ($groupedRpa as $rpa => $data) {
            $rpaDetails[] = "{$data['display']} [{$data['formatted_date']}]";
        }
        
        $rpaString = implode('; ', $rpaDetails);
    @endphp
    <span> {{ $rpaString }}</span>
@endif
                        @if (!empty($qualification_ifr) || $qualification_classe->isNotEmpty())

    @php
        $ifrExpiryDate = null;
        $typeMoteur = '';

        // Qualification IFR
        if (!empty($qualification_ifr)) {
            $ifrStartDate = Carbon::parse($qualification_ifr->date_examen);
            $ifrExpiryDate = $ifrStartDate->copy()
                ->addMonths(12)
                ->endOfMonth();

            if ($currentDate->gt($ifrExpiryDate)) {
                $ifrExpiryDate = null;
            } else {
                $ifrExpiryDate = $ifrExpiryDate->format('d-m-Y');
            }
        }

        // Plusieurs qualifications de classe
        if ($qualification_classe->isNotEmpty()) {
            $typeMoteur = $qualification_classe
                ->pluck('type_moteur')
                ->filter()
                ->unique()
                ->implode('/');
        }
    @endphp

    <p>
        @if (!empty($qualification_ifr) && !empty($typeMoteur))
            IR({{ $typeMoteur }})
            @if ($ifrExpiryDate)
                [{{ $ifrExpiryDate }}]
            @endif
        @else
            @if (!empty($qualification_ifr))
                IR
                @if ($ifrExpiryDate)
                    [{{ $ifrExpiryDate }}]
                @endif
            @endif

            @if (!empty($typeMoteur))
                {{ $typeMoteur }}
            @endif
        @endif
    </p>

@endif
@if (!empty($qualification_instructeur))
    @php
        $groupedInstructeur = [];
        foreach ($qualification_instructeur as $qualification_inst) {
            $instructeurStartDate = Carbon::parse($qualification_inst->date_examen);
            $instExpiryDate = $instructeurStartDate->copy()->addMonths(24)->endOfMonth();

            if ($currentDate->lte($instExpiryDate)) {
                // Créer une clé unique basée sur le type, machine et code
                $key = $qualification_inst->type_privilege . '|' . 
                       ($qualification_inst->machine ?? '') . '|' . 
                       ($qualification_inst->code ?? '');
                
                if (!isset($groupedInstructeur[$key]) || 
                    $instExpiryDate->gt($groupedInstructeur[$key]['expiry_date'])) {
                    $groupedInstructeur[$key] = [
                        'expiry_date' => $instExpiryDate,
                        'formatted_date' => $instExpiryDate->format('d-m-Y'),
                        'type' => $qualification_inst->type_privilege,
                        'machine' => $qualification_inst->machine,
                        'code' => $qualification_inst->code
                    ];
                }
            }
        }
        
        // Regrouper par date d'expiration pour l'affichage
        $groupedByExpiry = [];
        foreach ($groupedInstructeur as $data) {
            $expiryDate = $data['formatted_date'];
            if (!isset($groupedByExpiry[$expiryDate])) {
                $groupedByExpiry[$expiryDate] = [
                    'types' => [],
                    'machines' => [],
                    'codes' => []
                ];
            }
            
            if (!empty($data['type']) && !in_array($data['type'], $groupedByExpiry[$expiryDate]['types'])) {
                $groupedByExpiry[$expiryDate]['types'][] = $data['type'];
            }
            if (!empty($data['machine']) && !in_array($data['machine'], $groupedByExpiry[$expiryDate]['machines'])) {
                $groupedByExpiry[$expiryDate]['machines'][] = $data['machine'];
            }
            if (!empty($data['code']) && !in_array($data['code'], $groupedByExpiry[$expiryDate]['codes'])) {
                $groupedByExpiry[$expiryDate]['codes'][] = $data['code'];
            }
        }
        
        $instructeurDetails = [];
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
        $groupedExaminateur = [];
        foreach ($qualification_examinateur as $qualification_exam) {
            $examinateurStartDate = Carbon::parse($qualification_exam->date_examen);
            
            if(in_array($demande->typeLicence->id, [31 , 35])){
                $examExpiryDate = $examinateurStartDate->copy()->addMonths(24)->endOfMonth();
            } else {
                $examExpiryDate = $examinateurStartDate->copy()->addMonths(12)->endOfMonth();
            }
            
            if ($currentDate->lte($examExpiryDate)) {
                // Créer une clé unique basée sur le type, machine et code
                $key = $qualification_exam->type_privilege . '|' . 
                       ($qualification_exam->machine ?? '') . '|' . 
                       ($qualification_exam->code ?? '');
                
                if (!isset($groupedExaminateur[$key]) || 
                    $examExpiryDate->gt($groupedExaminateur[$key]['expiry_date'])) {
                    $groupedExaminateur[$key] = [
                        'expiry_date' => $examExpiryDate,
                        'formatted_date' => $examExpiryDate->format('d-m-Y'),
                        'type' => $qualification_exam->type_privilege,
                        'machine' => $qualification_exam->machine,
                        'code' => $qualification_exam->code
                    ];
                }
            }
        }
        
        $examinateurDetails = [];
        foreach ($groupedExaminateur as $data) {
            if(!empty($data['machine']) && !empty($data['code'])){
                $examinateurDetails[] = "{$data['type']} ({$data['machine']}) ({$data['code']}) [{$data['formatted_date']}]";
            } else {
                $examinateurDetails[] = "{$data['type']} [{$data['formatted_date']}]";
            }
        }
        
        $examinateurString = implode('; ', $examinateurDetails);
    @endphp
    
    @if (!empty($examinateurString))
        <p>{{ $examinateurString }}</p>
    @endif
@endif

@if (!empty($qualification_ulm))
    @php
        $ulmStartDate = $qualification_ulm->date_examen;
        $ulmStartDate = Carbon::parse($ulmStartDate);
        $ulmExpiryDate = $ulmStartDate->copy()->addMonths(12)->endOfMonth();
    @endphp
    @if ($currentDate->lte($ulmExpiryDate))
        @php
            $ulmExpiryDate = $ulmExpiryDate->format('d-m-Y');
        @endphp
        <p>{{ $qualification_ulm->ulm }} [{{ $ulmExpiryDate }}]</p>
    @endif
@endif
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
                    @if (!in_array($licence->demande->typeDemande->id, [1, 3]))
                        <p>XIVa Licence updated by :
                            @if (optional($licence->demande->etatDemande)->pel_dsv_signer)
                                PEL
                            @else
                                DSV
                            @endif
                        </p>
                    @endif

                    <p>XIVb Licence expiry date : {{ $date_expiration }}</p>

                </div>
            </div>
        </div>
    </div>
</body>
<script src="https://cdnjs.cloudflare.com/ajax/libs/jquery/3.6.0/jquery.min.js"></script>
<script src="https://cdnjs.cloudflare.com/ajax/libs/html2canvas/1.4.1/html2canvas.min.js"></script>
<script src="https://cdnjs.cloudflare.com/ajax/libs/jspdf/2.5.1/jspdf.umd.min.js"></script>


<script>
    $(document).ready(function() {
        // Fonction pour ajuster dynamiquement la taille de police du verso
        function adjustBackSideFontSize() {
            const backSide = document.getElementById('backSide');
            const content = document.getElementById('dynamicBackContent');
            const originalFontSize = 13; // Taille de police d'origine
            
            // Réinitialiser la taille de police
            content.style.fontSize = originalFontSize + 'px';
            
            // Vérifier si le contenu dépasse la hauteur du conteneur
            if (content.scrollHeight > backSide.offsetHeight) {
                // Calculer le ratio de réduction nécessaire
                const ratio = backSide.offsetHeight / content.scrollHeight;
                
                // Appliquer la nouvelle taille de police (avec un minimum de 8px)
                const newFontSize = Math.max(8, originalFontSize * ratio * 0.9);
                content.style.fontSize = newFontSize + 'px';
                
                // Ajuster également les autres éléments textuels
                const allElements = content.querySelectorAll('p, li, span');
                allElements.forEach(el => {
                    el.style.fontSize = newFontSize + 'px';
                });
            }
        }

        // Appeler la fonction d'ajustement au chargement et après un délai pour les images
        adjustBackSideFontSize();
        window.addEventListener('load', adjustBackSideFontSize);
        setTimeout(adjustBackSideFontSize, 500);

        // Réajuster en cas de redimensionnement
        window.addEventListener('resize', adjustBackSideFontSize);

        $("#printID").click(function() {
            const {
                jsPDF
            } = window.jspdf;
            let pdf = new jsPDF({
                orientation: "landscape",
                unit: "mm",
                format: [85.6, 54] // CR-80 card size in mm
            });

            let options = {
                scale: 2,
                useCORS: true,
                allowTaint: true
            };

            // Create a promise to handle both sides
            Promise.all([
                html2canvas(document.getElementById("frontSide"), options),
                html2canvas(document.getElementById("backSide"), options)
            ]).then(([frontCanvas, backCanvas]) => {
                // Add front side
                pdf.addImage(frontCanvas, 'PNG', 0, 0, 85.6, 54);

                // Add back side on new page
                pdf.addPage([85.6, 54], 'landscape');
                pdf.addImage(backCanvas, 'PNG', 0, 0, 85.6, 54);

                // Generate PDF as blob
                const pdfBlob = pdf.output('blob');
                const pdfUrl = URL.createObjectURL(pdfBlob);

                // Open in new tab with Adobe Reader print dialog
                const newWindow = window.open(pdfUrl);

                // Attempt to trigger print after PDF loads
                if (newWindow) {
                    newWindow.onload = function() {
                        setTimeout(function() {
                            newWindow.print();
                        }, 1000);
                    };
                }
            });
        });
    });
</script>

</html>