<?php

namespace Database\Seeders;

use App\Models\BurialPermitTransaction;
use App\Models\Cheque;
use App\Models\CtcCorporationTransaction;
use App\Models\CtcIndividualTransaction;
use App\Models\FormStock;
use App\Models\MarriageCertificateTransaction;
use App\Models\OrRptTransaction;
use App\Models\OrTransaction;
use App\Models\RptProperty;
use App\Models\TransactionLog;
use Illuminate\Database\Seeder;
use Illuminate\Support\Carbon;

/**
 * Adds 52 dummy transactions to each collection form so every printable
 * receipt and every Reporting & Abstract report can be previewed fully
 * populated: Individual Cedula, Corporation Cedula, OR/RPT, Official
 * Receipt, Marriage License, Burial. Skips the two Large Cattle certificate
 * forms (Form 53 / Form 28A) — no dedicated transaction model/print view
 * exists for them.
 *
 * Continues each form's serial numbers after whatever batches/transactions
 * are already on record (mirrors ChequeDummySeeder's approach), so it's
 * safe to run on a database that already has real or seeded entries.
 *
 * Run: php artisan db:seed --class=DummyReportPreviewSeeder
 */
class DummyReportPreviewSeeder extends Seeder
{
    private const COUNT = 52;

    private array $collectors = ['Juan Dela Cruz', 'Maria Santos', 'Jose Ramirez'];

    private array $treasurers = ['Marlaw Sol Emata', 'Cleofe Villanueva'];

    private array $surnames = [
        'Reyes', 'Santos', 'Cruz', 'Bautista', 'Ocampo', 'Garcia', 'Torres', 'Flores',
        'Ramos', 'Mendoza', 'Aquino', 'Villanueva', 'Domingo', 'Fernandez', 'Del Rosario',
        'Rivera', 'Gonzales', 'Castillo', 'Salazar', 'Navarro', 'Pascual', 'Aguilar',
        'Manalo', 'Esperida', 'Sirit', 'Emata', 'Ferrer', 'Lopez', 'Ramirez', 'Dizon',
    ];

    private array $maleFirstNames = [
        'Juan', 'Pedro', 'Jose', 'Ramon', 'Antonio', 'Ernesto', 'Ricardo', 'Danilo',
        'Rolando', 'Eduardo', 'Ferdinand', 'Renato', 'Marlon', 'Nestor', 'Bienvenido',
        'Alfredo', 'Roberto', 'Vicente', 'Armando', 'Cesar',
    ];

    private array $femaleFirstNames = [
        'Maria', 'Ana', 'Carmen', 'Rosario', 'Teresita', 'Corazon', 'Josefina', 'Leonor',
        'Remedios', 'Consuelo', 'Evangeline', 'Milagros', 'Perla', 'Angelica', 'Cristina',
        'Elena', 'Fe', 'Gloria', 'Imelda', 'Norma',
    ];

    private array $barangays = [
        'Poblacion', 'Sto. Domingo', 'Panggoy', 'Cagbolo', 'Tomalaytay', 'San Isidro',
        'Bulawan', 'Magsaysay', 'San Rafael', 'Casini', 'Del Pilar', 'Sabang',
    ];

    private array $businessTypes = [
        'Trading', 'Enterprises', 'Hardware', 'Construction Supply', 'Merchandising',
        'Agrivet Supply', 'Rice Mill', 'General Store', 'Marketing Corp.',
    ];

    private array $professions = [
        'Farmer', 'Fisherman', 'Public School Teacher', 'Market Vendor', 'Tricycle Driver',
        'Government Employee', 'Businessman', 'OFW', 'Carpenter', 'Barangay Official',
        'Nurse', 'Civil Engineer', 'Laborer', 'Sari-Sari Store Owner',
    ];

    public function run(): void
    {
        mt_srand(74205);

        $this->seedIndividualCedula();
        $this->seedCorporationCedula();
        $this->seedOrRpt();
        $this->seedOfficialReceipt();
        $this->seedMarriageCertificate();
        $this->seedBurial();

        mt_srand();

        $this->command?->info('Added ' . self::COUNT . ' dummy transactions to each of 6 collection forms (cattle forms skipped).');
    }

    // ── Individual Cedula (BIR0016) ─────────────────────────────────────────

