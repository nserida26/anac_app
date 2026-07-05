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
        Schema::table('demande_approbations', function (Blueprint $table) {
            $table->foreign(['user_id'], 'demande_approbations_ibfk_1')->references(['id'])->on('users')->onUpdate('NO ACTION')->onDelete('CASCADE');
            $table->foreign(['compagnie_id'], 'demande_approbations_ibfk_2')->references(['id'])->on('compagnies')->onUpdate('NO ACTION')->onDelete('CASCADE');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('demande_approbations', function (Blueprint $table) {
            $table->dropForeign('demande_approbations_ibfk_1');
            $table->dropForeign('demande_approbations_ibfk_2');
        });
    }
};
