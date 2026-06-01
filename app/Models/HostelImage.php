<?php

namespace App\Models;

use App\Models\Concerns\HasRouteUuid;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Storage;

class HostelImage extends Model
{
    use HasRouteUuid;

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
        // Use the Storage facade so URLs point to the `storage` symlink (e.g. /storage/hostels/...)
        // This relies on `php artisan storage:link` having been run and the `public` disk configured.
        return Storage::url($this->image_path);
    }

}
