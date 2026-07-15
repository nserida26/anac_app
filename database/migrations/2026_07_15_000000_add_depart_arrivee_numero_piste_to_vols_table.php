<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up()
    {
        Schema::table('vols', function (Blueprint $table) {
            $table->string('numero_piste_depart', 3)->nullable()->after('numero_piste');
            $table->string('numero_piste_arrivee', 3)->nullable()->after('numero_piste_depart');
        });
    }

    public function down()
    {
        Schema::table('vols', function (Blueprint $table) {
            $table->dropColumn(['numero_piste_depart', 'numero_piste_arrivee']);
        });
    }
};
