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
        Schema::table('medical_examinations', function (Blueprint $table) {
            $table->foreign(['centre_medical_id'], 'fk_centre_medical_id')->references(['id'])->on('centre_medicals')->onUpdate('NO ACTION')->onDelete('CASCADE');
            $table->foreign(['demande_id'], 'fk_medical_examinations_demande')->references(['id'])->on('demandes')->onUpdate('NO ACTION')->onDelete('CASCADE');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('medical_examinations', function (Blueprint $table) {
            $table->dropForeign('fk_centre_medical_id');
            $table->dropForeign('fk_medical_examinations_demande');
        });
    }
};
