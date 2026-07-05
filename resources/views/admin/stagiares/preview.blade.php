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
            background: linear-gradient(135deg, #e3f2fd 0%, #bbdefb 100%);
            min-height: 100vh;
            padding: 20px 0;
        }

        .id-card {
            width: 600px;
            height: 400px;
            padding: 15px;
            box-shadow: 3px 3px 15px rgba(33, 150, 243, 0.4);
            position: relative;
            margin-bottom: 20px;
            font-size: 13px;
            color: #0d47a1;
            font-weight: bold;
            border-radius: 12px;
            overflow: hidden;
            background: linear-gradient(to bottom, #87ceeb 0%, #b3e5fc 100%);
            border: 2px solid #42a5f5;
        }

        .id-card::before {
            content: "";
            position: absolute;
            top: 0;
            left: 0;
            right: 0;
            bottom: 0;
            background-image: url("data:image/svg+xml,%3Csvg xmlns='http://www.w3.org/2000/svg' width='150' height='150' viewBox='0 0 200 200'%3E%3Cpath fill='%2342a5f5' fill-opacity='0.2' d='M42.7,57.3l-5.4-5.4c-0.8-0.8-0.8-2,0-2.8l5.4-5.4c0.8-0.8,2-0.8,2.8,0l5.4,5.4c0.8,0.8,0.8,2,0,2.8l-5.4,5.4C44.7,58.1,43.5,58.1,42.7,57.3z M30,57.3l-5.4-5.4c-0.8-0.8-0.8-2,0-2.8l5.4-5.4c0.8-0.8,2-0.8,2.8,0l5.4,5.4c0.8,0.8,0.8,2,0,2.8l-5.4,5.4C32,58.1,30.8,58.1,30,57.3z M17.3,57.3l-5.4-5.4c-0.8-0.8-0.8-2,0-2.8l5.4-5.4c0.8-0.8,2-0.8,2.8,0l5.4,5.4c0.8,0.8,0.8,2,0,2.8l-5.4,5.4C19.3,58.1,18.1,58.1,17.3,57.3z'%3E%3C/path%3E%3Cpath fill='%2342a5f5' fill-opacity='0.2' d='M182.7,142.7l-5.4-5.4c-0.8-0.8-0.8-2,0-2.8l5.4-5.4c0.8-0.8,2-0.8,2.8,0l5.4,5.4c0.8,0.8,0.8,2,0,2.8l-5.4,5.4C184.7,143.5,183.5,143.5,182.7,142.7z M170,142.7l-5.4-5.4c-0.8-0.8-0.8-2,0-2.8l5.4-5.4c0.8-0.8,2-0.8,2.8,0l5.4,5.4c0.8,0.8,0.8,2,0,2.8l-5.4,5.4C172,143.5,170.8,143.5,170,142.7z M157.3,142.7l-5.4-5.4c-0.8-0.8-0.8-2,0-2.8l5.4-5.4c0.8-0.8,2-0.8,2.8,0l5.4,5.4c0.8,0.8,0.8,2,0,2.8l-5.4,5.4C159.3,143.5,158.1,143.5,157.3,142.7z'%3E%3C/path%3E%3Cpath fill='%2342a5f5' fill-opacity='0.2' d='M100,15c-5.5,0-10,4.5-10,10s4.5,10,10,10s10-4.5,10-10S105.5,15,100,15z M100,125c-5.5,0-10,4.5-10,10s4.5,10,10,10s10-4.5,10-10S105.5,125,100,125z'%3E%3C/path%3E%3C/svg%3E");
            z-index: -1;
        }

        .signature-demandeur {
            position: absolute;
            top: 270px;
            left: 30px;
            width: 40mm;
            text-align: left;
            display: flex;
            align-items: center;
            gap: 5px;
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
            border: 2px solid #42a5f5;
            border-radius: 4px;
            box-shadow: 0 2px 5px rgba(0, 0, 0, 0.2);
        }

        .id-details-front {
            margin-left: 175px;
        }

        .id-details-back {
            margin-left: 130px;
        }

        h5 {
            margin: 0 0;
            color: #0d47a1;
            font-weight: bold;
            line-height: 1.6;
            text-shadow: 1px 1px 2px rgba(255, 255, 255, 0.7);
        }

        p {
            font-size: 13px;
            margin: 0 0;
            color: #0d47a1;
            font-weight: bold;
        }

        .id-details-front p {
            line-height: 2.3;
        }

        li {
            font-size: 10px;
            color: #0d47a1;
            font-weight: bold;
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
            
            body {
                background: white;
                padding: 0;
            }
            
            .id-card {
                box-shadow: none;
                border: 1px solid #ccc;
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
        
        .card-title {
            background: linear-gradient(to right, #1976d2, #2196f3);
            color: white;
            padding: 8px 15px;
            border-radius: 6px;
            text-align: center;
            margin-top: 10px;
            box-shadow: 0 2px 4px rgba(0, 0, 0, 0.2);
        }
        
        .clouds {
            position: absolute;
            width: 100%;
            height: 100%;
            top: 0;
            left: 0;
            z-index: -1;
            overflow: hidden;
        }
        
        .cloud {
            position: absolute;
            background: rgba(255, 255, 255, 0.7);
            border-radius: 50%;
            box-shadow: 0 0 20px 8px white;
        }
        
        .cloud1 {
            width: 80px;
            height: 30px;
            top: 20px;
            right: 40px;
        }
        
        .cloud2 {
            width: 60px;
            height: 25px;
            bottom: 50px;
            left: 30px;
        }
        
        .airplane {
            position: absolute;
            font-size: 30px;
            top: 40px;
            right: 30px;
            transform: rotate(30deg);
            color: #0d47a1;
            opacity: 0.7;
        }
        
        .airplane-back {
            position: absolute;
            font-size: 25px;
            bottom: 60px;
            left: 30px;
            transform: rotate(-20deg);
            color: #0d47a1;
            opacity: 0.7;
        }
    </style>
</head>

<body>
    <div class="container mt-3 d-flex justify-content-center flex-column align-items-center">
        <div class="button-group">
            <button id="printID" class="btn btn-primary">Print ID</button>
            
        </div>
        <div id="idCardContainer">
            
            <!-- Front Side -->
            <div class="id-card" id="frontSide">
                    <div
                style="display: flex; align-items: center; border-bottom: 1px solid #000; padding-bottom: 5px; margin-bottom: 10px; direction: rtl;">
                <div style="flex: 1; text-align: right; font-size: 9px; font-weight: bold; padding-left: 10px; word-spacing: 2px; line-height: 1.2;"
                    class="arabic">
                    الجمهورية الإسلامية الموريتانية<br>
                    وزارة التجهيز والنقل<br>
                    الوكالة الوطنية للطيران المدني
                </div>
                <div style="flex: 0 0 auto; margin: 0 1px;">
                    <img src="{{ asset('assets/admin/imgs/logo.png') }}" alt="Logo ANAC" class="logo" height="50px" width="50px">
                </div>
                <div style="flex: 1; text-align: left; font-size: 9px; font-weight: bold; padding-right: 10px; direction: ltr; line-height: 1.2;"
                    class="french">
                    République Islamique de Mauritanie<br>
                    Ministère de l'Equipement et des Transports<br>
                    AGENCE NATIONALE DE L'AVIATION CIVILE
                </div>
            </div>
                <div class="clouds">
                    <div class="cloud cloud1"></div>
                    <div class="cloud cloud2"></div>
                </div>
                <div class="airplane">✈</div>
                
                <h5 class="card-title">
                    Trainee Card
                </h5>
                <img style="margin-top: 60px;" src="{{ asset('/uploads/' . $carte_stagiare->photo) }}" alt="Profile Photo"
                    class="photo">
                <p class="signature-demandeur">
                    VII <img src="{{ asset('/uploads/' . $carte_stagiare->signature) }}" width="120" height="80">
                </p>

                <!-- Placeholder for profile photo -->
                <div class="id-details-front">
                    <div style="display: flex; gap: 100px;">
                        <p>II {{ $carte_stagiare->numero_carte }}</p>
                    </div>
                    <p>III {{ $carte_stagiare->np }}</p>
                                        @php
                        use Carbon\Carbon;
                        $date_naissance = Carbon::parse($carte_stagiare->date_naissance);
                        $date_naissance = $date_naissance->format('d-m-Y');

                        $date_deliverance = Carbon::parse($carte_stagiare->date_deliverance);
                        $date_deliverance = $date_deliverance->format('d-m-Y');

                        $date_expiration = Carbon::parse($carte_stagiare->date_expiration);
                        $date_expiration = $date_expiration->format('d-m-Y');

                        $date_mise_a_jour = Carbon::parse($carte_stagiare->date_mise_a_jour);
                        $date_mise_a_jour = $date_mise_a_jour->format('d-m-Y');
                    @endphp
                    <div style="display: flex; gap: 45px;">
                        <p>IV {{ $date_naissance }}</p>
                        <p>VI {{ strtoupper($carte_stagiare->nationalite) }}</p>
                    </div>
                    <p>V {{ $carte_stagiare->adresse }}</p>
                </div>
            </div>

            <!-- Back Side -->
            <div class="id-card" id="backSide">
                <div class="clouds">
                    <div class="cloud cloud1"></div>
                    <div class="cloud cloud2"></div>
                </div>
                <div class="airplane-back">✈</div>
                
                <div class="id-details-back" id="dynamicBackContent">
                    
                    <p>IX First issue date {{ $date_deliverance }} <img src="{{ asset('/uploads/' . $carte_stagiare->signature_dg) }}"
                            width="100" height="50"></p>
                    
                    <p>X <img src="{{ asset('/uploads/' . $carte_stagiare->cachet) }}" width="100" height="110"
                            style="margin-right: 40px">

                        XI {!! QrCode::size(100)->errorCorrection('H')->margin(0)->encoding('UTF-8')->generate(
                                json_encode([

                                    $carte_stagiare->numero_carte,
                                    $carte_stagiare->np,
                                    $date_expiration,
                                ]),
                            ) !!}
                    </p>
                    <p>XIV Trainee card expiry date : {{ $date_expiration }}</p>
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