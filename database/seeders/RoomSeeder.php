<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use App\Models\room;
use App\Models\roomCategory;

class RoomSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $categories = roomCategory::all();

        foreach ($categories as $category){
            for ($i = 1; $i<=3; $i++){
                room::firstOrCreate(
                    [
                        'room_number' => $category->id * 100 + $i,
                    ],
                    [
                        'room_category_id' => $category->id,
                        'status' => 'available',
                    ]
                );
            }
        }

    }
}
