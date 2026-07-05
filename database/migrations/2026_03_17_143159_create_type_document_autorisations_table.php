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
        Schema::create('type_document_autorisations', function (Blueprint $table) {
            $table->integer('id', true);
            $table->integer('type_vol_id')->comment('Reference to flight type');
            $table->integer('type_demande_autorisation_id')->comment('Reference to authorization request type');
            $table->string('nom_fr', 100)->comment('French name of document type');
            $table->string('nom_en', 100)->comment('English name of document type');
            $table->timestamp('created_at')->nullable()->useCurrent();
            $table->timestamp('updated_at')->useCurrentOnUpdate()->nullable()->useCurrent();

            $table->unique(['type_vol_id', 'type_demande_autorisation_id', 'nom_fr'], 'uc_type_document');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('type_document_autorisations');
    }
};
