<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class OccupantMessage extends Model
{
    use HasFactory;

    protected $fillable = [
        'manager_id',
        'occupant_id',
        'recipient_email',
        'subject',
        'message',
        'status',
        'sent_at',
        'failed_at',
        'error_message',
    ];

    protected $casts = [
        'sent_at' => 'datetime',
        'failed_at' => 'datetime',
    ];

    public function manager(): BelongsTo
    {
        return $this->belongsTo(User::class, 'manager_id');
    }

    public function occupant(): BelongsTo
    {
        return $this->belongsTo(User::class, 'occupant_id');
    }
}
