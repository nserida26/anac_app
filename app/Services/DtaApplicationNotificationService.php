<?php

namespace App\Services;

use App\Models\DemandeApprobation;
use App\Models\User;

class DtaApplicationNotificationService
{
    protected $whatsApp;

    public function __construct(WhatsAppService $whatsApp)
    {
        $this->whatsApp = $whatsApp;
    }

    /**
     * Send rejection notification with detailed reasons
     */
    public function sendRejectionNotification(
        DemandeApprobation $demande,
        User $recipient,
        string $rejecterRole
    ): array {
        $reasons = $this->extractRejectionReasons($demande);

        $message = $this->buildRejectionMessage(
            $demande->reference,
            $rejecterRole,
            $reasons,
            $demande->compagnie->nom_entreprise
        );

        return $this->whatsApp->sendRichMessage(
            $recipient->whatsapp,
            $message
        );
    }

    /**
     * 
     */
    private function extractRejectionReasons(DemandeApprobation $demande): array
    {
        $reasons = [];

        if (!empty($demande->dg_motif)) {
            $reasons[] = "Motif général: " . $demande->dg_motif;
        }
        if (!empty($demande->dta_motif)) {
            $reasons[] = "Motif général: " . $demande->dta_motif;
        }
        if (!empty($demande->dsna_motif)) {
            $reasons[] = "DSNA: " . $demande->dsna_motif;
        }
        if (!empty($demande->dsad_motif)) {
            $reasons[] = "DSAD: " . $demande->dsad_motif;
        }
        if (!empty($demande->dsv_motif)) {
            $reasons[] = "DSV: " . $demande->dsv_motif;
        }

        return $reasons;
    }

    /**
     * Build formatted rejection message
     */
    private function buildRejectionMessage(
        string $reference,
        string $rejecterRole,
        array $reasons,
        string $applicantName
    ): string {
        $reasonsList = implode("\n", array_map(fn($r) => "• $r", $reasons));

        return <<<MSG
        ❌ *Demande en attente de compléments* ❌
        _Référence:_ *{$reference}*
        _Par:_ *{$rejecterRole}*
        _Demandeur:_ {$applicantName}

        📌 *Motifs :*
        {$reasonsList}

        🔗 _Lien vers la demande:_
        {$this->getDemandeLink($reference)}
        MSG;
    }
    /**
     * Generate application link
     */
    private function getDemandeLink(): string
    {
        return route('compagnie');
    }
}
