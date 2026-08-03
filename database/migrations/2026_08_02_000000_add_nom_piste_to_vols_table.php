<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up()
    {
        Schema::table('vols', function (Blueprint $table) {
            $table->string('nom_piste_depart')->nullable()->after('numero_piste_arrivee');
            $table->string('nom_piste_arrivee')->nullable()->after('nom_piste_depart');
        });
    }

    public function down()
    {
        Schema::table('vols', function (Blueprint $table) {
            $table->dropColumn(['nom_piste_depart', 'nom_piste_arrivee']);
        });
    }
};
