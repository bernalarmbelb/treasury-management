<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class CtcIndividualTransaction extends Model
{
    protected $fillable = [
        'form_stock_id',
        'certificate_number',
        'year',
        'place_of_issue',
        'date_issued',
        'date_issued_2',
        'surname',
        'first_name',
        'middle_name',
        'tin',
        'sex',
        'citizenship',
        'icr_no',
        'place_of_birth',
        'height',
        'civil_status',
        'weight',
        'date_of_birth',
        'profession',
        'a_community_tax_due',
        'item1_taxable_amount',
        'item1_community_tax_due',
        'item2_taxable_amount',
        'item2_community_tax_due',
        'item3_taxable_amount',
        'item3_community_tax_due',
        'total_community_tax_due',
        'interest',
        'amount_paid',
        'amount_in_words',
        'treasurer_name',
    ];

    protected $casts = [
        'date_issued' => 'date',
        'date_issued_2' => 'date',
        'date_of_birth' => 'date',
    ];

    public function formStock(): BelongsTo
    {
        return $this->belongsTo(FormStock::class);
    }
}
