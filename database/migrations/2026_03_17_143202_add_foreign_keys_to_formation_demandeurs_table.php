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
        Schema::table('formation_demandeurs', function (Blueprint $table) {
            $table->foreign(['centre_formation_id'], 'fk_formations_centre_formation')->references(['id'])->on('centre_formations')->onUpdate('NO ACTION')->onDelete('CASCADE');
            $table->foreign(['demande_id'], 'fk_formation_demandeurs_demande')->references(['id'])->on('demandes')->onUpdate('NO ACTION')->onDelete('CASCADE');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('formation_demandeurs', function (Blueprint $table) {
            $table->dropForeign('fk_formations_centre_formation');
            $table->dropForeign('fk_formation_demandeurs_demande');
        });
    }
};
