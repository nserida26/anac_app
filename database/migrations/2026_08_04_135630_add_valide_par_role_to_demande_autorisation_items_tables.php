<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    private array $tables = [
        'avions',
        'vols',
        'equipe_vols',
        'fret_vols',
        'personne_deces',
        'mdns',
        'receiving_parties',
        'document_autorisations',
    ];

    public function up()
    {
        foreach ($this->tables as $table) {
            Schema::table($table, function (Blueprint $blueprint) {
                $blueprint->string('valide_par_role', 20)->nullable()->after('motif');
            });
        }
    }

    public function down()
    {
        foreach ($this->tables as $table) {
            Schema::table($table, function (Blueprint $blueprint) {
                $blueprint->dropColumn('valide_par_role');
            });
        }
    }
};
