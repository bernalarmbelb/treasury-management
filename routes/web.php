<?php

use Illuminate\Support\Facades\Route;

Route::get('/', function () { return view('index'); })->name('home');

Route::get('/collections', function (\Illuminate\Http\Request $request) {
    $perPageOptions = [10, 25, 50, 100];
    $perPage = in_array((int) $request->input('per_page'), $perPageOptions) ? (int) $request->input('per_page') : 10;

    $sortable = ['serial_number', 'payee', 'transacted_at', 'form_type', 'status'];
    $sort = in_array($request->input('sort'), $sortable) ? $request->input('sort') : 'transacted_at';
    $direction = $request->input('direction') === 'asc' ? 'asc' : 'desc';

    $statusOptions = ['Completed', 'Cancelled'];
    $statuses = array_intersect((array) $request->input('status', []), $statusOptions);

    $formTypeOptions = ['Form 58', 'BIR0017', 'Form 53', 'Form 28A', 'BIR0016', 'Form 10', 'Form 5IC', 'Form 56'];
    $formTypes = array_intersect((array) $request->input('form_type', []), $formTypeOptions);

    $dateStart = $request->input('date_start');
    $dateEnd = $request->input('date_end');

    $transactions = \App\Models\TransactionLog::query()
        ->when($request->input('search'), function ($query, $search) {
            $query->where(function ($query) use ($search) {
                $query->where('serial_number', 'like', "%{$search}%")
                    ->orWhere('payee', 'like', "%{$search}%");
            });
        })
        ->when($statuses, function ($query, $statuses) {
            $query->whereIn('status', $statuses);
        })
        ->when($formTypes, function ($query, $formTypes) {
            $query->whereIn('form_type', $formTypes);
        })
        ->when($dateStart, function ($query, $dateStart) {
            $query->whereDate('transacted_at', '>=', $dateStart);
        })
        ->when($dateEnd, function ($query, $dateEnd) {
            $query->whereDate('transacted_at', '<=', $dateEnd);
        })
        ->orderBy($sort, $direction)
        ->paginate($perPage)
        ->withQueryString();

    $data = [
        'transactions' => $transactions,
        'perPageOptions' => $perPageOptions,
        'perPage' => $perPage,
        'sort' => $sort,
        'direction' => $direction,
    ];

    if ($request->ajax()) {
        return view('collection-management.partials.transactions-table', $data);
    }

    return view('collection-management.index', $data);
})->name('collections');
Route::get('/collections/transaction-entry', function (\Illuminate\Http\Request $request) {
    $perPageOptions = [10, 25, 50, 100];
    $perPage = in_array((int) $request->input('per_page'), $perPageOptions) ? (int) $request->input('per_page') : 10;

    $sortable = ['qty', 'form_name', 'form_code', 'added_date', 'added_by'];
    $sort = in_array($request->input('sort'), $sortable) ? $request->input('sort') : 'form_name';
    $direction = $request->input('direction') === 'desc' ? 'desc' : 'asc';

    $forms = \App\Models\FormStock::query()
        ->when($request->input('search'), function ($query, $search) {
            $query->where('form_name', 'like', "%{$search}%");
        })
        ->orderBy($sort, $direction)
        ->paginate($perPage)
        ->withQueryString();

    $data = [
        'forms' => $forms,
        'perPageOptions' => $perPageOptions,
        'perPage' => $perPage,
        'sort' => $sort,
        'direction' => $direction,
    ];

    if ($request->ajax()) {
        return view('collection-management.transaction-entry.partials.form-stocks-table', $data);
    }

    return view('collection-management.transaction-entry.index', $data);
})->name('transaction-entry');

