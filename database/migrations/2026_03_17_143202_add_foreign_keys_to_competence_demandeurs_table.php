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
        Schema::table('competence_demandeurs', function (Blueprint $table) {
            $table->foreign(['centre_formation_id'], 'fk_centre_formation_id')->references(['id'])->on('centre_formations')->onUpdate('NO ACTION')->onDelete('CASCADE');
            $table->foreign(['demande_id'], 'fk_competence_demandeurs_demande')->references(['id'])->on('demandes')->onUpdate('NO ACTION')->onDelete('CASCADE');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('competence_demandeurs', function (Blueprint $table) {
            $table->dropForeign('fk_centre_formation_id');
            $table->dropForeign('fk_competence_demandeurs_demande');
        });
    }
};
