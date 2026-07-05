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
        Schema::table('ordres_recette', function (Blueprint $table) {
            $table->foreign(['demande_id'], 'ordres_recette_ibfk_1')->references(['id'])->on('demandes')->onUpdate('NO ACTION')->onDelete('CASCADE');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('ordres_recette', function (Blueprint $table) {
            $table->dropForeign('ordres_recette_ibfk_1');
        });
    }
};
