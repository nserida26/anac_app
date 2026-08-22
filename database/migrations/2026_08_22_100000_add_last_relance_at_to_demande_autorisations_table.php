<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up()
    {
        Schema::table('demande_autorisations', function (Blueprint $table) {
            $table->timestamp('last_relance_at')->nullable()->after('date_soumission');
        });
    }

    public function down()
    {
        Schema::table('demande_autorisations', function (Blueprint $table) {
            $table->dropColumn('last_relance_at');
        });
    }
};
