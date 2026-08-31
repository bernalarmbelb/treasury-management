<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class BatchRequest extends Model
{
    protected $fillable = [
        'form_stock_id',
        'requested_by',
        'quantity',
        'note',
        'status',
        'reviewed_by',
        'reviewed_at',
        'resulting_batch_id',
        'notified_at',
    ];

    protected $casts = [
        'reviewed_at' => 'datetime',
        'notified_at' => 'datetime',
    ];

    public function formStock(): BelongsTo
    {
        return $this->belongsTo(FormStock::class);
    }

    public function requestedByUser(): BelongsTo
    {
        return $this->belongsTo(User::class, 'requested_by');
    }

    public function reviewedByUser(): BelongsTo
    {
        return $this->belongsTo(User::class, 'reviewed_by');
    }

    public function resultingBatch(): BelongsTo
    {
        return $this->belongsTo(FormBatch::class, 'resulting_batch_id');
    }
}
