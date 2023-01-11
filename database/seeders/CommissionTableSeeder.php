<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;

class CommissionTableSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        DB::table('commission')->insert([
            "id" => 1,
            "commission_val" => 3,
            "min_payout" => 10,
            "created_at" => now(),
            "updated_at" => now()
        ]);
    }
}