    private function seedIndividualCedula(): void
    {
        $form = FormStock::where('form_code', 'BIR0016')->first();
        if (! $form) {
            return;
        }

        $width = 9;
        $start = $this->nextSerialStart($form, $width);
        $this->makeCollectorBatches($form, $start, $width, self::COUNT + 8);

        for ($i = 0; $i < self::COUNT; $i++) {
            $number = str_pad((string) ($start + $i), $width, '0', STR_PAD_LEFT);
            $isMale = ($i % 2) === 0;
            $surname = $this->pick($this->surnames, $i);
            $first = $isMale ? $this->pick($this->maleFirstNames, $i) : $this->pick($this->femaleFirstNames, $i);
            $middle = $this->pick($this->surnames, $i + 7);
            $date = $this->spreadDate($i, self::COUNT);
            $dob = $date->copy()->subYears(18 + ($i % 50))->subDays($i);

            $income = 15000 + (($i * 3771) % 235000);
            $item1Due = min(round($income / 1000, 2), 4995.00);
            $hasProperty = ($i % 3) === 0;
            $item2Amt = $hasProperty ? 50000 + (($i * 971) % 400000) : 0;
            $item2Due = $hasProperty ? min(round($item2Amt / 1000, 2), 4995.00) : 0;
            $totalDue = min(5.00 + $item1Due + $item2Due, 5000.00);
            $interest = ($i % 11 === 0) ? round($totalDue * 0.02, 2) : 0;
            $amountPaid = round($totalDue + $interest, 2);

            $transaction = CtcIndividualTransaction::create([
                'form_stock_id' => $form->id,
                'certificate_prefix' => 'CCI',
                'certificate_number' => $number,
                'year' => $date->year,
                'place_of_issue' => 'Prieto Diaz, Sorsogon',
                'date_issued' => $date,
                'date_issued_2' => $date,
                'surname' => $surname,
                'first_name' => $first,
                'middle_name' => $middle,
                'tin' => (string) (100000000 + (($i * 7919) % 899999999)),
                'sex' => $isMale ? 'Male' : 'Female',
                'citizenship' => 'Filipino',
                'icr_no' => '',
                'place_of_birth' => 'Prieto Diaz, Sorsogon',
                'height' => (150 + ($i % 40)) . ' cm',
                'civil_status' => $this->pick(['Single', 'Married', 'Divorced', 'Widow / Widower / Legally Separated'], $i),
                'weight' => (45 + ($i % 40)) . ' kg',
                'date_of_birth' => $dob,
                'profession' => $this->pick($this->professions, $i),
                'a_community_tax_due' => 5.00,
                'item1_taxable_amount' => $income,
                'item1_community_tax_due' => $item1Due,
                'item2_taxable_amount' => $item2Amt,
                'item2_community_tax_due' => $item2Due,
                'item3_taxable_amount' => 0,
                'item3_community_tax_due' => 0,
                'total_community_tax_due' => $totalDue,
                'interest' => $interest,
                'amount_paid' => $amountPaid,
                'amount_in_words' => Cheque::spellAmount($amountPaid),
                'treasurer_name' => $this->pick($this->treasurers, $i),
            ]);

            $form->update(['qty' => max(0, $form->qty - 1)]);

            $payment = $this->randomPayment($i);
            TransactionLog::create([
                'serial_number' => 'CCI ' . $number,
                'payee' => trim("{$surname}, {$first} {$middle}"),
                'transacted_at' => $date,
                'form_type' => $form->form_code,
                'status' => 'Completed',
                'transaction_id' => $transaction->id,
                'transaction_type' => CtcIndividualTransaction::class,
                'amount' => $amountPaid,
                ...$payment,
            ]);
        }
    }

    // ── Corporation Cedula (BIR0017) ────────────────────────────────────────

