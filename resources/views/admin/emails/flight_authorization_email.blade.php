<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Autorisation - {{ $autorisation->code_autorisation }}</title>
    <style>
        /* Styles généraux */
        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
        }
        
        body {
            font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
            line-height: 1.6;
            color: #333;
            background-color: #f4f7fc;
            padding: 0;
            margin: 0;
        }
        
        /* Conteneur principal */
        .email-container {
            max-width: 650px;
            margin: 20px auto;
            background: #ffffff;
            border-radius: 12px;
            overflow: hidden;
            box-shadow: 0 10px 30px rgba(0, 0, 0, 0.1);
            border: 1px solid #e0e7ff;
        }
        
        /* En-tête */
        .email-header {
            background: linear-gradient(135deg, #1e3c72 0%, #2a5298 100%);
            color: white;
            padding: 30px;
            text-align: center;
            border-bottom: 4px solid #ffc107;
        }
        
        .logo {
            margin-bottom: 20px;
        }
        
        .logo img {
            max-width: 120px;
            height: auto;
        }
        
        .email-header h1 {
            font-size: 24px;
            margin-bottom: 10px;
            font-weight: 600;
        }
        
        .email-header p {
            font-size: 14px;
            opacity: 0.9;
            margin-top: 10px;
        }
        
        /* Corps du message */
        .email-body {
            padding: 40px 35px;
            background: #ffffff;
        }
        
        /* Message de bienvenue */
        .greeting {
            font-size: 18px;
            margin-bottom: 25px;
            color: #1e3c72;
            font-weight: 500;
        }
        
        /* Message principal */
        .message-content {
            margin-bottom: 30px;
            color: #555;
            line-height: 1.8;
        }
        
        /* Carte d'informations */
        .info-card {
            background: #f8f9fa;
            border-left: 4px solid #ffc107;
            border-radius: 8px;
            padding: 20px;
            margin: 25px 0;
            box-shadow: 0 2px 4px rgba(0, 0, 0, 0.05);
        }
        
        .info-item {
            margin-bottom: 12px;
            display: flex;
            align-items: flex-start;
        }
        
        .info-label {
            font-weight: 700;
            min-width: 140px;
            color: #1e3c72;
            font-size: 14px;
        }
        
        .info-value {
            color: #555;
            font-size: 14px;
            flex: 1;
        }
        
        .info-value strong {
            color: #2a5298;
            font-weight: 600;
        }
        
        /* Bouton d'accès direct */
        .access-button {
            text-align: center;
            margin: 30px 0;
        }
        
        .btn-primary {
            display: inline-block;
            background: linear-gradient(135deg, #ffc107 0%, #ffb300 100%);
            color: #1e3c72;
            text-decoration: none;
            padding: 12px 30px;
            border-radius: 50px;
            font-weight: 600;
            font-size: 16px;
            transition: all 0.3s ease;
            box-shadow: 0 4px 15px rgba(255, 193, 7, 0.3);
            border: none;
            cursor: pointer;
        }
        
        .btn-primary:hover {
            transform: translateY(-2px);
            box-shadow: 0 6px 20px rgba(255, 193, 7, 0.4);
            background: linear-gradient(135deg, #ffb300 0%, #ffa000 100%);
            color: #1e3c72;
        }
        
        /* Pied de page */
        .email-footer {
            background: #f8f9fa;
            padding: 25px 35px;
            text-align: center;
            border-top: 1px solid #e0e7ff;
            font-size: 12px;
            color: #777;
        }
        
        .footer-links {
            margin-bottom: 15px;
        }
        
        .footer-links a {
            color: #1e3c72;
            text-decoration: none;
            margin: 0 10px;
            font-size: 12px;
        }
        
        .footer-links a:hover {
            text-decoration: underline;
            color: #ffc107;
        }
        
        .signature {
            margin-top: 20px;
            padding-top: 20px;
            border-top: 1px solid #e0e7ff;
            font-size: 13px;
            color: #555;
        }
        
        .organization-name {
            font-weight: 700;
            color: #1e3c72;
            margin-bottom: 5px;
        }
        
        .contact-info {
            margin-top: 10px;
            font-size: 11px;
            color: #999;
        }
        
        /* Badges et étiquettes */
        .badge {
            display: inline-block;
            padding: 4px 12px;
            border-radius: 20px;
            font-size: 12px;
            font-weight: 600;
            margin-left: 8px;
        }
        
        .badge-success {
            background: #d4edda;
            color: #155724;
        }
        
        .badge-warning {
            background: #fff3cd;
            color: #856404;
        }
        
        /* Responsive */
        @media only screen and (max-width: 600px) {
            .email-container {
                margin: 10px;
                border-radius: 8px;
            }
            
            .email-header {
                padding: 20px;
            }
            
            .email-body {
                padding: 25px 20px;
            }
            
            .info-item {
                flex-direction: column;
            }
            
            .info-label {
                margin-bottom: 5px;
            }
            
            .btn-primary {
                padding: 10px 20px;
                font-size: 14px;
            }
            
            .footer-links a {
                display: inline-block;
                margin: 5px 8px;
            }
        }
        
        /* Impression */
        @media print {
            body {
                background: white;
                padding: 0;
                margin: 0;
            }
            
            .email-container {
                box-shadow: none;
                margin: 0;
                border: none;
            }
            
            .btn-primary {
                display: none;
            }
            
            .email-footer {
                page-break-inside: avoid;
            }
        }
        
        /* Animation */
        @keyframes fadeIn {
            from {
                opacity: 0;
                transform: translateY(20px);
            }
            to {
                opacity: 1;
                transform: translateY(0);
            }
        }
        
        .email-container {
            animation: fadeIn 0.5s ease-out;
        }
    </style>
</head>
<body>
    <div class="email-container">
        <!-- En-tête -->
        <div class="email-header">
            <div class="logo">
                <!-- Remplacez par votre logo réel -->
                <svg width="80" height="80" viewBox="0 0 100 100" fill="none" xmlns="http://www.w3.org/2000/svg">
                    <circle cx="50" cy="50" r="45" stroke="white" stroke-width="2" fill="none"/>
                    <path d="M50 20 L70 40 L50 60 L30 40 L50 20 Z" fill="white" opacity="0.9"/>
                    <rect x="45" y="55" width="10" height="25" fill="white" opacity="0.9"/>
                </svg>
            </div>
            <h1>Autorisation</h1>
            <p>Document officiel d'autorisation délivré par l'ANAC</p>
        </div>
        
        <!-- Corps -->
        <div class="email-body">
            <div class="greeting">
                Monsieur Le directeur,
            </div>
            
            <div class="message-content">
                <p>Nous avons l'honneur de vous adresser, pour exécution, l'autorisation de vol approuvée relative à la demande susvisée.</p>
                
                <p>Nous vous demandons de bien vouloir prendre toutes les dispositions nécessaires à son application, conformément aux textes en vigueur.</p>
                
                <p>Les détails de cette autorisation figurent ci-dessous. Nous vous prions d’en assurer la mise en œuvre et le suivi.</p>
            </div>

            
            <!-- Carte d'informations -->
            <div class="info-card">
                <div class="info-item">
                    <div class="info-label">📄 Numéro d'autorisation :</div>
                    <div class="info-value">
                        <strong>{{ $autorisation->code_autorisation }}</strong>
                        <span class="badge badge-success">Validé</span>
                    </div>
                </div>
                
                <div class="info-item">
                    <div class="info-label">✈️ Type d'autorisation :</div>
                    <div class="info-value">{{ $autorisation->demande->type->libelle }}</div>
                </div>
                
                <div class="info-item">
                    <div class="info-label">📅 Date d'émission :</div>
                    <div class="info-value">{{ now()->format('d/m/Y') }}</div>
                </div>
                
                <div class="info-item">
                    <div class="info-label">📆 Période de validité :</div>
                    <div class="info-value">Du {{ \Carbon\Carbon::parse($autorisation->demande->date_debut)->format('d/m/Y') }} au {{ \Carbon\Carbon::parse($autorisation->demande->date_fin)->format('d/m/Y') }}</div>
                </div>
                
                @if($autorisation->demande->sous_validite)
                <div class="info-item">
                    <div class="info-label">⏰ Sous-validité :</div>
                    <div class="info-value">{{ $autorisation->demande->sous_validite }} heures</div>
                </div>
                @endif
            </div>
            
            <!-- Bouton d'accès direct -->
            <div class="access-button">
                <a href="{{ route('public.autorisations.print', $autorisation) }}" class="btn-primary" target="_blank">
                    📄 Voir l'autorisation complète
                </a>
            </div>
            
            <!-- Note importante -->
            <div style="background: #e8f4fd; border-left: 3px solid #1e3c72; padding: 15px; border-radius: 6px; margin: 20px 0;">
                <p style="margin: 0; font-size: 13px; color: #1e3c72;">
                    <strong>ℹ️ Note importante :</strong><br>
                    La présente autorisation est délivrée pour exécution conformément aux dispositions réglementaires en vigueur. 
                    Il vous appartient de veiller à son application stricte et de vous assurer que les opérations de vol s’effectuent dans le respect des conditions qui y sont stipulées.
                    
                    <br><br>
                    Toute anomalie, incident ou non-conformité constaté(e) devra être signalé(e) sans délai à nos services.
                </p>
            </div>
            
            <div class="message-content">
                <p>Pour toute question relative à l’exécution de cette autorisation, nous vous invitons à prendre attache avec nos services aux coordonnées indiquées ci-dessous.</p>
                
                <p>Nous vous remercions de votre collaboration et vous prions d’agréer l’expression de notre considération distinguée.</p>
            </div>

        </div>
        
        <!-- Pied de page -->
        <div class="email-footer">
            
            <div class="signature">
                <div class="organization-name">AGENCE NATIONALE DE L'AVIATION CIVILE (ANAC)</div>
                <div>Direction de Transport Aérienne</div>
                <div class="contact-info">
                    <div>📍 Adresse : 01 BP 1234 Nouakchott 01, Mauritanie</div>
                    <div>📞 Tél : +225 27 20 30 40 50 | 📧 Email : autorisations@anac.mr</div>
                    <div>🌐 Web : www.anac.mr</div>
                </div>
                <div style="margin-top: 15px; font-size: 11px;">
                    Ce message est généré automatiquement, merci de ne pas y répondre directement.
                </div>
            </div>
        </div>
    </div>
</body>
</html>