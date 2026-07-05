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
        Schema::create('type_licence_qualification', function (Blueprint $table) {
            $table->integer('type_licence_id');
            $table->unsignedBigInteger('qualification_id')->index('fk_type_licence_qualification_qualification_id');

           $table->primary(['type_licence_id', 'qualification_id'], 'tlq_pk');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('type_licence_qualification');
    }
};