Route::post('/collections/transaction-entry/{formStock}/batches', function (\Illuminate\Http\Request $request, \App\Models\FormStock $formStock) {
    $validated = $request->validate([
        'registration_month' => ['required', 'integer', 'min:1', 'max:12'],
        'registration_day' => ['required', 'integer', 'min:1', 'max:31'],
        'registration_year' => ['required', 'integer', 'min:1900', 'max:2100'],
        'purchase_month' => ['required', 'integer', 'min:1', 'max:12'],
        'purchase_day' => ['required', 'integer', 'min:1', 'max:31'],
        'purchase_year' => ['required', 'integer', 'min:1900', 'max:2100'],
        'starting_serial_number' => ['required', 'string'],
        'ending_serial_number' => ['required', 'string'],
    ]);

    $registrationDate = \Illuminate\Support\Carbon::createFromDate(
        $validated['registration_year'],
        $validated['registration_month'],
        $validated['registration_day'],
    );

    $purchaseDate = \Illuminate\Support\Carbon::createFromDate(
        $validated['purchase_year'],
        $validated['purchase_month'],
        $validated['purchase_day'],
    );

    $startingNumber = (int) substr($validated['starting_serial_number'], -3);
    $endingNumber = (int) substr($validated['ending_serial_number'], -3);
    $qty = max(0, $endingNumber - $startingNumber + 1);

    $formStock->batches()->create([
        'registration_date' => $registrationDate,
        'purchase_date' => $purchaseDate,
        'starting_serial_number' => $validated['starting_serial_number'],
        'ending_serial_number' => $validated['ending_serial_number'],
        'added_by' => $request->user()?->name ?? 'System',
    ]);

    $formStock->update([
        'qty' => $formStock->qty + $qty,
        'added_date' => $purchaseDate,
    ]);

    $perPageOptions = [10, 25, 50, 100];
    $perPage = in_array((int) $request->input('per_page'), $perPageOptions) ? (int) $request->input('per_page') : 10;

    $sortable = ['qty', 'form_name', 'form_code', 'added_date', 'added_by'];
    $sort = in_array($request->input('sort'), $sortable) ? $request->input('sort') : 'form_name';
    $direction = $request->input('direction') === 'desc' ? 'desc' : 'asc';

    $forms = \App\Models\FormStock::query()
        ->when($request->input('search'), function ($query, $search) {
            $query->where('form_name', 'like', "%{$search}%");
        })
        ->orderBy($sort, $direction)
        ->paginate($perPage)
        ->withQueryString();

    return view('collection-management.transaction-entry.partials.form-stocks-table', [
        'forms' => $forms,
        'perPageOptions' => $perPageOptions,
        'perPage' => $perPage,
        'sort' => $sort,
        'direction' => $direction,
    ]);
})->name('transaction-entry.batches.store');

Route::get('/collections/transaction-entry/{formStock}/individual-cedula', function (\App\Models\FormStock $formStock) {
    return view('collection-management.transaction-entry.individual-cedula', [
        'form' => $formStock,
    ]);
})->name('transaction-entry.individual-cedula');

Route::post('/collections/transaction-entry/{formStock}/individual-cedula', function (\Illuminate\Http\Request $request, \App\Models\FormStock $formStock) {
    $validated = $request->validate([
        'certificate_prefix' => ['nullable', 'string'],
        'certificate_number' => ['required', 'string'],
        'year' => ['required', 'integer', 'min:1900', 'max:2100'],
        'surname' => ['required', 'string'],
        'first_name' => ['required', 'string'],
        'amount_paid' => ['required', 'numeric', 'min:0'],
        'place_of_issue' => ['nullable', 'string'],
        'date_issued' => ['nullable', 'date'],
        'date_issued_2' => ['nullable', 'date'],
        'middle_name' => ['nullable', 'string'],
        'tin' => ['nullable', 'array'],
        'tin.*' => ['nullable', 'string', 'max:1'],
        'sex' => ['nullable', 'string'],
        'citizenship' => ['nullable', 'string'],
        'icr_no' => ['nullable', 'string'],
        'place_of_birth' => ['nullable', 'string'],
        'height' => ['nullable', 'string'],
        'civil_status' => ['nullable', 'string'],
        'weight' => ['nullable', 'string'],
        'date_of_birth' => ['nullable', 'date'],
        'profession' => ['nullable', 'string'],
        'a_community_tax_due' => ['nullable', 'numeric', 'min:0'],
        'item1_taxable_amount' => ['nullable', 'numeric', 'min:0'],
        'item1_community_tax_due' => ['nullable', 'numeric', 'min:0'],
        'item2_taxable_amount' => ['nullable', 'numeric', 'min:0'],
        'item2_community_tax_due' => ['nullable', 'numeric', 'min:0'],
        'item3_taxable_amount' => ['nullable', 'numeric', 'min:0'],
        'item3_community_tax_due' => ['nullable', 'numeric', 'min:0'],
        'total_community_tax_due' => ['nullable', 'numeric', 'min:0'],
        'interest' => ['nullable', 'numeric', 'min:0'],
        'amount_in_words' => ['nullable', 'string'],
        'treasurer_name' => ['nullable', 'string'],
    ]);

    $validated['tin'] = implode('', $validated['tin'] ?? []);

    foreach ([
        'a_community_tax_due',
        'item1_taxable_amount',
        'item1_community_tax_due',
        'item2_taxable_amount',
        'item2_community_tax_due',
        'item3_taxable_amount',
        'item3_community_tax_due',
        'total_community_tax_due',
        'interest',
    ] as $field) {
        $validated[$field] ??= 0;
    }

    $formStock->ctcIndividualTransactions()->create($validated);

    $formStock->update([
        'qty' => max(0, $formStock->qty - 1),
    ]);

    $serialNumber = trim(($validated['certificate_prefix'] ?? '') . ' ' . $validated['certificate_number']);
    $payee = trim(implode(' ', array_filter([
        trim($validated['surname'] . ','),
        $validated['first_name'],
        $validated['middle_name'] ?? null,
    ])));

    \App\Models\TransactionLog::create([
        'serial_number' => $serialNumber,
        'payee' => $payee,
        'transacted_at' => now(),
        'form_type' => $formStock->form_code,
        'status' => 'Completed',
    ]);

    return response()->json([
        'message' => 'Transaction saved successfully.',
        'qty' => $formStock->qty,
        'redirect' => route('collections', [], false),
    ]);
})->name('transaction-entry.individual-cedula.store');

