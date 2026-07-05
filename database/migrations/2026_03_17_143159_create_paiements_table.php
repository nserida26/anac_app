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
        Schema::create('paiements', function (Blueprint $table) {
            $table->bigInteger('id', true);
            $table->string('reference');
            $table->unsignedBigInteger('demande_id')->index('demande_id');
            $table->decimal('montant', 10);
            $table->enum('statut', ['En attente', 'Réglée', 'Payé', 'Annulé'])->nullable()->default('En attente');
            $table->date('date_paiement')->nullable();
            $table->string('quittance')->nullable();
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
        Schema::dropIfExists('paiements');
    }
};
