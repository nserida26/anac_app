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
        Schema::table('qualification_demandeurs', function (Blueprint $table) {
            $table->foreign(['centre_formation_id'], 'fk_centre_formations_id')->references(['id'])->on('centre_formations')->onUpdate('NO ACTION')->onDelete('CASCADE');
            $table->foreign(['qualification_id'], 'fk_qualification_demandeurs_1')->references(['id'])->on('qualifications')->onUpdate('NO ACTION')->onDelete('CASCADE');
            $table->foreign(['demande_id'], 'fk_qualification_demandeurs_demande')->references(['id'])->on('demandes')->onUpdate('NO ACTION')->onDelete('CASCADE');
            $table->foreign(['type_avion_id'], 'fk_type_avion')->references(['id'])->on('type_avions')->onUpdate('NO ACTION')->onDelete('SET NULL');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('qualification_demandeurs', function (Blueprint $table) {
            $table->dropForeign('fk_centre_formations_id');
            $table->dropForeign('fk_qualification_demandeurs_1');
            $table->dropForeign('fk_qualification_demandeurs_demande');
            $table->dropForeign('fk_type_avion');
        });
    }
};
