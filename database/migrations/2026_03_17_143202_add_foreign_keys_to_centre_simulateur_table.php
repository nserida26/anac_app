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
        Schema::table('centre_simulateur', function (Blueprint $table) {
            $table->foreign(['centre_formation_id'], 'fk_centre_formation')->references(['id'])->on('centre_formations')->onUpdate('NO ACTION')->onDelete('CASCADE');
            $table->foreign(['simulateur_id'], 'fk_simulateur')->references(['id'])->on('simulateurs')->onUpdate('NO ACTION')->onDelete('CASCADE');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('centre_simulateur', function (Blueprint $table) {
            $table->dropForeign('fk_centre_formation');
            $table->dropForeign('fk_simulateur');
        });
    }
};
