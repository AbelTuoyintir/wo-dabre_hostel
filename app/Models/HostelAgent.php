<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

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
        return $this->hasMany(Hostel::class, 'agent_id');
    }

    public function commissions()
    {
        return $this->hasMany(AgentCommission::class, 'hostel_agent_id');
    }

    public function withdrawals()
    {
        return $this->hasMany(AgentWithdrawal::class, 'hostel_agent_id');
    }
}
