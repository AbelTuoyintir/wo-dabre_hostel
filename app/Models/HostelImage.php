<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class HostelImage extends Model
{
    //
    protected $fillable= [
        'hostel_id',
        'image_path',
        'is_primary',
        'order'
    ]
}
