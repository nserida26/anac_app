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
        Schema::table('employeur_demandeurs', function (Blueprint $table) {
            $table->foreign(['demande_id'], 'employeur_demandeurs_foreign')->references(['id'])->on('demandes')->onUpdate('NO ACTION')->onDelete('CASCADE');
            $table->foreign(['demande_id'], 'fk_employeur_demandeurs_demande')->references(['id'])->on('demandes')->onUpdate('NO ACTION')->onDelete('CASCADE');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('employeur_demandeurs', function (Blueprint $table) {
            $table->dropForeign('employeur_demandeurs_foreign');
            $table->dropForeign('fk_employeur_demandeurs_demande');
        });
    }
};
