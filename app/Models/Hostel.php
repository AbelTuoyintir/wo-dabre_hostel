<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Hostel extends Model
{
    //

    protected $fillable = [
        'name',
        'location',
        'location_name',
        'address',
        'price_per_semester',
        'description',
        'user_id',
    ];

    public function rooms()
    {
        return $this->hasMany(Room::class);
    }

    public function bookings()
    {
        return $this->hasManyThrough(Booking::class, Room::class);
    }

    public function reviews()
    {
        return $this->hasMany(Review::class);
    }

    public function manager()
    {
        return $this->belongsTo(User::class, 'manager_id');
    }

    public function managers()
    {
        return $this->belongsToMany(User::class, 'hostel_managers')
                    ->withTimestamps();
    }

    public function primaryImage()
    {
        return $this->hasOne(Image::class)->where('is_primary', true);
    }

    public function images()
    {
        return $this->hasMany(Image::class);
    }

    public function scopeApproved($query)
    {
        return $query->where('is_approved', true)
                     ->where('status', 'active');
    }

    public function scopeFeatured($query)
    {
        return $query->where('is_featured', true);
    }

    public function scopeNearby($query, $lat, $lng, $radius = 10)
    {
        return $query->selectRaw(
            '*, ( 6371 * acos( cos( radians(?) ) * cos( radians( latitude ) ) * cos( radians( longitude ) - radians(?) ) + sin( radians(?) ) * sin( radians( latitude ) ) ) ) AS distance',
            [$lat, $lng, $lat]
        )->having('distance', '<=', $radius)
         ->orderBy('distance');
    }

    public function getMinPriceAttribute()
    {
        return $this->rooms()
            ->where('status', 'available')
            ->whereColumn('current_occupancy', '<', 'capacity')
            ->min('price_per_month');
    }

    public function getAvailableRoomsCountAttribute()
    {
        return $this->rooms()
            ->where('status', 'available')
            ->whereColumn('current_occupancy', '<', 'capacity')
            ->count();
    }

    public function updateRating()
    {
        $this->rating = $this->reviews()->avg('rating') ?? 0;
        $this->save();
    }
}

