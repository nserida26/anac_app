<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Un centre d'expertise médicale certifié (ex. CEMPA) dispose d'un compte :
     * on le rattache à sa ligne de la liste centre_medicals, déjà utilisée par
     * les visites médicales déclarées dans les demandes de licence.
     */
    public function up()
    {
        Schema::table('centre_medicals', function (Blueprint $table) {
            $table->unsignedBigInteger('user_id')->nullable()->unique()->after('libelle');
            $table->foreign('user_id')->references('id')->on('users')->nullOnDelete();
        });
    }

    public function down()
    {
        Schema::table('centre_medicals', function (Blueprint $table) {
            $table->dropForeign(['user_id']);
            $table->dropUnique(['user_id']);
            $table->dropColumn('user_id');
        });
    }
};
