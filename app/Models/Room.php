<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Room extends Model
{
    use HasFactory;

    /**
     * Mass assignable attributes
     */
    protected $fillable = [
        'number',
        'capacity',
        'hostel_id',
        'gender',
        'status',
        'room_type',
    ];

    /**
     * Attribute casting
     */
    protected $casts = [
        'capacity' => 'integer',
    ];

    /**
     * Room belongs to a Hostel
     */
    public function hostel()
    {
        return $this->belongsTo(Hostel::class);
    }

    /**
     * Room has many Bookings
     */
    public function bookings()
    {
        return $this->hasMany(Booking::class);
    }

    /**
     * Status constants (avoid magic strings)
     */
    public const STATUS_FULL = 'full';
    public const STATUS_AVAILABLE = 'available';
    public const STATUS_UNAVAILABLE = 'unavailable';
    public const STATUS_INACTIVE = 'inactive';

    /**
     * Gender constants
     */
    public const GENDER_MALE = 'male';
    public const GENDER_FEMALE = 'female';
    public const GENDER_ANY = 'any';

    /**
     * Calculate occupancy rate percentage
     */
    public function occupancyRate()
    {
        if (!$this->capacity || $this->capacity == 0) {
            return 0;
        }
        return round(($this->current_occupancy ?? 0) / $this->capacity * 100);
    }

    /**
     * Get available spaces in room
     */
    public function availableSpaces()
    {
        return max(0, ($this->capacity ?? 0) - ($this->current_occupancy ?? 0));
    }

    /**
     * Get current booking query
     */
    public function currentBooking()
    {
        return $this->bookings()
            ->whereIn('status', ['confirmed', 'checked_in'])
            ->where('check_out_date', '>', now());
    }
}
