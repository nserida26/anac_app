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
        Schema::table('examinateurs', function (Blueprint $table) {
            $table->foreign(['centre_medical_id'], 'fk_examinateurs_centre_medical')->references(['id'])->on('centre_medicals')->onUpdate('NO ACTION')->onDelete('SET NULL');
            $table->foreign(['user_id'], 'fk_examinateurs_users')->references(['id'])->on('users')->onUpdate('NO ACTION')->onDelete('CASCADE');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('examinateurs', function (Blueprint $table) {
            $table->dropForeign('fk_examinateurs_centre_medical');
            $table->dropForeign('fk_examinateurs_users');
        });
    }
};
