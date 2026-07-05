<?php

namespace Database\Seeders;

use App\Models\User;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Str;
use Spatie\Permission\Models\Permission;
use Spatie\Permission\Models\Role;

class UserSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        $permissions = ['manage-dsv', 'manage-vr', 'manage-vi', 'manage-all'];
        $user = User::find(6);
        $user2 = User::find(64);
        $user3 = User::find(75);

        // Create all permissions
        foreach ($permissions as $permission) {
            Permission::firstOrCreate(['name' => $permission]);
        }

        // Assign permissions to user 6
        $user->givePermissionTo([
            'manage-dsv'
        ]);

        // Remove old permission if it exists
        if ($user->hasPermissionTo('manage-dta')) {
            $user->revokePermissionTo('manage-dta');
        }

        // Assign new permissions to user 64 instead of manage-dta
        $user2->givePermissionTo([
            'manage-vi'
        ]);
        $user3->givePermissionTo([
            'manage-vr'
        ]);

        // Optional: You might want to clean up the old permission if it's no longer needed
        $oldPermission = Permission::where('name', 'manage-dta')->first();
        if ($oldPermission) {
            $oldPermission->delete();
        }
    }
}