Route::get('/collections/transaction-entry/{formStock}/corporation-cedula', function (\App\Models\FormStock $formStock) {
    return view('collection-management.transaction-entry.corporation-cedula', [
        'form' => $formStock,
    ]);
})->name('transaction-entry.corporation-cedula');

Route::post('/collections/transaction-entry/{formStock}/corporation-cedula', function (\Illuminate\Http\Request $request, \App\Models\FormStock $formStock) {
    $validated = $request->validate([
        'certificate_prefix' => ['nullable', 'string'],
        'certificate_number' => ['required', 'string'],
        'year' => ['required', 'integer', 'min:1900', 'max:2100'],
        'company_name' => ['required', 'string'],
        'amount_paid' => ['required', 'numeric', 'min:0'],
        'place_of_issue' => ['nullable', 'string'],
        'date_issued' => ['nullable', 'date'],
        'tin' => ['nullable', 'array'],
        'tin.*' => ['nullable', 'string', 'max:1'],
        'date_of_registration' => ['nullable', 'date'],
        'address' => ['nullable', 'string'],
        'kind_of_organization' => ['nullable', 'string'],
        'nature_of_business' => ['nullable', 'string'],
        'a_community_tax_due' => ['nullable', 'numeric', 'min:0'],
        'item1_taxable_amount' => ['nullable', 'numeric', 'min:0'],
        'item1_community_tax_due' => ['nullable', 'numeric', 'min:0'],
        'item2_taxable_amount' => ['nullable', 'numeric', 'min:0'],
        'item2_community_tax_due' => ['nullable', 'numeric', 'min:0'],
        'total_community_tax_due' => ['nullable', 'numeric', 'min:0'],
        'interest' => ['nullable', 'numeric', 'min:0'],
        'amount_in_words' => ['nullable', 'string'],
        'treasurer_name' => ['nullable', 'string'],
    ]);

    $validated['tin'] = implode('', $validated['tin'] ?? []);

    foreach ([
        'a_community_tax_due',
        'item1_taxable_amount',
        'item1_community_tax_due',
        'item2_taxable_amount',
        'item2_community_tax_due',
        'total_community_tax_due',
        'interest',
    ] as $field) {
        $validated[$field] ??= 0;
    }

    $formStock->ctcCorporationTransactions()->create($validated);

    $formStock->update([
        'qty' => max(0, $formStock->qty - 1),
    ]);

    \App\Models\TransactionLog::create([
        'serial_number' => trim(($validated['certificate_prefix'] ?? '') . ' ' . $validated['certificate_number']),
        'payee' => $validated['company_name'],
        'transacted_at' => now(),
        'form_type' => $formStock->form_code,
        'status' => 'Completed',
    ]);

    return response()->json([
        'message' => 'Transaction saved successfully.',
        'qty' => $formStock->qty,
        'redirect' => route('collections', [], false),
    ]);
})->name('transaction-entry.corporation-cedula.store');

Route::get('/collections/transaction-entry/{formStock}/or-rpt', function (\App\Models\FormStock $formStock) {
    return view('collection-management.transaction-entry.or-rpt', [
        'form' => $formStock,
        'certificateNumber' => str_pad((\App\Models\OrRptTransaction::max('id') ?? 0) + 1, 7, '0', STR_PAD_LEFT),
    ]);
})->name('transaction-entry.or-rpt');

