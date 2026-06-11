<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class AgentWithdrawal extends Model
{
    /**
     * If your migration/table name is not the Laravel default `agent_withdrawals`,
     * uncomment and adjust the next line.
     */
    // protected $table = 'agent_withdrawals';

    protected $fillable = [
        'agent_id',
        'status',
        'amount',

        // Payment details (used in admin withdrawals view)
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

    public function agent(): BelongsTo
    {
        return $this->belongsTo(HostelAgent::class, 'agent_id');
    }
}

