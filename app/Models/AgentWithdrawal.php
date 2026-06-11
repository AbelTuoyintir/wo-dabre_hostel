<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class AgentWithdrawal extends Model
{
    protected $table = 'agent_withdrawals';

    protected $fillable = [
        // Canonical column in this app
        'hostel_agent_id',

        // Tests may set agent_id; we will map it in the model before save.
        'agent_id',

        'status',
        'amount',


        // Payment details
        'payment_method',
        'account_number',
        'account_name',
        'bank_name',

        // Processing details
        'processed_at',
        'processed_by',

        // Reasons/notes
        'notes',
        'rejection_reason',
    ];

    protected $casts = [
        'processed_at' => 'datetime',
        'amount' => 'decimal:2',
    ];

    protected static function booted(): void
    {
        static::creating(function (self $withdrawal) {
            // Compatibility: if tests provide agent_id, copy it into hostel_agent_id.
            // Use "creating" so the insert statement receives hostel_agent_id.
            if (empty($withdrawal->hostel_agent_id) && !empty($withdrawal->agent_id)) {
                $withdrawal->hostel_agent_id = $withdrawal->agent_id;
            }
        });

        static::updating(function (self $withdrawal) {
            if (empty($withdrawal->hostel_agent_id) && !empty($withdrawal->agent_id)) {
                $withdrawal->hostel_agent_id = $withdrawal->agent_id;
            }
        });
    }


    public function agent(): BelongsTo
    {
        return $this->belongsTo(HostelAgent::class, 'hostel_agent_id');
    }
}


