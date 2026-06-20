<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class OrRptTransactionEntry extends Model
{
    protected $fillable = [
        'or_rpt_transaction_id',
        'rpt_property_id',
        'payment_scheme',
        'installment_quarter',
        'tax_due',
        'discount',
        'penalty_percent',
        'penalty_amount',
        'amount',
    ];

    protected $casts = [
        'installment_quarter' => 'integer',
        'tax_due' => 'decimal:2',
        'discount' => 'decimal:2',
        'penalty_percent' => 'decimal:2',
        'penalty_amount' => 'decimal:2',
        'amount' => 'decimal:2',
    ];

    public function transaction(): BelongsTo
    {
        return $this->belongsTo(OrRptTransaction::class, 'or_rpt_transaction_id');
    }

    public function property(): BelongsTo
    {
        return $this->belongsTo(RptProperty::class, 'rpt_property_id');
    }
}
