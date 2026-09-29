<?php

use Illuminate\Database\Migrations\Migration;
use Spatie\Permission\Models\Role;
use Spatie\Permission\PermissionRegistrar;

return new class extends Migration
{
    /**
     * Rôle de pré-vérification DSV : les demandes que la DTA transmet à la DSV
     * passent d'abord par ce profil (mêmes actions valider/rejeter que la DSV,
     * voir dsv_verificateur_valider sur etat_demande_autorisations), avant de
     * réellement atteindre la DSV.
     */
    public function up()
    {
        Role::firstOrCreate(['name' => 'dsv_verificateur', 'guard_name' => 'web']);
        app(PermissionRegistrar::class)->forgetCachedPermissions();
    }

    public function down()
    {
        Role::where('name', 'dsv_verificateur')->where('guard_name', 'web')->delete();
        app(PermissionRegistrar::class)->forgetCachedPermissions();
    }
};
