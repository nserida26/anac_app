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
        Schema::create('ordres_recette', function (Blueprint $table) {
            $table->bigInteger('id', true);
            $table->string('reference');
            $table->unsignedBigInteger('demande_id')->index('demande_id');
            $table->decimal('montant', 10);
            $table->date('date_ordre');
            $table->string('ordre');
            $table->enum('statut', ['Généré', 'Validé', 'Rejeté'])->nullable()->default('Généré');
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
        Schema::dropIfExists('ordres_recette');
    }
};
