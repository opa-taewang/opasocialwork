<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;

class CurrencyTableSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        DB::table('currencies')->insert([
            "id" => "1",
            "name" => "USD",
            "code" => "USD",
            "symbol" => "$",
            "rate" => 1,
            "updated_at" => now(),
            "created_at" => now()
        ]);

        DB::table('currencies')->insert([
            "id" => "2",
            "name" => "Naira",
            "code" => "NGN",
            "symbol" => "₦",
            "rate" => 750,
            "updated_at" => now(),
            "created_at" => now()
        ]);
    }
}
