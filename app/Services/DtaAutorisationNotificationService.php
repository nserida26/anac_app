<?php
// App\Services\DtaAutorisationNotificationService.php

namespace App\Services;

use App\Models\DemandeAutorisation;
use App\Models\Autorisation;
use App\Models\User;
use Carbon\Carbon;

class DtaAutorisationNotificationService
{
    protected $whatsApp;

    public function __construct(WhatsAppService $whatsApp)
    {
        $this->whatsApp = $whatsApp;
    }

    /**
     * Notification quand le demandeur soumet une nouvelle demande
     */
    public function sendNewDemandeNotification(
        DemandeAutorisation $demande,
        User $recipient
    ): array {
        $message = $this->buildNewDemandeMessage(
            $demande->type->libelle,
            $demande->code,
            $demande->user->demandeur->np 
        );

        return $this->whatsApp->sendRichMessage(
            $recipient->whatsapp,
            $message
        );
    }

    /**
     * Notification quand le demandeur renvoie une demande rectifiée
     */
    public function sendRectifiedDemandeNotification(
        DemandeAutorisation $demande,
        User $recipient
    ): array {
        $message = <<<MSG
        📝 *DEMANDE RECTIFIÉE SOUMISE* 📝
        _Type:_ *{$demande->type->libelle}*
        _Numéro:_ {$demande->code}
        _Demandeur:_ {$demande->user->demandeur->np}

        📌 *Message:* Le demandeur a soumis une version rectifiée de la demande.

        🔗 *Accès direct:*
        {$this->getApplicationLink($demande->code)}
        MSG;

        return $this->whatsApp->sendRichMessage(
            $recipient->whatsapp,
            $message
        );
    }

    /**
     * Notification quand le DG annote au DTA
     */
    public function sendDGAnnotateToDTANotification(
        DemandeAutorisation $demande,
        User $dta
    ): array {
        $message = <<<MSG
        📝 *ANNOTATION DG VERS DTA* 📝
        _Type:_ *{$demande->type->libelle}*
        _Numéro:_ {$demande->code}
        _Demandeur:_ {$demande->user->demandeur->np}

        📌 *Message:* Le DG vous a annoté cette demande pour traitement.

        🔗 *Accès direct:*
        {$this->getApplicationLink($demande->code)}
        MSG;

        return $this->whatsApp->sendRichMessage(
            $dta->whatsapp,
            $message
        );
    }

    /**
     * Notification quand le DG annote à l'admin (SRTA)
     */
    public function sendDGAnnotateToAdminNotification(
        DemandeAutorisation $demande,
        User $srta,
        User $dta
    ): array {
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

        $this->whatsApp->sendRichMessage($srta->whatsapp, $srtaMessage);

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

        return $this->whatsApp->sendRichMessage($dta->whatsapp, $dtaMessage);
    }

    /**
     * Notification quand le DG rejette une demande
     */
    public function sendDGRejectionNotification(
        DemandeAutorisation $demande,
        User $dta,
        User $demandeur,
        string $motif
    ): array {
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

        $this->whatsApp->sendRichMessage($dta->whatsapp, $dtaMessage);

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

        return $this->whatsApp->sendRichMessage($demandeur->whatsapp, $demandeurMessage);
    }

    /**
     * Notification quand le DTA annote à la place du DG
     */
    public function sendDTAAnnotateForDGNotification(
        DemandeAutorisation $demande,
        User $dg
    ): array {
        $message = <<<MSG
        📝 *ANNOTATION DTA POUR LE DG* 📝
        _Type:_ *{$demande->type->libelle}*
        _Numéro:_ {$demande->code}
        _Demandeur:_ {$demande->user->demandeur->np }

        📌 *Message:* Le DTA annote à votre place.

        🔗 *Accès direct:*
        {$this->getApplicationLink($demande->code)}
        MSG;

        return $this->whatsApp->sendRichMessage($dg->whatsapp, $message);
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
        _Demandeur:_ {$autorisation->demande->user->demandeur->np }
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
    ): array {
        $message = <<<MSG
        ❌ *DEMANDE REJETÉE PAR LE DTA* ❌
        _Type:_ *{$demande->type->libelle}*
        _Numéro:_ {$demande->code}

        📌 *Message:* Le DTA a rejeté la demande.
        📝 *Motif de rejet:* {$motif}

        🔗 *Accès direct:*
        {$this->getApplicationLink($demande->code)}
        MSG;

        return $this->whatsApp->sendRichMessage($demandeur->whatsapp, $message);
    }

