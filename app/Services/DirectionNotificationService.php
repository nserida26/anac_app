<?php

namespace App\Services;

use App\Models\DemandeAutorisation;
use App\Models\User;
use App\Models\EtatDemandeAutorisation;
use Illuminate\Support\Facades\Log;

class DirectionNotificationService
{
    protected $whatsApp;
    //protected $emailService;

    public function __construct(WhatsAppService $whatsApp 
    //,EmailService $emailService = null
    )
    {
        $this->whatsApp = $whatsApp;
        //$this->emailService = $emailService;
    }

    /**
     * Notifier toutes les directions annotées
     */
    public function notifyAnnotedDirections(DemandeAutorisation $demande, array $annotatedDirections): array
    {
        $results = [];
        
        foreach ($annotatedDirections as $direction) {
            $result = $this->notifyDirection($demande, $direction);
            $results[$direction] = $result;
        }
        
        return $results;
    }

    /**
     * Notifier une direction spécifique
     */
    public function notifyDirection(DemandeAutorisation $demande, string $direction): array
    {
        try {
            // Récupérer les utilisateurs de la direction
            $users = User::whereHas('roles', function ($query) use ($direction) {
                $query->where('name', $direction);
            })->get();

            if ($users->isEmpty()) {
                
                return [
                    'success' => false,
                    'message' => "Aucun utilisateur trouvé pour la direction: {$direction}"
                ];
            }

            $results = [];
            foreach ($users as $user) {
                if (!empty($user->whatsapp)) {
                    $result = $this->sendWhatsAppNotification($demande, $user, $direction);
                    $results[] = $result;
                }
                
                //if (!empty($user->email)) {
                  //  $emailResult = $this->sendEmailNotification($demande, $user, $direction);
                    //$results[] = $emailResult;
                //}
            }

            return [
                'success' => true,
                'message' => "Notifications envoyées pour la direction: {$direction}",
                'details' => $results
            ];

        } catch (\Exception $e) {
            
            return [
                'success' => false,
                'message' => "Erreur: " . $e->getMessage()
            ];
        }
    }

    /**
     * Envoyer notification WhatsApp
     */
    private function sendWhatsAppNotification(DemandeAutorisation $demande, User $user, string $direction): array
    {
        try {
            $message = $this->buildDirectionMessage($demande, $direction);
            
            $response = $this->whatsApp->sendRichMessage(
                $user->whatsapp,
                $message
            );

            

            return [
                'type' => 'whatsapp',
                'recipient' => $user->email,
                'phone' => $user->whatsapp,
                'success' => true,
                'response' => $response
            ];

        } catch (\Exception $e) {
            
            return [
                'type' => 'whatsapp',
                'recipient' => $user->email,
                'success' => false,
                'error' => $e->getMessage()
            ];
        }
    }

    /**
     * Envoyer notification email
     
    private function sendEmailNotification(DemandeAutorisation $demande, User $user, string $direction): array
    {
        try {
            if (!$this->emailService) {
                return [
                    'type' => 'email',
                    'recipient' => $user->email,
                    'success' => false,
                    'error' => 'Email service not available'
                ];
            }

            $subject = $this->getEmailSubject($direction, $demande);
            $content = $this->buildEmailContent($demande, $direction, $user);

            $result = $this->emailService->send(
                $user->email,
                $subject,
                $content
            );

            

            return [
                'type' => 'email',
                'recipient' => $user->email,
                'success' => $result,
                'response' => 'Email sent'
            ];

        } catch (\Exception $e) {
            
            return [
                'type' => 'email',
                'recipient' => $user->email,
                'success' => false,
                'error' => $e->getMessage()
            ];
        }
    }*/

    /**
     * Construire le message pour la direction
     */
    private function buildDirectionMessage(DemandeAutorisation $demande, string $direction): string
    {
        $directionName = $this->getDirectionName($direction);
        $demandeur = optional($demande->user->demandeur)->np ?? 'N/A';
        $demandeType = $demande->type->libelle ?? 'N/A';
        
        // Ajouter les points si disponibles
        $pointsInfo = '';
        if (!empty($demande->points)) {
            $pointsInfo = "\n📝 *Points à considérer:*\n" . $demande->points . "\n";
        }

        return <<<MSG
        📋 *NOUVELLE DEMANDE POUR ANNOTATION* 📋

        *Direction:* {$directionName}
        *Type de demande:* {$demandeType}
        *Référence:* {$demande->code}
        *Demandeur:* {$demandeur}
        *Période:* {$demande->date_debut} - {$demande->date_fin}
        
        {$pointsInfo}
        🔗 *Accéder à la demande:*
        {$this->getDirectionLink($direction, $demande->id)}

        ⚡ *Action requise:* Veuillez examiner et annoter cette demande.

        Cordialement,
        DTA
        MSG;
    }

