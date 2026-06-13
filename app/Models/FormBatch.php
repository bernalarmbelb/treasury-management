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
}
