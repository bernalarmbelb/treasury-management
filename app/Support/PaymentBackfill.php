<?php

namespace App\Support;

use App\Models\BurialPermitTransaction;
use App\Models\CtcCorporationTransaction;
use App\Models\CtcIndividualTransaction;
use App\Models\MarriageCertificateTransaction;
use App\Models\OrRptTransaction;
use App\Models\OrTransaction;
use App\Models\TransactionLog;

class PaymentBackfill
{
    /** Amount field per polymorphic transaction class. */
    private const AMOUNT_FIELD = [
        OrTransaction::class => 'total',
        CtcIndividualTransaction::class => 'amount_paid',
        CtcCorporationTransaction::class => 'amount_paid',
        OrRptTransaction::class => 'amount_paid',
        BurialPermitTransaction::class => 'fee_amount',
        MarriageCertificateTransaction::class => 'fee_amount',
    ];

    /**
     * Give every existing log a normalized amount (from its linked transaction),
     * defaulting method to cash and recon status to pending where unset.
     */
    public static function run(): void
    {
        TransactionLog::with('transaction')->chunkById(200, function ($logs) {
            foreach ($logs as $log) {
                $field = self::AMOUNT_FIELD[$log->transaction_type] ?? null;
                $amount = ($field && $log->transaction) ? (float) ($log->transaction->{$field} ?? 0) : 0;

                $log->forceFill([
                    'amount'         => $log->amount ?? $amount,
                    'payment_method' => $log->payment_method ?: 'cash',
                    'recon_status'   => $log->recon_status ?: 'pending',
                ])->save();
            }
        });
    }
}
