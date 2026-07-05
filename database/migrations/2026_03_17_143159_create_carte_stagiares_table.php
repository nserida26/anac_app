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
        Schema::create('carte_stagiares', function (Blueprint $table) {
            $table->integer('id', true);
            $table->string('numero_carte', 50)->unique('numero_carte');
            $table->string('np', 50);
            $table->date('date_naissance');
            $table->text('adresse')->nullable();
            $table->string('nationalite', 50);
            $table->string('photo')->nullable();
            $table->string('signature')->nullable();
            $table->date('date_deliverance');
            $table->date('date_expiration');
            $table->string('signature_dg')->nullable();
            $table->string('signature_dsv')->nullable();
            $table->string('signature_pel')->nullable();
            $table->string('cachet')->nullable();
            $table->integer('demande_id')->index('demande_id');
            $table->integer('demandeur_id')->index('demandeur_id');
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
        Schema::dropIfExists('carte_stagiares');
    }
};
