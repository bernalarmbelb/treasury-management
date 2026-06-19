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
    public function applyBatch(array $validated, ?string $addedBy = null): void
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

        $startingNumber = (int) substr($validated['starting_serial_number'], -3);
        $endingNumber = (int) substr($validated['ending_serial_number'], -3);
        $qty = max(0, $endingNumber - $startingNumber + 1);

        $this->batches()->create([
            'registration_date' => $registrationDate,
            'purchase_date' => $purchaseDate,
            'starting_serial_number' => $validated['starting_serial_number'],
            'ending_serial_number' => $validated['ending_serial_number'],
            'added_by' => $addedBy ?? 'System',
        ]);

        $this->update([
            'qty' => $this->qty + $qty,
            'added_date' => $purchaseDate,
            'added_time' => now()->format('H:i:s'),
        ]);
    }

    public function batches(): HasMany
    {
        return $this->hasMany(FormBatch::class);
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
     * If the given (not yet saved) batch serial range overlaps a certificate
     * already recorded in a CM Transaction Entry for this form, returns the
     * conflicting certificate (e.g. "CCI2026-00007"); otherwise null. Only
     * forms with a certificate prefix (BIR0016/BIR0017) are checked.
     */
    public function conflictingCertificate(string $startingSerialNumber, string $endingSerialNumber): ?string
    {
        if (! in_array($this->form_code, ['BIR0016', 'BIR0017'])) {
            return null;
        }

        $batch = new FormBatch([
            'starting_serial_number' => $startingSerialNumber,
            'ending_serial_number' => $endingSerialNumber,
        ]);
        $batch->setRelation('formStock', $this);

        return $batch->conflictingCertificate();
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
}
