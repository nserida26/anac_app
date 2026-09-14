<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Opérateur représenté par la demande, choisi explicitement par le demandeur
     * (au lieu d'être deviné depuis $user->compagnie, ambigu quand un même
     * demandeur représente plusieurs opérateurs). Utile notamment pour les
     * demandes sans aéronef (type 4, dépouille mortelle) où l'opérateur ne peut
     * pas être déduit de la liste des avions.
     * Pas de contrainte FK : `demande_autorisations` est en MyISAM (legacy),
     * voir la note db-legacy-myisam-tables.
     */
    public function up()
    {
        Schema::table('demande_autorisations', function (Blueprint $table) {
            $table->unsignedBigInteger('compagnie_id')->nullable()->after('user_id');
        });
    }

    public function down()
    {
        Schema::table('demande_autorisations', function (Blueprint $table) {
            $table->dropColumn('compagnie_id');
        });
    }
};
