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
        Schema::table('compagnie_login_requests', function (Blueprint $table) {
            $table->foreign(['compagnie_user_id'], 'fk_compagnie_user')->references(['id'])->on('users')->onUpdate('NO ACTION')->onDelete('CASCADE');
            $table->foreign(['target_user_id'], 'fk_target_user')->references(['id'])->on('users')->onUpdate('NO ACTION')->onDelete('CASCADE');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('compagnie_login_requests', function (Blueprint $table) {
            $table->dropForeign('fk_compagnie_user');
            $table->dropForeign('fk_target_user');
        });
    }
};