    private function seedCorporationCedula(): void
    {
        $form = FormStock::where('form_code', 'BIR0017')->first();
        if (! $form) {
            return;
        }

        $width = 9;
        $start = $this->nextSerialStart($form, $width);
        $this->makeCollectorBatches($form, $start, $width, self::COUNT + 8);

        for ($i = 0; $i < self::COUNT; $i++) {
            $number = str_pad((string) ($start + $i), $width, '0', STR_PAD_LEFT);
            $date = $this->spreadDate($i, self::COUNT);
            $company = $this->pick($this->surnames, $i) . ' ' . $this->pick($this->businessTypes, $i + 3);

            $capital = 100000 + (($i * 15731) % 4900000);
            $grossReceipts = 200000 + (($i * 11213) % 9800000);
            $item1Due = min(round($capital / 5000, 2), 9995.00);
            $item2Due = min(round($grossReceipts / 5000, 2), 9995.00);
            $totalDue = min(500.00 + $item1Due + $item2Due, 10500.00);
            $interest = ($i % 9 === 0) ? round($totalDue * 0.02, 2) : 0;
            $amountPaid = round($totalDue + $interest, 2);

            $transaction = CtcCorporationTransaction::create([
                'form_stock_id' => $form->id,
                'certificate_prefix' => 'CCC',
                'certificate_number' => $number,
                'year' => $date->year,
                'place_of_issue' => 'Prieto Diaz, Sorsogon',
                'date_issued' => $date,
                'company_name' => $company,
                'tin' => (string) (200000000 + (($i * 8123) % 799999999)),
                'date_of_registration' => $date->copy()->subYears(1 + ($i % 15)),
                'address' => 'Brgy. ' . $this->pick($this->barangays, $i) . ', Prieto Diaz, Sorsogon',
                'kind_of_organization' => $this->pick(['Corporation', 'Association', 'Partnership'], $i),
                'nature_of_business' => $this->pick(['Retail Trading', 'Construction Services', 'Agri-Supply', 'Hardware', 'Food Services', 'Rice Milling', 'General Merchandise'], $i),
                'a_community_tax_due' => 500.00,
                'item1_taxable_amount' => $capital,
                'item1_community_tax_due' => $item1Due,
                'item2_taxable_amount' => $grossReceipts,
                'item2_community_tax_due' => $item2Due,
                'total_community_tax_due' => $totalDue,
                'interest' => $interest,
                'amount_paid' => $amountPaid,
                'amount_in_words' => Cheque::spellAmount($amountPaid),
                'treasurer_name' => $this->pick($this->treasurers, $i),
            ]);

            $form->update(['qty' => max(0, $form->qty - 1)]);

            $payment = $this->randomPayment($i);
            TransactionLog::create([
                'serial_number' => 'CCC ' . $number,
                'payee' => $company,
                'transacted_at' => $date,
                'form_type' => $form->form_code,
                'status' => 'Completed',
                'transaction_id' => $transaction->id,
                'transaction_type' => CtcCorporationTransaction::class,
                'amount' => $amountPaid,
                ...$payment,
            ]);
        }
    }

    // ── OR/RPT (Form 56) ─────────────────────────────────────────────────

