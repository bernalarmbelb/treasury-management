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
}
