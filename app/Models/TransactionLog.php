<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\MorphTo;

class TransactionLog extends Model
{
    protected $fillable = [
        'serial_number',
        'payee',
        'transacted_at',
        'form_type',
        'status',
        'transaction_id',
        'transaction_type',
        'archived_at',
        'amount',
        'payment_method',
        'payment_channel',
        'payer_bank_name',
        'payment_reference',
        'payment_reference_date',
        'recon_status',
        'deposit_id',
    ];

    protected $casts = [
        'transacted_at'          => 'datetime',
        'archived_at'            => 'datetime',
        'amount'                 => 'decimal:2',
        'payment_reference_date' => 'date',
    ];

    public function transaction(): MorphTo
    {
        return $this->morphTo();
    }

    public function cancelRequests(): HasMany
    {
        return $this->hasMany(CancelRequest::class);
    }

    public function deposit(): \Illuminate\Database\Eloquent\Relations\BelongsTo
    {
        return $this->belongsTo(Deposit::class);
    }

    public static function formName(string $formType): string
    {
        return [
            'BIR0016'  => 'Individual Cedula',
            'BIR0017'  => 'Corporation Cedula',
            'Form 10'  => 'Marriage License',
            'Form 5IC' => 'Official Receipt',
            'Form 56'  => 'OR RPT',
            'Form 58'  => 'Burial',
            'Form 53'  => 'Certificate of Ownership of Large Cattle',
            'Form 28A' => 'Certificate of Transfer of Large Cattle',
        ][$formType] ?? $formType;
    }
}
