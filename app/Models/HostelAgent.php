<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Schema;

class HostelAgent extends Model
{
    //
    protected $fillable = [
    'user_id', 'agent_code', 'phone', 'id_card_number', 'id_card_image',
    'address', 'city', 'region', 'emergency_contact', 'emergency_phone',
    'total_commission', 'available_balance', 'withdrawn_amount',
    'total_hostels_added', 'total_rooms_added', 'status', 'approved_at'
];

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function hostels()
    {
        if (Schema::hasColumn('hostels', 'agent_id')) {
            return $this->hasMany(Hostel::class, 'agent_id');
        }

        if (Schema::hasColumn('hostels', 'hostel_agent_id')) {
            return $this->hasMany(Hostel::class, 'hostel_agent_id');
        }

        return $this->hasMany(Hostel::class, 'user_id', 'user_id');
    }

    public function commissions()
    {
        return $this->hasMany(AgentCommission::class, 'hostel_agent_id');
    }

    public function withdrawals()
    {
        return $this->hasMany(AgentWithdrawal::class, 'hostel_agent_id');
    }

    public function addCommission($amount, $type, $description = null, $hostelId = null, $bookingId = null, $commissionPercentage = null)
    {
        $amount = (float) $amount;
        $commissionPercentage = $commissionPercentage ?? 20;

        $commission = $this->commissions()->create([
            'hostel_id' => $hostelId,
            'booking_id' => $bookingId,
            'amount' => $amount,
            'commission_percentage' => $commissionPercentage,
            'type' => $type,
            'status' => 'pending',
            'description' => $description,
        ]);

        $this->total_commission = (float) $this->total_commission + $amount;
        $this->available_balance = (float) $this->available_balance + $amount;
        $this->save();

        return $commission;
    }

    public function deductFromBalance($amount)
    {
        $amount = (float) $amount;

        $this->available_balance = max(0, (float) $this->available_balance - $amount);
        $this->save();

        return $this;
    }

    public function withdraw($amount, $paymentMethod, $accountNumber, $accountName, $bankName = null)
    {
        $amount = (float) $amount;

        $available = (float) ($this->available_balance ?? 0);

        if ($amount <= 0) {
            throw new \InvalidArgumentException('Withdrawal amount must be greater than 0.');
        }

        // Keep this aligned with DashboardController validation.
        if ($amount < 50) {
            throw new \InvalidArgumentException('Minimum withdrawal amount is 50.');
        }

        if ($amount > $available) {
            throw new \InvalidArgumentException('Insufficient available balance.');
        }

        $withdrawal = $this->withdrawals()->create([
            'amount' => $amount,
            'payment_method' => $paymentMethod,
            'account_number' => $accountNumber,
            'account_name' => $accountName,
            'bank_name' => $bankName,
            'status' => 'pending',
        ]);

        // Deduct from balance after creating the withdrawal request.
        $this->deductFromBalance($amount);

        return $withdrawal;
    }
}

