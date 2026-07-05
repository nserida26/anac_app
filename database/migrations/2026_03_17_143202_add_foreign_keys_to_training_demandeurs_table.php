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
        Schema::table('training_demandeurs', function (Blueprint $table) {
            $table->foreign(['centre_formation_id'], 'fk_centre_formation_id_3')->references(['id'])->on('centre_formations')->onUpdate('NO ACTION')->onDelete('CASCADE');
            $table->foreign(['demande_id'], 'fk_training_demandeurs_demande')->references(['id'])->on('demandes')->onUpdate('NO ACTION')->onDelete('CASCADE');
            $table->foreign(['simulateur_id'], 'fk_training_demandeurs_simulateur_id')->references(['id'])->on('simulateurs')->onUpdate('NO ACTION')->onDelete('NO ACTION');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('training_demandeurs', function (Blueprint $table) {
            $table->dropForeign('fk_centre_formation_id_3');
            $table->dropForeign('fk_training_demandeurs_demande');
            $table->dropForeign('fk_training_demandeurs_simulateur_id');
        });
    }
};
