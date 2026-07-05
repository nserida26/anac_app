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
        Schema::create('etat_demande_approbations', function (Blueprint $table) {
            $table->bigIncrements('id');
            $table->boolean('compagnie_cree_demande')->default(false);
            $table->boolean('dg_annoter')->default(false);
            $table->boolean('dta_dg_annoter')->nullable()->default(false);
            $table->boolean('dg_rejeter')->default(false);
            $table->boolean('dta_annoter')->default(false);
            $table->boolean('dta_rejeter')->default(false);
            $table->boolean('service_annoter')->default(false);
            $table->boolean('service_valider')->default(false);
            $table->boolean('dta_valider')->default(false);
            $table->boolean('dg_valider')->nullable()->default(false);
            $table->boolean('dta_dg_valider')->nullable()->default(false);
            $table->unsignedBigInteger('user_id')->nullable();
            $table->unsignedBigInteger('demande_id')->nullable();
            $table->timestamps();
            $table->boolean('service_tout_valider')->nullable()->default(false);
            $table->boolean('dsv_valider')->default(false);
            $table->boolean('dsad_valider')->default(false);
            $table->boolean('dsna_valider')->default(false);
            $table->boolean('dta_notifier')->nullable()->default(false);
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('etat_demande_approbations');
    }
};
