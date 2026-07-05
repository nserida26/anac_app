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
        Schema::table('licence_demandeurs', function (Blueprint $table) {
            $table->foreign(['autorite_id'], 'fk_licence_demandeurs_autorite_id')->references(['id'])->on('autorites')->onUpdate('NO ACTION')->onDelete('CASCADE');
            $table->foreign(['demande_id'], 'fk_licence_demandeurs_demande_id')->references(['id'])->on('demandes')->onUpdate('NO ACTION')->onDelete('CASCADE');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('licence_demandeurs', function (Blueprint $table) {
            $table->dropForeign('fk_licence_demandeurs_autorite_id');
            $table->dropForeign('fk_licence_demandeurs_demande_id');
        });
    }
};
