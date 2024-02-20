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
        $IsABPPExist = DB::table('roles')->where('name', '=', 'ABPP')->exists();
        if (!$IsABPPExist) {
            DB::table('roles')->insert([
                'name' => 'ABPP',
                'guard_name' => 'web',
                'created_at' => now(),
                'updated_at' => now()
            ]);
        }
    }
}
