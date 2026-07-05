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
        Schema::create('training_demandeurs', function (Blueprint $table) {
            $table->bigIncrements('id');
            $table->string('type');
            $table->date('date');
            $table->integer('validite');
            $table->timestamps();
            $table->unsignedBigInteger('centre_formation_id')->index('fk_centre_formation_id_3');
            $table->unsignedBigInteger('demande_id')->index('fk_training_demandeurs_demande');
            $table->string('document')->nullable();
            $table->boolean('valider')->nullable();
            $table->text('motif')->nullable();
            $table->unsignedBigInteger('simulateur_id')->nullable()->index('fk_training_demandeurs_simulateur_id');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('training_demandeurs');
    }
};
