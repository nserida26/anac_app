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
        Schema::create('paiement_autorisations', function (Blueprint $table) {
            $table->bigIncrements('id');
            $table->text('reference')->nullable();
            $table->unsignedBigInteger('user_id')->nullable();
            $table->unsignedBigInteger('tarif_id')->index('tarif_id');
            $table->string('methode', 50);
            $table->decimal('montant_total', 10);
            $table->string('statut', 50);
            $table->date('date_paiement')->nullable();
            $table->timestamp('created_at')->useCurrent();
            $table->timestamp('updated_at')->useCurrentOnUpdate()->useCurrent();
            $table->string('justificatif')->nullable();
            $table->unsignedBigInteger('demande_autorisation_id')->nullable();
            $table->string('cachet_daf', 200)->nullable();
            $table->string('cachet_dg', 200)->nullable();
            $table->string('signature_daf', 200)->nullable();
            $table->string('signature_dg', 200)->nullable();
            $table->string('daf_signataire')->nullable();
            $table->string('dg_signataire')->nullable();
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('paiement_autorisations');
    }
};
