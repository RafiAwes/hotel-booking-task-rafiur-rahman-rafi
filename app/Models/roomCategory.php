<?php

namespace App\Models;

use Carbon\Carbon;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class roomCategory extends Model
{
    use HasFactory;

    protected $fillable = [
        'name',
        'base_price',
    ];

    public function rooms(){
        return $this-hasMany(room::class, 'room_category_id', 'id');
    }

    public function getPriceForDate($date){

        $base = $this->base_price;
        $day = Carbon::parse($date)->format('l');

        if (in_array($day, ['Friday', 'Saturday'])) {
            return $base += $base * 0.20;
        }
        return $base;
    }

    public function calculateTotalPrice($checkIn, $checkOut)
    {
        $from = Carbon::parse($checkIn);
        $to = Carbon::parse($checkOut);
        $days = $from->diffInDays($to);

        $total = 0;

        for ($date = $from->copy(); $date->lt($to); $date->addDay()) {
            $total += $this->getPriceForDate($date);
        }

        if ($days >= 3) {
            $total -= $total * 0.10;
        }

        return $total;
    }
}
