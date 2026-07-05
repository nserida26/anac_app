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
        Schema::create('checklist_demande', function (Blueprint $table) {
            $table->bigIncrements('id');
            $table->unsignedBigInteger('demande_id')->index('checklist_demande_demande_id_foreign');
            $table->unsignedBigInteger('checklist_id')->index('checklist_demande_checklist_id_foreign');
            $table->enum('etat', ['OUI', 'NON'])->nullable();
            $table->enum('mise_en_oeuvre', ['S', 'NS', 'S/O'])->nullable();
            $table->text('observations')->nullable();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('checklist_demande');
    }
};
