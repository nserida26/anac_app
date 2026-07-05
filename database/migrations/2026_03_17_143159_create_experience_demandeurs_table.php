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
        Schema::create('experience_demandeurs', function (Blueprint $table) {
            $table->bigIncrements('id');
            $table->string('nature');
            $table->integer('total');
            $table->integer('six_mois');
            $table->integer('trois_mois');
            $table->timestamps();
            $table->unsignedBigInteger('demande_id')->index('fk_experience_demandeurs_demande');
            $table->string('document')->nullable();
            $table->boolean('valider')->nullable()->default(true);
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
        Schema::dropIfExists('experience_demandeurs');
    }
};
