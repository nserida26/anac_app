<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Rapport médical (rapport + attestation d'une visite) :
     * - envoyé par un examinateur individuel, ou par un centre d'expertise
     *   médicale pour l'un de ses examinateurs validés (centre_medical_id) ;
     * - l'évaluateur valide, émet une réserve ou une suggestion, et peut
     *   réduire la validité, avant la validation de la SMA.
     */
    public function up()
    {
        Schema::table('examens_medicaux', function (Blueprint $table) {
            $table->foreignId('centre_medical_id')->nullable()->after('examinateur_id')
                ->constrained('centre_medicals')->nullOnDelete();
            $table->foreignId('soumis_par')->nullable()->after('centre_medical_id')
                ->constrained('users')->nullOnDelete();
            $table->enum('avis_evaluateur', ['valide', 'reserve', 'suggestion'])->nullable()->after('validite_evaluateur');
            $table->text('observations_evaluateur')->nullable()->after('avis_evaluateur');
        });
    }

    public function down()
    {
        Schema::table('examens_medicaux', function (Blueprint $table) {
            $table->dropForeign(['centre_medical_id']);
            $table->dropForeign(['soumis_par']);
            $table->dropColumn(['centre_medical_id', 'soumis_par', 'avis_evaluateur', 'observations_evaluateur']);
        });
    }
};
