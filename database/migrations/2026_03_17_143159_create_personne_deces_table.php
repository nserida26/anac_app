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
        Schema::create('personne_deces', function (Blueprint $table) {
            $table->bigInteger('id', true);
            $table->string('nom_prenom')->nullable();
            $table->string('numero_passport', 50)->nullable();
            $table->bigInteger('demande_autorisation_id')->nullable();
            $table->string('justificatif')->nullable();
            $table->boolean('valider')->nullable();
            $table->string('motif')->nullable();
            $table->timestamp('updated_at')->nullable();
            $table->timestamp('created_at')->nullable();
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('personne_deces');
    }
};
