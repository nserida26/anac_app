<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * La qualité d'examinateur désigné par l'ANAC est portée par la table
     * designations_examinateur (types de licence + période) : l'ancienne case
     * is_examinateur n'est plus lue nulle part.
     *
     * Doit passer après 2026_09_27_120100 (qui la lit une dernière fois pour
     * qualifier les formations existantes). À lancer une fois le déploiement validé.
     */
    public function up()
    {
        Schema::table('demandeurs', function (Blueprint $table) {
            $table->dropColumn('is_examinateur');
        });
    }

    /** Recrée la colonne ; les anciennes valeurs ne sont pas restaurées. */
    public function down()
    {
        Schema::table('demandeurs', function (Blueprint $table) {
            $table->boolean('is_examinateur')->nullable()->default(false)->after('dossier');
        });
    }
};
