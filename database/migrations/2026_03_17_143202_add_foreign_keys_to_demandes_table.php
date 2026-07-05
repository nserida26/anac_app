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
        Schema::table('demandes', function (Blueprint $table) {
            $table->foreign(['demandeur_id'])->references(['id'])->on('demandeurs')->onUpdate('NO ACTION')->onDelete('CASCADE');
            $table->foreign(['evaluateur_id'], 'fk_demandes_evaluateur_id')->references(['id'])->on('users')->onUpdate('NO ACTION')->onDelete('CASCADE');
            $table->foreign(['type_demande_id'], 'fk_demandes_type_demande_id')->references(['id'])->on('type_demandes')->onUpdate('CASCADE')->onDelete('CASCADE');
            $table->foreign(['type_licence_id'], 'fk_demandes_type_licence_id')->references(['id'])->on('type_licences')->onUpdate('CASCADE')->onDelete('CASCADE');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('demandes', function (Blueprint $table) {
            $table->dropForeign('demandes_demandeur_id_foreign');
            $table->dropForeign('fk_demandes_evaluateur_id');
            $table->dropForeign('fk_demandes_type_demande_id');
            $table->dropForeign('fk_demandes_type_licence_id');
        });
    }
};