    private function seedOrRpt(): void
    {
        $form = FormStock::where('form_code', 'Form 56')->first();
        if (! $form) {
            return;
        }

        $width = 7;
        $start = $this->nextSerialStart($form, $width);
        $this->makeCollectorBatches($form, $start, $width, self::COUNT + 8);

        for ($i = 0; $i < self::COUNT; $i++) {
            $number = str_pad((string) ($start + $i), $width, '0', STR_PAD_LEFT);
            $date = $this->spreadDate($i, self::COUNT);
            $owner = $this->pick($this->surnames, $i) . ', ' . $this->pick($i % 2 === 0 ? $this->maleFirstNames : $this->femaleFirstNames, $i);
            $barangay = $this->pick($this->barangays, $i);

            $land = 30000 + (($i * 4231) % 300000);
            $improvement = ($i % 2 === 0) ? 20000 + (($i * 977) % 150000) : 0;
            $assessedTotal = $land + $improvement;
            $annualTax = round($assessedTotal * 0.01, 2);

            $property = RptProperty::updateOrCreate(
                ['tax_declaration_number' => 'TD-' . $date->year . '-' . str_pad((string) (100 + $i), 6, '0', STR_PAD_LEFT)],
                [
                    'declared_owner' => $owner,
                    'location' => 'Brgy. ' . $barangay . ', Prieto Diaz, Sorsogon',
                    'lot_block_number' => 'Lot ' . (10 + $i) . ', Blk ' . (1 + ($i % 6)),
                    'municipality_province' => 'Prieto Diaz, Sorsogon',
                    'city' => 'Prieto Diaz',
                    'assessed_value_land' => $land,
                    'assessed_value_improvement' => $improvement,
                    'assessed_value_total' => $assessedTotal,
                    'annual_tax_due' => $annualTax,
                ]
            );

            $isInstallment = ($i % 4) === 3;
            $quarter = $isInstallment ? (($i % 4) + 1) : null;
            $taxDue = $isInstallment ? round($annualTax / 4, 2) : $annualTax;
            $discount = (! $isInstallment && $i % 5 === 0) ? round($taxDue * 0.10, 2) : 0;
            $penaltyPct = ($i % 13 === 0) ? 2.00 : 0;
            $penaltyAmt = $penaltyPct > 0 ? round($taxDue * $penaltyPct / 100, 2) : 0;
            $entryAmount = round($taxDue - $discount + $penaltyAmt, 2);

            $orRpt = OrRptTransaction::create([
                'form_stock_id' => $form->id,
                'certificate_number' => $number,
                'previous_receipt_number' => $i > 0 ? str_pad((string) ($start + $i - 1), $width, '0', STR_PAD_LEFT) : null,
                'previous_receipt_date' => $i > 0 ? $date->copy()->subYear()->format('Y-m-d') : null,
                'previous_receipt_year' => $i > 0 ? (string) ($date->year - 1) : null,
                'municipality_province' => 'Prieto Diaz, Sorsogon',
                'city' => 'Prieto Diaz',
                'transaction_date' => $date,
                'client_name' => $owner,
                'payment_in_words' => Cheque::spellAmount($entryAmount),
                'amount_paid' => $entryAmount,
                'treasurer_deputy' => $this->pick($this->treasurers, $i),
                'basic_tax' => true,
                'special_education_fund' => ($i % 2) === 0,
            ]);

            $orRpt->entries()->create([
                'rpt_property_id' => $property->id,
                'payment_scheme' => $isInstallment ? 'installment' : 'full',
                'installment_quarter' => $quarter,
                'tax_due' => $taxDue,
                'discount' => $discount,
                'penalty_percent' => $penaltyPct,
                'penalty_amount' => $penaltyAmt,
                'amount' => $entryAmount,
            ]);

            $form->update(['qty' => max(0, $form->qty - 1)]);

            $payment = $this->randomPayment($i);
            TransactionLog::create([
                'serial_number' => $number,
                'payee' => $owner,
                'transacted_at' => $date,
                'form_type' => $form->form_code,
                'status' => 'Completed',
                'transaction_id' => $orRpt->id,
                'transaction_type' => OrRptTransaction::class,
                'amount' => $entryAmount,
                ...$payment,
            ]);
        }
    }

    // ── Official Receipt (Form 5IC) ─────────────────────────────────────────

