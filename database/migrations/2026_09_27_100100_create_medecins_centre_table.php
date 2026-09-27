<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Médecins déclarés par un centre d'expertise médicale sur son compte
     * (l'équivalent des instructeurs d'un centre de formation).
     */
    public function up()
    {
        Schema::create('medecins_centre', function (Blueprint $table) {
            $table->engine = 'InnoDB';
            $table->id();
            $table->foreignId('centre_medical_id')->constrained('centre_medicals')->cascadeOnDelete();
            $table->string('nom');
            $table->string('prenom');
            $table->string('email')->unique();
            $table->string('telephone', 20);
            $table->string('numero_ordre', 50);
            $table->date('date_naissance');
            $table->string('nationalite', 100);
            $table->text('adresse');
            $table->string('document_justificatif');
            $table->enum('statut', ['actif', 'inactif'])->default('actif');
            $table->timestamps();
        });
    }

    public function down()
    {
        Schema::dropIfExists('medecins_centre');
    }
};
