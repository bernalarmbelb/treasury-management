<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

class RptProperty extends Model
{
    protected $fillable = [
        'tax_declaration_number',
        'declared_owner',
        'location',
        'lot_block_number',
        'municipality_province',
        'city',
        'assessed_value_land',
        'assessed_value_improvement',
        'assessed_value_total',
        'annual_tax_due',
    ];

    public function entries(): HasMany
    {
        return $this->hasMany(OrRptTransactionEntry::class);
    }

    /** Quarter numbers (1–4) already paid for this property. */
    public function paidQuarters(): array
    {
        return $this->entries()
            ->where('payment_scheme', 'installment')
            ->whereNotNull('installment_quarter')
            ->pluck('installment_quarter')
            ->map(fn ($q) => (int) $q)
            ->unique()
            ->sort()
            ->values()
            ->all();
    }
}
