<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml">
<head>
    <meta name="viewport" content="width=device-width, initial-scale=1.0" />
    <meta http-equiv="Content-Type" content="text/html; charset=UTF-8" />
    <meta name="color-scheme" content="light">
    <meta name="supported-color-schemes" content="light">
    <title>{{ config('app.name') }}</title>
    <style>
        /* ── ANAC Email Styles ── */
        :root {
            --anac-primary: #0d2137;
            --anac-primary-light: #1a3a5c;
            --anac-accent: #c8a951;
            --anac-white: #ffffff;
            --anac-gray-50: #f8f9fa;
            --anac-gray-100: #f1f3f5;
            --anac-gray-200: #e9ecef;
            --anac-gray-400: #ced4da;
            --anac-gray-600: #6c757d;
            --anac-gray-800: #343a40;
        }

        body {
            margin: 0;
            padding: 0;
            background-color: var(--anac-gray-100);
            font-family: 'Source Sans Pro', -apple-system, BlinkMacSystemFont, 'Segoe UI', Roboto, Helvetica, Arial, sans-serif;
            -webkit-font-smoothing: antialiased;
            -moz-osx-font-smoothing: grayscale;
        }

        .wrapper {
            width: 100%;
            background-color: var(--anac-gray-100);
            padding: 40px 0;
        }

        .content {
            max-width: 600px;
            margin: 0 auto;
        }

        .inner-body {
            width: 100%;
            background-color: var(--anac-white);
            border-radius: 12px;
            overflow: hidden;
            box-shadow: 0 4px 20px rgba(0, 0, 0, 0.08);
        }

        .email-brand-bar {
            background: linear-gradient(135deg, var(--anac-primary) 0%, var(--anac-primary-light) 100%);
            padding: 24px 40px;
            text-align: center;
        }

        .email-brand-bar .brand-name {
            font-size: 18px;
            font-weight: 700;
            color: var(--anac-white);
            letter-spacing: 2px;
            text-transform: uppercase;
            margin: 0;
            line-height: 1.4;
        }

        .content-cell {
            padding: 40px;
        }

        .email-title {
            font-size: 24px;
            font-weight: 700;
            color: var(--anac-primary);
            margin: 0 0 12px 0;
            line-height: 1.3;
        }

        .email-subtitle {
            font-size: 15px;
            color: var(--anac-gray-600);
            margin: 0 0 28px 0;
            line-height: 1.6;
        }

        .email-text {
            font-size: 15px;
            color: var(--anac-gray-800);
            line-height: 1.7;
            margin: 0 0 16px 0;
        }

        .email-divider {
            border: none;
            border-top: 1px solid var(--anac-gray-200);
            margin: 28px 0;
        }

        /* Button */
        .email-btn {
            display: inline-block;
            background: var(--anac-primary);
            color: var(--anac-white) !important;
            text-decoration: none;
            padding: 14px 36px;
            border-radius: 8px;
            font-size: 15px;
            font-weight: 600;
            letter-spacing: 0.3px;
            transition: background 0.2s;
        }

        .email-btn:hover {
            background: var(--anac-primary-light);
        }

        .email-btn--accent {
            background: var(--anac-accent);
        }

        .email-btn--accent:hover {
            background: #b8993d;
        }

        /* Footer */
        .email-footer {
            background-color: var(--anac-gray-50);
            padding: 28px 40px;
            text-align: center;
            border-top: 1px solid var(--anac-gray-200);
        }

        .email-footer p {
            font-size: 12px;
            color: var(--anac-gray-600);
            margin: 0 0 4px 0;
            line-height: 1.5;
        }

        .email-footer a {
            color: var(--anac-primary);
            text-decoration: none;
        }

        .email-footer .footer-links {
            margin-top: 12px;
        }

        .email-footer .footer-links a {
            font-size: 12px;
            color: var(--anac-gray-600);
            text-decoration: none;
            margin: 0 8px;
        }

        .email-footer .footer-links a:hover {
            color: var(--anac-accent);
        }

        /* Panel */
        .email-panel {
            background: var(--anac-gray-50);
            border: 1px solid var(--anac-gray-200);
            border-radius: 8px;
            padding: 20px 24px;
            margin: 24px 0;
        }

        .email-panel p {
            font-size: 13px;
            color: var(--anac-gray-600);
            margin: 0;
            line-height: 1.6;
        }

        /* Subcopy (fallback URL) */
        .email-subcopy {
            margin-top: 24px;
            padding-top: 24px;
            border-top: 1px solid var(--anac-gray-200);
        }

        .email-subcopy p {
            font-size: 13px;
            color: var(--anac-gray-600);
            line-height: 1.6;
        }

        .email-subcopy .break-all {
            word-break: break-all;
            color: var(--anac-primary);
        }

        /* Responsive */
        @media only screen and (max-width: 600px) {
            .wrapper { padding: 20px 0; }
            .content-cell { padding: 28px 24px; }
            .email-brand-bar { padding: 24px 20px; }
            .email-footer { padding: 20px 24px; }
            .inner-body { border-radius: 8px; }
            .email-title { font-size: 20px; }
            .email-btn { display: block !important; text-align: center !important; }
        }
    </style>
</head>
<body>
    <table class="wrapper" width="100%" cellpadding="0" cellspacing="0" role="presentation">
        <tr>
            <td align="center">
                <table class="content" width="100%" cellpadding="0" cellspacing="0" role="presentation">
                    {{ $header ?? '' }}

                    <!-- Email Body -->
                    <tr>
                        <td class="body" width="100%" cellpadding="0" cellspacing="0">
                            <table class="inner-body" align="center" width="100%" cellpadding="0" cellspacing="0" role="presentation">
                                <!-- Brand Bar -->
                                <tr>
                                    <td class="email-brand-bar">
                                        <p class="brand-name">ANAC Mauritanie</p>
                                    </td>
                                </tr>

                                <!-- Body Content -->
                                <tr>
                                    <td class="content-cell">
                                        {{ Illuminate\Mail\Markdown::parse($slot) }}
                                        {{ $subcopy ?? '' }}
                                    </td>
                                </tr>

                                <!-- Footer -->
                                <tr>
                                    <td class="email-footer" style="padding: 28px 40px; text-align: center; border-top: 1px solid #e9ecef; background-color: #f8f9fa;">
                                        <p style="font-size: 12px; color: #6c757d; margin: 0 0 4px 0; line-height: 1.5;">
                                            © {{ date('Y') }} ANAC Mauritanie — Agence Nationale de l'Aviation Civile
                                        </p>
                                        <p style="font-size: 12px; color: #6c757d; margin: 0; line-height: 1.5;">
                                            Tous droits réservés.
                                        </p>
                                    </td>
                                </tr>
                            </table>
                        </td>
                    </tr>
                </table>
            </td>
        </tr>
    </table>
</body>
</html>
