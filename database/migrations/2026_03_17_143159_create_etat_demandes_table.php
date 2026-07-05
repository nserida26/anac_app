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
        Schema::create('etat_demandes', function (Blueprint $table) {
            $table->bigIncrements('id');
            $table->boolean('demandeur_cree_demande')->nullable()->default(false);
            $table->boolean('dg_annoter')->nullable()->default(false);
            $table->boolean('dsv_dg_annoter')->nullable()->default(false);
            $table->boolean('dg_rejeter')->nullable()->default(false);
            $table->boolean('dsv_dg_rejeter')->nullable()->default(false);
            $table->boolean('dsv_annoter')->nullable()->default(false);
            $table->boolean('pel_annoter')->nullable()->default(false);
            $table->boolean('evaluateur_annoter')->nullable()->default(false);
            $table->boolean('evaluateur_valider')->nullable()->default(false);
            $table->boolean('sm_valider')->nullable()->default(false);
            $table->boolean('sl_valider')->nullable()->default(false);
            $table->boolean('pel_valider')->nullable()->default(false);
            $table->boolean('dsv_valider')->nullable()->default(false);
            $table->boolean('dg_valider')->nullable()->default(false);
            $table->boolean('dsv_dg_valider')->nullable()->default(false);
            $table->boolean('dsv_recette')->nullable()->default(false);
            $table->boolean('daf_demande_pay')->nullable()->default(false);
            $table->boolean('daf_confirme_pay')->nullable()->default(false);
            $table->boolean('demandeur_payer')->nullable()->default(false);
            $table->boolean('compagnie_payer')->nullable()->default(false);
            $table->boolean('agent_enroler')->nullable()->default(false);
            $table->boolean('pel_valider_enrol')->nullable()->default(false);
            $table->boolean('dg_signer')->nullable()->default(false);
            $table->boolean('dsv_dg_signer')->nullable()->default(false);
            $table->boolean('dsv_signer')->nullable()->default(false);
            $table->boolean('pel_dsv_signer')->nullable()->default(false);
            $table->boolean('pel_licence_valider')->nullable()->default(false);
            $table->boolean('agent_imprimer')->nullable()->default(false);
            $table->unsignedBigInteger('user_id')->nullable()->index('user_id');
            $table->unsignedBigInteger('demande_id')->nullable()->index('demande_id');
            $table->timestamps();
            $table->boolean('dsv_rejeter')->nullable()->default(false);
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('etat_demandes');
    }
};
