<!DOCTYPE html>
<html lang="fr">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>ORDRE {{ $ordre->reference }} / {{ date('Y') }}</title>
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

        .document-footer {
            border-top: 2px solid #2c3e50;
            padding-top: 30px;
            margin-top: 30px;
        }
        .signature-block {
            display: flex;
            justify-content: space-between;
            flex-wrap: wrap;
        }
        
        .signature {
            width: 48%;
            min-width: 300px;
            margin-bottom: 30px;
            position: relative;
        }
        
        .signature-content {
            text-align: center;
            padding: 15px;
            border: 1px solid #e0e0e0;
            border-radius: 5px;
            background: #f9f9f9;
        }
        
        .signatory-name {
            font-weight: bold;
            font-size: 16px;
            margin: 10px 0 5px;
            color: #2c3e50;
        }
        
        .signatory-title {
            font-size: 14px;
            color: #7f8c8d;
            margin-bottom: 15px;
        }
        
        .signature-line {
            width: 200px;
            height: 1px;
            background-color: #333;
            margin: 20px auto;
        }
        
        .signature-image {
            max-width: 150px;
            max-height: 80px;
            margin: 10px auto;
            display: block;
        }
        
        .stamp {
            position: absolute;
            top: 10px;
            right: 10px;
            opacity: 0.85;
            max-width: 100px;
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

        <div class="invoice-title">ORDRE N° {{ $ordre->reference }} / {{ date('Y') }}</div>

        <div class="invoice-info">
            <div>
                <p><strong>Date d'émission:</strong> {{ date('d/m/y', strtotime($ordre->date_ordre)) }} </p>
                <p><strong>Référence:</strong> {{ $ordre->demande->code }}/DSV/PEL/{{date('Y')}}</p>
            </div>
            <div>
                <p><strong>Client:</strong>
                    @if (empty($ordre->demande->demandeur->compagnie))
                        {{ $ordre->demande->demandeur->np }}
                    @else
                        {{ $ordre->demande->demandeur->compagnie->nom_entreprise }}
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
                <td>{{ $ordre->reference }}</td>
                <td>{{ $ordre->demande->typeDemande->nom_fr }} {{ $ordre->demande->typeLicence->nom }}:
                    {{ strtoupper($ordre->demande->demandeur->np) }}
                    N°
                    @if(!empty($ordre->demande->licence))
                        {{ $ordre->demande->licence->numero_licence }}
                    @else
                        {{ $ordre->demande->code }}
                    @endif
                    Le {{ date('d/m/y', strtotime($ordre->created_at)) }} </td>
                <td>1</td>
                <td>{{ number_format($ordre->montant, 0, ',', ' ') }}</td>
                <td>{{ number_format($ordre->montant, 0, ',', ' ') }}</td>
            </tr>
        </tbody>
    </table>

    <div class="total">
        <p><strong>TOTAL A PAYER EN MRU</strong> <strong>{{ number_format($ordre->montant, 0, ',', ' ') }}</strong>
        </p>
    </div>

    <p>La présente facture, libellée en Ouguiya MRU, est arrêtée à la somme de :</p>
    <p><strong>{{ strtoupper(App\Helpers\NumberToWords::convertToWords($ordre->montant)) }}</strong></p>

    <div class="document-footer">
                <div class="signature-block">
                    <!-- First Signatory -->
                    <div class="signature">
                        <div class="signature-content">
                            <div class="signatory-name">
                                @if(!empty($dsv->signature->nom ))
                                    {{ $dsv->signature->nom }}
                                @endif
                                
                                </div>
                            <div class="signatory-title">Le Directeur</div>
                            <div class="signature-line"></div>
                            <img src="{{ asset('/uploads/' . $dsv->signature->signature) }}" alt="Signature" class="signature-image">
                            <img src="{{ asset('/uploads/' . $dsv->cachet->cachet) }}" alt="Cachet officiel" class="stamp">
                        </div>
                    </div>
                    
                    <!-- Second Signatory -->
                    <div class="signature">
                        <div class="signature-content">
                            <div class="signatory-name">                                
                                @if(!empty($pel->signature->nom ))
                                    {{ $pel->signature->nom }}
                                @endif</div>
                            <div class="signatory-title">Chef Service PEL</div>
                            <div class="signature-line"></div>
                            <img src="{{ asset('/uploads/' . $pel->signature->signature) }}" alt="Signature" class="signature-image">
                            <img src="{{ asset('/uploads/' . $pel->cachet->cachet) }}" alt="Cachet officiel" class="stamp">
                        </div>
                    </div>
                </div>
    </div>

    <button class="print-button" onclick="window.print()">Imprimer l'ordre</button>
</body>

</html>
