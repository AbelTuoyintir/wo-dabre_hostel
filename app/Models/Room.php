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
}
