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
        Schema::create('avions', function (Blueprint $table) {
            $table->bigInteger('id', true);
            $table->string('immatriculation', 20);
            $table->unsignedBigInteger('compagnie_aerienne_id')->index('compagnie_aerienne_id');
            $table->unsignedBigInteger('type_avion_id')->index('type_avion_id');
            $table->timestamp('created_at')->useCurrent();
            $table->timestamp('updated_at')->useCurrentOnUpdate()->useCurrent();
            $table->unsignedBigInteger('proprietaire_id')->nullable()->index('fk1_avions_proprietaire_id');
            $table->bigInteger('demande_autorisation_id')->nullable()->default(0);
            $table->boolean('valider')->nullable();
            $table->text('motif')->nullable();
            $table->unsignedBigInteger('demande_approbation_id')->nullable();
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('avions');
    }
};
