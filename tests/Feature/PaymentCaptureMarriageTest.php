<?php

use App\Models\FormStock;
use App\Models\MarriageCertificateTransaction;
use App\Models\TransactionLog;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;

uses(RefreshDatabase::class);

it('captures a fee + payment on a Marriage transaction log', function () {
    $user = User::factory()->create(['status' => User::STATUS_ACTIVATED]);
    $stock = FormStock::create(['qty' => 10, 'form_name' => 'Marriage License', 'form_code' => 'Form 10', 'added_date' => now()->toDateString(), 'added_by' => 'T']);

    $this->actingAs($user)->postJson("/collections/transaction-entry/{$stock->id}/marriage-certificate", [
        'certificate_number' => '0000001',
        'husband_name' => 'Jose Rizal',
        'wife_name' => 'Josephine Bracken',
        'fee_amount' => 150,
        'payment_method' => 'cash',
    ])->assertOk();

    $log = TransactionLog::first();
    expect((float) $log->amount)->toBe(150.0);
    expect($log->payment_method)->toBe('cash');
    expect((float) MarriageCertificateTransaction::first()->fee_amount)->toBe(150.0);
});
