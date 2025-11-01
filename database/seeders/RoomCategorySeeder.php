<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use App\Models\roomCategory;

class RoomCategorySeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $categories = [
            ['name' => 'Premium Deluxe','base_price' => 12000,],
            ['name' => 'Super Deluxe','base_price' => 10000,],
            ['name' => 'Standard Deluxe','base_price' => 8000,],
        ];

        foreach ($categories as $category) {
            roomCategory::firstOrCreate($category);
        }
    }
}
