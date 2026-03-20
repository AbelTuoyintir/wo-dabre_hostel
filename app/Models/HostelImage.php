<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Storage;

class HostelImage extends Model
{
    protected $fillable = [
        'hostel_id',
        'room_id',
        'image_path',
        'is_primary',
        'order',
        'type'
    ];

    protected $casts = [
        'is_primary' => 'boolean',
    ];

    public function hostel()
    {
        return $this->belongsTo(Hostel::class);
    }

    public function room()
    {
        return $this->belongsTo(Room::class);
    }

    // Accessor for blade compatibility
    public function getPathAttribute()
    {
        return $this->image_path;
    }

    public function getUrlAttribute()
    {
        return Storage::disk('public')->url($this->image_path);
    }
}
