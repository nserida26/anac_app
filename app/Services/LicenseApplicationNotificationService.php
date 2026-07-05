<?php
// app/Services/ApplicationNotificationService.php

namespace App\Services;

use App\Models\ExamenMedical;
use App\Models\User;
use Carbon\Carbon;

class LicenseApplicationNotificationService
{
    protected $whatsApp;

    public function __construct(WhatsAppService $whatsApp)
    {
        $this->whatsApp = $whatsApp;
    }

    public function sendApplicationActionRequired(
        string $demandeNumber,
        string $demandeType,
        string $recipientRole,
        string $recipientPhone,
        string $actionType,
        string $applicantName
    ): array {
        $actionConfig = $this->getActionConfig($actionType);

        $message = $this->buildApplicationMessage(
            $demandeNumber,
            $demandeType,
            $recipientRole,
            $actionConfig,
            $applicantName
        );

        return $this->whatsApp->sendRichMessage($recipientPhone, $message);
    }

    private function getActionConfig(string $actionType): array
    {
        return match ($actionType) {
            'validation' => [
                'icon' => '✅',
                'action' => 'VALIDATION REQUISE',
                'instruction' => 'Veuillez valider cette demande de licence'
            ],
            'annotation' => [
                'icon' => '📝',
                'action' => 'ANNOTATION REQUISE',
                'instruction' => 'Veuillez traiter cette demande'
            ],
            'rejection' => [
                'icon' => '❌',
                'action' => 'REJET DE LA DEMANDE',
                'instruction' => 'Veuillez prendre connaissance des motifs de rejet',
            ],
            'technical_review' => ['icon' => '⚙️', 'action' => 'REVUE TECHNIQUE', 'instruction' => 'Veuillez examiner la conformité technique'],
            'payment' => [
                'icon' => '💳',
                'action' => 'FACTURE À PAYER',
                'instruction' => 'Nous vous remercions de régler la facture en pièce jointe dans les plus brefs délais.',
                'has_attachment' => true
            ],
            'payed' => [
                'icon' => '💳',
                'action' => 'FACTURE PAYÉE',
                'instruction' => 'Merci de confirmer la bonne réception.',
                'has_attachment' => true
            ],
            'signature' => ['icon' => '🖋️', 'action' => 'SIGNATURE REQUISE', 'instruction' => 'Veuillez signer cette demande'],
            'signed' => ['icon' => '✅', 'action' => 'LICENCE SIGNEE', 'instruction' => 'Veuillez procéder à l\'émission de la licence'],
            'correction' => ['icon' => '✏️', 'action' => 'CORRECTIONS REQUISES', 'instruction' => 'Veuillez apporter les modifications demandées'],
            default => ['icon' => '📄', 'action' => 'ACTION REQUISE', 'instruction' => 'Veuillez traiter cette demande']
        };
    }

    public function sendPaymentRequest(
        string $applicationNumber,
        string $applicationType,
        string $recipientPhone,
        string $recipientRole,
        float $amount,
        string $dueDate,
        string $invoiceUrl,
        string $applicantName
    ): array {
        $actionConfig = $this->getActionConfig('payment');

        $message = $this->buildPaymentMessage(
            $applicationNumber,
            $applicationType,
            $recipientRole,
            $amount,
            $dueDate,
            $applicantName,
            $actionConfig
        );

        return $this->whatsApp->sendWithAttachment(
            to: $recipientPhone,
            message: $message,
            documentUrl: $invoiceUrl,
            filename: "Facture_{$applicationNumber}.pdf"
        );
    }

    private function buildPaymentMessage(
        string $applicationNumber,
        string $applicationType,
        string $recipientRole,
        float $amount,
        string $dueDate,
        string $applicantName,
        array $actionConfig
    ): string {
        return <<<MSG
    {$actionConfig['icon']} *{$actionConfig['action']}* {$actionConfig['icon']}
    _Demande:_ *{$applicationType}*  
    _Numéro:_ {$applicationNumber}  
    _Destinataire:_ *{$recipientRole}*  
    {$this->buildApplicantLine($applicantName)}

    💰 *Montant:* {$amount} MRU  
    ⏳ *Échéance:* {$dueDate}  

    📄 *Facture jointe* (PDF)

    💳 *Modes de paiement:*  
    • Virement bancaire  
    • Paiement en ligne  
    • Espèces à l'ANAC
    MSG;
    }
    public function sendValidationConfirmation(
        string $applicationNumber,
        string $applicationType,
        string $recipientPhone,
        string $recipientRole,
        string $validatorRole,
        string $applicantName,
        array $nextSteps = []
    ): array {
        $message = $this->buildValidationMessage(
            $applicationNumber,
            $applicationType,
            $recipientRole,
            $validatorRole,
            $applicantName,
            $nextSteps
        );

        return $this->whatsApp->sendRichMessage($recipientPhone, $message);
    }

