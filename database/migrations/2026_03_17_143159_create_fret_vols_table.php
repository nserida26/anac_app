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
        Schema::create('fret_vols', function (Blueprint $table) {
            $table->bigIncrements('id');
            $table->unsignedBigInteger('demande_autorisation_id');
            $table->string('nature', 50)->comment('Normal, Dangereux, Perissable, Vivant');
            $table->decimal('poids', 10)->comment('Poids en kilogrammes');
            $table->text('instructions_speciales')->nullable();
            $table->timestamp('created_at')->nullable()->useCurrent();
            $table->timestamp('updated_at')->useCurrentOnUpdate()->nullable()->useCurrent();
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
        Schema::dropIfExists('fret_vols');
    }
};
