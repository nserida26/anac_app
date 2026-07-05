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
        Schema::table('formations', function (Blueprint $table) {
            $table->foreign(['demandeur_id'], 'formations_ibfk_1')->references(['id'])->on('demandeurs')->onUpdate('NO ACTION')->onDelete('CASCADE');
            $table->foreign(['centre_formation_id'], 'formations_ibfk_2')->references(['id'])->on('centre_formations')->onUpdate('NO ACTION')->onDelete('CASCADE');
            $table->foreign(['type_formation_id'], 'formations_ibfk_3')->references(['id'])->on('type_formations')->onUpdate('NO ACTION')->onDelete('CASCADE');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('formations', function (Blueprint $table) {
            $table->dropForeign('formations_ibfk_1');
            $table->dropForeign('formations_ibfk_2');
            $table->dropForeign('formations_ibfk_3');
        });
    }
};