    /**
     * Notification quand la SRTA valide la demande
     */
    public function sendSRTAValidationNotification(
        DemandeAutorisation $demande,
        User $dta
    ): array {
        $message = <<<MSG
        ✅ *VALIDATION SRTA* ✅
        _Type:_ *{$demande->type->libelle}*
        _Numéro:_ {$demande->code}
        _Demandeur:_ {$demande->user->demandeur->np }

        📌 *Message:* La SRTA a validé les informations de la demande.

        🔗 *Accès direct:*
        {$this->getApplicationLink($demande->code)}
        MSG;

        return $this->whatsApp->sendRichMessage($dta->whatsapp, $message);
    }

    /**
     * Notification quand le DTA transmet la demande aux directions
     */
    public function sendDTATransmitToDirectionsNotification(
        DemandeAutorisation $demande,
        array $directions
    ): array {
        $results = [];
        
        foreach ($directions as $direction => $user) {
            if ($user && !empty($user->whatsapp)) {
                $directionLabel = $this->getDirectionLabel($direction);
                
                $message = <<<MSG
                📤 *TRANSMISSION POUR AVIS* 📤
                _Type:_ *{$demande->type->libelle}*
                _Numéro:_ {$demande->code}
                _Demandeur:_ {$demande->user->demandeur->np }

                📌 *Message:* Le DTA vous a annoté cette demande pour une vérification et avis.

                🔗 *Accès direct:*
                {$this->getApplicationLink($demande->code)}
                MSG;

                $results[$direction] = $this->whatsApp->sendRichMessage($user->whatsapp, $message);
            }
        }

        return $results;
    }
/**
 * Notification quand le DTA retire des directions spécifiques
 */
public function sendDirectionsRemovedNotification(
    DemandeAutorisation $demande,
    User $dta,
    array $directionsRemoved,
    ?string $motif = null
): array {
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

    return $this->whatsApp->sendRichMessage($dta->whatsapp, $message);
}
    /**
     * Notification quand le DTA retire la demande aux directions
     */
public function sendDTARemoveFromDirectionsNotification(
    DemandeAutorisation $demande,
    array $directions,
    ?string $motif = null
): array {
    $results = [];
    $directionsList = implode(', ', array_map(function($dir) {
        return strtoupper($dir);
    }, array_keys($directions)));
    
    foreach ($directions as $direction => $user) {
        if ($user && !empty($user->whatsapp)) {
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

            $results[$direction] = $this->whatsApp->sendRichMessage($user->whatsapp, $message);
        }
    }

    return $results;
}

    /**
     * Notification quand une direction valide les infos
     */
    public function sendDirectionValidationNotification(
        DemandeAutorisation $demande,
        User $dta,
        string $direction
    ): array {
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

        return $this->whatsApp->sendRichMessage($dta->whatsapp, $message);
    }

    /**
     * Notification quand le DTA valide la demande (pour signature DG)
     */
    public function sendDTAValidationForDGSignatureNotification(
        DemandeAutorisation $demande,
        User $dg
    ): array {
        $message = <<<MSG
        ✍️ *DEMANDE VALIDÉE - SIGNATURE REQUISE* ✍️
        _Type:_ *{$demande->type->libelle}*
        _Numéro:_ {$demande->code}
        _Demandeur:_ {$demande->user->demandeur->np }

        📌 *Message:* Demande validée par la DTA. Veuillez s'il vous plaît signer l'autorisation.

        🔗 *Accès direct:*
        {$this->getApplicationLink($demande->code)}
        MSG;

        return $this->whatsApp->sendRichMessage($dg->whatsapp, $message);
    }

    /**
     * Notification quand le DG signe l'autorisation
     */
    public function sendDGSignatureNotification(
        DemandeAutorisation $demande,
        User $dta,
        User $demandeur
    ): array {
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

        $this->whatsApp->sendRichMessage($dta->whatsapp, $dtaMessage);

        // Notification au demandeur
        $demandeurMessage = <<<MSG
        ✍️ *AUTORISATION SIGNÉE* ✍️
        _Type:_ *{$demande->type->libelle}*
        _Numéro:_ {$demande->code}

        📌 *Message:* Votre autorisation a été signée par le DG. Vous pouvez la télécharger.

        🔗 *Accès direct:*
        {$this->getApplicationLink($demande->code)}
        MSG;

        return $this->whatsApp->sendRichMessage($demandeur->whatsapp, $demandeurMessage);
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
    ): array {
        $message = $this->buildRejectionMessage(
            $demande->code,
            $rejecterRole,
            $reasons,
            optional($demande->user->demandeur)->np 
        );

        return $this->whatsApp->sendRichMessage(
            $recipient->whatsapp,
            $message
        );
    }

    /**
     * Méthodes privées utilitaires
     */
    private function getDirectionLabel(string $direction): string
    {
        return match ($direction) {
            'dsv' => 'DSV',
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
        return route('public.autorisations.print',$autorisation);
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