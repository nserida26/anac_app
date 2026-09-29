<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>{{ $subjectLine }}</title>
    <style>
        * { margin: 0; padding: 0; box-sizing: border-box; }
        body {
            font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
            line-height: 1.6;
            color: #333;
            background-color: #f4f7fc;
        }
        .email-container {
            max-width: 600px;
            margin: 20px auto;
            background: #ffffff;
            border-radius: 12px;
            overflow: hidden;
            box-shadow: 0 10px 30px rgba(0, 0, 0, 0.1);
            border: 1px solid #e0e7ff;
        }
        .email-header {
            background: linear-gradient(135deg, #1e3c72 0%, #2a5298 100%);
            color: white;
            padding: 24px 30px;
        }
        .email-header h1 { font-size: 18px; margin: 0; }
        .email-header p { font-size: 13px; opacity: 0.85; margin-top: 4px; }
        .email-body { padding: 30px; font-size: 15px; white-space: pre-line; }
        .email-footer {
            padding: 16px 30px;
            font-size: 12px;
            color: #888;
            border-top: 1px solid #eee;
        }
    </style>
</head>
<body>
    <div class="email-container">
        <div class="email-header">
            <h1>Agence Nationale de l'Aviation Civile</h1>
            <p>Direction du Transport Aérien</p>
        </div>
        <div class="email-body">
            {{-- Le texte source contient des *mots* en gras (convention WhatsApp) : on les
                 convertit en HTML après avoir échappé le reste pour éviter toute injection. --}}
            @php
                $escaped = e($bodyText);
                $formatted = preg_replace('/\*(.+?)\*/', '<strong>$1</strong>', $escaped);
            @endphp
            {!! $formatted !!}
        </div>
        <div class="email-footer">
            Cet e-mail a été envoyé automatiquement par la plateforme ANAC. Vous pouvez désactiver
            ce canal de notification depuis votre profil.
        </div>
    </div>
</body>
</html>