    private function buildValidationMessage(
        string $applicationNumber,
        string $applicationType,
        string $recipientRole,
        string $validatorRole,
        ?string $applicantName,
        array $nextSteps
    ): string {
        $nextStepsList = implode("\n", array_map(
            fn($step) => "• {$step}",
            $nextSteps
        ));

        return <<<MSG
    ✅ *VALIDATION CONFIRMÉE* ✅
    
    _Demande:_ *{$applicationType}*  
    _Numéro:_ {$applicationNumber}  
    _Validé par:_ *{$validatorRole}*  
    _Destinataire:_ *{$recipientRole}*  
    {$this->buildApplicantLine($applicantName)}
    📌 *Prochaines étapes:*  
    {$nextStepsList}
    
    🔗 *Accès direct:*  
    {$this->getApplicationLink($applicationNumber)}
    MSG;
    }
    private function buildApplicationMessage(
        string $demandeNumber,
        string $demandeType,
        string $recipientRole,
        array $actionConfig,
        string $applicantName
    ): string {
        return <<<MSG
            {$actionConfig['icon']} *{$actionConfig['action']}* {$actionConfig['icon']}
            _Demande:_ *{$demandeType}*  
            _Numéro:_ {$demandeNumber}  
            _Destinataire:_ *{$recipientRole}*  
            {$this->buildApplicantLine($applicantName)}
            📌 *Instruction:* {$actionConfig['instruction']}  
            🔗 *Accès direct:*  
            {$this->getApplicationLink($demandeNumber)}
            MSG;
    }

    private function buildApplicantLine(?string $applicantName): string
    {
        return $applicantName ? "_Demandeur:_ {$applicantName}\n" : "";
    }
    public function sendApplicationRejection(
        string $applicationNumber,
        string $applicationType,
        string $recipientPhone,
        string $rejecterRole,
        ?array $reasons = null,
        string $applicantName
    ): array {
        $actionConfig = $this->getActionConfig('rejection');

        $message = $this->buildRejectionMessage(
            $applicationNumber,
            $applicationType,
            $rejecterRole,
            $reasons,
            $applicantName,
            $actionConfig
        );

        return $this->whatsApp->sendRichMessage($recipientPhone, $message);
    }

    private function buildRejectionMessage(
        string $applicationNumber,
        string $applicationType,
        string $rejecterRole,
        ?array $reasons,
        string $applicantName,
        array $actionConfig
    ): string {
        $reasonsSection = $reasons
            ? "📌 *Motifs:*\n" . implode("\n", array_map(fn($r) => "• $r", $reasons)) . "\n"
            : "";

        return <<<MSG
    {$actionConfig['icon']} *{$actionConfig['action']}* {$actionConfig['icon']}
    
    _Demande:_ *{$applicationType}*  
    _Numéro:_ {$applicationNumber}  
    _Rejeté par:_ *{$rejecterRole}*  
    {$this->buildApplicantLine($applicantName)}
    
    {$reasonsSection}
    🔗 *Accès au dossier:*  
    {$this->getApplicationLink($applicationNumber)}
    MSG;
    }

    public function notifyPaymentSettled(
        string $invoiceNumber,
        string $applicationNumber,
        string $applicationType,
        string $recipientPhone,
        string $recipientRole,
        string $paymentDate
    ): array {
        $message = $this->buildPaymentSettledMessage(
            $invoiceNumber,
            $applicationNumber,
            $applicationType,
            $recipientRole,
            $paymentDate
        );

        return $this->whatsApp->sendRichMessage($recipientPhone, $message);
    }

    private function buildPaymentSettledMessage(
        string $invoiceNumber,
        string $applicationNumber,
        string $applicationType,
        string $recipientRole,
        string $paymentDate
    ): string {

        return <<<MSG
        ✅ *FACTURE RÉGLÉE* ✅

        _Demande:_ *{$applicationType}*  
        _Numéro demande:_ {$applicationNumber}  
        _Numéro facture:_ {$invoiceNumber}  
        _Destinataire:_ *{$recipientRole}*  
        📅 *Date paiement:* {$paymentDate}
        MSG;
    }

