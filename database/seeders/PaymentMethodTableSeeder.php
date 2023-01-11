<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;

class PaymentMethodTableSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        DB::table('payment_methods')->insert([
            "id" => "1",
            "name" => "FlutterWave",
            "slug" => "flutterwave",
            "status" => "ACTIVE",
            "is_disabled_default" => "0",
            "created_at" => now(),
            "updated_at" => now()
        ]);

        DB::table('payment_methods')->insert([
            "id" => "2",
            "name" => "Bitcoin | Bitcoin Cash | Ethereum | Litecoin",
            "slug" => "bitcoin",
            "status" => "ACTIVE",
            "is_disabled_default" => "0",
            "created_at" => now(),
            "updated_at" => now()
        ]);

        DB::table('payment_methods')->insert([
            "id" => "3",
            "name" => "Bank\/Other",
            "slug" => "bank-other",
            "status" => "ACTIVE",
            "is_disabled_default" => "0",
            "created_at" => now(),
            "updated_at" => now()
        ]);

        DB::table('payment_methods')->insert([
            "id" => "4",
            "name" => "Opasocial Points",
            "slug" => "points",
            "status" => "ACTIVE",
            "is_disabled_default" => "0",
            "created_at" => now(),
            "updated_at" => now()
        ]);
    }
}
