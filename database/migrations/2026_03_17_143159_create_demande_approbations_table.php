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
        Schema::create('demande_approbations', function (Blueprint $table) {
            $table->integer('id', true);
            $table->string('reference', 50)->index('idx_reference');
            $table->enum('saison', ['ETE', 'HIVER'])->index('idx_saison');
            $table->unsignedBigInteger('user_id')->index('user_id');
            $table->unsignedBigInteger('compagnie_id')->index('compagnie_id');
            $table->date('date_demande')->nullable();
            $table->date('date_debut')->nullable();
            $table->date('date_fin')->nullable();
            $table->date('date_approbation')->nullable();
            $table->enum('statut', ['EN_ATTENTE', 'APPROUVEE', 'REJETEE'])->nullable()->default('EN_ATTENTE');
            $table->text('dg_motif')->nullable();
            $table->text('dta_motif')->nullable();
            $table->date('date_soumission')->nullable();
            $table->string('dsna_motif', 100)->nullable();
            $table->string('dsad_motif', 100)->nullable();
            $table->string('dsv_motif', 100)->nullable();
            $table->boolean('amender')->nullable()->default(false);

            $table->unique(['reference'], 'reference');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('demande_approbations');
    }
};
