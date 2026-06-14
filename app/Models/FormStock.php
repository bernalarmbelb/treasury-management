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
        'added_by',
    ];

    protected $casts = [
        'added_date' => 'date',
    ];

    public function batches(): HasMany
    {
        return $this->hasMany(FormBatch::class);
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
}
