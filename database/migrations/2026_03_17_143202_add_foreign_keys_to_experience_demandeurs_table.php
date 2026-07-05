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
        Schema::table('experience_demandeurs', function (Blueprint $table) {
            $table->foreign(['demande_id'], 'fk_experience_demandeurs_demande')->references(['id'])->on('demandes')->onUpdate('NO ACTION')->onDelete('CASCADE');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('experience_demandeurs', function (Blueprint $table) {
            $table->dropForeign('fk_experience_demandeurs_demande');
        });
    }
};