    private function seedOfficialReceipt(): void
    {
        $form = FormStock::where('form_code', 'Form 5IC')->first();
        if (! $form) {
            return;
        }

        $width = 7;
        $start = max($this->nextSerialStart($form, $width), (OrTransaction::max('id') ?? 0) + 1);
        $this->makeCollectorBatches($form, $start, $width, self::COUNT + 8);

        $descriptions = [
            ['Business Permit Fee', '4-02-01-030'],
            ["Mayor's Permit Fee", '4-02-01-031'],
            ['Sanitary Inspection Fee', '4-02-01-050'],
            ['Garbage Collection Fee', '4-02-01-060'],
            ['Market Stall Rental', '4-02-02-010'],
            ['Building Permit Fee', '4-02-01-070'],
            ['Zoning Clearance Fee', '4-02-01-080'],
            ['Occupational Tax', '4-02-01-090'],
            ['Miscellaneous Fees', '4-02-01-999'],
        ];

        $methodMap = ['cash', 'check', 'money_order'];

        for ($i = 0; $i < self::COUNT; $i++) {
            $number = str_pad((string) ($start + $i), $width, '0', STR_PAD_LEFT);
            $date = $this->spreadDate($i, self::COUNT);
            $payor = $this->pick($this->surnames, $i) . ', ' . $this->pick($i % 2 === 0 ? $this->maleFirstNames : $this->femaleFirstNames, $i);

            $lineCount = 1 + ($i % 3);
            $items = [];
            $total = 0;
            for ($l = 0; $l < $lineCount; $l++) {
                [$desc, $code] = $descriptions[($i + $l) % count($descriptions)];
                $amount = 150 + (($i * 137 + $l * 53) % 4850);
                $items[] = ['description' => $desc, 'account_code' => $code, 'amount' => $amount];
                $total += $amount;
            }

            $method = $methodMap[$i % 3];
            $draweeBank = $method === 'check' ? $this->pick(['Land Bank of the Philippines', 'DBP', 'Metrobank'], $i) : null;
            $checkNumber = $method === 'check' ? (string) (600000 + $i) : null;
            $checkDate = $method === 'check' ? $date : null;

            $transaction = OrTransaction::create([
                'form_stock_id' => $form->id,
                'certificate_number' => $number,
                'date_issued' => $date,
                'agency' => 'Office of the Municipal Treasurer',
                'fund' => $this->pick(['General Fund', 'Special Education Fund', 'Trust Fund'], $i),
                'payor' => $payor,
                'items' => $items,
                'total' => $total,
                'amount_in_words' => Cheque::spellAmount($total),
                'payment_method' => $method,
                'drawee_bank' => $draweeBank,
                'check_number' => $checkNumber,
                'check_date' => $checkDate,
            ]);

            $form->update(['qty' => max(0, $form->qty - 1)]);

            $orMethod = $method === 'check' ? 'cheque' : $method;
            TransactionLog::create([
                'serial_number' => 'No. ' . $number . ' U',
                'payee' => $payor,
                'transacted_at' => $date,
                'form_type' => $form->form_code,
                'status' => 'Completed',
                'transaction_id' => $transaction->id,
                'transaction_type' => OrTransaction::class,
                'amount' => $total,
                'payment_method' => $orMethod,
                'payment_channel' => null,
                'payer_bank_name' => $orMethod === 'cheque' ? $draweeBank : null,
                'payment_reference' => $orMethod === 'cheque' ? $checkNumber : null,
                'payment_reference_date' => $orMethod === 'cheque' ? $checkDate : null,
                'recon_status' => 'pending',
            ]);
        }
    }

    // ── Marriage License (Form 10) ──────────────────────────────────────────

    private function seedMarriageCertificate(): void
    {
        $form = FormStock::where('form_code', 'Form 10')->first();
        if (! $form) {
            return;
        }

        $width = 7;
        $start = $this->nextSerialStart($form, $width);
        $this->makeCollectorBatches($form, $start, $width, self::COUNT + 8);

        for ($i = 0; $i < self::COUNT; $i++) {
            $number = str_pad((string) ($start + $i), $width, '0', STR_PAD_LEFT);
            $date = $this->spreadDate($i, self::COUNT);
            $husband = $this->pick($this->maleFirstNames, $i) . ' ' . $this->pick($this->surnames, $i);
            $wife = $this->pick($this->femaleFirstNames, $i + 5) . ' ' . $this->pick($this->surnames, $i + 2);
            $fee = 200 + (($i % 5) * 50);

            $transaction = MarriageCertificateTransaction::create([
                'form_stock_id' => $form->id,
                'certificate_number' => $number,
                'husband_name' => $husband,
                'husband_age_years' => 21 + ($i % 30),
                'husband_age_months' => $i % 12,
                'husband_address' => 'Brgy. ' . $this->pick($this->barangays, $i) . ', Prieto Diaz, Sorsogon',
                'wife_name' => $wife,
                'wife_age_years' => 20 + ($i % 28),
                'wife_age_months' => ($i + 3) % 12,
                'wife_address' => 'Brgy. ' . $this->pick($this->barangays, $i + 1) . ', Prieto Diaz, Sorsogon',
                'witness_day' => (string) (1 + ($i % 28)),
                'witness_month' => $date->format('F'),
                'witness_year' => $date->format('y'),
                'instructions_day' => (string) (1 + ($i % 28)),
                'instructions_month' => $date->format('F'),
                'instructions_year' => $date->format('y'),
                'registry_number' => $date->year . '-' . str_pad((string) ($i + 1), 5, '0', STR_PAD_LEFT),
                'local_civil_registrar_of' => 'Prieto Diaz, Sorsogon',
                'email' => null,
                'message' => null,
                'fee_amount' => $fee,
            ]);

            $form->update(['qty' => max(0, $form->qty - 1)]);

            $payment = $this->randomPayment($i);
            TransactionLog::create([
                'serial_number' => 'No. ' . $number,
                'payee' => $husband . ' & ' . $wife,
                'transacted_at' => $date,
                'form_type' => $form->form_code,
                'status' => 'Completed',
                'transaction_id' => $transaction->id,
                'transaction_type' => MarriageCertificateTransaction::class,
                'amount' => $fee,
                ...$payment,
            ]);
        }
    }

