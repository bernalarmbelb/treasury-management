<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class OrTransaction extends Model
{
    protected $fillable = [
        'form_stock_id',
        'certificate_number',
        'date_issued',
        'agency',
        'fund',
        'payor',
        'items',
        'total',
        'amount_in_words',
        'payment_method',
        'drawee_bank',
        'check_number',
        'check_date',
    ];

    protected $casts = [
        'date_issued' => 'date',
        'check_date' => 'date',
        'items' => 'array',
        'total' => 'decimal:2',
    ];

    public function formStock(): BelongsTo
    {
        return $this->belongsTo(FormStock::class);
    }
}
