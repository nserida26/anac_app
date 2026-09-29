<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Choix du/des canaux de notification. WhatsApp reste activé par défaut (comportement
     * actuel inchangé) ; l'e-mail est un canal supplémentaire, désactivé par défaut, que le
     * demandeur active lui-même depuis son profil.
     */
    public function up()
    {
        Schema::table('users', function (Blueprint $table) {
            $table->boolean('notify_whatsapp')->default(true)->after('whatsapp');
            $table->boolean('notify_email')->default(false)->after('notify_whatsapp');
        });
    }

    public function down()
    {
        Schema::table('users', function (Blueprint $table) {
            $table->dropColumn(['notify_whatsapp', 'notify_email']);
        });
    }
};
