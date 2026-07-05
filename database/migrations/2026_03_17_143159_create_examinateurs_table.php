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
        Schema::create('examinateurs', function (Blueprint $table) {
            $table->bigIncrements('id');
            $table->string('np');
            $table->timestamps();
            $table->unsignedBigInteger('user_id')->index('fk_examinateurs_users');
            $table->unsignedBigInteger('centre_medical_id')->nullable()->index('fk_examinateurs_centre_medical');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('examinateurs');
    }
};
