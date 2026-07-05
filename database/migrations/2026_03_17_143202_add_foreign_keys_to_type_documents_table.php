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
        Schema::table('type_documents', function (Blueprint $table) {
            $table->foreign(['type_licence_id'], 'type_documents_ibfk_1')->references(['id'])->on('type_licences')->onUpdate('NO ACTION')->onDelete('NO ACTION');
            $table->foreign(['type_demande_id'], 'type_documents_ibfk_2')->references(['id'])->on('type_demandes')->onUpdate('NO ACTION')->onDelete('NO ACTION');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('type_documents', function (Blueprint $table) {
            $table->dropForeign('type_documents_ibfk_1');
            $table->dropForeign('type_documents_ibfk_2');
        });
    }
};
