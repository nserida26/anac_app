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
        Schema::create('licences', function (Blueprint $table) {
            $table->bigIncrements('id');
            $table->string('categorie_licence');
            $table->string('machine_licence');
            $table->string('type_licence');
            $table->string('numero_licence');
            $table->string('np');
            $table->date('date_naissance');
            $table->text('adresse');
            $table->string('nationalite');
            $table->string('photo')->nullable();
            $table->string('signature')->nullable();
            $table->string('signature_dg');
            $table->string('signature_dsv');
            $table->string('cachet');
            $table->date('date_deliverance');
            $table->date('date_mise_a_jour')->nullable();
            $table->date('date_expiration');
            $table->unsignedBigInteger('demande_id')->nullable()->index('fk_licences_demande_id');
            $table->boolean('licence_valide')->nullable()->default(false);
            $table->boolean('licence_bloque')->nullable()->default(false);
            $table->timestamp('created_at')->nullable()->useCurrent();
            $table->timestamp('updated_at')->useCurrentOnUpdate()->nullable()->useCurrent();
            $table->unsignedBigInteger('demandeur_id')->index('fk_demandeur_id');
            $table->string('signature_pel')->nullable();
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('licences');
    }
};
