<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Un examinateur médical est soit individuel (il a son propre compte,
     * user_id), soit déclaré par un centre d'expertise médicale (sans compte :
     * c'est le centre qui agit pour lui) puis validé par l'ANAC. Les champs
     * reprennent ceux de examinateurs_centre pour partager la même validation
     * (voir App\Models\Concerns\ValidableParAnac).
     */
    public function up()
    {
        // Sans doctrine/dbal, ->change() est indisponible : ALTER direct.
        DB::statement('ALTER TABLE examinateurs MODIFY user_id BIGINT UNSIGNED NULL');

        Schema::table('examinateurs', function (Blueprint $table) {
            $table->string('nom')->nullable()->after('np');
            $table->string('prenom')->nullable()->after('nom');
            $table->string('email')->nullable()->after('prenom');
            $table->string('telephone', 20)->nullable()->after('email');
            // Pour un examinateur médical : numéro d'agrément.
            $table->string('numero_licence_examinateur', 50)->nullable()->after('telephone');
            $table->date('date_naissance')->nullable();
            $table->string('nationalite', 100)->nullable();
            $table->text('adresse')->nullable();
            $table->string('document_justificatif')->nullable();
            $table->date('date_debut_validite')->nullable();
            $table->date('date_fin_validite')->nullable();
            $table->enum('statut_validation', ['en_attente', 'valide', 'refuse'])->default('en_attente');
            $table->text('motif_refus')->nullable();
            $table->unsignedBigInteger('valide_par')->nullable();
            $table->dateTime('date_validation')->nullable();

            $table->foreign('valide_par')->references('id')->on('users')->nullOnDelete();
        });

        // Les examinateurs individuels existants ont été créés par l'ANAC : déjà validés.
        DB::table('examinateurs')->update(['statut_validation' => 'valide']);
    }

    public function down()
    {
        Schema::table('examinateurs', function (Blueprint $table) {
            $table->dropForeign(['valide_par']);
            $table->dropColumn([
                'nom', 'prenom', 'email', 'telephone', 'numero_licence_examinateur', 'date_naissance',
                'nationalite', 'adresse', 'document_justificatif', 'date_debut_validite', 'date_fin_validite',
                'statut_validation', 'motif_refus', 'valide_par', 'date_validation',
            ]);
        });

        // Les examinateurs déclarés par un centre n'ont pas de compte : ils disparaissent.
        DB::table('examinateurs')->whereNull('user_id')->delete();
        self::rendreUserIdObligatoire();
    }

    /** MySQL refuse de modifier une colonne portant une clé étrangère : on la retire le temps de l'ALTER. */
    public static function rendreUserIdObligatoire(): void
    {
        Schema::table('examinateurs', fn (Blueprint $table) => $table->dropForeign('fk_examinateurs_users'));
        DB::statement('ALTER TABLE examinateurs MODIFY user_id BIGINT UNSIGNED NOT NULL');
        Schema::table('examinateurs', function (Blueprint $table) {
            $table->foreign(['user_id'], 'fk_examinateurs_users')->references(['id'])->on('users')->onUpdate('NO ACTION')->onDelete('CASCADE');
        });
    }
};
