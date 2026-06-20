<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class FormBatch extends Model
{
    protected $fillable = [
        'form_stock_id',
        'registration_date',
        'purchase_date',
        'starting_serial_number',
        'ending_serial_number',
        'assigned_to',
        'added_by',
    ];

    protected $casts = [
        'registration_date' => 'date',
        'purchase_date' => 'date',
    ];

    public function formStock(): BelongsTo
    {
        return $this->belongsTo(FormStock::class);
    }

    /**
     * Starting quantity for this batch, computed from the trailing digits of
     * the starting/ending serial numbers, inclusive (e.g. "2026-00001" to
     * "2026-00005" => 5).
     */
    public function startingQty(): int
    {
        [$start, $end] = $this->serialRange();

        return max(0, $end - $start + 1);
    }

    /**
     * Number of serial numbers within this batch's range that have already
     * been used in a CMTE transaction (Individual/Corporation Cedula, OR RPT,
     * Official Receipt) for the same form stock.
     */
    public function usedQty(): int
    {
        [$start, $end] = $this->serialRange();

        return $this->transactionSerialNumbers()
            ->filter(fn (int $number) => $number >= $start && $number <= $end)
            ->unique()
            ->count();
    }

    public function remainingQty(): int
    {
        return max(0, $this->startingQty() - $this->usedQty());
    }

    public function status(): string
    {
        return $this->remainingQty() === 0 ? 'Complete' : 'Incomplete';
    }

    /**
     * The oldest unused serial number within this batch's range, formatted
     * to match the starting serial number's prefix/padding, or null if every
     * serial number in this batch has already been used.
     */
    public function nextAvailableSerialNumber(): ?string
    {
        [$start, $end] = $this->serialRange();
        $used = $this->transactionSerialNumbers()->flip();
        $length = strlen($this->trailingDigits($this->starting_serial_number));

        for ($number = $start; $number <= $end; $number++) {
            if (! $used->has($number)) {
                return str_pad((string) $number, $length, '0', STR_PAD_LEFT);
            }
        }

        return null;
    }

    /**
     * The ending serial number formatted to match the starting serial
     * number's prefix/padding, e.g. starting "2026-00001" with an ending
     * input of "005" displays as "2026-00005".
     */
    public function displayEndingSerialNumber(): string
    {
        $startingDigits = $this->trailingDigits($this->starting_serial_number);
        $endingDigits = $this->trailingDigits($this->ending_serial_number);

        if ($startingDigits === '' || $endingDigits === '') {
            return $this->ending_serial_number;
        }

        $length = strlen($startingDigits);
        $padded = str_pad((string) ((int) $endingDigits), $length, '0', STR_PAD_LEFT);

        return substr($this->starting_serial_number, 0, -$length) . $padded;
    }

    /**
     * The non-numeric portion of the starting serial number, e.g. "2026-" for
     * "2026-00001".
     */
    public function serialPrefix(): string
    {
        $digits = $this->trailingDigits($this->starting_serial_number);

        return $digits === '' ? $this->starting_serial_number : substr($this->starting_serial_number, 0, -strlen($digits));
    }

    /**
     * The certificate prefix a CM Transaction Entry record must use to be
     * considered part of this batch, e.g. "CCI2026-" for a BIR0016 batch
     * whose starting serial number is "2026-00001".
     */
    public function expectedCertificatePrefix(): string
    {
        $abbreviation = match ($this->formStock?->form_code) {
            'BIR0016' => 'CCI',
            'BIR0017' => 'CCC',
            default => '',
        };

        return $abbreviation . $this->serialPrefix();
    }

    /**
     * Whether a CM Transaction Entry certificate (prefix + number) belongs to
     * this batch, i.e. its prefix matches and its number falls within range.
     */
    public function matchesCertificate(string $prefix, string $number): bool
    {
        if ($prefix !== $this->expectedCertificatePrefix()) {
            return false;
        }

        [$start, $end] = $this->serialRange();
        $value = $this->trailingNumber($number);

        return $value >= $start && $value <= $end;
    }

    /**
     * The numeric serial range [start, end] for this batch, derived from the
     * trailing digits of the starting/ending serial numbers.
     */
    public function serialRange(): array
    {
        $start = $this->trailingNumber($this->starting_serial_number);
        $end = $this->trailingNumber($this->ending_serial_number);

        return $start <= $end ? [$start, $end] : [$end, $start];
    }

    /**
     * Numeric certificate/serial numbers of CMTE transactions recorded for
     * this batch's form stock that belong to this batch (matching prefix for
     * BIR0016/BIR0017), used to compute the used count.
     */
    private function transactionSerialNumbers(): \Illuminate\Support\Collection
    {
        $formStock = $this->formStock;

        if (! $formStock) {
            return collect();
        }

        return match ($formStock->form_code) {
            'BIR0016' => $this->matchingCertificateNumbers($formStock->ctcIndividualTransactions),
            'BIR0017' => $this->matchingCertificateNumbers($formStock->ctcCorporationTransactions),
            'Form 56' => $formStock->orRptTransactions
                ->filter(fn ($t) => $this->certificatePrefix($t->certificate_number) === $this->expectedCertificatePrefix())
                ->map(fn ($t) => $this->trailingNumber($t->certificate_number)),
            'Form 5IC' => $formStock->orTransactions->map(fn ($t) => $this->trailingNumber($t->certificate_number)),
            'Form 10' => $formStock->marriageCertificateTransactions->map(fn ($t) => $this->trailingNumber($t->certificate_number)),
            default => collect(),
        };
    }

    /**
     * Trailing numbers of transactions whose certificate_prefix matches this
     * batch's expected prefix, so a "2026-" batch isn't depleted by
     * transactions recorded under an unrelated prefix (e.g. "CCI2022").
     */
    private function matchingCertificateNumbers(\Illuminate\Support\Collection $transactions): \Illuminate\Support\Collection
    {
        $expectedPrefix = $this->expectedCertificatePrefix();

        return $transactions
            ->filter(fn ($t) => ($t->certificate_prefix ?? '') === $expectedPrefix)
            ->map(fn ($t) => $this->trailingNumber($t->certificate_number));
    }

    /**
     * The formatted certificate (expected prefix + padded number) of an
     * existing CMTE transaction whose serial number falls inside this
     * batch's range, or null if none of this batch's serial numbers have
     * already been issued. Used to stop a new ORAF/CM batch from being
     * added when its range overlaps a serial number already recorded in
     * the Transaction Logs.
     */
    public function conflictingCertificate(): ?string
    {
        [$start, $end] = $this->serialRange();
        $length = strlen($this->trailingDigits($this->starting_serial_number));

        $number = $this->transactionSerialNumbers()
            ->first(fn (int $number) => $number >= $start && $number <= $end);

        if ($number === null) {
            return null;
        }

        return $this->expectedCertificatePrefix() . str_pad((string) $number, $length, '0', STR_PAD_LEFT);
    }

    private function trailingNumber(string $serial): int
    {
        return (int) $this->trailingDigits($serial);
    }

    private function trailingDigits(string $serial): string
    {
        preg_match('/(\d+)$/', $serial, $matches);

        return $matches[1] ?? '';
    }

    /**
     * The leading (non-trailing-digit) portion of a certificate number, e.g.
     * "ORRPT" for "ORRPT001" or "" for "0000001". Used to exclude OR/RPT
     * certificates recorded under a different prefix (e.g. legacy synthetic
     * serials) when counting a batch's used serials.
     */
    private function certificatePrefix(string $serial): string
    {
        $digits = $this->trailingDigits($serial);

        return $digits === '' ? $serial : substr($serial, 0, -strlen($digits));
    }
}
