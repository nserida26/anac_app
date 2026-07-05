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
        Schema::create('itineraire_vols', function (Blueprint $table) {
            $table->bigIncrements('id');
            $table->unsignedBigInteger('demande_approbation_id');
            $table->unsignedBigInteger('vol_id');
            $table->unsignedBigInteger('aeroport_id');
            $table->time('heure_depart');
            $table->time('heure_arrivee');
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
        Schema::dropIfExists('itineraire_vols');
    }
};
