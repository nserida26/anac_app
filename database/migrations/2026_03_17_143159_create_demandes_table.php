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
        Schema::create('demandes', function (Blueprint $table) {
            $table->bigIncrements('id');
            $table->unsignedBigInteger('demandeur_id')->index('demandes_demandeur_id_foreign');
            $table->string('code')->unique('demandes_unique_code');
            $table->date('date');
            $table->string('description');
            $table->string('checklist_sla')->nullable();
            $table->string('checklist_sma')->nullable();
            $table->string('checklist_admin')->nullable();
            $table->string('signature')->nullable();
            $table->string('nom_responsable');
            $table->boolean('mise_a_jour')->nullable()->default(false);
            $table->string('status')->nullable();
            $table->timestamps();
            $table->integer('type_demande_id')->nullable()->index('fk_demandes_type_demande_id');
            $table->integer('type_licence_id')->nullable()->index('fk_demandes_type_licence_id');
            $table->unsignedBigInteger('evaluateur_id')->nullable()->index('fk_demandes_evaluateur_id');
            $table->text('motif_dg')->nullable();
            $table->text('motif_dsv')->nullable();
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('demandes');
    }
};
