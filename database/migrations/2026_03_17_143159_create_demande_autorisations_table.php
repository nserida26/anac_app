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
        Schema::create('demande_autorisations', function (Blueprint $table) {
            $table->bigIncrements('id');
            $table->date('date_fin')->nullable();
            $table->date('date_debut')->nullable();
            $table->string('statut', 50);
            $table->text('dsna_motif')->nullable();
            $table->text('dsad_motif')->nullable();
            $table->text('dsv_motif')->nullable();
            $table->longText('directions_annotees')->nullable();
            $table->dateTime('date_soumission')->nullable();
            $table->dateTime('date_validation')->nullable();
            $table->timestamp('created_at')->useCurrent();
            $table->timestamp('updated_at')->useCurrentOnUpdate()->useCurrent();
            $table->unsignedBigInteger('type_demande_autorisation_id')->nullable()->index('demande_autorisations_type_demande_autorisation_id_foreign');
            $table->bigInteger('user_id');
            $table->text('dg_motif')->nullable();
            $table->text('dta_motif')->nullable();
            $table->tinyInteger('sous_validite')->nullable();
            $table->string('code', 200)->nullable();
            $table->bigInteger('type_vol_id')->nullable();
            $table->text('objet')->nullable();
            $table->text('points')->nullable();
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('demande_autorisations');
    }
};
