<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;
use Spatie\Permission\Models\Role;
use Spatie\Permission\PermissionRegistrar;

return new class extends Migration
{
    private const CEMPA = "Centre d'Expertise Médicale du Personnel Aéronautique (CEMPA)";

    /**
     * Rôle des comptes « centre d'expertise médicale » et ligne du CEMPA,
     * nouveau centre certifié, dans la liste des centres médicaux.
     */
    public function up()
    {
        Role::firstOrCreate(['name' => 'centre_medical', 'guard_name' => 'web']);
        app(PermissionRegistrar::class)->forgetCachedPermissions();

        if (!DB::table('centre_medicals')->where('libelle', self::CEMPA)->exists()) {
            DB::table('centre_medicals')->insert(['libelle' => self::CEMPA]);
        }
    }

    public function down()
    {
        // Le CEMPA n'est retiré que s'il n'est encore rattaché à rien.
        $cempa = DB::table('centre_medicals')->where('libelle', self::CEMPA)->whereNull('user_id')->value('id');
        if ($cempa && !DB::table('medical_examinations')->where('centre_medical_id', $cempa)->exists()) {
            DB::table('centre_medicals')->where('id', $cempa)->delete();
        }

        Role::where('name', 'centre_medical')->where('guard_name', 'web')->delete();
        app(PermissionRegistrar::class)->forgetCachedPermissions();
    }
};
