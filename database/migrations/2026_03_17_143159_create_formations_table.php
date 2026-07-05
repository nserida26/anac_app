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
        Schema::create('formations', function (Blueprint $table) {
            $table->bigIncrements('id');
            $table->string('attestation');
            $table->unsignedBigInteger('demandeur_id')->index('demandeur_id');
            $table->unsignedBigInteger('centre_formation_id')->index('centre_formation_id');
            $table->unsignedBigInteger('type_formation_id')->index('type_formation_id');
            $table->string('lieu')->nullable();
            $table->date('date_formation');
            $table->timestamp('created_at')->useCurrent();
            $table->timestamp('updated_at')->useCurrentOnUpdate()->useCurrent();
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('formations');
    }
};
