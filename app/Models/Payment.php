<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Payment extends Model
{
    use HasFactory;

    /**
     * The table associated with the model.
     *
     * @var string
     */
    protected $table = 'payments';

    /**
     * The attributes that are mass assignable.
     *
     * @var array<int, string>
     */
    protected $fillable = [
        'booking_id',
        'amount',
        'status',
        'payment_method',
        'transaction_id',
        'paid_at',
        'refund_amount',
        'refund_reference',
        'refunded_at',
    ];

    /**
     * The attributes that should be cast.
     *
     * @var array<string, string>
     */
    protected $casts = [
        'amount' => 'decimal:2',
        'refund_amount' => 'decimal:2',
        'paid_at' => 'datetime',
        'refunded_at' => 'datetime',
        'created_at' => 'datetime',
        'updated_at' => 'datetime',
    ];

    /**
     * The attributes that should be mutated to dates.
     *
     * @var array
     */
    protected $dates = [
        'paid_at',
        'refunded_at',
        'created_at',
        'updated_at',
    ];

    /**
     * Default attribute values.
     *
     * @var array
     */
    protected $attributes = [
        'status' => 'pending',
    ];

    /**
     * Get the booking that owns the payment.
     */
    public function booking()
    {
        return $this->belongsTo(Booking::class);
    }

    /**
     * Get the user associated with the payment through booking.
     */
    public function user()
    {
        return $this->hasOneThrough(
            User::class,
            Booking::class,
            'id', // Foreign key on bookings table
            'id', // Foreign key on users table
            'booking_id', // Local key on payments table
            'user_id' // Local key on bookings table
        );
    }

    /**
     * Scope a query to only include completed payments.
     */
    public function scopeCompleted($query)
    {
        return $query->where('status', 'completed');
    }

    /**
     * Scope a query to only include pending payments.
     */
    public function scopePending($query)
    {
        return $query->where('status', 'pending');
    }

    /**
     * Scope a query to only include failed payments.
     */
    public function scopeFailed($query)
    {
        return $query->where('status', 'failed');
    }

    /**
     * Scope a query to only include refunded payments.
     */
    public function scopeRefunded($query)
    {
        return $query->where('status', 'refunded');
    }

    /**
     * Check if payment is completed.
     */
    public function isCompleted(): bool
    {
        return $this->status === 'completed';
    }

    /**
     * Check if payment is pending.
     */
    public function isPending(): bool
    {
        return $this->status === 'pending';
    }

    /**
     * Check if payment is failed.
     */
    public function isFailed(): bool
    {
        return $this->status === 'failed';
    }

    /**
     * Check if payment is refunded.
     */
    public function isRefunded(): bool
    {
        return $this->status === 'refunded';
    }

    /**
     * Mark payment as completed.
     */
    public function markAsCompleted(string $transactionId = null, string $paymentMethod = null): bool
    {
        $this->status = 'completed';
        $this->paid_at = now();
        
        if ($transactionId) {
            $this->transaction_id = $transactionId;
        }
        
        if ($paymentMethod) {
            $this->payment_method = $paymentMethod;
        }
        
        return $this->save();
    }

    /**
     * Mark payment as failed.
     */
    public function markAsFailed(): bool
    {
        $this->status = 'failed';
        return $this->save();
    }

    /**
     * Process a refund.
     */
    public function refund(float $amount, string $refundReference = null): bool
    {
        $this->status = 'refunded';
        $this->refund_amount = $amount;
        $this->refunded_at = now();
        
        if ($refundReference) {
            $this->refund_reference = $refundReference;
        }
        
        return $this->save();
    }

    /**
     * Get formatted amount.
     */
    public function getFormattedAmountAttribute(): string
    {
        return '₵' . number_format($this->amount, 2);
    }

    /**
     * Get formatted refund amount.
     */
    public function getFormattedRefundAmountAttribute(): string
    {
        return $this->refund_amount ? '₵' . number_format($this->refund_amount, 2) : 'N/A';
    }

    /**
     * Get status badge class for styling.
     */
    public function getStatusBadgeClassAttribute(): string
    {
        return match($this->status) {
            'completed' => 'bg-green-100 text-green-800',
            'pending' => 'bg-yellow-100 text-yellow-800',
            'failed' => 'bg-red-100 text-red-800',
            'refunded' => 'bg-purple-100 text-purple-800',
            default => 'bg-gray-100 text-gray-800',
        };
    }

    /**
     * Get payment method display name.
     */
    public function getPaymentMethodDisplayAttribute(): string
    {
        return match($this->payment_method) {
            'mobile_money' => 'Mobile Money',
            'card' => 'Card Payment',
            'bank_transfer' => 'Bank Transfer',
            'cash' => 'Cash',
            default => ucfirst($this->payment_method ?? 'N/A'),
        };
    }

    /**
     * Get payment method icon.
     */
    public function getPaymentMethodIconAttribute(): string
    {
        return match($this->payment_method) {
            'mobile_money' => 'fas fa-mobile-alt',
            'card' => 'fas fa-credit-card',
            'bank_transfer' => 'fas fa-university',
            'cash' => 'fas fa-money-bill-wave',
            default => 'fas fa-credit-card',
        };
    }
}