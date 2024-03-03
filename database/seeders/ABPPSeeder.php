<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class ABPPSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        // $IsABPPExist = DB::table('roles')->where('name', '=', 'ABPP')->exists();
        // if (!$IsABPPExist) {
        //     DB::table('roles')->insert([
        //         'name' => 'ABPP',
        //         'guard_name' => 'web',
        //         'created_at' => now(),
        //         'updated_at' => now()
        //     ]);
        // }

        $userBulkExist = DB::table('permissions')->where('name', '=', 'user.bulk')->exists();
        if (!$userBulkExist) {
            DB::table('permissions')->insert([
                'name' => 'user.bulk',
                'guard_name' => 'web',
                'created_at' => now(),
                'updated_at' => now()
            ]);
        }

        $admin = DB::table('roles')->where('name', '=', 'admin')->first();
        $instructor = DB::table('roles')->where('name', '=', 'instructor')->first();
        $userBulk = DB::table('permissions')->where('name', '=', 'user.bulk')->first();
        // $abpp = DB::table('roles')->where('name', '=', 'ABPP')->first();

        // $roleHasPermissions = DB::table('role_has_permissions')
        //     ->where('role_id', '=', $abpp->id)
        //     ->where('permission_id', '=', $userBulk->id)->exists();
        // if (!$roleHasPermissions) {
        //     DB::table('role_has_permissions')->insert([
        //         'role_id' => $abpp->id,
        //         'permission_id' => $userBulk->id,
        //     ]);
        // }

        $roleHasPermissions = DB::table('role_has_permissions')
            ->where('role_id', '=', $admin->id)
            ->where('permission_id', '=', $userBulk->id)->exists();
        if (!$roleHasPermissions) {
            DB::table('role_has_permissions')->insert([
                'role_id' => $admin->id,
                'permission_id' => $userBulk->id,
            ]);
        }

        $roleHasPermissions = DB::table('role_has_permissions')
            ->where('role_id', '=', $instructor->id)
            ->where('permission_id', '=', $userBulk->id)->exists();
        if (!$roleHasPermissions) {
            DB::table('role_has_permissions')->insert([
                'role_id' => $instructor->id,
                'permission_id' => $userBulk->id,
            ]);
        }
    }
}
