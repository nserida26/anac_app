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
        Schema::create('experience_maintenance_demandeurs', function (Blueprint $table) {
            $table->bigInteger('id', true);
            $table->text('description_maintenance');
            $table->date('date_debut');
            $table->date('date_fin');
            $table->timestamp('created_at')->useCurrent();
            $table->timestamp('updated_at')->useCurrentOnUpdate()->useCurrent();
            $table->unsignedBigInteger('demande_id')->index('fk_experience_maintenance_demandeurs_demande');
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
        Schema::dropIfExists('experience_maintenance_demandeurs');
    }
};
