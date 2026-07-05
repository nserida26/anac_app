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
        Schema::create('examens_medicaux', function (Blueprint $table) {
            $table->bigInteger('id', true);
            $table->unsignedBigInteger('demandeur_id')->index('demandeur_id');
            $table->unsignedBigInteger('examinateur_id')->index('examinateur_id');
            $table->bigInteger('evaluateur_id')->nullable();
            $table->date('date_examen');
            $table->bigInteger('validite');
            $table->bigInteger('validite_evaluateur')->default(0);
            $table->enum('aptitude', ['Apte', 'Inapte']);
            $table->string('attestation')->nullable();
            $table->text('rapport')->nullable();
            $table->text('rapport_evaluateur')->nullable();
            $table->boolean('valider_examinateur')->nullable()->default(false);
            $table->boolean('valider_evaluateur')->nullable()->default(false);
            $table->boolean('valider_sma')->nullable()->default(false);
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
        Schema::dropIfExists('examens_medicaux');
    }
};
