<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up()
    {
        Schema::table('etat_demande_autorisations', function (Blueprint $table) {
            $table->boolean('srta_valider')->default(false)->after('service_valider');
        });
    }

    public function down()
    {
        Schema::table('etat_demande_autorisations', function (Blueprint $table) {
            $table->dropColumn('srta_valider');
        });
    }
};
