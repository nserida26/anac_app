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
        Schema::create('equipe_vols', function (Blueprint $table) {
            $table->bigIncrements('id');
            $table->unsignedBigInteger('demande_autorisation_id')->index('fk_equipe_vol');
            $table->string('nom', 50);
            $table->string('prenom', 50);
            $table->unsignedTinyInteger('age');
            $table->string('email', 100);
            $table->string('fonction', 50)->nullable();
            $table->string('licence_numero', 50)->nullable();
            $table->date('licence_expiration')->nullable();
            $table->timestamp('created_at')->nullable()->useCurrent();
            $table->timestamp('updated_at')->useCurrentOnUpdate()->nullable()->useCurrent();
            $table->string('justificatif')->nullable();
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
        Schema::dropIfExists('equipe_vols');
    }
};