    public function confirmToPayer(
        string $invoiceNumber,
        string $applicationNumber,
        string $applicationType,
        string $recipientPhone,
        string $recipientName,
        float $amount,
        string $paymentDate
    ): array {
        $message = $this->buildPayerMessage(
            $invoiceNumber,
            $applicationNumber,
            $applicationType,
            $recipientName,
            $amount,
            $paymentDate
        );

        return $this->whatsApp->sendRichMessage($recipientPhone, $message);
    }

    /**
     * Notify finance department
     */
    private function buildPayerMessage(
        string $invoiceNumber,
        string $applicationNumber,
        string $applicationType,
        string $recipientName,
        float $amount,
        string $paymentDate,
    ): string {

        return <<<MSG
        ✅ *PAYMENT CONFIRMED* ✅

        *Invoice:* #{$invoiceNumber}
        *Demande:* {$applicationType} (#{$applicationNumber})
        *Destinataire:* {$recipientName}

        💵 *Amount:* {$amount} MRU
        📅 *Date:* {$paymentDate}
        MSG;
    }

    public function notifyLicenseGenerated(
        string $licenseNumber,
        string $licenseType,
        string $recipientPhone,
        string $recipientName,
        string $issueDate,
        string $expiryDate,
    ): array {
        $message = $this->buildLicenseMessage(
            $licenseNumber,
            $licenseType,
            $recipientName,
            $issueDate,
            $expiryDate
        );

        return $this->whatsApp->sendRichMessage($recipientPhone, $message);
    }

    private function buildLicenseMessage(
        string $licenseNumber,
        string $licenseType,
        string $recipientName,
        string $issueDate,
        string $expiryDate
    ): string {

        return <<<MSG
    🎫 *LICENCE GÉNÉRÉE* 🎫

    *Destinataire:* {$recipientName}
    *TYPE DE LICENCE:* {$licenseType}
    *NUMERO LICENCE:* {$licenseNumber}

    📅 *DATE DE DELIVERANCE:* {$issueDate}
    📅 *DATE D'EXPIRATION:* {$expiryDate}

    🏛️ *ANAC Mauritania*
MSG;
    }
    private function getApplicationLink(): string
    {
        return route('login');
    }

    public function sendMedicalValidationRequest(
        string $demandeNumber,
        string $applicantName,
        User $medicalEvaluator,
        ?ExamenMedical $examen = null
    ): array {
        $dateExamen = Carbon::parse($examen->date_examen);
        $expiryDate = $dateExamen->copy()->addDays(15)->format('Y-m-d');
        $actionConfig = [
            'type' => 'medical_validation',
            'urgency' => 'high',
            'template' => [
                'en' => "🚑 *Medical Validation Request*\n\n" .
                    "Patient: *{applicantName}*\n" .
                    "Case #: *{demandeNumber}*\n" .
                    "Action Required: *Validate medical report*\n" .
                    "Deadline: *{deadline}*\n\n" .
                    "Review: {document_url}",
                'fr' => "🚑 *Demande de Validation Médicale*\n\n" .
                    "Patient: *{applicantName}*\n" .
                    "Dossier #: *{demandeNumber}*\n" .
                    "Action Requise: *Valider le rapport médical*\n" .
                    "Échéance: *{deadline}*\n\n" .
                    "Consulter: {document_url}"
            ],
            'default_deadline' => now()->addDays(15)->format('Y-m-d')
        ];


        $message = str_replace(
            [
                '{applicantName}',
                '{demandeNumber}',
                '{deadline}',
                '{document_url}'
            ],
            [
                $applicantName,
                $demandeNumber,
                $expiryDate ?? $actionConfig['default_deadline'],
                route('evaluateur')
            ],
            $actionConfig['template'][app()->getLocale()]
        );

        return $this->whatsApp->sendRichMessage(
            $medicalEvaluator->whatsapp,
            $message,
            [
                'buttons' => [
                    ['type' => 'url', 'text' => 'View Report', 'url' => route('evaluateur')],
                    ['type' => 'reply', 'text' => 'Approve'],
                    ['type' => 'reply', 'text' => 'Request Changes']
                ],
                'header' => 'Medical Validation Required'
            ]
        );
    }
}
