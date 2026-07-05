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
        Schema::create('document_autorisations', function (Blueprint $table) {
            $table->bigIncrements('id');
            $table->unsignedBigInteger('demande_autorisation_id');
            $table->string('url')->comment('Storage path/URL of the document');
            $table->timestamp('created_at')->nullable()->useCurrent();
            $table->timestamp('updated_at')->useCurrentOnUpdate()->nullable()->useCurrent();
            $table->bigInteger('type_document_id');
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
        Schema::dropIfExists('document_autorisations');
    }
};
