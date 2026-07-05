<!DOCTYPE html>
<html lang="fr">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Facture {{ $facture->reference }} / {{ date('Y') }}</title>
    <style>
        body {
            font-family: Arial, sans-serif;
            margin: 20px;
            line-height: 1.5;
        }

        /* Header Styles */
        .header {
            text-align: center;
            margin-bottom: 10px;
            border-bottom: 2px solid #000;
            padding-bottom: 20px;
        }

        .arabic {
            font-size: 16px;
            direction: rtl;
            margin-bottom: 5px;
        }

        .french {
            font-size: 14px;
            margin-bottom: 5px;
        }

        .logo {
            max-height: 80px;
            margin: 10px 0;
        }

        /* Invoice Info */
        .invoice-title {
            font-size: 24px;
            font-weight: bold;
            margin: 15px 0;
            text-transform: uppercase;
        }

        .invoice-info {
            display: flex;
            justify-content: space-between;
        }

        /* Table Styles */
        table {
            width: 100%;
            border-collapse: collapse;
            margin-bottom: 10px;
        }

        th,
        td {
            border: 1px solid #ddd;
            padding: 8px;
            text-align: left;
        }

        th {
            background-color: #f2f2f2;
        }

        /* Total and Signatures */
        .total {
            font-weight: bold;
            text-align: right;
            margin-bottom: 10px;
        }

        .signatures {
            display: flex;
            justify-content: space-between;
            margin-top: 50px;
        }

        /* Print Button */
        .print-button {
            display: block;
            margin: 20px auto;
            padding: 10px 20px;
            background-color: #4CAF50;
            color: white;
            border: none;
            border-radius: 4px;
            cursor: pointer;
            font-size: 16px;
        }

        .print-button:hover {
            background-color: #45a049;
        }

        @media print {
            .print-button {
                display: none;
            }
        }
    </style>
</head>

<body>

    <div class="header">
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

        <div class="invoice-title">FACTURE N° {{ $facture->reference }} / {{ date('Y') }}</div>

        <div class="invoice-info">
            <div>
                <p><strong>Date d'émission:</strong> {{ date('d/m/y', strtotime($facture->date_facture)) }} </p>
                <p><strong>Référence:</strong> {{ $facture->demande->code }}/DSV/PEL/{{date('Y')}}</p>
            </div>
            <div>
                <p><strong>Client:</strong>
                    @if (empty($facture->demande->demandeur->compagnie))
                        {{ $facture->demande->demandeur->np }}
                    @else
                        {{ $facture->demande->demandeur->compagnie->nom_entreprise }}
                    @endif
                </p>
            </div>
        </div>
    </div>

    <table>
        <thead>
            <tr>
                <th>Référence</th>
                <th>Désignation</th>
                <th>Qté</th>
                <th>Px unitaire</th>
                <th>Montant</th>
            </tr>
        </thead>
        <tbody>
            <tr>
                <td>{{ $facture->reference }}</td>
                <td>{{ $facture->demande->typeDemande->nom_fr }} {{ $facture->demande->typeLicence->nom }}:
                    {{ strtoupper($facture->demande->demandeur->np) }}
                    N°
                    @if(!empty($facture->demande->licence))
                        {{ $facture->demande->licence->numero_licence }}
                    @else
                        {{ $facture->demande->code }}
                    @endif
                    Le {{ date('d/m/y', strtotime($facture->created_at)) }} </td>
                <td>1</td>
                <td>{{ number_format($facture->montant, 0, ',', ' ') }}</td>
                <td>{{ number_format($facture->montant, 0, ',', ' ') }}</td>
            </tr>
        </tbody>
    </table>

    <div class="total">
        <p><strong>TOTAL A PAYER EN MRU</strong> <strong>{{ number_format($facture->montant, 0, ',', ' ') }}</strong>
        </p>
    </div>

    <p>La présente facture, libellée en Ouguiya MRU, est arrêtée à la somme de :</p>
    <p><strong>{{ strtoupper(App\Helpers\NumberToWords::convertToWords($facture->montant)) }}</strong></p>

    <div class="signatures">
        <div>
            <div style="text-align: right; padding-right: 15px;">
                <div style="margin-bottom: 3px; font-size: 12px;"><strong>Nom du signataire:</strong></div>
                <div style="margin-bottom: 3px; font-weight: bold; font-size: 14px;">Ahmed Ould Abdallah</div>
                <div style="margin-bottom: 5px; font-size: 12px;">Le Directeur Financier</div>
                <img src="{{ asset('/uploads/sc/51OgNTsTuNZwG2Sd59chRcJsTlbRPD5lhETUhkAA.png') }}"
                    style="width: 100px; height: 110px;" class="signature-img">
            </div>
        </div>
        <div style="position: relative; width: 100%; margin-top: 20px;">
            <div style="display: flex; justify-content: flex-end; position: relative;">
                <div style="position: absolute; left: 15%; bottom: 0;">
                    <img src="{{ asset('/uploads/' . $dg->cachet->cachet) }}" style="width: 110px; opacity: 0.9;"
                        class="cachet-img">
                </div>
                <div style="text-align: right; padding-right: 15px;">
                    <div style="margin-bottom: 3px; font-size: 12px;"><strong>Nom du signataire:</strong></div>
                    <div style="margin-bottom: 3px; font-weight: bold; font-size: 14px;">N'GADE ABDOULAYE ABASSE</div>
                    <div style="margin-bottom: 5px; font-size: 12px;">Directeur général</div>
                    <img src="{{ asset('/uploads/' . $dg->signature->signature) }}"
                        style="width: 100px; height: 110px;" class="signature-img">
                </div>
            </div>
        </div>
    </div>

    <button class="print-button" onclick="window.print()">Imprimer la Facture</button>
</body>

</html>
