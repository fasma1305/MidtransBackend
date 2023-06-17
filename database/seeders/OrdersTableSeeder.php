<?php

namespace Database\Seeders;

use Hamcrest\Core\HasToString;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;

class OrdersTableSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        DB::table('orders')->insert([
            'id' => Str::uuid()->toString(),
            'name' => 'Baju',
            'price' => '50000',
            'stock' => 100
        ]);
    }
}
