<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up()
    {
        Schema::table('etat_demande_autorisations', function (Blueprint $table) {
            // La DTA a renvoyé le dossier à la SRTA pour une revérification
            $table->boolean('dta_demande_reverif')->default(false)->after('srta_valider');
        });

        Schema::table('demande_autorisations', function (Blueprint $table) {
            // Motif de la demande de revérification saisi par la DTA
            $table->text('reverif_motif')->nullable()->after('dta_motif');
        });
    }

    public function down()
    {
        Schema::table('etat_demande_autorisations', function (Blueprint $table) {
            $table->dropColumn('dta_demande_reverif');
        });

        Schema::table('demande_autorisations', function (Blueprint $table) {
            $table->dropColumn('reverif_motif');
        });
    }
};
