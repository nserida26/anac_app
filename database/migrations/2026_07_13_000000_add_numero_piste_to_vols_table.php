<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up()
    {
        Schema::table('vols', function (Blueprint $table) {
            $table->string('numero_piste', 20)->nullable()->after('numero_vol');
        });

        DB::statement('ALTER TABLE vols MODIFY numero_vol VARCHAR(20) NULL');
    }

    public function down()
    {
        Schema::table('vols', function (Blueprint $table) {
            $table->dropColumn('numero_piste');
        });

        DB::statement("UPDATE vols SET numero_vol = '' WHERE numero_vol IS NULL");
        DB::statement('ALTER TABLE vols MODIFY numero_vol VARCHAR(20) NOT NULL');
    }
};
