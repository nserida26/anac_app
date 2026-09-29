<?php
// App\Services\DtaAutorisationNotificationService.php

namespace App\Services;

use App\Mail\GenericAutorisationNotification;
use App\Models\DemandeAutorisation;
use App\Models\Autorisation;
use App\Models\User;
use Carbon\Carbon;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Mail;

class DtaAutorisationNotificationService
{
    protected $whatsApp;

    public function __construct(WhatsAppService $whatsApp)
    {
        $this->whatsApp = $whatsApp;
    }

    /**
     * Envoie une notification à un utilisateur selon les canaux qu'il a activés
     * (WhatsApp par défaut ; e-mail en plus s'il l'a choisi dans son profil).
     * Remplace les appels directs à $this->whatsApp->sendRichMessage($user->whatsapp, ...)
     * partout où un objet User (et pas seulement un numéro) est disponible.
     */
    protected function notify(User $user, string $message, string $subject = 'Notification ANAC'): void
    {
        if (($user->notify_whatsapp ?? true) && !empty($user->whatsapp)) {
            $this->whatsApp->sendRichMessage($user->whatsapp, $message);
        }

        if (!empty($user->notify_email) && !empty($user->email)) {
            try {
                Mail::to($user->email)->queue(new GenericAutorisationNotification($subject, $message));
            } catch (\Throwable $e) {
                // Un échec d'envoi e-mail ne doit jamais empêcher la suite du workflow.
                Log::error("Échec de l'envoi e-mail de notification à {$user->email} : " . $e->getMessage());
            }
        }
    }

    /**
     * Notification quand le demandeur soumet une nouvelle demande
     */
    public function sendNewDemandeNotification(
        DemandeAutorisation $demande,
        User $recipient
    ): void {
        $message = $this->buildNewDemandeMessage(
            $demande->type->libelle,
            $demande->code,
            $demande->user->demandeur->np
        );

        $this->notify($recipient, $message, 'Nouvelle demande - ' . $demande->code);
    }

    /**
     * Notification quand le demandeur renvoie une demande rectifiée
     */
    public function sendRectifiedDemandeNotification(
        DemandeAutorisation $demande,
        User $recipient
    ): void {
        $message = <<<MSG
        📝 *DEMANDE RECTIFIÉE SOUMISE* 📝
        _Type:_ *{$demande->type->libelle}*
        _Numéro:_ {$demande->code}
        _Demandeur:_ {$demande->user->demandeur->np}

        📌 *Message:* Le demandeur a soumis une version rectifiée de la demande.

        🔗 *Accès direct:*
        {$this->getApplicationLink($demande->code)}
        MSG;

        $this->notify($recipient, $message, 'Demande rectifiée - ' . $demande->code);
    }

    /**
     * Notification quand le DG annote au DTA
     */
    public function sendDGAnnotateToDTANotification(
        DemandeAutorisation $demande,
        User $dta
    ): void {
        $message = <<<MSG
        📝 *ANNOTATION DG VERS DTA* 📝
        _Type:_ *{$demande->type->libelle}*
        _Numéro:_ {$demande->code}
        _Demandeur:_ {$demande->user->demandeur->np}

        📌 *Message:* Le DG vous a annoté cette demande pour traitement.

        🔗 *Accès direct:*
        {$this->getApplicationLink($demande->code)}
        MSG;

        $this->notify($dta, $message, 'Annotation DG - ' . $demande->code);
    }

    /**
     * Notification quand le DG annote à l'admin (SRTA)
     */
    public function sendDGAnnotateToAdminNotification(
        DemandeAutorisation $demande,
        User $srta,
        User $dta
    ): void {
        $subject = 'Annotation DG vers SRTA - ' . $demande->code;

        // Notification à la SRTA
        $srtaMessage = <<<MSG
        📝 *ANNOTATION DG VERS SRTA* 📝
        _Type:_ *{$demande->type->libelle}*
        _Numéro:_ {$demande->code}
        _Demandeur:_ {$demande->user->demandeur->np}

        📌 *Message:* Le DG vous a annoté cette demande.

        🔗 *Accès direct:*
        {$this->getApplicationLink($demande->code)}
        MSG;

        $this->notify($srta, $srtaMessage, $subject);

        // Notification à la DTA
        $dtaMessage = <<<MSG
        📝 *ANNOTATION DG VERS SRTA* 📝
        _Type:_ *{$demande->type->libelle}*
        _Numéro:_ {$demande->code}
        _Demandeur:_ {$demande->user->demandeur->np }

        📌 *Message:* Le DG a annoté cette demande à la SRTA.

        🔗 *Accès direct:*
        {$this->getApplicationLink($demande->code)}
        MSG;

        $this->notify($dta, $dtaMessage, $subject);
    }

