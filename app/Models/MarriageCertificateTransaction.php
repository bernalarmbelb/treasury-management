<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class MarriageCertificateTransaction extends Model
{
    protected $fillable = [
        'form_stock_id',
        'certificate_number',
        'husband_name',
        'husband_age_years',
        'husband_age_months',
        'husband_address',
        'wife_name',
        'wife_age_years',
        'wife_age_months',
        'wife_address',
        'witness_day',
        'witness_month',
        'witness_year',
        'instructions_day',
        'instructions_month',
        'instructions_year',
        'registry_number',
        'local_civil_registrar_of',
        'email',
        'message',
        'fee_amount',
    ];

    protected $casts = [
        'fee_amount' => 'decimal:2',
    ];

    public function formStock(): BelongsTo
    {
        return $this->belongsTo(FormStock::class);
    }
}
