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
        Schema::create('vol_approbations', function (Blueprint $table) {
            $table->bigIncrements('id');
            $table->string('numero_vol');
            $table->unsignedBigInteger('aeroport_depart_id');
            $table->unsignedBigInteger('aeroport_arrivee_id');
            $table->time('heure_depart');
            $table->time('heure_arrivee');
            $table->date('date_debut');
            $table->date('date_fin');
            $table->unsignedBigInteger('demande_approbation_id');
            $table->longText('jours_operation');
            $table->timestamps();
            $table->text('motif')->nullable();
            $table->boolean('valider')->nullable()->default(true);
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('vol_approbations');
    }
};
