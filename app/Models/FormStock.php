<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

class FormStock extends Model
{
    protected $fillable = [
        'qty',
        'form_name',
        'form_code',
        'added_date',
        'added_time',
        'added_by',
    ];

    protected $casts = [
        'added_date' => 'date',
    ];

    /**
     * Apply a new batch of forms: creates a FormBatch record, adds the
     * computed quantity to qty, and updates added_date/added_time to the
     * purchase date.
     */
    public function applyBatch(array $validated, ?string $addedBy = null): FormBatch
    {
        $registrationDate = \Illuminate\Support\Carbon::createFromDate(
            $validated['registration_year'],
            $validated['registration_month'],
            $validated['registration_day'],
        );

        $purchaseDate = \Illuminate\Support\Carbon::createFromDate(
            $validated['purchase_year'],
            $validated['purchase_month'],
            $validated['purchase_day'],
        );

        $batch = $this->batches()->create([
            'registration_date' => $registrationDate,
            'purchase_date' => $purchaseDate,
            'starting_serial_number' => $validated['starting_serial_number'],
            'ending_serial_number' => $validated['ending_serial_number'],
            'added_by' => $addedBy ?? 'System',
        ]);

        // Quantity is derived from the full trailing digits of the serial range
        // (same logic Report Logs uses), so it stays correct for ranges that
        // cross a hundreds/thousands boundary (e.g. …998 → …1005 = 8).
        $this->update([
            'qty' => $this->qty + $batch->startingQty(),
            'added_date' => $purchaseDate,
            'added_time' => now()->format('H:i:s'),
        ]);

        return $batch;
    }

    public function batches(): HasMany
    {
        return $this->hasMany(FormBatch::class);
    }

    public function batchRequests(): HasMany
    {
        return $this->hasMany(BatchRequest::class);
    }

    /**
     * The default Starting Serial Number for a new batch: the serial right
     * after this form's highest existing batch ending serial (e.g. last batch
     * ends "2026-00005" => "2026-00006"). Null when the form has no batches
     * yet, so the first batch is entered manually.
     */
    public function nextBatchStartingSerial(): ?string
    {
        $last = $this->batches
            ->sortByDesc(fn (FormBatch $batch) => $batch->serialRange()[1])
            ->first();

        return $last?->nextSerialNumber();
    }

    /**
     * The oldest batch that still has an unused serial number, used to
     * default the serial number/prefix fields for a new transaction entry.
     */
    public function nextAvailableBatch(): ?FormBatch
    {
        foreach ($this->batches()->oldest()->get() as $batch) {
            if ($batch->nextAvailableSerialNumber() !== null) {
                return $batch;
            }
        }

        return null;
    }

    /**
     * Whether the given certificate prefix + number corresponds to a serial
     * number within one of this form's batches (i.e. it was actually issued
     * as part of an ORAF batch).
     */
    public function hasAvailableSerial(string $prefix, string $number): bool
    {
        return $this->batches->contains(fn (FormBatch $batch) => $batch->matchesCertificate($prefix, $number));
    }

    /**
     * The form's currently available quantity. When ORAF batches have been
     * recorded for this form, this is the sum of each batch's remainingQty()
     * (matching the Report Logs totals); otherwise it falls back to the
     * stored `qty` column.
     */
    public function availableQty(): int
    {
        if ($this->batches->isEmpty()) {
            return $this->qty;
        }

        return $this->batches->sum(fn (FormBatch $batch) => $batch->remainingQty());
    }

    /**
     * Validates a proposed new batch serial range against everything already
     * on record for this form and returns a user-facing "Error in adding
     * batch" message (listing the offending serials) when it must be blocked,
     * or null when the range is free to add. Applies to ALL forms.
     *
     * A serial is considered unavailable when it either:
     *   1. falls inside an existing batch's serial range (same prefix), or
     *   2. has already been issued in a CM Transaction Entry.
     */
    public function batchConflictMessage(string $startingSerialNumber, string $endingSerialNumber): ?string
    {
        $probe = new FormBatch([
            'starting_serial_number' => $startingSerialNumber,
            'ending_serial_number' => $endingSerialNumber,
        ]);
        $probe->setRelation('formStock', $this);

        [$start, $end] = $probe->serialRange();
        $prefix = $probe->serialPrefix();
        preg_match('/(\d+)$/', $startingSerialNumber, $matches);
        $length = strlen($matches[1] ?? '');

        $conflicts = collect();

        // 1) Overlap with an existing batch's serial range (same prefix only,
        //    so a "2026-" batch never collides with a "2025-" batch).
        foreach ($this->batches as $batch) {
            if ($batch->serialPrefix() !== $prefix) {
                continue;
            }

            [$batchStart, $batchEnd] = $batch->serialRange();
            for ($number = max($start, $batchStart); $number <= min($end, $batchEnd); $number++) {
                $conflicts->push($number);
            }
        }

        // 2) Overlap with a serial already issued in a CM Transaction Entry.
        $probe->transactionSerialNumbers()
            ->filter(fn (int $number) => $number >= $start && $number <= $end)
            ->each(fn (int $number) => $conflicts->push($number));

        $conflicts = $conflicts->unique()->sort()->values();

        if ($conflicts->isEmpty()) {
            return null;
        }

        // List the offending serials (cap the enumeration so the alert stays
        // readable for very large overlaps).
        $shown = $conflicts->take(25)
            ->map(fn (int $number) => $prefix . str_pad((string) $number, $length, '0', STR_PAD_LEFT))
            ->implode(', ');

        if ($conflicts->count() > 25) {
            $shown .= ' and ' . ($conflicts->count() - 25) . ' more';
        }

        return "Error in adding batch: serial number {$shown} is already used.";
    }

    public function ctcIndividualTransactions(): HasMany
    {
        return $this->hasMany(CtcIndividualTransaction::class);
    }

    public function ctcCorporationTransactions(): HasMany
    {
        return $this->hasMany(CtcCorporationTransaction::class);
    }

    public function orRptTransactions(): HasMany
    {
        return $this->hasMany(OrRptTransaction::class);
    }

    public function orTransactions(): HasMany
    {
        return $this->hasMany(OrTransaction::class);
    }

    public function marriageCertificateTransactions(): HasMany
    {
        return $this->hasMany(MarriageCertificateTransaction::class);
    }

    public function burialPermitTransactions(): HasMany
    {
        return $this->hasMany(BurialPermitTransaction::class);
    }
}
