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
        Schema::create('medical_examinations', function (Blueprint $table) {
            $table->bigIncrements('id');
            $table->date('date_examen');
            $table->integer('validite');
            $table->timestamps();
            $table->unsignedBigInteger('centre_medical_id')->index('fk_centre_medical_id');
            $table->unsignedBigInteger('demande_id')->index('fk_medical_examinations_demande');
            $table->string('document')->nullable();
            $table->boolean('valider')->nullable();
            $table->text('motif')->nullable();
            $table->boolean('valider_evaluateur')->nullable()->default(false);
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('medical_examinations');
    }
};
