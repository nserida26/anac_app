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
        Schema::table('licences', function (Blueprint $table) {
            $table->foreign(['demandeur_id'], 'fk_demandeur_id')->references(['id'])->on('demandeurs')->onUpdate('NO ACTION')->onDelete('NO ACTION');
            $table->foreign(['demande_id'], 'fk_licences_demande_id')->references(['id'])->on('demandes')->onUpdate('NO ACTION')->onDelete('SET NULL');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('licences', function (Blueprint $table) {
            $table->dropForeign('fk_demandeur_id');
            $table->dropForeign('fk_licences_demande_id');
        });
    }
};