    /**
     * Notification quand le DG rejette une demande
     */
    public function sendDGRejectionNotification(
        DemandeAutorisation $demande,
        User $dta,
        User $demandeur,
        string $motif
    ): void {
        $subject = 'Demande rejetée par le DG - ' . $demande->code;

        // Notification à la DTA
        $dtaMessage = <<<MSG
        ❌ *DEMANDE REJETÉE PAR LE DG* ❌
        _Type:_ *{$demande->type->libelle}*
        _Numéro:_ {$demande->code}
        _Demandeur:_ {$demande->user->demandeur->np }

        📌 *Message:* Le DG a rejeté la demande.
        📝 *Motif de rejet:* {$motif}

        🔗 *Accès direct:*
        {$this->getApplicationLink($demande->code)}
        MSG;

        $this->notify($dta, $dtaMessage, $subject);

        // Notification au demandeur
        $demandeurMessage = <<<MSG
        ❌ *DEMANDE REJETÉE PAR LE DG* ❌
        _Type:_ *{$demande->type->libelle}*
        _Numéro:_ {$demande->code}

        📌 *Message:* Le DG a rejeté la demande.
        📝 *Motif de rejet:* {$motif}

        🔗 *Accès direct:*
        {$this->getApplicationLink($demande->code)}
        MSG;

        $this->notify($demandeur, $demandeurMessage, $subject);
    }

    /**
     * Notification quand le DTA annote à la place du DG
     */
    public function sendDTAAnnotateForDGNotification(
        DemandeAutorisation $demande,
        User $dg
    ): void {
        $message = <<<MSG
        📝 *ANNOTATION DTA POUR LE DG* 📝
        _Type:_ *{$demande->type->libelle}*
        _Numéro:_ {$demande->code}
        _Demandeur:_ {$demande->user->demandeur->np }

        📌 *Message:* Le DTA annote à votre place.

        🔗 *Accès direct:*
        {$this->getApplicationLink($demande->code)}
        MSG;

        $this->notify($dg, $message, 'Annotation DTA pour le DG - ' . $demande->code);
    }

    /**
     * Notification quand le DTA valide
     */
    public function sendAutorisationNotification(
        Autorisation $autorisation,
        $phone
    ): array {
        $message = <<<MSG
        *✅ AUTORISATION VALIDÉE*
        
        _Type:_ *{$autorisation->demande->type->libelle}*
        _Numéro:_ {$autorisation->code_autorisation}
        _Demandeur:_ {$autorisation->demandeur_np}
        📌 *Message:*ANAC vous a notifié cette autorisation.

        🔗 *Accès direct:*
        {$this->getLink($autorisation)}
        MSG;

        return $this->whatsApp->sendRichMessage($phone, $message);
    }

    /**
     * Notification quand le DTA rejette une demande
     */
    public function sendDTARejectionNotification(
        DemandeAutorisation $demande,
        User $demandeur,
        string $motif
    ): void {
        $message = <<<MSG
        ❌ *DEMANDE REJETÉE PAR LE DTA* ❌
        _Type:_ *{$demande->type->libelle}*
        _Numéro:_ {$demande->code}

        📌 *Message:* Le DTA a rejeté la demande.
        📝 *Motif de rejet:* {$motif}

        🔗 *Accès direct:*
        {$this->getApplicationLink($demande->code)}
        MSG;

        $this->notify($demandeur, $message, 'Demande rejetée par la DTA - ' . $demande->code);
    }

    /**
     * Notification quand la SRTA valide la demande
     */
    public function sendSRTAValidationNotification(
        DemandeAutorisation $demande,
        User $dta
    ): void {
        $message = <<<MSG
        ✅ *VALIDATION SRTA* ✅
        _Type:_ *{$demande->type->libelle}*
        _Numéro:_ {$demande->code}
        _Demandeur:_ {$demande->user->demandeur->np }

        📌 *Message:* La SRTA a validé les informations de la demande.

        🔗 *Accès direct:*
        {$this->getApplicationLink($demande->code)}
        MSG;

        $this->notify($dta, $message, 'Validation SRTA - ' . $demande->code);
    }

