<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use App\Models\Room;
use App\Models\Availability;
use Carbon\Carbon;

class availability extends Model
{
    use HasFactory;

    protected $fillable = [
        'room_id',
        'date',
        'is_available',
    ];

    protected $casts = [
        'date' => 'date',
        'is_available' => 'boolean',
    ];


    public function room()
    {
        return $this->belongsTo(Room::class);
    }


    public function scopeAvailable($query)
    {
        return $query->where('is_available', true);
    }


    public function scopeForDate($query, $date)
    {
        return $query->where('date', Carbon::parse($date)->toDateString());
    }


    public static function isAvailableForRange($roomId, $checkIn, $checkOut)
    {
        return !self::where('room_id', $roomId)
            ->whereBetween('date', [
                Carbon::parse($checkIn),
                Carbon::parse($checkOut)->subDay()
            ])
            ->where('is_available', false)
            ->exists();
    }


    public static function markAsBooked($roomId, $checkIn, $checkOut)
    {
        $period = new \DatePeriod(
            Carbon::parse($checkIn),
            new \DateInterval('P1D'),
            Carbon::parse($checkOut)
        );

        foreach ($period as $date) {
            self::updateOrCreate(
                ['room_id' => $roomId, 'date' => $date->format('Y-m-d')],
                ['is_available' => false]
            );
        }
    }


    public static function markAsAvailable($roomId, $checkIn, $checkOut)
    {
        $period = new \DatePeriod(
            Carbon::parse($checkIn),
            new \DateInterval('P1D'),
            Carbon::parse($checkOut)
        );

        foreach ($period as $date) {
            self::updateOrCreate(
                ['room_id' => $roomId, 'date' => $date->format('Y-m-d')],
                ['is_available' => true]
            );
        }
    }
}
