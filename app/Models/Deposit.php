<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Deposit extends Model
{
    protected $fillable = [
        'deposit_date',
        'bank_account_id',
        'slip_number',
        'prepared_by',
    ];

    protected $casts = [
        'deposit_date' => 'date',
    ];

    public function bankAccount(): BelongsTo
    {
        return $this->belongsTo(BankAccount::class);
    }

    /** Collections included in this deposit. */
    public function transactionLogs(): HasMany
    {
        return $this->hasMany(TransactionLog::class);
    }

    public function total(): float
    {
        return (float) $this->transactionLogs()->sum('amount');
    }
}