    /**
     * Notification quand la DTA renvoie le dossier à la SRTA pour revérification
     */
    public function sendDTAReverificationRequestNotification(
        DemandeAutorisation $demande,
        User $srta,
        string $motif
    ): void {
        $message = <<<MSG
        🔄 *DEMANDE DE REVÉRIFICATION* 🔄
        _Type:_ *{$demande->type->libelle}*
        _Numéro:_ {$demande->code}
        _Demandeur:_ {$demande->user->demandeur->np }

        📌 *Message:* La DTA vous demande de revérifier ce dossier.
        _Motif:_ {$motif}

        🔗 *Accès direct:*
        {$this->getApplicationLink($demande->code)}
        MSG;

        $this->notify($srta, $message, 'Demande de revérification - ' . $demande->code);
    }

    /**
     * Notification quand le DTA transmet la demande aux directions
     */
    public function sendDTATransmitToDirectionsNotification(
        DemandeAutorisation $demande,
        array $directions
    ): void {
        foreach ($directions as $direction => $user) {
            if ($user) {
                $message = <<<MSG
                📤 *TRANSMISSION POUR AVIS* 📤
                _Type:_ *{$demande->type->libelle}*
                _Numéro:_ {$demande->code}
                _Demandeur:_ {$demande->user->demandeur->np }

                📌 *Message:* Le DTA vous a annoté cette demande pour une vérification et avis.

                🔗 *Accès direct:*
                {$this->getApplicationLink($demande->code)}
                MSG;

                $this->notify($user, $message, 'Transmission pour avis - ' . $demande->code);
            }
        }
    }
/**
 * Notification quand le DTA retire des directions spécifiques
 */
public function sendDirectionsRemovedNotification(
    DemandeAutorisation $demande,
    User $dta,
    array $directionsRemoved,
    ?string $motif = null
): void {
    $directionsList = implode(', ', array_map('strtoupper', $directionsRemoved));

    $message = <<<MSG
    ↩️ *DIRECTIONS RETIRÉES* ↩️
    _Type:_ *{$demande->type->libelle}*
    _Numéro:_ {$demande->code}
    _Demandeur:_ {$demande->user->demandeur->np}

    📌 *Directions retirées:* {$directionsList}
    MSG;

    if ($motif) {
        $message .= "\n📝 *Motif:* {$motif}";
    }

    $message .= "\n\n🔗 *Accès direct:*\n{$this->getApplicationLink($demande->code)}";

    $this->notify($dta, $message, 'Directions retirées - ' . $demande->code);
}
    /**
     * Notification quand le DTA retire la demande aux directions
     */
public function sendDTARemoveFromDirectionsNotification(
    DemandeAutorisation $demande,
    array $directions,
    ?string $motif = null
): void {
    foreach ($directions as $direction => $user) {
        if ($user) {
            $message = <<<MSG
            ↩️ *DEMANDE RETIRÉE* ↩️
            _Type:_ *{$demande->type->libelle}*
            _Numéro:_ {$demande->code}
            _Demandeur:_ {$demande->user->demandeur->np}

            📌 *Message:* La direction {$direction} a été retirée de cette demande.
            MSG;

            if ($motif) {
                $message .= "\n📝 *Motif:* {$motif}";
            }

            $message .= "\n\n🔗 *Accès direct:*\n{$this->getApplicationLink($demande->code)}";

            $this->notify($user, $message, 'Demande retirée - ' . $demande->code);
        }
    }
}

    /**
     * Notification quand une direction valide les infos
     */
    public function sendDirectionValidationNotification(
        DemandeAutorisation $demande,
        User $dta,
        string $direction
    ): void {
        $directionLabel = $this->getDirectionLabel($direction);

        $message = <<<MSG
        ✅ *VALIDATION DIRECTION* ✅
        _Type:_ *{$demande->type->libelle}*
        _Numéro:_ {$demande->code}
        _Demandeur:_ {$demande->user->demandeur->np }

        📌 *Message:* {$directionLabel} a validé les informations de la demande.

        🔗 *Accès direct:*
        {$this->getApplicationLink($demande->code)}
        MSG;

        $this->notify($dta, $message, 'Validation direction - ' . $demande->code);
    }

