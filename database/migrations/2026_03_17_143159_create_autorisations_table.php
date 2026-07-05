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
        Schema::create('autorisations', function (Blueprint $table) {
            $table->bigIncrements('id');
            $table->unsignedBigInteger('demande_id')->index('demande_id');
            $table->string('code_autorisation', 50)->unique('code_autorisation');
            $table->date('date_delivrance')->nullable();
            $table->date('date_expiration')->nullable();
            $table->string('statut', 50);
            $table->timestamp('created_at')->useCurrent();
            $table->timestamp('updated_at')->useCurrentOnUpdate()->useCurrent();
            $table->string('signature_dta')->nullable();
            $table->string('signature_dg')->nullable();
            $table->string('cachet')->nullable();
            $table->string('signature_srta')->nullable();
            $table->string('nom_signataire')->nullable();
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('autorisations');
    }
};
