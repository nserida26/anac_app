<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * - rapport : rapport d'examen (obligatoire dès qu'un examinateur intervient).
     * - formateur_demandeur_id / qualite_formateur : un détenteur de licence qui
     *   enregistre lui-même une formation agit « en tant qu'instructeur » ou
     *   « en tant qu'examinateur ». Jusqu'ici son id de demandeur était rangé dans
     *   instructeur_id / examinateur_id, colonnes qui désignent pourtant les
     *   tables instructeurs / examinateurs_centre des centres de formation.
     */
    public function up()
    {
        Schema::table('formations', function (Blueprint $table) {
            $table->string('rapport')->nullable()->after('attestation');
            $table->foreignId('formateur_demandeur_id')->nullable()->after('examinateur_id')
                ->constrained('demandeurs')->nullOnDelete();
            $table->enum('qualite_formateur', ['instructeur', 'examinateur'])->nullable()->after('formateur_demandeur_id');
        });

        // Formations enregistrées par un détenteur : l'id est celui d'un demandeur
        // formateur, absent des tables instructeurs / examinateurs_centre.
        foreach ($this->formationsDeDetenteurs() as $formation) {
            $formateurId = $formation->instructeur_id ?: $formation->examinateur_id;
            $formateur = DB::table('demandeurs')->where('id', $formateurId)->first(['is_examinateur']);
            DB::table('formations')->where('id', $formation->id)->update([
                'formateur_demandeur_id' => $formateurId,
                // Décision ANAC : « examinateur » si la personne était désignée, sinon « instructeur ».
                'qualite_formateur' => optional($formateur)->is_examinateur ? 'examinateur' : 'instructeur',
                'instructeur_id' => null,
                'examinateur_id' => null,
            ]);
        }
    }

    public function down()
    {
        // Reconstitue l'ancien rangement : l'id du demandeur formateur dans les deux colonnes,
        // comme l'avaient toutes les formations enregistrées par un détenteur avant cette migration.
        DB::table('formations')->whereNotNull('formateur_demandeur_id')->update([
            'instructeur_id' => DB::raw('formateur_demandeur_id'),
            'examinateur_id' => DB::raw('formateur_demandeur_id'),
        ]);

        Schema::table('formations', function (Blueprint $table) {
            $table->dropForeign(['formateur_demandeur_id']);
            $table->dropColumn(['rapport', 'formateur_demandeur_id', 'qualite_formateur']);
        });
    }

    private function formationsDeDetenteurs()
    {
        return DB::table('formations')
            ->where(fn ($q) => $q->whereNotNull('instructeur_id')->orWhereNotNull('examinateur_id'))
            ->get()
            ->filter(function ($f) {
                $id = $f->instructeur_id ?: $f->examinateur_id;
                $instructeurCentre = $f->instructeur_id && DB::table('instructeurs')->where('id', $f->instructeur_id)->exists();
                $examinateurCentre = $f->examinateur_id && DB::table('examinateurs_centre')->where('id', $f->examinateur_id)->exists();
                $demandeurFormateur = DB::table('demandeurs')->where('id', $id)
                    ->where(fn ($q) => $q->where('is_instructeur', 1)->orWhere('is_examinateur', 1))->exists();

                return !$instructeurCentre && !$examinateurCentre && $demandeurFormateur;
            });
    }
};
