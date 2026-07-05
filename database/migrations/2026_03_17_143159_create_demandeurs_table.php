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
        Schema::create('demandeurs', function (Blueprint $table) {
            $table->bigIncrements('id');
            $table->string('np');
            $table->string('photo')->nullable();
            $table->string('dossier')->nullable();
            $table->date('date_naissance');
            $table->string('lieu_naissance');
            $table->string('adresse');
            $table->string('nationalite');
            $table->string('adresse_employeur')->nullable();
            $table->string('signature')->nullable();
            $table->timestamps();
            $table->unsignedBigInteger('user_id')->index('fk_demandeurs_user');
            $table->unsignedBigInteger('compagnie_id')->nullable()->index('fk_demandeurs_compagnie');
            $table->boolean('valider_compagnie')->default(false);
            $table->boolean('is_examinateur')->nullable()->default(false);
            $table->boolean('is_instructeur')->nullable()->default(false);
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('demandeurs');
    }
};
