<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class room extends Model
{
    use HasFactory;

    protected $fillable = [
        'room_category_id',
        'room_number',
        'status',
    ];

    public function category(){
        return $this->belongsTo(roomCategory::class, 'room_category_id', 'id');
    }

    public function bookings(){
        return $this->hasMany(booking::class);
    }

    public function availabilities(){
        return $this->hasMany(availability::class);
    }

    public function available($query){
        return $query->where('status', 'available');
    }

    public function scopeAvailable($query){
        return $query->where('status', 'available');
    }

    public function isAvailableForRange($checkIn, $checkOut){
        return availability::isAvailableForRange($this->id, $checkIn, $checkOut);
    }
}
