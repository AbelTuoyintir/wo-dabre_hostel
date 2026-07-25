<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Amenity extends Model
{
    protected $fillable = [
        'name',
        'icon',
    ];

    public function hostels()
    {
        return $this->belongsToMany(Hostel::class, 'hostel_amenity');
    }
}
