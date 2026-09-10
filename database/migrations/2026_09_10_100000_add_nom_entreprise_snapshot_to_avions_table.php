<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Fige le nom de l'opérateur (compagnie) au moment où l'aéronef est rattaché
     * à une demande d'autorisation, pour que renommer une compagnie n'altère pas
     * l'historique des demandes déjà déposées. Voir Avion::booted().
     */
    public function up()
    {
        Schema::table('avions', function (Blueprint $table) {
            $table->string('nom_entreprise_snapshot')->nullable()->after('compagnie_aerienne_id');
        });
    }

    public function down()
    {
        Schema::table('avions', function (Blueprint $table) {
            $table->dropColumn('nom_entreprise_snapshot');
        });
    }
};
