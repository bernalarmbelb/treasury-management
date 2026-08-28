<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

class BankAccount extends Model
{
    protected $fillable = [
        'bank_name',
        'account_number',
        'account_name',
        'is_active',
        'opening_balance',
    ];

    protected $casts = [
        'is_active' => 'boolean',
        'opening_balance' => 'decimal:2',
    ];

    public function cheques(): HasMany
    {
        return $this->hasMany(Cheque::class);
    }

    /** "LBP — Sorsogon · 00782-1019-43" style label for selects. */
    public function label(): string
    {
        return $this->bank_name . ' · ' . $this->account_number;
    }
}
