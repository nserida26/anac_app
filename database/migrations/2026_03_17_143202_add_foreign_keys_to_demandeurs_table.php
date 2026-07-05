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
        Schema::table('demandeurs', function (Blueprint $table) {
            $table->foreign(['compagnie_id'], 'fk_demandeurs_compagnie')->references(['id'])->on('compagnies')->onUpdate('NO ACTION')->onDelete('SET NULL');
            $table->foreign(['user_id'], 'fk_demandeurs_user')->references(['id'])->on('users')->onUpdate('NO ACTION')->onDelete('CASCADE');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('demandeurs', function (Blueprint $table) {
            $table->dropForeign('fk_demandeurs_compagnie');
            $table->dropForeign('fk_demandeurs_user');
        });
    }
};
