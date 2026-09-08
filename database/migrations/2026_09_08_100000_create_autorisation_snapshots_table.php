<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Table figée : au moment de la délivrance d'une autorisation, on y enregistre
     * les données du demandeur et de l'opérateur telles qu'elles existaient alors.
     * Toute modification ultérieure de ces entités n'affecte plus l'autorisation.
     */
    public function up()
    {
        Schema::create('autorisation_snapshots', function (Blueprint $table) {
            $table->bigIncrements('id');
            // Pas de contrainte de clé étrangère : la table `autorisations` est en
            // MyISAM (legacy) et ne supporte pas les FK. Simple index unique.
            $table->unsignedBigInteger('autorisation_id');
            $table->unique('autorisation_id');

            // Demandeur (personne / compte à l'origine de la demande)
            $table->string('demandeur_np')->nullable();
            $table->string('demandeur_email')->nullable();
            $table->string('demandeur_telephone')->nullable();
            $table->string('demandeur_adresse')->nullable();
            $table->string('demandeur_nationalite')->nullable();

            // Opérateur (exploitant de l'aéronef) — distinct du demandeur
            $table->string('operateur_nom')->nullable();
            $table->string('operateur_email')->nullable();
            $table->string('operateur_telephone')->nullable();
            $table->string('operateur_code')->nullable();

            $table->timestamps();
        });
    }

    public function down()
    {
        Schema::dropIfExists('autorisation_snapshots');
    }
};
