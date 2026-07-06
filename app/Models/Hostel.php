<?php

namespace App\Models;

use App\Models\Concerns\HasRouteUuid;
use App\Models\Review;
use App\Models\User;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Schema;

class Hostel extends Model
{
    use HasRouteUuid;

    //

    protected $fillable = [
        'name',
        'location',
        'address',
        'email',
        'description',
        'phone',
        'rating',
        'manager_id',
        'hostel_agent_id',
        'is_approved',
        'is_featured'
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
        return $this->hasMany(Review::class)->where('status', 'published');
    }

    public function averageRating()
    {
        return $this->reviews()->avg('rating') ?? 0;
    }

    public function reviewsCount()
    {
        return $this->reviews()->count();
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

 

    public function amenities()
    {
        // IMPORTANT: Laravel relationship methods must always return a Relation instance.
        // Returning non-relationship values (e.g. collect()) can crash when the attribute is accessed.
        if (!\Schema::hasTable('hostel_amenity')) {
            // Return an empty but valid relationship instance.
            return $this->belongsToMany(Amenity::class, 'hostel_amenity', 'hostel_id', 'amenity_id')
                ->whereRaw('1 = 0');
        }

        return $this->belongsToMany(Amenity::class, 'hostel_amenity', 'hostel_id', 'amenity_id');
    }


    public function images()
    {
        return $this->hasMany(HostelImage::class)->where('type', 'hostel');
    }

    public function primaryImage()
    {
        return $this->hasOne(HostelImage::class)->where('type', 'hostel')->where('is_primary', true);
    }

    public function galleryImages()
    {
        return $this->hasMany(HostelImage::class)->where('type', 'hostel')->where('is_primary', false)->orderBy('order');
    }

    public function complaints()
    {
        return $this->hasMany(Complaint::class);
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
        $minPrice = $this->rooms()
            ->where('status', 'available')
            ->whereColumn('current_occupancy', '<', 'capacity')
            ->min('room_cost');

        // Convert to float or return null
        return $minPrice ? (float) $minPrice : null;
    }

    public function getAvailableRoomsCountAttribute()
    {
        return $this->rooms()
            ->where('status', 'available')
            ->whereColumn('current_occupancy', '<', 'capacity')
            ->count();
    }

    public function getReviewsCountAttribute()
    {
        return $this->reviews()->count();
    }

    public function updateRating()
    {
        $this->rating = $this->reviews()->avg('rating') ?? 0;
        $this->save();
    }
}
