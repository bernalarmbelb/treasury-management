<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class CancelRequest extends Model
{
    protected $fillable = [
        'transaction_log_id',
        'requested_by',
        'reason',
        'status',
        'reviewed_by',
        'reviewed_at',
        'notified_at',
    ];

    protected $casts = [
        'reviewed_at' => 'datetime',
        'notified_at' => 'datetime',
    ];

    public function transactionLog(): BelongsTo
    {
        return $this->belongsTo(TransactionLog::class);
    }

    public function requestedByUser(): BelongsTo
    {
        return $this->belongsTo(User::class, 'requested_by');
    }

    public function reviewedByUser(): BelongsTo
    {
        return $this->belongsTo(User::class, 'reviewed_by');
    }
}
