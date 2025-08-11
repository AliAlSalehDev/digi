<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use Carbon\Carbon;

class SaucesSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $now = Carbon::now()->toDateTimeString();

        $sauces = [
            [
                'id' => 1,
                'name' => '24 SAUCE',
                'unit' => null,
                'calories' => 253.2,
                'protein' => 2.3,
                'carbs' => 11.6,
                'fats_per_100g' => 22.3,
                'price' => 3.872,
                'created_at' => $now,
                'updated_at' => $now,
            ],
            [
                'id' => 2,
                'name' => 'MUSHROOM SAUCE',
                'unit' => null,
                'calories' => 313.4,
                'protein' => 8.9,
                'carbs' => 37.6,
                'fats_per_100g' => 14.3,
                'price' => 3.872,
                'created_at' => $now,
                'updated_at' => $now,
            ],
            [
                'id' => 3,
                'name' => 'TOMATO SAUCE',
                'unit' => null,
                'calories' => 87.4,
                'protein' => 3.6,
                'carbs' => 20.2,
                'fats_per_100g' => 0.5,
                'price' => 3.872,
                'created_at' => $now,
                'updated_at' => $now,
            ],
            [
                'id' => 4,
                'name' => 'GARLIC LEMON SAUCE',
                'unit' => null,
                'calories' => 308.1,
                'protein' => 2.7,
                'carbs' => 4.3,
                'fats_per_100g' => 31.5,
                'price' => 3.872,
                'created_at' => $now,
                'updated_at' => $now,
            ],
            [
                'id' => 5,
                'name' => 'WHITE SAUCE',
                'unit' => null,
                'calories' => 269.9,
                'protein' => 2.5,
                'carbs' => 3.3,
                'fats_per_100g' => 27.7,
                'price' => 3.872,
                'created_at' => $now,
                'updated_at' => $now,
            ],
            [
                'id' => 6,
                'name' => 'YELLOW SAUCE',
                'unit' => null,
                'calories' => 276,
                'protein' => 2.5,
                'carbs' => 15.6,
                'fats_per_100g' => 23.3,
                'price' => 3.872,
                'created_at' => $now,
                'updated_at' => $now,
            ],
            [
                'id' => 7,
                'name' => 'BROWN SAUCE',
                'unit' => null,
                'calories' => 340,
                'protein' => 3,
                'carbs' => 4,
                'fats_per_100g' => 35,
                'price' => 3.872,
                'created_at' => $now,
                'updated_at' => $now,
            ],
            [
                'id' => 8,
                'name' => 'BBQ SAUCE',
                'unit' => null,
                'calories' => 180,
                'protein' => 1,
                'carbs' => 45,
                'fats_per_100g' => 0.5,
                'price' => 3.872,
                'created_at' => $now,
                'updated_at' => $now,
            ],
        ];

        DB::table('sauces')->insert($sauces);
    }
}