    // ── Burial (Form 58) ────────────────────────────────────────────────────

    private function seedBurial(): void
    {
        $form = FormStock::where('form_code', 'Form 58')->first();
        if (! $form) {
            return;
        }

        $width = 7;
        $start = $this->nextSerialStart($form, $width);
        $this->makeCollectorBatches($form, $start, $width, self::COUNT + 8);

        $causes = ['Cardiac Arrest', 'Pneumonia', 'Natural Causes', 'Respiratory Failure', 'Hypertension', 'Diabetes Complications', 'Old Age', 'Stroke'];
        $cemeteries = ['Prieto Diaz Public Cemetery', 'Sto. Niño Memorial Park', 'Barangay Poblacion Cemetery', 'Holy Cross Cemetery'];
        $permissionTypes = ['Inter', 'Inter', 'Inter', 'Disinter', 'Remove'];

        for ($i = 0; $i < self::COUNT; $i++) {
            $number = str_pad((string) ($start + $i), $width, '0', STR_PAD_LEFT);
            $date = $this->spreadDate($i, self::COUNT);
            $isMale = ($i % 2) === 0;
            $deceased = $this->pick($this->surnames, $i) . ', ' . $this->pick($isMale ? $this->maleFirstNames : $this->femaleFirstNames, $i);
            $applicant = $this->pick($this->surnames, $i + 4) . ', ' . $this->pick($i % 2 === 0 ? $this->femaleFirstNames : $this->maleFirstNames, $i + 4);
            $permission = $this->pick($permissionTypes, $i);
            $isDisinter = $permission === 'Disinter';
            $fee = 100 + (($i % 4) * 25);

            $transaction = BurialPermitTransaction::create([
                'form_stock_id' => $form->id,
                'certificate_number' => $number,
                'series_letter' => 'C',
                'applicant_name' => $applicant,
                'city_municipality' => 'Prieto Diaz',
                'province' => 'Sorsogon',
                'permission_type' => $permission,
                'deceased_name' => $deceased,
                'nationality' => 'Filipino',
                'age' => 1 + ($i % 89),
                'sex' => $isMale ? 'M' : 'F',
                'date_of_death' => $date->copy()->subDays(1 + ($i % 5)),
                'cause_of_death' => $this->pick($causes, $i),
                'cemetery_name' => $this->pick($cemeteries, $i),
                'infectious' => $isDisinter ? $this->pick(['Non-infectious', 'Infectious'], $i) : null,
                'embalmed' => $isDisinter ? $this->pick(['Embalmed', 'Not Embalmed'], $i) : null,
                'disposition' => $isDisinter ? 'Re-interment at ' . $this->pick($cemeteries, $i + 1) : null,
                'fee_amount' => $fee,
                'date_issued' => $date,
                'municipal_secretary' => $this->pick($this->treasurers, $i),
            ]);

            $form->update(['qty' => max(0, $form->qty - 1)]);

            $payment = $this->randomPayment($i);
            TransactionLog::create([
                'serial_number' => 'No. ' . $number . ' C',
                'payee' => $applicant,
                'transacted_at' => $date,
                'form_type' => $form->form_code,
                'status' => 'Completed',
                'transaction_id' => $transaction->id,
                'transaction_type' => BurialPermitTransaction::class,
                'amount' => $fee,
                ...$payment,
            ]);
        }
    }

    // ── Shared helpers ──────────────────────────────────────────────────────

    /** Deterministic pick from a list, varied per index so it doesn't cycle in lockstep with other fields. */
    private function pick(array $list, int $seed): mixed
    {
        return $list[$seed % count($list)];
    }

    /** Spreads index i of n across the last ~10 weeks up to today, business hours. */
    private function spreadDate(int $i, int $n): Carbon
    {
        $daysBack = (int) round((($n - 1 - $i) / max(1, $n - 1)) * 70);

        return Carbon::now()->subDays($daysBack)->setTime(8 + ($i % 8), ($i * 7) % 60, 0);
    }

