<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class CtcCorporationTransaction extends Model
{
    protected $fillable = [
        'form_stock_id',
        'certificate_prefix',
        'certificate_number',
        'year',
        'place_of_issue',
        'date_issued',
        'company_name',
        'tin',
        'date_of_registration',
        'address',
        'kind_of_organization',
        'nature_of_business',
        'a_community_tax_due',
        'item1_taxable_amount',
        'item1_community_tax_due',
        'item2_taxable_amount',
        'item2_community_tax_due',
        'total_community_tax_due',
        'interest',
        'amount_paid',
        'amount_in_words',
        'treasurer_name',
    ];

    protected $casts = [
        'date_issued' => 'date',
        'date_of_registration' => 'date',
    ];

    public function formStock(): BelongsTo
    {
        return $this->belongsTo(FormStock::class);
    }
}
