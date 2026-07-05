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
        Schema::table('interruption_demandeurs', function (Blueprint $table) {
            $table->foreign(['demande_id'], 'interruption_demandeurs_foreign')->references(['id'])->on('demandes')->onUpdate('NO ACTION')->onDelete('CASCADE');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('interruption_demandeurs', function (Blueprint $table) {
            $table->dropForeign('interruption_demandeurs_foreign');
        });
    }
};
