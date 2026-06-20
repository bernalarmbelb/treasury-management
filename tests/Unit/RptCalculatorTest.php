<?php

use App\Support\RptCalculator;

uses(Tests\TestCase::class);

beforeEach(function () {
    config([
        'rpt.basic_tax_rate'    => 0.01,
        'rpt.sef_rate'          => 0.01,
        'rpt.penalty_per_month' => 0.02,
        'rpt.penalty_cap'       => 0.72,
        'rpt.discount_rate'     => 0.10,
    ]);
    $this->calc = new RptCalculator();
});

it('computes tax due as 2% of assessed value', function () {
    expect($this->calc->taxDue(100000))->toBe(2000.0);
});

it('computes the quarterly installment as a quarter of tax due', function () {
    expect($this->calc->quarterly(2000))->toBe(500.0);
});

it('computes the early-payment discount', function () {
    expect($this->calc->discount(2000))->toBe(200.0);
});

it('computes penalty as 2% per month on the base amount', function () {
    expect($this->calc->penalty(2000, 3))->toBe(120.0);
});

it('caps the penalty at 72%', function () {
    expect($this->calc->penalty(2000, 40))->toBe(1440.0); // 2000 * 0.72
});

// Example 1: full payment, on time, with discount
it('totals a full on-time payment as tax due minus discount', function () {
    $taxDue = $this->calc->taxDue(100000);          // 2000
    $discount = $this->calc->discount($taxDue);      // 200
    expect($this->calc->fullTotal($taxDue, $discount, 0))->toBe(1800.0);
});

// Example 2: full payment, late 3 months, no discount
it('totals a full late payment as tax due plus penalty', function () {
    $taxDue = $this->calc->taxDue(100000);          // 2000
    $penalty = $this->calc->penalty($taxDue, 3);     // 120
    expect($this->calc->fullTotal($taxDue, 0, $penalty))->toBe(2120.0);
});

// Example 3: installment Q1, on time
it('totals an on-time installment as the quarterly amount', function () {
    $quarterly = $this->calc->quarterly($this->calc->taxDue(100000)); // 500
    expect($this->calc->installmentTotal($quarterly, 0))->toBe(500.0);
});

// Example 4: installment Q2, late 1 month (penalty on the quarterly base)
it('totals a late installment as quarterly plus penalty on the quarter', function () {
    $quarterly = $this->calc->quarterly($this->calc->taxDue(100000)); // 500
    $penalty = $this->calc->penalty($quarterly, 1);                    // 10
    expect($this->calc->installmentTotal($quarterly, $penalty))->toBe(510.0);
});