    /**
     * Construire le contenu de l'email
     
    private function buildEmailContent(DemandeAutorisation $demande, string $direction, User $user): string
    {
        $directionName = $this->getDirectionName($direction);
        $demandeur = optional($demande->user->demandeur)->np ?? 'N/A';
        $demandeType = $demande->type->libelle ?? 'N/A';
        $link = $this->getDirectionLink($direction, $demande->id);

        $pointsInfo = '';
        if (!empty($demande->points)) {
            $pointsInfo = "<h4>Points à considérer:</h4><p>" . nl2br(e($demande->points)) . "</p>";
        }

        return <<<HTML
        <!DOCTYPE html>
        <html>
        <head>
            <style>
                body { font-family: Arial, sans-serif; line-height: 1.6; }
                .container { max-width: 600px; margin: 0 auto; padding: 20px; }
                .header { background-color: #007bff; color: white; padding: 20px; text-align: center; }
                .content { padding: 20px; border: 1px solid #ddd; }
                .button { display: inline-block; padding: 10px 20px; background-color: #007bff; 
                         color: white; text-decoration: none; border-radius: 5px; margin: 20px 0; }
                .footer { margin-top: 20px; padding-top: 20px; border-top: 1px solid #ddd; 
                         font-size: 12px; color: #666; }
            </style>
        </head>
        <body>
            <div class="container">
                <div class="header">
                    <h2>Nouvelle demande pour annotation</h2>
                </div>
                <div class="content">
                    <h3>Direction: {$directionName}</h3>
                    
                    <p><strong>Type de demande:</strong> {$demandeType}</p>
                    <p><strong>Référence:</strong> {$demande->code}</p>
                    <p><strong>Demandeur:</strong> {$demandeur}</p>
                    <p><strong>Période:</strong> {$demande->date_debut} - {$demande->date_fin}</p>
                    
                    {$pointsInfo}
                    
                    <p>Veuillez examiner cette demande et fournir vos annotations.</p>
                    
                    <a href="{$link}" class="button">Accéder à la demande</a>
                    
                    <p>Ce lien vous permet d'accéder directement à la demande pour annotation.</p>
                </div>
                <div class="footer">
                    <p>Cet email a été envoyé automatiquement par le système de gestion des autorisations.</p>
                    <p>Merci de ne pas répondre à cet email.</p>
                </div>
            </div>
        </body>
        </html>
        HTML;
    }
*/

    /**
     * Obtenir le nom complet de la direction
     */
    private function getDirectionName(string $directionCode): string
    {
        return match ($directionCode) {
            'dsv' => 'DSV',
            'dsna' => 'DSNA',
            'dsad' => 'DSAD',
            'dta' => 'DTA',
            'dg' => 'Direction Générale (DG)',
            default => $directionCode,
        };
    }

    /**
     * Obtenir le lien pour la direction
     */
    private function getDirectionLink(string $direction, int $demandeId): string
    {
        return match ($direction) {
            'dsv' => route('login'),
            'dsna' => route('login'),
            'dsad' => route('login'),
            default => route('login'),
        };
    }

    /**
     * Obtenir le sujet de l'email
     */
    private function getEmailSubject(string $direction, DemandeAutorisation $demande): string
    {
        $directionName = $this->getDirectionName($direction);
        return "[DTA] Demande à annoter - {$directionName} - {$demande->code}";
    }

    /**
     * Vérifier si une direction a déjà validé
     */
    public function checkDirectionValidation(DemandeAutorisation $demande, string $direction): bool
    {
        $etat = $demande->etatDemande;
        if (!$etat) {
            return false;
        }

        return match ($direction) {
            'dsv' => (bool) $etat->dsv_valider,
            'dsna' => (bool) $etat->dsna_valider,
            'dsad' => (bool) $etat->dsad_valider,
            default => false,
        };
    }

    /**
     * Envoyer un rappel aux directions non répondues
     */
    public function sendReminderToUnansweredDirections(DemandeAutorisation $demande): array
    {
        $annotatedDirections = json_decode($demande->directions_annotees) ?? [];
        $results = [];

        foreach ($annotatedDirections as $direction) {
            if (!$this->checkDirectionValidation($demande, $direction)) {
                $result = $this->sendReminder($demande, $direction);
                $results[$direction] = $result;
            }
        }

        return $results;
    }

    /**
     * Envoyer un rappel à une direction
     */
    private function sendReminder(DemandeAutorisation $demande, string $direction): array
    {
        $users = User::whereHas('roles', function ($query) use ($direction) {
            $query->where('name', $direction);
        })->get();

        $results = [];
        foreach ($users as $user) {
            if (!empty($user->whatsapp)) {
                $message = $this->buildReminderMessage($demande, $direction);
                $response = $this->whatsApp->sendRichMessage($user->whatsapp, $message);
                $results[] = [
                    'user' => $user->email,
                    'type' => 'whatsapp',
                    'success' => isset($response['success']) ? $response['success'] : false
                ];
            }
        }

        return $results;
    }

    /**
     * Construire le message de rappel
     */
    private function buildReminderMessage(DemandeAutorisation $demande, string $direction): string
    {
        $directionName = $this->getDirectionName($direction);
        $daysPending = $this->getDaysPending($demande);

        return <<<MSG
        ⏰ *RAPPEL - DEMANDE EN ATTENTE* ⏰

        *Direction:* {$directionName}
        *Référence:* {$demande->code}
        *En attente depuis:* {$daysPending} jour(s)
        
        🔗 *Accéder à la demande:*
        {$this->getDirectionLink($direction, $demande->id)}

        ⚡ *Action requise:* Veuillez traiter cette demande en attente d'annotation.

        Cordialement,
        DTA
        MSG;
    }

    /**
     * Calculer le nombre de jours en attente
     */
    private function getDaysPending(DemandeAutorisation $demande): int
    {
        $updatedAt = $demande->updated_at ?? now();
        return now()->diffInDays($updatedAt);
    }
}