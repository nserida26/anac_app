<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Désignation par l'ANAC d'un détenteur de licence (déjà instructeur) comme
     * examinateur, pour des types de licence donnés et une période donnée.
     * Remplace la simple case « is_examinateur » : les désignations existantes,
     * sans types ni dates, ne sont pas reprises (décision ANAC).
     */
    public function up()
    {
        Schema::create('designations_examinateur', function (Blueprint $table) {
            $table->engine = 'InnoDB';
            $table->id();
            $table->foreignId('demandeur_id')->constrained('demandeurs')->cascadeOnDelete();
            $table->date('date_debut');
            $table->date('date_fin');
            $table->foreignId('designe_par')->nullable()->constrained('users')->nullOnDelete();
            $table->timestamp('retiree_le')->nullable();
            $table->foreignId('retiree_par')->nullable()->constrained('users')->nullOnDelete();
            $table->timestamps();
        });

        Schema::create('designation_examinateur_type_licence', function (Blueprint $table) {
            $table->engine = 'InnoDB';
            $table->unsignedBigInteger('designation_examinateur_id');
            // type_licences.id est un INT signé (table ancienne), pas un BIGINT UNSIGNED.
            $table->integer('type_licence_id');
            $table->primary(['designation_examinateur_id', 'type_licence_id'], 'designation_type_licence_primary');
            // Noms explicites : les noms générés dépassent la limite MySQL de 64 caractères.
            $table->foreign('designation_examinateur_id', 'fk_designation_tl_designation')
                ->references('id')->on('designations_examinateur')->cascadeOnDelete();
            $table->foreign('type_licence_id', 'fk_designation_tl_type_licence')
                ->references('id')->on('type_licences')->cascadeOnDelete();
        });
    }

    public function down()
    {
        Schema::dropIfExists('designation_examinateur_type_licence');
        Schema::dropIfExists('designations_examinateur');
    }
};
