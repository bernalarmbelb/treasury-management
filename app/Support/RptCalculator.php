<?php

namespace App\Support;

class RptCalculator
{
    /** Annual tax due = assessed value × (basic tax rate + SEF rate). */
    public function taxDue(float $assessedTotal): float
    {
        $rate = (float) config('rpt.basic_tax_rate') + (float) config('rpt.sef_rate');

        return round($assessedTotal * $rate, 2);
    }

    /** One quarterly installment = a quarter of the annual tax due. */
    public function quarterly(float $taxDue): float
    {
        return round($taxDue / 4, 2);
    }

    /** Early-payment discount (full payment, on time only). */
    public function discount(float $taxDue): float
    {
        return round($taxDue * (float) config('rpt.discount_rate'), 2);
    }

    /**
     * Penalty for late payment: penalty_per_month per month, capped at
     * penalty_cap, applied to the given base amount (full tax due for full
     * payment, the quarterly amount for an installment).
     */
    public function penalty(float $base, int $monthsLate): float
    {
        $percent = min($monthsLate * (float) config('rpt.penalty_per_month'), (float) config('rpt.penalty_cap'));

        return round($base * $percent, 2);
    }

    /** Row total for a full payment: tax due − discount + penalty. */
    public function fullTotal(float $taxDue, float $discount, float $penalty): float
    {
        return round($taxDue - $discount + $penalty, 2);
    }

    /** Row total for an installment: quarterly amount + penalty. */
    public function installmentTotal(float $quarterly, float $penalty): float
    {
        return round($quarterly + $penalty, 2);
    }
}
