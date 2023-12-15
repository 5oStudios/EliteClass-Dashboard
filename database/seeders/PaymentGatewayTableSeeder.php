<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class PaymentGatewayTableSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        DB::table('payment_gateway')->delete();
        
        DB::table('payment_gateway')->insert(array (
            0 => 
            array (
                'id' => '1',
                'name' => 'myfatoorah',
                'payment_method' => 'VISA/MASTER',
                'type' => 'percentage',
                'charges' => '3.000',
                'created_at' => now(),
                'updated_at' => now(),
            ),
            1 => 
            array (
                'id' => '2',
                'name' => 'myfatoorah',
                'payment_method' => 'KNET',
                'type' => 'fixed',
                'charges' => '0.250',
                'created_at' => now(),
                'updated_at' => now(),
            ),
        ));
    }
}
