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
        Schema::create('qualification_demandeurs', function (Blueprint $table) {
            $table->bigIncrements('id');
            $table->date('date_examen');
            $table->timestamps();
            $table->unsignedBigInteger('qualification_id')->index('fk_qualification_demandeurs_1');
            $table->unsignedBigInteger('centre_formation_id')->index('fk_centre_formations_id');
            $table->string('lieu');
            $table->string('document')->nullable();
            $table->boolean('valider')->nullable();
            $table->text('motif')->nullable();
            $table->unsignedBigInteger('type_avion_id')->nullable()->index('fk_type_avion');
            $table->string('type_moteur')->nullable();
            $table->string('type_privilege')->nullable();
            $table->string('machine')->nullable();
            $table->longText('amt')->nullable();
            $table->unsignedBigInteger('demande_id')->index('fk_qualification_demandeurs_demande');
            $table->string('rpa')->nullable();
            $table->longText('atc')->nullable();
            $table->string('ulm')->nullable();
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('qualification_demandeurs');
    }
};
