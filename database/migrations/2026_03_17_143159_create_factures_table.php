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
        Schema::create('factures', function (Blueprint $table) {
            $table->bigInteger('id', true);
            $table->string('reference');
            $table->unsignedBigInteger('demande_id')->index('demande_id');
            $table->decimal('montant', 10);
            $table->string('facture');
            $table->enum('statut', ['Facturée', 'Confirmée', 'Annulée'])->nullable()->default('Facturée');
            $table->date('date_facture');
            $table->date('date_limite')->nullable();
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
        Schema::dropIfExists('factures');
    }
};