    /**
     * Notification quand le DTA valide la demande (pour signature DG)
     */
    public function sendDTAValidationForDGSignatureNotification(
        DemandeAutorisation $demande,
        User $dg
    ): void {
        $message = <<<MSG
        ✍️ *DEMANDE VALIDÉE - SIGNATURE REQUISE* ✍️
        _Type:_ *{$demande->type->libelle}*
        _Numéro:_ {$demande->code}
        _Demandeur:_ {$demande->user->demandeur->np }

        📌 *Message:* Demande validée par la DTA. Veuillez s'il vous plaît signer l'autorisation.

        🔗 *Accès direct:*
        {$this->getApplicationLink($demande->code)}
        MSG;

        $this->notify($dg, $message, 'Signature requise - ' . $demande->code);
    }

    /**
     * Notification quand le DG signe l'autorisation
     */
    public function sendDGSignatureNotification(
        DemandeAutorisation $demande,
        User $dta,
        User $demandeur
    ): void {
        // Notification à la DTA
        $dtaMessage = <<<MSG
        ✍️ *AUTORISATION SIGNÉE PAR LE DG* ✍️
        _Type:_ *{$demande->type->libelle}*
        _Numéro:_ {$demande->code}
        _Demandeur:_ {$demande->user->demandeur->np }

        📌 *Message:* Autorisation signée par le DG.

        🔗 *Accès direct:*
        {$this->getApplicationLink($demande->code)}
        MSG;

        $this->notify($dta, $dtaMessage, 'Autorisation signée - ' . $demande->code);

        // Notification au demandeur
        $demandeurMessage = <<<MSG
        ✍️ *AUTORISATION SIGNÉE* ✍️
        _Type:_ *{$demande->type->libelle}*
        _Numéro:_ {$demande->code}

        📌 *Message:* Votre autorisation a été signée par le DG. Vous pouvez la télécharger.

        🔗 *Accès direct:*
        {$this->getApplicationLink($demande->code)}
        MSG;

        $this->notify($demandeur, $demandeurMessage, 'Autorisation signée - ' . $demande->code);
    }

    /**
     * Méthodes existantes (à conserver)...
     */
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

    public function sendAcknowledgmentNotification(
        DemandeAutorisation $demande,
        User $recipient
    ): array {
        $message = $this->buildAcknowledgmentMessage(
            $demande->code,
            optional($demande->user->demandeur)->np ,
            $this->formatSubmissionDate($demande->date_soumission)
        );

        return $this->whatsApp->sendRichMessage(
            $recipient->whatsapp,
            $message
        );
    }

    public function sendRejectionNotification(
        DemandeAutorisation $demande,
        User $recipient,
        string $rejecterRole,
        array $reasons,
    ): void {
        $message = $this->buildRejectionMessage(
            $demande->code,
            $rejecterRole,
            $reasons,
            optional($demande->user->demandeur)->np
        );

        $this->notify($recipient, $message, 'Demande rejetée - ' . $demande->code);
    }

    public function sendRejectionCancelledNotification(
        DemandeAutorisation $demande,
        User $recipient
    ): void {
        $message = <<<MSG
        ✅ *REJET RETIRÉ*
        _Type:_ *{$demande->type->libelle}*
        _Numéro:_ {$demande->code}

        📌 *Message:* Le rejet précédent de votre demande était une erreur. Il a été retiré et votre dossier reprend son traitement.

        🔗 *Accès direct:*
        {$this->getApplicationLink($demande->code)}
        MSG;

        $this->notify($recipient, $message, 'Rejet retiré - ' . $demande->code);
    }

    /**
     * Méthodes privées utilitaires
     */
    private function getDirectionLabel(string $direction): string
    {
        return match ($direction) {
            'dsv' => 'DSV',
            'dsv_verificateur' => 'Vérificateur DSV',
            'dsna' => 'DSNA',
            'dsad' => 'DSAD',
            'dsf' => 'DSF',
            default => strtoupper($direction)
        };
    }

    private function buildNewDemandeMessage(string $type, string $numero, string $demandeur): string
    {
        return <<<MSG
        📢 *NOUVELLE DEMANDE SOUMISE* 📢
        _Type:_ *{$type}*
        _Numéro:_ {$numero}
        _Demandeur:_ {$demandeur}

        📌 *Message:* Une nouvelle demande a été soumise.

        🔗 *Accès direct:*
        {$this->getApplicationLink($numero)}
        MSG;
    }

