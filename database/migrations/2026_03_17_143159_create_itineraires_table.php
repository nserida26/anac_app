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
        Schema::create('itineraires', function (Blueprint $table) {
            $table->bigIncrements('id');
            $table->unsignedBigInteger('demande_autorisation_id')->index('vol_id');
            $table->unsignedBigInteger('aeroport_id')->nullable()->index('itineraires_aeroport_id_foreign');
            $table->timestamp('created_at')->useCurrent();
            $table->timestamp('updated_at')->useCurrentOnUpdate()->useCurrent();
            $table->time('date_depart')->nullable();
            $table->time('date_arrivee')->nullable();
            $table->boolean('valider')->nullable();
            $table->text('motif')->nullable();
            $table->bigInteger('vol_id')->nullable()->index('itineraires_vol_id_foreign');
            $table->integer('order_aero')->nullable()->default(0);
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('itineraires');
    }
};
