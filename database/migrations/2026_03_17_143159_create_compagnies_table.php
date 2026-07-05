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
        Schema::create('compagnies', function (Blueprint $table) {
            $table->bigIncrements('id');
            $table->string('nom_entreprise');
            $table->string('adresse');
            $table->double('panier')->nullable()->default(0);
            $table->double('plafond')->nullable();
            $table->unsignedBigInteger('user_id')->index('fk_compagnies_user');
            $table->timestamps();
            $table->string('code', 100)->nullable();
            $table->string('email', 100)->nullable();
            $table->string('telephone', 100)->nullable();
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('compagnies');
    }
};
