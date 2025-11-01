<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use App\Models\Room;
use App\Models\Availability;   
use Carbon\Carbon;

class availabilitySeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $rooms = Room::all();
        $start = Carbon::today();
        $end = Carbon::today()->addDays(30);

        foreach ($rooms as $room) {
            $period = new \DatePeriod(
                $start,
                new \DateInterval('P1D'),
                $end
            );

            foreach ($period as $date) {
                Availability::updateOrCreate(
                    [
                        'room_id' => $room->id,
                        'date' => $date->format('Y-m-d')
                    ],
                    [
                        'is_available' => true
                    ]
                );
            }
        }
    }
}

