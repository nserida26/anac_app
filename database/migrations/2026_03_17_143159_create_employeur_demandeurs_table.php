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
        Schema::create('employeur_demandeurs', function (Blueprint $table) {
            $table->bigInteger('id', true);
            $table->string('employeur');
            $table->date('periode_du');
            $table->date('periode_au');
            $table->string('fonction');
            $table->timestamp('created_at')->useCurrent();
            $table->timestamp('updated_at')->useCurrentOnUpdate()->useCurrent();
            $table->unsignedBigInteger('demande_id')->index('employeur_demandeurs_foreign');
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
        Schema::dropIfExists('employeur_demandeurs');
    }
};
