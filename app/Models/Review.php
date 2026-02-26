<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Review extends Model
{
    use HasFactory;

    protected $fillable = [
        'user_id',
        'hostel_id',
        'booking_id',
        'rating',
        'title',
        'review',
        'pros',
        'cons',
        'stay_duration',
        'is_verified',
        'status',
        'helpful_count',
        'reported_count',
    ];

    protected $casts = [
        'rating' => 'integer',
        'is_verified' => 'boolean',
        'helpful_count' => 'integer',
        'reported_count' => 'integer',
    ];

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function hostel()
    {
        return $this->belongsTo(Hostel::class);
    }

    public function booking()
    {
        return $this->belongsTo(Booking::class);
    }
}