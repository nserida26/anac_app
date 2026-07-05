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
        Schema::create('validation_licences', function (Blueprint $table) {
            $table->integer('id', true);
            $table->integer('demande_id');
            $table->integer('type_licence_id');
            $table->integer('compagnie_id')->nullable();
            $table->string('numero_validation', 50);
            $table->string('num_licence', 50);
            $table->date('date_delivrance_licence');
            $table->string('lieu_delivrance_licence', 100);
            $table->string('type_appareil', 50);
            $table->string('immatriculation_appareil', 20);
            $table->date('date_debut_validite');
            $table->date('date_fin_validite');
            $table->date('date_emission');
            $table->text('restrictions')->nullable();
            $table->boolean('is_active')->nullable()->default(true);
            $table->string('signataire_nom', 100)->nullable()->default('Abba SIDI MHAMED');
            $table->string('signataire_titre', 100)->nullable()->default('Directeur de la Sécurité des Vols');
            $table->timestamps();
            $table->string('signature_path', 100);
            $table->string('cachet_path', 100);
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('validation_licences');
    }
};
