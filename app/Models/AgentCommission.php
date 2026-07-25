<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class AgentCommission extends Model
{
    protected $table = 'agent_commissions';

    protected $fillable = [
        'hostel_agent_id',
        'hostel_id',
        'booking_id',
        'amount',
        'commission_percentage',
        'type',
        'status',
        'description',
        'paid_at',
    ];

    public function agent()
    {
        return $this->belongsTo(HostelAgent::class, 'hostel_agent_id');
    }

    public function hostel()
    {
        return $this->belongsTo(Hostel::class, 'hostel_id');
    }

    public function booking()
    {
        return $this->belongsTo(Booking::class, 'booking_id');
    }
}

