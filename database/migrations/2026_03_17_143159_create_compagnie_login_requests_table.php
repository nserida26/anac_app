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
        Schema::create('compagnie_login_requests', function (Blueprint $table) {
            $table->bigIncrements('id');
            $table->unsignedBigInteger('compagnie_user_id')->index('fk_compagnie_user');
            $table->unsignedBigInteger('target_user_id')->index('fk_target_user');
            $table->string('token')->unique('token');
            $table->timestamp('expires_at')->useCurrentOnUpdate()->useCurrent();
            $table->boolean('accepted')->default(false);
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('compagnie_login_requests');
    }
};
