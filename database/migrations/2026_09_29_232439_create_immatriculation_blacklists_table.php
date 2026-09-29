<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateImmatriculationBlacklistsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('immatriculation_blacklists', function (Blueprint $table) {
            $table->id();
            // Motif bloqué : préfixe ("4X") ou immatriculation exacte ("4X-ABC"),
            // comparé en majuscules — voir ImmatriculationBlacklist::isBlacklisted().
            $table->string('pattern', 20);
            $table->text('motif')->nullable();
            $table->unsignedBigInteger('created_by')->nullable();
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
        Schema::dropIfExists('immatriculation_blacklists');
    }
}
