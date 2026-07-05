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
        Schema::create('interruption_demandeurs', function (Blueprint $table) {
            $table->bigInteger('id', true);
            $table->date('date_debut');
            $table->date('date_fin');
            $table->text('raison');
            $table->timestamp('created_at')->useCurrent();
            $table->timestamp('updated_at')->useCurrentOnUpdate()->useCurrent();
            $table->unsignedBigInteger('demande_id')->index('interruption_demandeurs_foreign');
            $table->string('document')->nullable();
            $table->boolean('valider')->nullable();
            $table->text('motif')->nullable();
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('interruption_demandeurs');
    }
};
