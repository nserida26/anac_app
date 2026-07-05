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
        Schema::table('etat_demandes', function (Blueprint $table) {
            $table->foreign(['user_id'], 'etat_demandes_ibfk_1')->references(['id'])->on('users')->onUpdate('NO ACTION')->onDelete('CASCADE');
            $table->foreign(['demande_id'], 'etat_demandes_ibfk_2')->references(['id'])->on('demandes')->onUpdate('NO ACTION')->onDelete('CASCADE');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('etat_demandes', function (Blueprint $table) {
            $table->dropForeign('etat_demandes_ibfk_1');
            $table->dropForeign('etat_demandes_ibfk_2');
        });
    }
};
