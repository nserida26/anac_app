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
        Schema::create('vols', function (Blueprint $table) {
            $table->bigInteger('id', true);
            $table->string('numero_vol', 20);
            $table->unsignedBigInteger('aeroport_depart_id')->nullable();
            $table->unsignedBigInteger('aeroport_arrivee_id')->nullable();
            $table->time('date_depart')->nullable();
            $table->time('date_arrivee')->nullable();
            $table->char('demande_autorisation_id', 36)->index('compagnie_aerienne_id');
            $table->integer('nbr_passagers');
            $table->timestamp('created_at')->useCurrent();
            $table->timestamp('updated_at')->useCurrentOnUpdate()->useCurrent();
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
        Schema::dropIfExists('vols');
    }
};
