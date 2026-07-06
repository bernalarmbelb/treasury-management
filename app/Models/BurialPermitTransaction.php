<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class BurialPermitTransaction extends Model
{
    protected $fillable = [
        'form_stock_id',
        'certificate_number',
        'series_letter',
        'applicant_name',
        'city_municipality',
        'province',
        'permission_type',
        'deceased_name',
        'nationality',
        'age',
        'sex',
        'date_of_death',
        'cause_of_death',
        'cemetery_name',
        'infectious',
        'embalmed',
        'disposition',
        'fee_amount',
        'date_issued',
        'municipal_secretary',
    ];

    protected $casts = [
        'date_of_death' => 'date',
        'date_issued' => 'date',
        'fee_amount' => 'decimal:2',
    ];

    public function formStock(): BelongsTo
    {
        return $this->belongsTo(FormStock::class);
    }
}
