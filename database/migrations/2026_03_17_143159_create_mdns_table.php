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
        Schema::create('mdns', function (Blueprint $table) {
            $table->bigIncrements('id');
            $table->date('date_autorisation')->nullable();
            $table->string('numero_mdn')->nullable();
            $table->bigInteger('pays_id')->nullable();
            $table->bigInteger('demande_autorisation_id')->nullable();
            $table->string('motif')->nullable();
            $table->boolean('valider')->nullable();
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
        Schema::dropIfExists('mdns');
    }
};
