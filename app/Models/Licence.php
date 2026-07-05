<?php
// app/Models/Licence.php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Mail;
use App\Mail\LicenceExpiryNotification;
use Carbon\Carbon;

class Licence extends Model
{
    use HasFactory;

    protected $fillable = [
        'type_calcul',
        'jours_supplementaires',
        'categorie_licence',
        'machine_licence',
        'type_licence',
        'numero_licence',
        'np',
        'date_naissance',
        'adresse',
        'nationalite',
        'photo',
        'signature',
        'date_deliverance',
        'date_mise_a_jour',
        'date_expiration',
        'licence_valide',
        'licence_bloque',
        'signature_dg',
        'signature_dsv',
        'signature_pel',
        'cachet',
        'demande_id',
        'demandeur_id'
    ];

    protected $casts = [
        'date_naissance' => 'date',
        'date_deliverance' => 'date',
        'date_mise_a_jour' => 'date',
        'date_expiration' => 'date',
        'machine_licence' => 'string',
    ];

    // Relationships
    public function demande()
    {
        return $this->belongsTo(Demande::class, 'demande_id');
    }

    public function demandeur()
    {
        return $this->belongsTo(Demandeur::class, 'demandeur_id');
    }

    public function getExpiryStatusAttribute(): array
    {
        $days = now()->diffInDays($this->date_expiration);
        if ($this->isBlocked()) {
            return ['key' => 'blocked'];
        }
        if ($this->isExpired()) {
            return ['key' => 'expired'];
        }
        if ($this->isNearingExpiry(15)) { // Changed to 15 days
            return ['key' => 'expiring_soon', 'days' => $days];
        }
        return ['key' => 'valid', 'days' => $days];
    }

    // Status Checkers
    public function isValid(): bool
    {
        return $this->licence_valide && !$this->licence_bloque && !$this->isExpired();
    }

    public function isExpired(): bool
    {
        return now()->greaterThan($this->date_expiration);
    }

    public function isBlocked(): bool
    {
        return $this->licence_bloque;
    }

    public function isNearingExpiry(int $days = 15): bool
    {
        return !$this->isExpired() && now()->diffInDays($this->date_expiration) <= $days;
    }

    // State Changers
    public function markAsExpired(): void
    {
        $this->update([
            'licence_valide' => false,
            'licence_bloque' => true,
            'date_mise_a_jour' => now()
        ]);
    }

    public function renew(\DateTimeInterface $newExpiryDate): void
    {
        $this->update([
            'licence_valide' => true,
            'licence_bloque' => false,
            'date_expiration' => $newExpiryDate,
            'date_mise_a_jour' => now()
        ]);
    }

    // Scopes
    public function scopeValid($query)
    {
        return $query->where('licence_valide', true)
            ->where('licence_bloque', false)
            ->where('date_expiration', '>', now());
    }

    public function scopeExpired($query)
    {
        return $query->where(function ($q) {
            $q->where('licence_valide', false)
                ->orWhere('licence_bloque', true)
                ->orWhere('date_expiration', '<=', now());
        });
    }

    public function scopeExpiringSoon($query, int $days = 15)
    {
        return $query->where('licence_valide', true)
            ->where('licence_bloque', false)
            ->whereBetween('date_expiration', [now(), now()->addDays($days)]);
    }
    
    // New scope for filtering by license type
    public function scopeByType($query, $type)
    {
        if ($type && $type !== 'all') {
            return $query->where('type_licence', $type);
        }
        return $query;
    }
    
    // Get distinct license types for filter dropdown
    public static function getDistinctTypes()
    {
        return self::select('type_licence')
            ->distinct()
            ->whereNotNull('type_licence')
            ->pluck('type_licence');
    }
    
    // Send WhatsApp notification
    public function sendExpiryNotification()
    {
        if (!$this->demandeur) {
            return false;
        }
        
        $daysLeft = now()->diffInDays($this->date_expiration);
        
        // Send to demandeur (applicant)
        $this->sendWhatsAppToDemandeur($daysLeft);
        
        // Send to operateur (company if exists)
        if ($this->demandeur->compagnie) {
            $this->sendWhatsAppToOperateur($daysLeft);
        }
        
        return true;
    }
    