    private function getActionConfig(string $actionType): array
    {
        return match ($actionType) {
            'validation' => [
                'icon' => '✅',
                'action' => 'VALIDATION REQUISE',
                'instruction' => 'Veuillez valider cette demande d\'autorisation'
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
            'correction' => ['icon' => '✏️', 'action' => 'CORRECTIONS REQUISES', 'instruction' => 'Veuillez apporter les modifications demandées'],
            'validated' => ['icon' => '✅', 'action' => 'AUTORISATION VALIDÉE', 'instruction' => 'Veuillez imprimer votre autorisation validée'],
            'validated_direction' => ['icon' => '✅', 'action' => 'DEMANDE VALIDÉE', 'instruction' => 'Monsieur le Directeur, Je vous informe que la demande que vous avez transmise a été validée. Cordialement.'],
            'payment_confirmed' => ['icon' => '💳', 'action' => 'PAIEMENT CONFIRMÉE', 'instruction' => 'La facture a été confirmée par la DAF'],
            'payment_direction' => [
                'icon' => '💳',
                'action' => 'FACTURE À PAYER',
                'instruction' => 'Monsieur le Directeur, Je vous informe que la demande sous état de paiement.',
                'has_attachment' => true
            ],
            'reminder' => [
                'icon' => '⏰',
                'action' => 'RELANCE - DOSSIER EN ATTENTE',
                'instruction' => 'Ce dossier attend votre action depuis plusieurs jours. Merci de le traiter dès que possible.'
            ],
            default => ['icon' => '📄', 'action' => 'ACTION REQUISE', 'instruction' => 'Veuillez traiter cette demande']
        };
    }

    private function extractRejectionReasons(DemandeAutorisation $demande): array
    {
        $reasons = [];

        if (!empty($demande->dg_motif)) {
            $reasons[] = "DG: " . $demande->dg_motif;
        }
        if (!empty($demande->dta_motif)) {
            $reasons[] = "DTA: " . $demande->dta_motif;
        }
        if (!empty($demande->dsv_motif)) {
            $reasons[] = "Commentaire DSV: " . $demande->dsv_motif;
        }
        if (!empty($demande->dsna_motif)) {
            $reasons[] = "Commentaire DSNA: " . $demande->dsna_motif;
        }
        if (!empty($demande->dsad_motif)) {
            $reasons[] = "Commentaire DSAD: " . $demande->dsad_motif;
        }

        return $reasons;
    }

    private function formatSubmissionDate($date): string
    {
        if ($date instanceof \Carbon\Carbon) {
            return $date->format('d/m/Y');
        }
        
        if (is_string($date)) {
            try {
                return Carbon::parse($date)->format('d/m/Y');
            } catch (\Exception $e) {
                return $date;
            }
        }
        
        return date('d/m/Y');
    }

    private function getApplicationLink(string $demandeNumber): string
    {
        return route('login');
    }
    private function getLink(Autorisation $autorisation): string
    {
        return route('public.autorisations.download', $autorisation);
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

    private function buildAcknowledgmentMessage(
        string $demandeId,
        string $applicantName,
        string $submissionDate
    ): string {
        return <<<MSG
        ✅ *Accusé de réception - Demande d'autorisation* ✅

        Nous accusons réception de votre demande d'autorisation.

        📋 *Détails de la demande :*
        • _ID Demande:_ *{$demandeId}*
        • _Demandeur:_ {$applicantName}
        • _Date de soumission:_ {$submissionDate}

        📝 *Prochaines étapes :*
        Votre demande est en cours de traitement. 
        Vous serez informé de l'avancement de l'instruction.
        🔗 _Suivre votre demande:_
        {$this->getDemandeLink()}

        _Merci pour votre confiance_
        MSG;
    }

    private function buildRejectionMessage(
        string $demandeId,
        string $rejecterRole,
        array $reasons,
        string $applicantName
    ): string {
        $reasonsList = implode("\n", array_map(fn($r) => "• $r", $reasons));

        return <<<MSG
        ❌ *Demande d'autorisation à compléter* ❌
        _ID Demande:_ *{$demandeId}*
        _Mise en attente par :_ *{$rejecterRole}*
        _Demandeur:_ {$applicantName}

        📌 *Motifs :*
        {$reasonsList}

        🔗 _Lien vers la demande:_
        {$this->getDemandeLink()}
        MSG;
    }

    private function getDemandeLink(): string
    {
        return route('user');
    }
}
