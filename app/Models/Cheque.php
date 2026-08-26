<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Cheque extends Model
{
    protected $fillable = [
        'bank_account_id',
        'account_name',
        'cheque_date',
        'check_number',
        'pay_to_order_of',
        'amount',
        'amount_in_words',
        'nature_of_payment',
        'status',
        'recon_status',
        'created_by',
        'archived_at',
    ];

    protected $casts = [
        'cheque_date' => 'date',
        'amount' => 'decimal:2',
        'archived_at' => 'datetime',
    ];

    public function bankAccount(): BelongsTo
    {
        return $this->belongsTo(BankAccount::class);
    }

    /**
     * Convert a peso amount to its cheque-style words,
     * e.g. 6090 => "Six thousand ninety and 00/100 pesos".
     */
    public static function spellAmount(float|int|string $amount): string
    {
        $amount = round((float) $amount, 2);
        $pesos = (int) floor($amount);
        $centavos = (int) round(($amount - $pesos) * 100);

        $words = $pesos === 0 ? 'zero' : self::wordsForInteger($pesos);

        return ucfirst(trim($words))
            . ' and ' . str_pad((string) $centavos, 2, '0', STR_PAD_LEFT) . '/100 pesos';
    }

    /** Pure-PHP integer speller (no intl dependency). Handles up to trillions. */
    private static function wordsForInteger(int $number): string
    {
        $ones = ['', 'one', 'two', 'three', 'four', 'five', 'six', 'seven', 'eight', 'nine',
            'ten', 'eleven', 'twelve', 'thirteen', 'fourteen', 'fifteen', 'sixteen',
            'seventeen', 'eighteen', 'nineteen'];
        $tens = ['', '', 'twenty', 'thirty', 'forty', 'fifty', 'sixty', 'seventy', 'eighty', 'ninety'];
        $scales = ['', ' thousand', ' million', ' billion', ' trillion'];

        if ($number < 20) {
            return $ones[$number];
        }

        // Split into 3-digit groups (least significant first).
        $groups = [];
        while ($number > 0) {
            $groups[] = $number % 1000;
            $number = intdiv($number, 1000);
        }

        $parts = [];
        foreach ($groups as $i => $group) {
            if ($group === 0) {
                continue;
            }

            $parts[$i] = self::wordsForHundreds($group, $ones, $tens) . $scales[$i];
        }

        return trim(implode(' ', array_reverse($parts)));
    }

    private static function wordsForHundreds(int $group, array $ones, array $tens): string
    {
        $hundreds = intdiv($group, 100);
        $remainder = $group % 100;

        $text = '';
        if ($hundreds > 0) {
            $text .= $ones[$hundreds] . ' hundred';
        }

        if ($remainder > 0) {
            $text .= $text ? ' ' : '';

            if ($remainder < 20) {
                $text .= $ones[$remainder];
            } else {
                $text .= $tens[intdiv($remainder, 10)];
                if ($remainder % 10 > 0) {
                    $text .= ' ' . $ones[$remainder % 10];
                }
            }
        }

        return $text;
    }
}
