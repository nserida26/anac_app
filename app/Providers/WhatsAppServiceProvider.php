<?php
// app/Providers/WhatsAppServiceProvider.php
namespace App\Providers;

use Illuminate\Support\ServiceProvider;
use App\Services\WhatsApp\WhatsAppInterface;
use App\Services\WhatsApp\Providers\UltramsgService;

class WhatsAppServiceProvider extends ServiceProvider
{
    public function register()
    {
        $this->app->bind(WhatsAppInterface::class, UltramsgService::class);

        // Alternative more explicit binding:
        $this->app->bind(WhatsAppInterface::class, function ($app) {
            return new UltramsgService();
        });
    }
}
