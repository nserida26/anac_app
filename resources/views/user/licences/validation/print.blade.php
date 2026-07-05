<!DOCTYPE html>
<html lang="fr">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Validation de Licence Étrangère</title>
    <style>
        body {
            font-family: Arial, sans-serif;
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
        <p><strong>Titulaire de la licence :</strong> {{ $validation->demande->demandeur->nationalite }}</p>
        <p><strong>de Pilote :</strong> {{ $validation->demande->typeLicence->nom }}<br>
            <strong>N° :</strong> {{ $validation->demande->licences->first()->num_licence }}
        </p>
        <p><strong>Délivrée le :</strong>
            {{ date('d/m/Y', strtotime($validation->demande->licences->first()->date_licence)) }}
            <br>
            <strong>Au :</strong> {{ $validation->demande->licences->first()->lieu_delivrance }}
        </p>
        <p>Est autorisé à piloter l'appareil de type Embraer ERJ-145 immatriculé 5T-CLD
            @isset($validation->demande->demandeur->compagnie)
                exploité commercialement par la
                compagnie {{ $validation->demande->demandeur->compagnie->nom_entreprise ?? ' - ' }},
            @endisset
            dans les limites fixées
            par sa
            propre licence et les qualifications qui y
            sont attachées.
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
            D.S.V.</p>
        <img src="{{ asset('/uploads/' . $validation->signature_path) }}" style="width: 100px; height: 110px;"
            class="signature-img">
        <img src="{{ asset('/uploads/' . $validation->cachet_path) }}" style="width: 110px; opacity: 0.9;"
            class="cachet-img">
    </div>
</body>

</html>
