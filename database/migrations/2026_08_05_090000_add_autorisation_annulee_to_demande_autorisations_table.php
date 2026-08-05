<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up()
    {
        Schema::table('demande_autorisations', function (Blueprint $table) {
            $table->string('autorisation_annulee', 100)->nullable()->after('objet');
        });
    }

    public function down()
    {
        Schema::table('demande_autorisations', function (Blueprint $table) {
            $table->dropColumn('autorisation_annulee');
        });
    }
};
