<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Booking extends Model
{
    use HasFactory;

    protected $fillable = [
        'booking_number',
        'user_id',
        'hostel_id',
        'room_id',
        'check_in_date',
        'check_out_date',
        'total_amount',
        'amount_paid',
        'payment_status',
        'booking_status',
        'special_requests',
        'payment_date',
        'payment_method',
        'transaction_id',
    ];

    protected $casts = [
        'check_in_date' => 'date',
        'check_out_date' => 'date',
        'payment_date' => 'datetime',
        'total_amount' => 'decimal:2',
        'amount_paid' => 'decimal:2',
    ];

    /**
     * Get the user that owns the booking
     */
    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    /**
     * Get the hostel that owns the booking
     */
    public function hostel(): BelongsTo
    {
        return $this->belongsTo(Hostel::class);
    }

    /**
     * Get the room that owns the booking
     */
    public function room(): BelongsTo
    {
        return $this->belongsTo(Room::class);
    }

    /**
     * Get the payments for this booking
     */
    public function payments(): HasMany
    {
        return $this->hasMany(Payment::class);
    }

    /**
     * Get the complaints for this booking
     */
    public function complaints(): HasMany
    {
        return $this->hasMany(Complaint::class);
    }

    /**
     * Check if booking is active
     */
    public function isActive(): bool
    {
        return in_array($this->booking_status, ['confirmed', 'checked_in']);
    }

    /**
     * Check if booking can be cancelled
     */
    public function canBeCancelled(): bool
    {
        return in_array($this->booking_status, ['pending', 'confirmed']);
    }

    /**
     * Get the balance due
     */
    public function getBalanceDueAttribute(): float
    {
        return max(0, $this->total_amount - $this->amount_paid);
    }

    /**
     * Check if payment is complete
     */
    public function isPaid(): bool
    {
        return $this->payment_status === 'paid';
    }

    /**
     * Get the number of nights
     */
    public function getNightsAttribute(): int
    {
        return $this->check_in_date->diffInDays($this->check_out_date);
    }
}