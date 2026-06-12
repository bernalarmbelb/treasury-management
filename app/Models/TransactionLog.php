<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class TransactionLog extends Model
{
    protected $fillable = [
        'serial_number',
        'payee',
        'transacted_at',
        'form_type',
        'status',
    ];

    protected $casts = [
        'transacted_at' => 'datetime',
    ];
}