    /** The next free numeric serial (as an int) for this form, continuing after any existing batch's ending serial. */
    private function nextSerialStart(FormStock $form, int $width): int
    {
        $max = $form->batches()->get()->reduce(function (int $carry, $batch) {
            return max($carry, $batch->serialRange()[1]);
        }, 0);

        return $max + 1;
    }

    /** Registers a FormBatch covering [start, start+count) and grows the form's qty, mirroring FormStock::applyBatch(). */
    private function makeBatch(FormStock $form, int $start, int $width, int $count): \App\Models\FormBatch
    {
        $end = $start + $count - 1;
        $purchaseDate = Carbon::now()->subDays(75);

        return $form->applyBatch([
            'registration_year' => $purchaseDate->year,
            'registration_month' => $purchaseDate->month,
            'registration_day' => $purchaseDate->day,
            'purchase_year' => $purchaseDate->year,
            'purchase_month' => $purchaseDate->month,
            'purchase_day' => $purchaseDate->day,
            'starting_serial_number' => str_pad((string) $start, $width, '0', STR_PAD_LEFT),
            'ending_serial_number' => str_pad((string) $end, $width, '0', STR_PAD_LEFT),
        ], 'Seeder');
    }

    /**
     * Splits [start, start+poolSize) into 3 contiguous sub-batches (random
     * sizes) and hands one to each of the 3 active collectors, in a random
     * order. This is how a transaction's serial number ends up "belonging"
     * to a given collector (Report Logs / My Batch Report scope by which
     * batch's range a certificate number falls into), so transactions later
     * created across [start, start+COUNT) land under a mix of collectors
     * instead of a single one.
     */
    private function makeCollectorBatches(FormStock $form, int $start, int $width, int $poolSize): void
    {
        $sizes = $this->randomSplit($poolSize, count($this->collectors));
        $order = $this->collectors;
        shuffle($order);

        $cursor = $start;
        foreach ($sizes as $index => $size) {
            $batch = $this->makeBatch($form, $cursor, $width, $size);
            $batch->update(['assigned_to' => $order[$index]]);
            $cursor += $size;
        }
    }

    /** Splits $total into $parts positive, randomly-sized chunks (min 3 each) that sum back to $total. */
    private function randomSplit(int $total, int $parts): array
    {
        $min = 3;
        $remaining = max(0, $total - $min * $parts);

        $cuts = [];
        for ($k = 0; $k < $parts - 1; $k++) {
            $cuts[] = mt_rand(0, $remaining);
        }
        sort($cuts);

        $sizes = [];
        $prev = 0;
        foreach ($cuts as $cut) {
            $sizes[] = $min + ($cut - $prev);
            $prev = $cut;
        }
        $sizes[] = $min + ($remaining - $prev);

        return $sizes;
    }

    /** A plausible payment breakdown for the unified transaction_logs columns. */
    private function randomPayment(int $i): array
    {
        $methods = ['cash', 'cash', 'cash', 'cheque', 'online', 'online', 'money_order'];
        $method = $methods[$i % count($methods)];
        $date = $this->spreadDate($i, self::COUNT);

        return match ($method) {
            'cheque' => [
                'payment_method' => 'cheque',
                'payment_channel' => null,
                'payer_bank_name' => $this->pick(['Land Bank of the Philippines', 'DBP', 'Metrobank', 'BDO', 'PNB'], $i),
                'payment_reference' => (string) (700000 + $i),
                'payment_reference_date' => $date,
                'recon_status' => 'pending',
            ],
            'online' => [
                'payment_method' => 'online',
                'payment_channel' => $this->pick(['GCash', 'LandBank Link.BizPortal', 'Maya', 'Other'], $i),
                'payer_bank_name' => null,
                'payment_reference' => 'REF' . str_pad((string) ($i + 1000), 8, '0', STR_PAD_LEFT),
                'payment_reference_date' => $date,
                'recon_status' => 'pending',
            ],
            'money_order' => [
                'payment_method' => 'money_order',
                'payment_channel' => null,
                'payer_bank_name' => null,
                'payment_reference' => 'MO' . str_pad((string) ($i + 1000), 8, '0', STR_PAD_LEFT),
                'payment_reference_date' => $date,
                'recon_status' => 'pending',
            ],
            default => [
                'payment_method' => 'cash',
                'payment_channel' => null,
                'payer_bank_name' => null,
                'payment_reference' => null,
                'payment_reference_date' => null,
                'recon_status' => 'pending',
            ],
        };
    }
}
