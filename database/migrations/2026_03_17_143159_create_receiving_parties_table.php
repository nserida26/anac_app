<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('receiving_parties', function (Blueprint $table) {
            $table->bigIncrements('id');
            $table->string('nom_contact', 100)->comment('Nom et prénoms du contact');
            $table->string('telephone_contact', 20)->comment('Numéro de téléphone');
            $table->string('email_contact', 100)->nullable()->comment('Adresse email');
            $table->string('fonction_contact', 100)->nullable()->comment('Fonction du contact');
            $table->text('autres_renseignements')->nullable()->comment('Informations supplémentaires');
            $table->string('piece_identite_path')->nullable()->comment('Chemin vers le fichier de pièce d\'identité');
            $table->timestamp('created_at')->nullable()->useCurrent();
            $table->timestamp('updated_at')->useCurrentOnUpdate()->nullable()->useCurrent();
            $table->integer('demande_autorisation_id')->nullable()->comment('Référence au vol associé');
            $table->integer('compagnie_id')->nullable()->comment('Référence à la compagnie');
            $table->boolean('valider')->nullable();
            $table->text('motif')->nullable();
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('receiving_parties');
    }
};
