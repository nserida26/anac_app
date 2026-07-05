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
        Schema::create('competence_demandeurs', function (Blueprint $table) {
            $table->bigIncrements('id');
            $table->string('type');
            $table->date('date');
            $table->integer('validite');
            $table->integer('niveau')->nullable();
            $table->timestamps();
            $table->unsignedBigInteger('centre_formation_id')->index('fk_centre_formation_id');
            $table->unsignedBigInteger('demande_id')->index('fk_competence_demandeurs_demande');
            $table->string('document')->nullable();
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
        Schema::dropIfExists('competence_demandeurs');
    }
};
