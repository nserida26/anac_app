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
        Schema::table('documents', function (Blueprint $table) {
            $table->foreign(['demande_id'], 'documents_foreign')->references(['id'])->on('demandes')->onUpdate('NO ACTION')->onDelete('CASCADE');
            $table->foreign(['demande_id'], 'fk_documents_demande')->references(['id'])->on('demandes')->onUpdate('NO ACTION')->onDelete('CASCADE');
            $table->foreign(['type_document_id'], 'fk_documents_type_document')->references(['id'])->on('type_documents')->onUpdate('NO ACTION')->onDelete('NO ACTION');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('documents', function (Blueprint $table) {
            $table->dropForeign('documents_foreign');
            $table->dropForeign('fk_documents_demande');
            $table->dropForeign('fk_documents_type_document');
        });
    }
};
