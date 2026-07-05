<!DOCTYPE html>
<html lang="fr">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Validation de Licence Étrangère</title>
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
        @font-face {
            font-family: 'Louguiya';
            src: url('/assets/admin/fonts/Louguiya/Louguiya Bold.ttf') format('ttf'),
                url('/assets/admin/fonts/Louguiya/Louguiya Bold.ttf') format('ttf');
            font-weight: normal;
            font-style: normal;
            font-display: swap;
        }

                body {
            font-family: 'LouguiyaFR', sans-serif;
                        margin: 0;
            padding: 20px;
        }

        .header {
            text-align: center;
            margin-bottom: 20px;
        }

        .ministry {
            font-weight: bold;
            margin-bottom: 5px;
        }

        .agency {
            font-weight: bold;
            margin-bottom: 15px;
        }

        .direction {
            text-align: center;
            margin-bottom: 20px;
            border-bottom: 1px solid #000;
            padding-bottom: 10px;
        }

        .bilingual-text {
            display: flex;
            justify-content: space-between;
            margin-bottom: 10px;
        }

        .arabic {
            text-align: left;
            font-size: 18px;
            font-family: 'Louguiya', sans-serif;
        }

        .french {
            text-align: right;
            font-style: italic;
        }

        .title {
            text-align: center;
            font-size: 18px;
            font-weight: bold;
            margin-bottom: 30px;
            text-decoration: underline;
        }

        .license-info {
            margin-bottom: 30px;
        }

        .license-info p {
            margin: 5px 0;
        }

        .validity {
            margin-bottom: 20px;
        }

        .signature {
            margin-top: 50px;
            text-align: right;
        }

        .footer {
            margin-top: 50px;
            font-size: 12px;
            text-align: center;
        }

        .iso {
            text-align: center;
            margin-top: 20px;
            font-style: italic;
        }

        .contact {
            text-align: center;
            margin-top: 30px;
            font-size: 12px;
        }

        .signature-img {
            height: 35px !important;
            width: auto !important;
        }

        .cachet-img {
            width: 80px !important;
        }
    </style>
</head>

<body>
    <div
        style="display: flex; align-items: center; border-bottom: 1px solid #000; padding-bottom: 5px; margin-bottom: 10px; direction: rtl;">
        <div style="flex: 1; text-align: right; font-size: 20px; font-weight: bold; padding-left: 10px; word-spacing: 2px; line-height: 1.2;"
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


    <div class="direction">
        <strong>Direction de la Sécurité des Vols</strong>
    </div>

    <div class="bilingual-text">
        <div class="arabic"> {{ $validation->numero_validation }} : رقم</div>
        <div class="french">Numéro : {{ $validation->numero_validation }} </div>
    </div>
    <div class="bilingual-text">
        <div class="arabic"> {{ date('y/m/d') }} انواكشوط في</div>
        <div class="french">Nouakchott, le {{ date('d/m/y') }}</div>
    </div>
    <div class="title">
        VALIDATION DE LICENCE ETRANGERE
    </div>

    <div class="license-info">
        <p><strong>Monsieur :</strong> {{ $validation->demande->demandeur->np }}</p>
        <p><strong>Titulaire de la licence :</strong> {{ strtoupper($validation->demande->demandeur->nationalite) }}</p>
        <p><strong>de @if(in_array($validation->demande->typeLicence->id,[27,28,29,30,31,32])) Pilote @endif :</strong> {{ $validation->demande->typeLicence->nom }}<br>
            <strong>N° :</strong> {{ $validation->num_licence }}
        </p>
        <p><strong>Délivrée le :</strong>
            {{ date('d/m/Y', strtotime($validation->date_delivrance_licence)) }}
            <br>
            <strong>Au :</strong> {{ strtoupper($validation->lieu_delivrance_licence) }}
        </p>
        <p>
            

            @if(isset($qualification_types) && !empty($qualification_types) && $qualification_types->isNotEmpty())
                @if(in_array($validation->demande->typeLicence->id,[27,28,29,30,31,32])) Est autorisé à piloter l'appareil de type :  @endif
                @if(in_array($validation->demande->typeLicence->id,[37,38])) Est autorisé à effectuer les traveaux de maintenance des appareils de type :  @endif
                @if(in_array($validation->demande->typeLicence->id,[39])) Est autorisé à exercer les fonctions de Personnel Navigant de Cabine à bord de l'avion de type :  @endif
                
                @foreach($qualification_types as $qualification_type)
                    
                    <strong>{{$qualification_type->code}}</strong>
                @endforeach
                immatriculé(s) <strong> 5T- </strong>
                             @isset($validation->demande->demandeur->compagnie)
                exploité commercialement par la
                compagnie {{ $validation->demande->demandeur->compagnie->nom_entreprise ?? ' - ' }},
                @endisset
                dans les limites fixées
                par sa
                propre licence et les qualifications qui y
                sont attachées.
            @endif
            

        </p>
    </div>

    <div class="validity">
        <p><strong>Valable du :</strong> {{ date('d/m/Y', strtotime($validation->date_debut_validite)) }}
            <strong>Au :</strong> {{ date('d/m/Y', strtotime($validation->date_fin_validite)) }}
        </p>
        <p>La présente validation n'est valable qu'utilisée conjointement avec la licence en cause et en état de
            validité.</p>
        <p><strong>Fait à Nouakchott :</strong> le {{ date('d/m/Y', strtotime($validation->date_debut_validite)) }}</p>
    </div>

    <div class="signature">
        <p>
            {{ $validation->signataire_nom }}</p>
    </div>

    <div class="signature">
        <p><strong>{{ $validation->signataire_titre }}</strong><br>
        </p>
        <img src="{{ asset('/uploads/' . $validation->signature_path) }}" style="width: 100px; height: 110px;"
            class="signature-img">
        <img src="{{ asset('/uploads/' . $validation->cachet_path) }}" 
            class="cachet-img">
    </div>
</body>

</html>
