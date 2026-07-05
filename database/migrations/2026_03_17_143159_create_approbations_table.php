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
        Schema::create('approbations', function (Blueprint $table) {
            $table->bigIncrements('id');
            $table->string('saison');
            $table->date('date_approbation');
            $table->string('reference');
            $table->date('date_debut');
            $table->date('date_fin');
            $table->unsignedBigInteger('compagnie_id');
            $table->unsignedBigInteger('demande_id');
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
        Schema::dropIfExists('approbations');
    }
};
