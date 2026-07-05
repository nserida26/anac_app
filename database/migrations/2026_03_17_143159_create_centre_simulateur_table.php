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
        Schema::create('centre_simulateur', function (Blueprint $table) {
            $table->bigInteger('id', true);
            $table->unsignedBigInteger('centre_formation_id')->index('fk_centre_formation');
            $table->unsignedBigInteger('simulateur_id')->index('fk_simulateur');
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
        Schema::dropIfExists('centre_simulateur');
    }
};
