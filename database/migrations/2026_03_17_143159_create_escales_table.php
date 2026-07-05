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
        Schema::create('escales', function (Blueprint $table) {
            $table->bigIncrements('id');
            $table->unsignedBigInteger('vol_id');
            $table->unsignedBigInteger('aeroport_id');
            $table->time('date_arrivee');
            $table->time('date_depart');
            $table->integer('ordre');
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('escales');
    }
};
