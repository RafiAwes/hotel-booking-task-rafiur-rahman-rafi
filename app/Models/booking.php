<?php

namespace App\Models;

use Carbon\Carbon;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class booking extends Model
{
    use HasFactory;

    protected $fillable = [
        'name',
        'email',
        'phone_number',
        'room_id',
        'check_in_date',
        'check_out_date',
        'total_price',
    ];

    public function room(){
        return $this->belongsTo(room::class);
    }

    public function overlaps($checkin, $checkout){
        return !(
            Carbon::parse($this->check_in_date)->lt($checkin) || Carbon::parse($this->check_out_date)->gt($checkout)
        );
    }

    protected static function booted(){
        static::created(function($booking){
            availability::markAsBooked(
                $booking->room_id,
                $booking->check_in_date,
                $booking->check_out_date
            );
        });
    }

}
