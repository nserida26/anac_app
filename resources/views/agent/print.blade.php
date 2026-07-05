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

        /* Dans la section <style> de votre head, modifiez ces valeurs */
        .id-card {
            font-size: 15px;
            /* Augmenté de 13px à 14px */
        }

        p {
            font-size: 15px;
            /* Augmenté de 13px à 14px */
        }

        li {
            font-size: 12px;
            /* Augmenté de 10px à 12px */
        }

        .id-details-front p {
            line-height: 1.7;
            /* Légèrement augmenté de 2.3 */
        }

        h5 {
            line-height: 1.2;
            /* Légèrement augmenté de 1.6 */
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
                    <p>IX This licence is valid only with a valid medical for <br> the holder’s privileges
                    </p>
                </div>


            </div>

            <!-- Back Side -->
            <div class="id-card" id="backSide">
                <div class="id-details-back">

                    <p>X First issue date {{ $date_deliverance }} </p>
                    <p>X Reissue date {{ date('d-m-Y') }}<img src="{{ asset('/uploads/' . $licence->signature_dg) }}"
                            width="100" height="50"></p>
                    @if (!in_array($licence->demande->typeDemande->id, [1, 3]))
                        <p>X Reissue date {{ $date_mise_a_jour }}
                            @if (optional($licence->demande->etatDemande)->pel_dsv_signer)
                                <img src="{{ asset('/uploads/' . $licence->signature_pel) }}" width="100"
                                    height="50">
                            @else
                                <img src="{{ asset('/uploads/' . $licence->signature_dsv) }}" width="100"
                                    height="50">
                            @endif
                        </p>
                    @endif
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
                    <p style="margin-top: 10px">
                        XII
                        @php
                            $typeDetails = [];
                            $currentDate = Carbon::now();
                        @endphp
                        @if (!empty($qualification_types) && $qualification_types->isNotEmpty())
                            @foreach ($qualification_types as $qualification_type)
                                @php
                                    $typeStartDate = $qualification_type->date_examen;
                                    $typeStartDate = Carbon::parse($typeStartDate);

                                    $codeAirCraft = $qualification_type->code;

                                    if (in_array($demande->typeLicence->id, [36, 39])) {
                                        # code...
                                        $typeExpiryDate = $typeStartDate->copy()->addMonths(24)->endOfMonth();
                                        if ($currentDate->lte($typeExpiryDate)) {
                                            $typeExpiryDate = $typeExpiryDate->format('d-m-Y');
                                            $typeDetails[] = "{$codeAirCraft}";
                                        }
                                    } else {
                                        $typeExpiryDate = $typeStartDate->copy()->addMonths(12)->endOfMonth();
                                        if ($currentDate->lte($typeExpiryDate)) {
                                            $typeExpiryDate = $typeExpiryDate->format('d-m-Y');
                                            $typeDetails[] = "{$codeAirCraft} [{$typeExpiryDate}]";
                                        }
                                    }

                                @endphp
                            @endforeach
                            @php

                                if (in_array($demande->typeLicence->id, [36, 39])) {
                                    # code...
                                    $typeString = implode(', ', $typeDetails);
                                } else {
                                    $typeString = implode('; ', $typeDetails);
                                }
                            @endphp
                            @if (in_array($demande->typeLicence->id, [36, 39]))
                                <span> {{ $typeString }} [{{ $typeExpiryDate }}]</span>
                            @else
                                <span> {{ $typeString }}</span>
                            @endif



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
                                    $rpaExpiryDate = $rpaStartDate->copy()->addMonths(12)->endOfMonth();
                                    $rpa = $qualification_rpa->rpa;
                                    if ($currentDate->lte($rpaExpiryDate)) {
                                        $rpaExpiryDate = $rpaExpiryDate->format('d-m-Y');
                                        $rpaDetails[] = "{$rpa} [{$rpaExpiryDate}]";
                                    }
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
                            <br>
                            <span>
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
                            </span>
                        @endif
                    <ul class="no-bullets">



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
                                <li>{{ $qualification_ulm->ulm }}
                                    [{{ $ulmExpiryDate }}]</li>
                            @endif

                        @endif

                        @if (!empty($qualification_instructeur))
                            @php
                                $instructeurDetails = [];
                            @endphp
                            @foreach ($qualification_instructeur as $qualification_instructeur)
                                @php
                                    $instructeurStartDate = $qualification_instructeur->date_examen;
                                    $instructeurStartDate = Carbon::parse($instructeurStartDate);
                                    $instExpiryDate = $instructeurStartDate->copy()->addMonths(12)->endOfMonth();

                                    if ($currentDate->lte($instExpiryDate)) {
                                        $instExpiryDateFormatted = $instExpiryDate->format('d-m-Y');
                                        $instructeurDetails[] =
                                            "{$qualification_instructeur->type_privilege} " .
                                            "({$qualification_instructeur->machine}) " .
                                            "({$qualification_instructeur->code}) [{$instExpiryDateFormatted}]";
                                    }
                                @endphp
                            @endforeach
                            @php
                                $instructeurString = implode('; ', $instructeurDetails);
                            @endphp
                            @if (!empty($instructeurString))
                                <li>{{ $instructeurString }}</li>
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
                                    $examExpiryDate = $examinateurStartDate->copy()->addMonths(12)->endOfMonth();

                                    if ($currentDate->lte($examExpiryDate)) {
                                        $examExpiryDateFormatted = $examExpiryDate->format('d-m-Y');
                                        $examinateurDetails[] =
                                            "{$qualification_examinateur->type_privilege} " .
                                            "({$qualification_examinateur->machine}) " .
                                            "({$qualification_examinateur->code}) [{$examExpiryDateFormatted}]";
                                    }
                                @endphp
                            @endforeach
                            @php
                                $examinateurString = implode('; ', $examinateurDetails);
                            @endphp
                            @if (!empty($examinateurString))
                                <li>{{ $examinateurString }}</li>
                            @endif
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
                    @if (!in_array($licence->demande->typeDemande->id, [1, 3]))
                        <p>XIVa Licence updated by:
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
