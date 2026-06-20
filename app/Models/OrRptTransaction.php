<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class OrRptTransaction extends Model
{
    protected $fillable = [
        'form_stock_id',
        'certificate_number',
        'previous_receipt_number',
        'previous_receipt_date',
        'previous_receipt_year',
        'municipality_province',
        'city',
        'transaction_date',
        'client_name',
        'payment_in_words',
        'amount_paid',
        'treasurer_deputy',
        'basic_tax',
        'special_education_fund',
    ];

    protected $casts = [
        'transaction_date' => 'date',
        'basic_tax' => 'boolean',
        'special_education_fund' => 'boolean',
    ];

    public function formStock(): BelongsTo
    {
        return $this->belongsTo(FormStock::class);
    }

    public function entries(): HasMany
    {
        return $this->hasMany(OrRptTransactionEntry::class, 'or_rpt_transaction_id');
    }
}
