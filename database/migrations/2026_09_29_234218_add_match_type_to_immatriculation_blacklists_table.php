<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AddMatchTypeToImmatriculationBlacklistsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('immatriculation_blacklists', function (Blueprint $table) {
            // 'prefix' : bloque tout ce qui commence par le motif (ex. "4X").
            // 'exact'  : bloque uniquement cette immatriculation précise (ex. "4X-ABC").
            $table->enum('match_type', ['prefix', 'exact'])->default('prefix')->after('pattern');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('immatriculation_blacklists', function (Blueprint $table) {
            $table->dropColumn('match_type');
        });
    }
}
