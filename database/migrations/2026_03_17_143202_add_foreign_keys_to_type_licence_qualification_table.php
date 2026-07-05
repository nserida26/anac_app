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
        Schema::table('type_licence_qualification', function (Blueprint $table) {
            $table->foreign(['qualification_id'], 'fk_type_licence_qualification_qualification_id')->references(['id'])->on('qualifications')->onUpdate('NO ACTION')->onDelete('CASCADE');
            $table->foreign(['type_licence_id'], 'fk_type_licence_qualification_type_licence_id')->references(['id'])->on('type_licences')->onUpdate('NO ACTION')->onDelete('CASCADE');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('type_licence_qualification', function (Blueprint $table) {
            $table->dropForeign('fk_type_licence_qualification_qualification_id');
            $table->dropForeign('fk_type_licence_qualification_type_licence_id');
        });
    }
};