Route::post('/collections/transaction-entry/{formStock}/or-rpt', function (\Illuminate\Http\Request $request, \App\Models\FormStock $formStock) {
    $validated = $request->validate([
        'previous_receipt_number' => ['nullable', 'string'],
        'previous_receipt_date' => ['nullable', 'string'],
        'previous_receipt_year' => ['nullable', 'string'],
        'municipality_province' => ['nullable', 'string'],
        'city' => ['nullable', 'string'],
        'transaction_date' => ['nullable', 'date'],
        'client_name' => ['required', 'string'],
        'payment_in_words' => ['nullable', 'string'],
        'amount_paid' => ['required', 'numeric', 'min:0'],
        'treasurer_deputy' => ['nullable', 'string'],
        'basic_tax' => ['nullable', 'boolean'],
        'special_education_fund' => ['nullable', 'boolean'],
    ]);

    $validated['certificate_number'] = str_pad((\App\Models\OrRptTransaction::max('id') ?? 0) + 1, 7, '0', STR_PAD_LEFT);

    $formStock->orRptTransactions()->create($validated);

    $formStock->update([
        'qty' => max(0, $formStock->qty - 1),
    ]);

    \App\Models\TransactionLog::create([
        'serial_number' => $validated['certificate_number'],
        'payee' => $validated['client_name'],
        'transacted_at' => now(),
        'form_type' => $formStock->form_code,
        'status' => 'Completed',
    ]);

    return response()->json([
        'message' => 'Transaction saved successfully.',
        'qty' => $formStock->qty,
        'redirect' => route('collections', [], false),
    ]);
})->name('transaction-entry.or-rpt.store');

Route::get('/collections/transaction-entry/{formStock}/official-receipt', function (\App\Models\FormStock $formStock) {
    return view('collection-management.transaction-entry.official-receipt', [
        'form' => $formStock,
        'certificateNumber' => str_pad((\App\Models\OrTransaction::max('id') ?? 0) + 1, 7, '0', STR_PAD_LEFT),
    ]);
})->name('transaction-entry.official-receipt');

Route::post('/collections/transaction-entry/{formStock}/official-receipt', function (\Illuminate\Http\Request $request, \App\Models\FormStock $formStock) {
    $validated = $request->validate([
        'date_issued' => ['nullable', 'date'],
        'agency' => ['nullable', 'string'],
        'fund' => ['nullable', 'string'],
        'payor' => ['required', 'string'],
        'items' => ['required', 'array'],
        'items.*.description' => ['nullable', 'string'],
        'items.*.account_code' => ['nullable', 'string'],
        'items.*.amount' => ['nullable', 'numeric', 'min:0'],
        'total' => ['required', 'numeric', 'min:0'],
        'amount_in_words' => ['nullable', 'string'],
        'payment_method' => ['required', 'in:cash,check,money_order'],
        'drawee_bank' => ['nullable', 'string'],
        'check_number' => ['nullable', 'string'],
        'check_date' => ['nullable', 'date'],
    ]);

    foreach ($validated['items'] as &$item) {
        $item['amount'] = $item['amount'] ?? 0;
    }

    $validated['certificate_number'] = str_pad((\App\Models\OrTransaction::max('id') ?? 0) + 1, 7, '0', STR_PAD_LEFT);

    $formStock->orTransactions()->create($validated);

    $formStock->update([
        'qty' => max(0, $formStock->qty - 1),
    ]);

    \App\Models\TransactionLog::create([
        'serial_number' => 'No. ' . $validated['certificate_number'] . ' U',
        'payee' => $validated['payor'],
        'transacted_at' => now(),
        'form_type' => $formStock->form_code,
        'status' => 'Completed',
    ]);

    return response()->json([
        'message' => 'Transaction saved successfully.',
        'qty' => $formStock->qty,
        'redirect' => route('collections', [], false),
    ]);
})->name('transaction-entry.official-receipt.store');

Route::get('/official-receipts-accountable-forms', function () { return view('official-receipt-accountable-forms.index'); })->name('official-receipts-accountable-forms');
Route::get('/reporting-abstract', function () { return view('reporting-abstract.index'); })->name('reporting-abstract');
Route::get('/bank-deposit-reconciliation', function () { return view('bank-deposit-reconciliation.index'); })->name('bank-deposit-reconciliation');
Route::get('/cheque-management', function () { return view('cheque-management.index'); })->name('cheque-management');
Route::get('/user-management', function () { return view('user-management.index'); })->name('user-management');
Route::get('/records', function () { return view('records.index'); })->name('records');
Route::get('archive-records', function () { return view('archive-records.index'); })->name('archives');