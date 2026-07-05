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
        Schema::create('documents', function (Blueprint $table) {
            $table->bigIncrements('id');
            $table->string('url');
            $table->boolean('valider')->nullable();
            $table->text('motif')->nullable();
            $table->timestamps();
            $table->unsignedBigInteger('demande_id')->index('documents_foreign');
            $table->integer('type_document_id')->nullable()->index('fk_documents_type_document');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('documents');
    }
};