    protected function sendWhatsAppToDemandeur($daysLeft)
    {
        $phone = $this->demandeur->user->whatsapp;
        
        if (!$phone) {
            return false;
        }
        
        
        $message = $this->buildExpiryMessage($daysLeft, $this->demandeur->np);
        
        // Use your WhatsApp service
        $whatsappService = app(\App\Services\WhatsAppService::class);
        return $whatsappService->sendRichMessage($phone, $message);
    }
    
    protected function sendWhatsAppToOperateur($daysLeft)
    {
        $compagnie = $this->demandeur->compagnie;
        $phone = $compagnie->user->whatsapp;
        
        if (!$phone) {
            return false;
        }
        
        $message = $this->buildExpiryMessageForOperateur($daysLeft, $compagnie->nom_entreprise);
        
        $whatsappService = app(\App\Services\WhatsAppService::class);
        return $whatsappService->sendRichMessage($phone, $message);
    }
    
    protected function buildExpiryMessage($daysLeft, $recipientName)
    {
        $locale = app()->getLocale();
        
        if ($locale === 'fr') {
            return "⚠️ *AVIS D'EXPIRATION DE LICENCE* ⚠️\n\n" .
                "Cher/Chère *{$recipientName}*,\n\n" .
                "Votre licence *{$this->numero_licence}* ({$this->type_licence}) arrivera à expiration dans *{$daysLeft} jour(s)*.\n\n" .
                "📅 *Date d'expiration:* " . $this->date_expiration->format('d/m/Y') . "\n" .
                "🏷️ *Type:* {$this->type_licence}\n" .
                "📋 *Catégorie:* {$this->categorie_licence}\n\n" .
                "Veuillez procéder au renouvellement de votre licence avant la date d'expiration pour éviter toute interruption.\n\n" .
                "🔗 *Lien de renouvellement:* " . route('login') . "\n\n" .
                "🏛️ *ANAC Mauritanie*";
        }
        
        return "⚠️ *LICENSE EXPIRY NOTICE* ⚠️\n\n" .
            "Dear *{$recipientName}*,\n\n" .
            "Your license *{$this->numero_licence}* ({$this->type_licence}) will expire in *{$daysLeft} day(s)*.\n\n" .
            "📅 *Expiry Date:* " . $this->date_expiration->format('d/m/Y') . "\n" .
            "🏷️ *Type:* {$this->type_licence}\n" .
            "📋 *Category:* {$this->categorie_licence}\n\n" .
            "Please renew your license before the expiry date to avoid any interruption.\n\n" .
            "🔗 *Renewal Link:* " .route('login') . "\n\n" .
            "🏛️ *ANAC Mauritania*";
    }
    
    protected function buildExpiryMessageForOperateur($daysLeft, $operateurName)
    {
        $locale = app()->getLocale();
        $demandeurName = $this->demandeur->np;
        
        if ($locale === 'fr') {
            return "⚠️ *AVIS D'EXPIRATION DE LICENCE* ⚠️\n\n" .
                "Cher/Chère *{$operateurName}*,\n\n" .
                "La licence de votre agent *{$demandeurName}* ({$this->numero_licence} - {$this->type_licence}) expirera dans *{$daysLeft} jour(s)*.\n\n" .
                "📅 *Date d'expiration:* " . $this->date_expiration->format('d/m/Y') . "\n" .
                "👤 *Agent:* {$demandeurName}\n" .
                "🏷️ *Type:* {$this->type_licence}\n\n" .
                "Veuillez rappeler à votre agent de renouveler sa licence avant la date d'expiration.\n\n" .
                "🏛️ *ANAC Mauritanie*";
        }
        
        return "⚠️ *LICENSE EXPIRY NOTICE* ⚠️\n\n" .
            "Dear *{$operateurName}*,\n\n" .
            "Your agent *{$demandeurName}*'s license ({$this->numero_licence} - {$this->type_licence}) will expire in *{$daysLeft} day(s)*.\n\n" .
            "📅 *Expiry Date:* " . $this->date_expiration->format('d/m/Y') . "\n" .
            "👤 *Agent:* {$demandeurName}\n" .
            "🏷️ *Type:* {$this->type_licence}\n\n" .
            "Please remind your agent to renew their license before the expiry date.\n\n" .
            "🏛️ *ANAC Mauritania*";
    }
}