<?php

use Illuminate\Support\Facades\Route;

Route::get('/', function () { return view('index'); })->name('home');

Route::get('/login', function () { return view('login'); })->name('login');

Route::post('/login', function (\Illuminate\Http\Request $request) {
    $credentials = $request->validate([
        'username' => ['required', 'string'],
        'password' => ['required', 'string'],
    ]);

    $user = \App\Models\User::where('username', $credentials['username'])
        ->orWhere('email', $credentials['username'])
        ->first();

    if (! $user || ! \Illuminate\Support\Facades\Hash::check($credentials['password'], $user->password)) {
        return back()->withErrors(['username' => 'Invalid username or password.'])->onlyInput('username');
    }

    if (! $user->isActivated()) {
        return back()->withErrors(['username' => 'This account has been '.$user->status.' and cannot sign in.'])->onlyInput('username');
    }

    \Illuminate\Support\Facades\Auth::login($user, $request->boolean('remember'));
    $request->session()->regenerate();

    return redirect()->intended(route('home'));
})->name('login.attempt');

Route::post('/logout', function (\Illuminate\Http\Request $request) {
    \Illuminate\Support\Facades\Auth::logout();
    $request->session()->invalidate();
    $request->session()->regenerateToken();

    return redirect()->route('login');
})->name('logout');

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
        ->whereNull('archived_at')
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
        'isAdmin' => $request->user()?->hasRole('admin') ?? false,
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

    if ($message = $formStock->batchConflictMessage($validated['starting_serial_number'], $validated['ending_serial_number'])) {
        return response()->json([
            'message' => $message,
        ], 422);
    }

    $formStock->applyBatch($validated, $request->user()?->name);

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
    $batch = $formStock->nextAvailableBatch();

    return view('collection-management.transaction-entry.individual-cedula', [
        'form' => $formStock,
        'nextSerialPrefix' => $batch?->expectedCertificatePrefix(),
        'nextSerialNumber' => $batch?->nextAvailableSerialNumber(),
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

    $certificatePrefix = $validated['certificate_prefix'] ?? '';

    if (! $formStock->hasAvailableSerial($certificatePrefix, $validated['certificate_number'])) {
        return response()->json([
            'message' => "Serial number {$certificatePrefix}{$validated['certificate_number']} was not found in the available stock. Cannot proceed.",
        ], 422);
    }

    if ($formStock->ctcIndividualTransactions()
        ->where('certificate_prefix', $certificatePrefix)
        ->where('certificate_number', $validated['certificate_number'])
        ->exists()) {
        return response()->json([
            'message' => "Serial number {$certificatePrefix}{$validated['certificate_number']} is already taken.",
        ], 422);
    }

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

    $ctcTransaction = $formStock->ctcIndividualTransactions()->create($validated);

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
        'serial_number'    => $serialNumber,
        'payee'            => $payee,
        'transacted_at'    => now(),
        'form_type'        => $formStock->form_code,
        'status'           => 'Completed',
        'transaction_id'   => $ctcTransaction->id,
        'transaction_type' => \App\Models\CtcIndividualTransaction::class,
    ]);

    \App\Models\ActivityLog::record('Collection Management - Add Entry - ' . \App\Models\TransactionLog::formName($formStock->form_code) . ' - ' . $serialNumber);

    return response()->json([
        'message' => 'Transaction saved successfully.',
        'qty' => $formStock->qty,
        'redirect' => route('collections', [], false),
    ]);
})->name('transaction-entry.individual-cedula.store');

Route::get('/collections/transaction-entry/{formStock}/corporation-cedula', function (\App\Models\FormStock $formStock) {
    $batch = $formStock->nextAvailableBatch();

    return view('collection-management.transaction-entry.corporation-cedula', [
        'form' => $formStock,
        'nextSerialPrefix' => $batch?->expectedCertificatePrefix(),
        'nextSerialNumber' => $batch?->nextAvailableSerialNumber(),
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

    $certificatePrefix = $validated['certificate_prefix'] ?? '';

    if (! $formStock->hasAvailableSerial($certificatePrefix, $validated['certificate_number'])) {
        return response()->json([
            'message' => "Serial number {$certificatePrefix}{$validated['certificate_number']} was not found in the available stock. Cannot proceed.",
        ], 422);
    }

    if ($formStock->ctcCorporationTransactions()
        ->where('certificate_prefix', $certificatePrefix)
        ->where('certificate_number', $validated['certificate_number'])
        ->exists()) {
        return response()->json([
            'message' => "Serial number {$certificatePrefix}{$validated['certificate_number']} is already taken.",
        ], 422);
    }

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

    $ctcCorpTransaction = $formStock->ctcCorporationTransactions()->create($validated);

    $formStock->update([
        'qty' => max(0, $formStock->qty - 1),
    ]);

    $serialNumber = trim(($validated['certificate_prefix'] ?? '') . ' ' . $validated['certificate_number']);

    \App\Models\TransactionLog::create([
        'serial_number'    => $serialNumber,
        'payee'            => $validated['company_name'],
        'transacted_at'    => now(),
        'form_type'        => $formStock->form_code,
        'status'           => 'Completed',
        'transaction_id'   => $ctcCorpTransaction->id,
        'transaction_type' => \App\Models\CtcCorporationTransaction::class,
    ]);

    \App\Models\ActivityLog::record('Collection Management - Add Entry - ' . \App\Models\TransactionLog::formName($formStock->form_code) . ' - ' . $serialNumber);

    return response()->json([
        'message' => 'Transaction saved successfully.',
        'qty' => $formStock->qty,
        'redirect' => route('collections', [], false),
    ]);
})->name('transaction-entry.corporation-cedula.store');

Route::get('/collections/transaction-entry/{formStock}/or-rpt', function (\App\Models\FormStock $formStock) {
    $batch = $formStock->nextAvailableBatch();
    $nextSerial = $batch?->nextAvailableSerialNumber();

    return view('collection-management.transaction-entry.or-rpt', [
        'form' => $formStock,
        // Full serial = batch prefix (e.g. "ORRPT") + next available number
        // (e.g. "001"); nextAvailableSerialNumber() returns digits only.
        'certificateNumber' => $nextSerial !== null
            ? $batch->expectedCertificatePrefix() . $nextSerial
            : str_pad((\App\Models\OrRptTransaction::max('id') ?? 0) + 1, 7, '0', STR_PAD_LEFT),
        'rptRates' => config('rpt'),
    ]);
})->name('transaction-entry.or-rpt');

Route::post('/collections/transaction-entry/{formStock}/or-rpt', function (\Illuminate\Http\Request $request, \App\Models\FormStock $formStock) {
    $validated = $request->validate([
        'serial_number' => ['nullable', 'string'],
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

        'entries' => ['required', 'array', 'min:1'],
        'entries.*.tax_declaration_number' => ['required', 'string'],
        'entries.*.declared_owner' => ['required', 'string'],
        'entries.*.location' => ['required', 'string'],
        'entries.*.lot_block_number' => ['nullable', 'string'],
        'entries.*.assessed_value_land' => ['nullable', 'numeric', 'min:0'],
        'entries.*.assessed_value_improvement' => ['nullable', 'numeric', 'min:0'],
        'entries.*.assessed_value_total' => ['required', 'numeric', 'min:0'],
        'entries.*.tax_due' => ['required', 'numeric', 'min:0'],
        'entries.*.payment_scheme' => ['required', 'in:full,installment'],
        'entries.*.installment_quarter' => ['nullable', 'integer', 'between:1,4'],
        'entries.*.discount' => ['nullable', 'numeric', 'min:0'],
        'entries.*.penalty_percent' => ['nullable', 'numeric', 'min:0'],
        'entries.*.penalty_amount' => ['nullable', 'numeric', 'min:0'],
        'entries.*.amount' => ['required', 'numeric', 'min:0'],
    ]);

    // Rule 1 & 2: exactly one scheme per row. 'full' must NOT carry a quarter;
    // 'installment' MUST carry a quarter (1–4).
    $validator = \Illuminate\Support\Facades\Validator::make([], []);
    foreach ($validated['entries'] as $i => $entry) {
        $scheme = $entry['payment_scheme'];
        $quarter = $entry['installment_quarter'] ?? null;

        if ($scheme === 'full' && $quarter !== null) {
            $validator->errors()->add("entries.{$i}.installment_quarter", 'A full payment cannot also have an installment quarter.');
        }
        if ($scheme === 'installment' && $quarter === null) {
            $validator->errors()->add("entries.{$i}.installment_quarter", 'An installment payment requires a quarter (1–4).');
        }
    }

    // Receipt tie-out: header amount must equal the sum of entry totals.
    $entryTotal = collect($validated['entries'])->sum(fn ($e) => (float) $e['amount']);
    if (round($entryTotal, 2) !== round((float) $validated['amount_paid'], 2)) {
        $validator->errors()->add('amount_paid', 'Amount paid must equal the sum of all entry totals.');
    }

    if ($validator->errors()->isNotEmpty()) {
        throw new \Illuminate\Validation\ValidationException($validator);
    }

    $payload = \Illuminate\Support\Facades\DB::transaction(function () use ($validated, $formStock) {
        // Record the serial the clerk confirmed on the form (defaulted from the
        // booklet's next available serial); fall back to the batch serial
        // (prefix + number), then to a synthetic running number only if no
        // batch is on record.
        $certificateNumber = $validated['serial_number'] ?? null;
        if (! $certificateNumber) {
            $batch = $formStock->nextAvailableBatch();
            $nextSerial = $batch?->nextAvailableSerialNumber();
            $certificateNumber = $nextSerial !== null
                ? $batch->expectedCertificatePrefix() . $nextSerial
                : str_pad((\App\Models\OrRptTransaction::max('id') ?? 0) + 1, 7, '0', STR_PAD_LEFT);
        }

        $orRptTransaction = $formStock->orRptTransactions()->create([
            'certificate_number' => $certificateNumber,
            'previous_receipt_number' => $validated['previous_receipt_number'] ?? null,
            'previous_receipt_date' => $validated['previous_receipt_date'] ?? null,
            'previous_receipt_year' => $validated['previous_receipt_year'] ?? null,
            'municipality_province' => $validated['municipality_province'] ?? null,
            'city' => $validated['city'] ?? null,
            'transaction_date' => $validated['transaction_date'] ?? null,
            'client_name' => $validated['client_name'],
            'payment_in_words' => $validated['payment_in_words'] ?? null,
            'amount_paid' => $validated['amount_paid'],
            'treasurer_deputy' => $validated['treasurer_deputy'] ?? null,
            'basic_tax' => $validated['basic_tax'] ?? false,
            'special_education_fund' => $validated['special_education_fund'] ?? false,
        ]);

        foreach ($validated['entries'] as $entry) {
            $property = \App\Models\RptProperty::updateOrCreate(
                ['tax_declaration_number' => $entry['tax_declaration_number']],
                [
                    'declared_owner' => $entry['declared_owner'],
                    'location' => $entry['location'],
                    'lot_block_number' => $entry['lot_block_number'] ?? null,
                    'municipality_province' => $validated['municipality_province'] ?? null,
                    'city' => $validated['city'] ?? null,
                    'assessed_value_land' => $entry['assessed_value_land'] ?? 0,
                    'assessed_value_improvement' => $entry['assessed_value_improvement'] ?? 0,
                    'assessed_value_total' => $entry['assessed_value_total'],
                    'annual_tax_due' => $entry['tax_due'],
                ]
            );

            $orRptTransaction->entries()->create([
                'rpt_property_id' => $property->id,
                'payment_scheme' => $entry['payment_scheme'],
                'installment_quarter' => $entry['installment_quarter'] ?? null,
                'tax_due' => $entry['tax_due'],
                'discount' => $entry['discount'] ?? 0,
                'penalty_percent' => $entry['penalty_percent'] ?? 0,
                'penalty_amount' => $entry['penalty_amount'] ?? 0,
                'amount' => $entry['amount'],
            ]);
        }

        $formStock->update(['qty' => max(0, $formStock->qty - 1)]);

        \App\Models\TransactionLog::create([
            'serial_number'    => $certificateNumber,
            'payee'            => $validated['client_name'],
            'transacted_at'    => now(),
            'form_type'        => $formStock->form_code,
            'status'           => 'Completed',
            'transaction_id'   => $orRptTransaction->id,
            'transaction_type' => \App\Models\OrRptTransaction::class,
        ]);

        \App\Models\ActivityLog::record('Collection Management - Add Entry - ' . \App\Models\TransactionLog::formName($formStock->form_code) . ' - ' . $certificateNumber);

        return ['qty' => $formStock->qty];
    });

    return response()->json([
        'message' => 'Transaction saved successfully.',
        'qty' => $payload['qty'],
        'redirect' => route('collections', [], false),
    ]);
})->name('transaction-entry.or-rpt.store');

Route::get('/rpt-properties/{taxDeclarationNumber}', function (string $taxDeclarationNumber) {
    $property = \App\Models\RptProperty::where('tax_declaration_number', $taxDeclarationNumber)->first();

    if (! $property) {
        return response()->json(['found' => false]);
    }

    return response()->json([
        'found' => true,
        'property' => $property->only([
            'declared_owner', 'location', 'lot_block_number',
            'municipality_province', 'city',
            'assessed_value_land', 'assessed_value_improvement',
            'assessed_value_total', 'annual_tax_due',
        ]),
        'paid_quarters' => $property->paidQuarters(),
    ]);
})->where('taxDeclarationNumber', '.*')->name('rpt-properties.lookup');

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

    $orTransaction = $formStock->orTransactions()->create($validated);

    $formStock->update([
        'qty' => max(0, $formStock->qty - 1),
    ]);

    \App\Models\TransactionLog::create([
        'serial_number'    => 'No. ' . $validated['certificate_number'] . ' U',
        'payee'            => $validated['payor'],
        'transacted_at'    => now(),
        'form_type'        => $formStock->form_code,
        'status'           => 'Completed',
        'transaction_id'   => $orTransaction->id,
        'transaction_type' => \App\Models\OrTransaction::class,
    ]);

    \App\Models\ActivityLog::record('Collection Management - Add Entry - ' . \App\Models\TransactionLog::formName($formStock->form_code) . ' - No. ' . $validated['certificate_number']);

    return response()->json([
        'message' => 'Transaction saved successfully.',
        'qty' => $formStock->qty,
        'redirect' => route('collections', [], false),
    ]);
})->name('transaction-entry.official-receipt.store');

Route::get('/collections/transaction-entry/{formStock}/marriage-certificate', function (\App\Models\FormStock $formStock) {
    $batch = $formStock->nextAvailableBatch();

    return view('collection-management.transaction-entry.marriage-certificate', [
        'form' => $formStock,
        'certificateNumber' => $batch?->nextAvailableSerialNumber()
            ?? str_pad((\App\Models\MarriageCertificateTransaction::max('id') ?? 0) + 1, 7, '0', STR_PAD_LEFT),
    ]);
})->name('transaction-entry.marriage-certificate');

Route::post('/collections/transaction-entry/{formStock}/marriage-certificate', function (\Illuminate\Http\Request $request, \App\Models\FormStock $formStock) {
    $validated = $request->validate([
        'certificate_number' => ['required', 'string'],
        'husband_name' => ['required', 'string'],
        'husband_age_years' => ['nullable', 'integer', 'min:0', 'max:255'],
        'husband_age_months' => ['nullable', 'integer', 'min:0', 'max:11'],
        'husband_address' => ['nullable', 'string'],
        'wife_name' => ['required', 'string'],
        'wife_age_years' => ['nullable', 'integer', 'min:0', 'max:255'],
        'wife_age_months' => ['nullable', 'integer', 'min:0', 'max:11'],
        'wife_address' => ['nullable', 'string'],
        'witness_day' => ['nullable', 'string'],
        'witness_month' => ['nullable', 'string'],
        'witness_year' => ['nullable', 'string'],
        'instructions_day' => ['nullable', 'string'],
        'instructions_month' => ['nullable', 'string'],
        'instructions_year' => ['nullable', 'string'],
        'registry_number' => ['nullable', 'string'],
        'local_civil_registrar_of' => ['nullable', 'string'],
        'email' => ['nullable', 'email'],
        'message' => ['nullable', 'string'],
    ]);

    $mcTransaction = $formStock->marriageCertificateTransactions()->create($validated);

    $formStock->update([
        'qty' => max(0, $formStock->qty - 1),
    ]);

    \App\Models\TransactionLog::create([
        'serial_number'    => 'No. ' . $validated['certificate_number'],
        'payee'            => $validated['husband_name'] . ' & ' . $validated['wife_name'],
        'transacted_at'    => now(),
        'form_type'        => $formStock->form_code,
        'status'           => 'Completed',
        'transaction_id'   => $mcTransaction->id,
        'transaction_type' => \App\Models\MarriageCertificateTransaction::class,
    ]);

    \App\Models\ActivityLog::record('Collection Management - Add Entry - ' . \App\Models\TransactionLog::formName($formStock->form_code) . ' - No. ' . $validated['certificate_number']);

    return response()->json([
        'message' => 'Transaction saved successfully.',
        'redirect' => route('collections', [], false),
    ]);
})->name('transaction-entry.marriage-certificate.store');

Route::get('/collections/transaction-entry/{formStock}/burial', function (\App\Models\FormStock $formStock) {
    $batch = $formStock->nextAvailableBatch();

    return view('collection-management.transaction-entry.burial', [
        'form' => $formStock,
        'certificateNumber' => $batch?->nextAvailableSerialNumber()
            ?? str_pad((\App\Models\BurialPermitTransaction::max('id') ?? 0) + 1, 7, '0', STR_PAD_LEFT),
    ]);
})->name('transaction-entry.burial');

Route::post('/collections/transaction-entry/{formStock}/burial', function (\Illuminate\Http\Request $request, \App\Models\FormStock $formStock) {
    $validated = $request->validate([
        'certificate_number' => ['required', 'string'],
        'series_letter' => ['nullable', 'string', 'max:5'],
        'applicant_name' => ['nullable', 'string'],
        'city_municipality' => ['nullable', 'string'],
        'province' => ['nullable', 'string'],
        'permission_type' => ['nullable', 'in:Inter,Disinter,Remove'],
        'deceased_name' => ['required', 'string'],
        'nationality' => ['nullable', 'string'],
        'age' => ['nullable', 'integer', 'min:0', 'max:200'],
        'sex' => ['nullable', 'string'],
        'date_of_death' => ['nullable', 'date'],
        'cause_of_death' => ['nullable', 'string'],
        'cemetery_name' => ['nullable', 'string'],
        'infectious' => ['nullable', 'string'],
        'embalmed' => ['nullable', 'string'],
        'disposition' => ['nullable', 'string'],
        'fee_amount' => ['nullable', 'numeric', 'min:0'],
        'date_issued' => ['nullable', 'date'],
        'municipal_secretary' => ['nullable', 'string'],
    ]);

    // Fields 7–9 only apply to a disinterment; drop any stray values otherwise.
    if (($validated['permission_type'] ?? null) !== 'Disinter') {
        $validated['infectious'] = null;
        $validated['embalmed'] = null;
        $validated['disposition'] = null;
    }

    $burialTransaction = $formStock->burialPermitTransactions()->create($validated);

    $formStock->update([
        'qty' => max(0, $formStock->qty - 1),
    ]);

    $serial = 'No. ' . $validated['certificate_number'] . ($validated['series_letter'] ? ' ' . $validated['series_letter'] : '');

    \App\Models\TransactionLog::create([
        'serial_number'    => $serial,
        'payee'            => $validated['applicant_name'] ?: $validated['deceased_name'],
        'transacted_at'    => now(),
        'form_type'        => $formStock->form_code,
        'status'           => 'Completed',
        'transaction_id'   => $burialTransaction->id,
        'transaction_type' => \App\Models\BurialPermitTransaction::class,
    ]);

    \App\Models\ActivityLog::record('Collection Management - Add Entry - ' . \App\Models\TransactionLog::formName($formStock->form_code) . ' - ' . $serial);

    return response()->json([
        'message' => 'Transaction saved successfully.',
        'redirect' => route('collections', [], false),
    ]);
})->name('transaction-entry.burial.store');

Route::get('/collections/{log}', function (\Illuminate\Http\Request $request, \App\Models\TransactionLog $log) {
    $log->load('transaction');

    $pendingRequest = $log->cancelRequests()
        ->with('requestedByUser')
        ->where('status', 'pending')
        ->latest()
        ->first();

    return view('collection-management.view', [
        'log'            => $log,
        'transaction'    => $log->transaction,
        'formName'       => \App\Models\TransactionLog::formName($log->form_type),
        'isAdmin'        => $request->user()?->hasRole('admin') ?? false,
        'pendingRequest' => $pendingRequest,
    ]);
})->name('collections.view');

Route::post('/collections/{log}/archive', function (\App\Models\TransactionLog $log) {
    if ($log->status !== 'Cancelled') {
        return response()->json(['message' => 'Only cancelled transactions can be archived.'], 422);
    }

    $log->update(['archived_at' => now()]);

    \App\Models\ActivityLog::record('Collection Management - Archive Transaction - ' . \App\Models\TransactionLog::formName($log->form_type) . ' - ' . $log->payee . ' - ' . $log->serial_number);

    return response()->json(['message' => 'Transaction archived successfully.']);
})->name('collections.archive');

Route::post('/collections/bulk-archive', function (\Illuminate\Http\Request $request) {
    $request->validate(['ids' => ['required', 'array'], 'ids.*' => ['integer']]);

    $logs = \App\Models\TransactionLog::whereIn('id', $request->ids)
        ->where('status', 'Cancelled')
        ->whereNull('archived_at')
        ->get();

    if ($logs->isEmpty()) {
        return response()->json(['message' => 'No eligible cancelled transactions to archive.'], 422);
    }

    $logs->each->update(['archived_at' => now()]);

    \App\Models\ActivityLog::record('Collection Management - Bulk Archive Transaction - ' . $logs->count() . ' transaction(s)');

    return response()->json(['message' => $logs->count() . ' transaction(s) archived successfully.']);
})->name('collections.bulk-archive');

Route::post('/collections/{log}/cancel', function (\Illuminate\Http\Request $request, \App\Models\TransactionLog $log) {
    if (!$request->user()?->hasRole('admin')) {
        return response()->json(['message' => 'Unauthorized.'], 403);
    }

    if ($log->status === 'Cancelled') {
        return response()->json(['message' => 'This transaction is already cancelled.'], 422);
    }

    $log->update(['status' => 'Cancelled']);

    \App\Models\ActivityLog::record('Collection Management - Cancel Transaction - ' . \App\Models\TransactionLog::formName($log->form_type) . ' - ' . $log->payee . ' - ' . $log->serial_number);

    return response()->json(['message' => 'Transaction cancelled successfully.']);
})->name('collections.cancel');

Route::post('/collections/bulk-cancel', function (\Illuminate\Http\Request $request) {
    if (!$request->user()?->hasRole('admin')) {
        return response()->json(['message' => 'Unauthorized.'], 403);
    }

    $request->validate(['ids' => ['required', 'array'], 'ids.*' => ['integer']]);

    $logs = \App\Models\TransactionLog::whereIn('id', $request->ids)
        ->where('status', 'Completed')
        ->get();

    if ($logs->isEmpty()) {
        return response()->json(['message' => 'No eligible transactions to cancel.'], 422);
    }

    $logs->each->update(['status' => 'Cancelled']);

    \App\Models\ActivityLog::record('Collection Management - Bulk Cancel Transaction - ' . $logs->count() . ' transaction(s)');

    return response()->json(['message' => $logs->count() . ' transaction(s) cancelled successfully.']);
})->name('collections.bulk-cancel');

Route::post('/collections/bulk-cancel-request', function (\Illuminate\Http\Request $request) {
    $validated = $request->validate([
        'ids'    => ['required', 'array'],
        'ids.*'  => ['integer'],
        'reason' => ['nullable', 'string', 'max:500'],
    ]);

    $logs = \App\Models\TransactionLog::whereIn('id', $validated['ids'])
        ->where('status', 'Completed')
        ->get();

    if ($logs->isEmpty()) {
        return response()->json(['message' => 'No eligible transactions to submit requests for.'], 422);
    }

    $skipped = 0;
    foreach ($logs as $log) {
        if ($log->cancelRequests()->where('status', 'pending')->exists()) {
            $skipped++;
            continue;
        }
        $log->cancelRequests()->create([
            'requested_by' => $request->user()?->id,
            'reason'       => $validated['reason'] ?? null,
            'status'       => 'pending',
        ]);
    }

    $submitted = $logs->count() - $skipped;

    \App\Models\ActivityLog::record('Collection Management - Bulk Request Cancel - ' . $submitted . ' transaction(s)');

    $msg = "{$submitted} cancel request(s) submitted.";
    if ($skipped > 0) $msg .= " {$skipped} skipped (already pending).";

    return response()->json(['message' => $msg]);
})->name('collections.bulk-cancel-request');

Route::post('/collections/{log}/reject-cancel-request', function (\Illuminate\Http\Request $request, \App\Models\TransactionLog $log) {
    if (! $request->user()?->hasRole('admin')) {
        return response()->json(['message' => 'Unauthorized.'], 403);
    }

    $cancelRequest = $log->cancelRequests()->where('status', 'pending')->latest()->first();
    if (! $cancelRequest) {
        return response()->json(['message' => 'No pending cancel request found.'], 422);
    }

    $cancelRequest->update([
        'status'      => 'rejected',
        'reviewed_by' => $request->user()?->id,
        'reviewed_at' => now(),
    ]);

    $requesterName = $cancelRequest->requestedByUser?->name ?? 'Unknown';

    \App\Models\ActivityLog::record('Collection Management - Reject Cancel Request - ' . \App\Models\TransactionLog::formName($log->form_type) . ' - ' . $log->payee . ' - ' . $log->serial_number . ' - requested by ' . $requesterName);

    return response()->json(['message' => 'Cancel request rejected successfully.']);
})->name('collections.reject-cancel-request');

Route::post('/collections/{log}/cancel-request', function (\Illuminate\Http\Request $request, \App\Models\TransactionLog $log) {
    $validated = $request->validate([
        'reason' => ['nullable', 'string', 'max:500'],
    ]);

    if ($log->status === 'Cancelled') {
        return response()->json(['message' => 'This transaction is already cancelled.'], 422);
    }

    if ($log->cancelRequests()->where('status', 'pending')->exists()) {
        return response()->json(['message' => 'A cancel request is already pending for this transaction.'], 422);
    }

    $log->cancelRequests()->create([
        'requested_by' => $request->user()?->id,
        'reason'       => $validated['reason'] ?? null,
        'status'       => 'pending',
    ]);

    \App\Models\ActivityLog::record('Collection Management - Request Cancel - ' . \App\Models\TransactionLog::formName($log->form_type) . ' - ' . $log->payee . ' - ' . $log->serial_number);

    return response()->json(['message' => 'Cancel request submitted successfully.']);
})->name('collections.cancel-request');

// ── Notifications ─────────────────────────────────────────────────────────
Route::get('/notifications/count', function (\Illuminate\Http\Request $request) {
    $user = $request->user();
    if (! $user) return response()->json(['count' => 0]);

    if ($user->hasRole('admin')) {
        $count = \App\Models\CancelRequest::where('status', 'pending')->count();
    } else {
        $count = \App\Models\CancelRequest::where('requested_by', $user->id)
            ->where('status', 'rejected')
            ->whereNull('notified_at')
            ->count();
    }

    return response()->json(['count' => $count]);
})->name('notifications.count');

Route::get('/notifications', function (\Illuminate\Http\Request $request) {
    $user = $request->user();
    if (! $user) return response()->json(['items' => []]);

    if ($user->hasRole('admin')) {
        $items = \App\Models\CancelRequest::with(['transactionLog', 'requestedByUser'])
            ->where('status', 'pending')
            ->orderBy('created_at', 'desc')
            ->limit(20)
            ->get()
            ->map(fn ($r) => [
                'id'           => $r->id,
                'type'         => 'cancel_request',
                'serial'       => $r->transactionLog?->serial_number,
                'payee'        => $r->transactionLog?->payee,
                'requested_by' => $r->requestedByUser?->name,
                'created_at'   => $r->created_at->diffForHumans(),
                'url'          => route('collections.view', $r->transaction_log_id),
            ]);
    } else {
        $items = \App\Models\CancelRequest::with(['transactionLog'])
            ->where('requested_by', $user->id)
            ->where('status', 'rejected')
            ->orderBy('reviewed_at', 'desc')
            ->limit(20)
            ->get()
            ->map(fn ($r) => [
                'id'         => $r->id,
                'type'       => 'request_rejected',
                'serial'     => $r->transactionLog?->serial_number,
                'payee'      => $r->transactionLog?->payee,
                'created_at' => $r->reviewed_at?->diffForHumans() ?? $r->created_at->diffForHumans(),
                'url'        => route('collections.view', $r->transaction_log_id),
                'seen'       => (bool) $r->notified_at,
            ]);
    }

    return response()->json(['items' => $items]);
})->name('notifications.list');

Route::post('/notifications/mark-seen', function (\Illuminate\Http\Request $request) {
    $user = $request->user();
    if ($user && ! $user->hasRole('admin')) {
        \App\Models\CancelRequest::where('requested_by', $user->id)
            ->where('status', 'rejected')
            ->whereNull('notified_at')
            ->update(['notified_at' => now()]);
    }
    return response()->json(['ok' => true]);
})->name('notifications.mark-seen');

Route::get('/official-receipts-accountable-forms', function (\Illuminate\Http\Request $request) {
    $perPageOptions = [10, 25, 50, 100];
    $perPage = in_array((int) $request->input('per_page'), $perPageOptions) ? (int) $request->input('per_page') : 10;

    $sortable = ['qty', 'form_name', 'form_code', 'added_date', 'added_time', 'added_by'];
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
        return view('official-receipt-accountable-forms.partials.forms-table', $data);
    }

    return view('official-receipt-accountable-forms.index', $data);
})->name('official-receipts-accountable-forms');

Route::post('/official-receipts-accountable-forms/{formStock}/batches', function (\Illuminate\Http\Request $request, \App\Models\FormStock $formStock) {
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

    if ($message = $formStock->batchConflictMessage($validated['starting_serial_number'], $validated['ending_serial_number'])) {
        return response()->json([
            'message' => $message,
        ], 422);
    }

    $formStock->applyBatch($validated, $request->user()?->name);

    $perPageOptions = [10, 25, 50, 100];
    $perPage = in_array((int) $request->input('per_page'), $perPageOptions) ? (int) $request->input('per_page') : 10;

    $sortable = ['qty', 'form_name', 'form_code', 'added_date', 'added_time', 'added_by'];
    $sort = in_array($request->input('sort'), $sortable) ? $request->input('sort') : 'form_name';
    $direction = $request->input('direction') === 'desc' ? 'desc' : 'asc';

    $forms = \App\Models\FormStock::query()
        ->when($request->input('search'), function ($query, $search) {
            $query->where('form_name', 'like', "%{$search}%");
        })
        ->orderBy($sort, $direction)
        ->paginate($perPage)
        ->withQueryString();

    return view('official-receipt-accountable-forms.partials.forms-table', [
        'forms' => $forms,
        'perPageOptions' => $perPageOptions,
        'perPage' => $perPage,
        'sort' => $sort,
        'direction' => $direction,
    ]);
})->name('official-receipts-accountable-forms.batches.store');

Route::get('/official-receipts-accountable-forms/{formStock}/report-logs', function (\Illuminate\Http\Request $request, \App\Models\FormStock $formStock) {
    $perPageOptions = [10, 25, 50, 100];
    $perPage = in_array((int) $request->input('per_page'), $perPageOptions) ? (int) $request->input('per_page') : 10;

    $sortable = ['starting_serial_number', 'ending_serial_number', 'created_at', 'added_by'];
    $sort = in_array($request->input('sort'), $sortable) ? $request->input('sort') : 'created_at';
    $direction = $request->input('direction') === 'desc' ? 'desc' : 'asc';

    $batches = $formStock->batches()
        ->when($request->input('search'), function ($query, $search) {
            $query->where(function ($q) use ($search) {
                $q->where('starting_serial_number', 'like', "%{$search}%")
                    ->orWhere('ending_serial_number', 'like', "%{$search}%");
            });
        })
        ->orderBy($sort, $direction)
        ->paginate($perPage)
        ->withQueryString();

    $data = [
        'formStock' => $formStock,
        'batches' => $batches,
        'perPageOptions' => $perPageOptions,
        'perPage' => $perPage,
        'sort' => $sort,
        'direction' => $direction,
        'collectors' => \App\Models\User::whereHas('roles', fn ($q) => $q->where('slug', 'collector'))->orderBy('name')->pluck('name'),
    ];

    if ($request->ajax()) {
        return view('official-receipt-accountable-forms.partials.report-logs-table', $data);
    }

    return view('official-receipt-accountable-forms.report-logs', $data);
})->name('official-receipts-accountable-forms.report-logs');

Route::patch('/official-receipts-accountable-forms/batches/{batch}/assign', function (\Illuminate\Http\Request $request, \App\Models\FormBatch $batch) {
    $validated = $request->validate([
        'assigned_to' => ['nullable', 'string', 'max:150'],
    ]);

    $batch->update(['assigned_to' => $validated['assigned_to'] ?: null]);

    return response()->json(['assigned_to' => $batch->assigned_to]);
})->name('official-receipts-accountable-forms.batches.assign');

Route::get('/official-receipts-accountable-forms/{formStock}/preview', function (\Illuminate\Http\Request $request, \App\Models\FormStock $formStock) {
    $validated = $request->validate([
        'from_month' => ['required', 'integer', 'min:1', 'max:12'],
        'from_year'  => ['required', 'integer', 'min:1900', 'max:' . now()->year],
        'to_month'   => ['required', 'integer', 'min:1', 'max:12'],
        'to_year'    => ['required', 'integer', 'min:1900', 'max:' . now()->year],
    ]);

    $fromDate = \Illuminate\Support\Carbon::create($validated['from_year'], $validated['from_month'], 1)->startOfMonth();
    $toDate   = \Illuminate\Support\Carbon::create($validated['to_year'], $validated['to_month'], 1)->endOfMonth();

    $batchCollection = $formStock->batches()
        ->whereBetween('created_at', [$fromDate, $toDate])
        ->orderBy('created_at')
        ->get();

    $batches = $batchCollection->map(fn($b) => [
        'starting_serial' => $b->starting_serial_number,
        'ending_serial'   => $b->displayEndingSerialNumber(),
        'initial_qty'     => $b->startingQty(),
        'used'            => $b->usedQty(),
        'remaining'       => $b->remainingQty(),
        'added_date'      => $b->created_at->format('F j, Y'),
        'added_time'      => $b->created_at->format('h:i A'),
        'status'          => $b->status(),
        'added_by'        => $b->added_by ?? '',
        'remarks'         => '',
    ]);

    $names     = ['','January','February','March','April','May','June','July','August','September','October','November','December'];
    $fromLabel = $names[$validated['from_month']] . ' ' . $validated['from_year'];
    $toLabel   = $names[$validated['to_month']] . ' ' . $validated['to_year'];

    return response()->json([
        'form_name'       => $formStock->form_name,
        'form_code'       => $formStock->form_code,
        'period'          => $fromLabel === $toLabel ? $fromLabel : "{$fromLabel} – {$toLabel}",
        'batches'         => $batches,
        'total_remaining' => $batchCollection->sum(fn($b) => $b->remainingQty()),
    ]);
})->name('official-receipts-accountable-forms.preview');

Route::get('/official-receipts-accountable-forms/{formStock}/export', function (\Illuminate\Http\Request $request, \App\Models\FormStock $formStock) {
    $validated = $request->validate([
        'from_month'   => ['required', 'integer', 'min:1', 'max:12'],
        'from_year'    => ['required', 'integer', 'min:1900', 'max:' . now()->year],
        'to_month'     => ['required', 'integer', 'min:1', 'max:12'],
        'to_year'      => ['required', 'integer', 'min:1900', 'max:' . now()->year],
        'officer_name' => ['nullable', 'string', 'max:100'],
        'designation'  => ['nullable', 'string', 'max:100'],
    ]);

    $fromDate = \Illuminate\Support\Carbon::create($validated['from_year'], $validated['from_month'], 1)->startOfMonth();
    $toDate   = \Illuminate\Support\Carbon::create($validated['to_year'], $validated['to_month'], 1)->endOfMonth();

    $batchCollection = $formStock->batches()
        ->whereBetween('created_at', [$fromDate, $toDate])
        ->orderBy('created_at')
        ->get();

    $names     = ['','January','February','March','April','May','June','July','August','September','October','November','December'];
    $fromLabel = $names[$validated['from_month']] . ' ' . $validated['from_year'];
    $toLabel   = $names[$validated['to_month']] . ' ' . $validated['to_year'];
    $period    = $fromLabel === $toLabel ? strtoupper($fromLabel) : strtoupper("{$fromLabel} – {$toLabel}");

    $officerName  = strtoupper($validated['officer_name'] ?? '');
    $designation  = strtoupper($validated['designation'] ?? '');
    $totalRemaining = $batchCollection->sum(fn($b) => $b->remainingQty());

    $slug = strtolower(str_replace([' ', '/'], '-', $formStock->form_code));
    $filename = "{$slug}-report-logs-{$validated['from_year']}-{$validated['from_month']}-to-{$validated['to_year']}-{$validated['to_month']}.csv";

    return response()->streamDownload(function () use ($batchCollection, $formStock, $period, $officerName, $designation, $totalRemaining) {
        $handle = fopen('php://output', 'w');
        $e = ''; // empty cell helper

        // Header section (10 columns: A-J)
        fputcsv($handle, [$e, $e, $e, $e, 'Summary Report ' . strtoupper($formStock->form_name), $e, $e, $e, $e, $e]);
        fputcsv($handle, [$e, $e, $e, $e, "Provincial or City Treasurer's Office", $e, $e, $e, $e, $e]);
        fputcsv($handle, [$e, $e, $e, $e, 'Month of ' . $period, $e, $e, $e, $e, $e]);
        fputcsv($handle, [$e, $e, $e, $e, $e, $e, $e, $e, $e, $e]); // blank row

        // Accountable officer row
        fputcsv($handle, [$officerName, $e, $designation, $e, $e, $e, $e, $e, 'Municipality of Prieto-Diaz', $e]);
        fputcsv($handle, ['Accountable Officer', $e, 'Designation', $e, $e, $e, $e, $e, 'Province of Sorsogon', $e]);
        fputcsv($handle, [$e, $e, $e, $e, $e, $e, $e, $e, $e, $e]); // blank row

        // Column headers
        fputcsv($handle, ['Starting OR Serial Number', 'Ending OR Serial Number', 'Initial Quantity', 'Used Forms', 'Remaining Forms', 'Added Date', 'Added Time', 'Status', 'Added By', 'Remarks']);

        // Data rows
        foreach ($batchCollection as $batch) {
            fputcsv($handle, [
                $batch->starting_serial_number,
                $batch->displayEndingSerialNumber(),
                $batch->startingQty(),
                $batch->usedQty(),
                $batch->remainingQty(),
                $batch->created_at->format('F j, Y'),
                $batch->created_at->format('h:i A'),
                $batch->status(),
                $batch->added_by ?? '',
                '',
            ]);
        }

        // Total row
        fputcsv($handle, [$e, $e, $e, $e, $e, $e, $e, $e, $e, $e]); // blank row
        fputcsv($handle, [$e, $e, $e, 'Total Unused Forms', $totalRemaining, $e, $e, $e, $e, $e]);

        fclose($handle);
    }, $filename);
})->name('official-receipts-accountable-forms.export');

Route::get('/reporting-abstract', function (\Illuminate\Http\Request $request) {
    $perPageOptions = [10, 25, 50, 100];
    $perPage = in_array((int) $request->input('per_page'), $perPageOptions) ? (int) $request->input('per_page') : 10;

    $reports = collect([
        ['label' => "Treasurer's Monthly Report of Accountability for Accountable Forms", 'slug' => 'treasurers-monthly'],
        ['label' => 'Consolidated Report of Accountability for Accountable Forms (CRAAF)', 'slug' => 'craaf'],
        ['label' => 'Summary of Community Tax Certificate', 'slug' => 'summary-ctc'],
        ['label' => 'Reports of Checks Issued', 'slug' => null, 'url' => route('cheque-management.report', [], false)],
        ['label' => 'Report of Collection and Deposit', 'slug' => null],
        ['label' => 'Report of Accountability for Accountable Forms (RAAF)', 'slug' => 'raaf'],
        ['label' => 'Abstract of Community Tax Certificate', 'slug' => 'abstract-ctc'],
    ])->when($request->input('search'), function ($collection, $search) {
        return $collection->filter(fn ($report) => str_contains(strtolower($report['label']), strtolower($search)));
    })->values();

    $page = (int) ($request->input('page') ?: 1);

    $reports = new \Illuminate\Pagination\LengthAwarePaginator(
        $reports->forPage($page, $perPage)->values(),
        $reports->count(),
        $perPage,
        $page,
        ['path' => $request->url(), 'query' => $request->query()]
    );

    $data = [
        'reports' => $reports,
        'perPageOptions' => $perPageOptions,
        'perPage' => $perPage,
    ];

    if ($request->ajax()) {
        return view('reporting-abstract.partials.reports-table', $data);
    }

    return view('reporting-abstract.index', $data);
})->name('reporting-abstract');

Route::get('/reporting-abstract/{report}/preview', function (\Illuminate\Http\Request $request, string $report) {
    if (! in_array($report, ram_report_slugs(), true)) {
        abort(404);
    }

    $validated = $request->validate([
        'from_month' => ['required', 'integer', 'min:1', 'max:12'],
        'from_year'  => ['required', 'integer', 'min:1900', 'max:' . now()->year],
        'to_month'   => ['required', 'integer', 'min:1', 'max:12'],
        'to_year'    => ['required', 'integer', 'min:1900', 'max:' . now()->year],
    ]);

    [$fromDate, $toDate, $periodLabel] = ram_resolve_period($validated);

    $built = ram_build_report($report, $fromDate, $toDate);

    return response()->json([
        'title'    => $built['title'],
        'period'   => $periodLabel,
        'sections' => $built['sections'],
    ]);
})->name('reporting-abstract.preview');

Route::get('/reporting-abstract/{report}/export', function (\Illuminate\Http\Request $request, string $report) {
    if (! in_array($report, ram_report_slugs(), true)) {
        abort(404);
    }

    $validated = $request->validate([
        'from_month'   => ['required', 'integer', 'min:1', 'max:12'],
        'from_year'    => ['required', 'integer', 'min:1900', 'max:' . now()->year],
        'to_month'     => ['required', 'integer', 'min:1', 'max:12'],
        'to_year'      => ['required', 'integer', 'min:1900', 'max:' . now()->year],
        'officer_name' => ['nullable', 'string', 'max:100'],
        'designation'  => ['nullable', 'string', 'max:100'],
    ]);

    [$fromDate, $toDate, $periodLabel] = ram_resolve_period($validated);

    $built = ram_build_report($report, $fromDate, $toDate);

    $slug = strtolower(str_replace([' ', '/'], '-', $report));
    $filename = "{$slug}-{$validated['from_year']}-{$validated['from_month']}-to-{$validated['to_year']}-{$validated['to_month']}.xlsx";

    \App\Models\ActivityLog::record('Reporting & Abstract - Export Report - ' . $built['title'] . ' - ' . $periodLabel);

    if (in_array($report, ['treasurers-monthly', 'craaf'], true)) {
        $monthNames = ['', 'JANUARY', 'FEBRUARY', 'MARCH', 'APRIL', 'MAY', 'JUNE', 'JULY', 'AUGUST', 'SEPTEMBER', 'OCTOBER', 'NOVEMBER', 'DECEMBER'];
        $headerPeriod = 'From  ' . $monthNames[$validated['from_month']] . ' 1 TO '
            . $monthNames[$validated['to_month']] . ' ' . $toDate->day . ', ' . $validated['to_year'];

        return export_treasurers_monthly_xlsx(
            $built,
            $headerPeriod,
            $validated['officer_name'] ?? null,
            $validated['designation'] ?? null,
            $filename
        );
    }

    if ($report === 'abstract-ctc') {
        return export_abstract_ctc_xlsx($built, $periodLabel, $filename);
    }

    return export_ram_report_xlsx(
        $built,
        $periodLabel,
        $validated['officer_name'] ?? null,
        $validated['designation'] ?? null,
        $filename
    );
})->name('reporting-abstract.export');
Route::get('/bank-deposit-reconciliation', function () { return view('bank-deposit-reconciliation.index'); })->name('bank-deposit-reconciliation');
// ── Cheque Management (disbursement) ──────────────────────────────────────
Route::get('/cheque-management', function (\Illuminate\Http\Request $request) {
    $perPageOptions = [10, 25, 50, 100];
    $perPage = in_array((int) $request->input('per_page'), $perPageOptions) ? (int) $request->input('per_page') : 10;

    $sortable = ['created_at', 'pay_to_order_of', 'check_number', 'amount', 'status'];
    $sort = in_array($request->input('sort'), $sortable) ? $request->input('sort') : 'created_at';
    $direction = $request->input('direction') === 'asc' ? 'asc' : 'desc';

    $dateStart = $request->input('date_start');
    $dateEnd = $request->input('date_end');

    $cheques = \App\Models\Cheque::query()
        ->with('bankAccount')
        ->whereNull('archived_at')
        ->when($request->input('search'), function ($query, $search) {
            $query->where(function ($query) use ($search) {
                $query->where('pay_to_order_of', 'like', "%{$search}%")
                    ->orWhere('check_number', 'like', "%{$search}%");
            });
        })
        ->when($dateStart, fn ($query, $dateStart) => $query->whereDate('created_at', '>=', $dateStart))
        ->when($dateEnd, fn ($query, $dateEnd) => $query->whereDate('created_at', '<=', $dateEnd))
        ->orderBy($sort, $direction)
        ->paginate($perPage)
        ->withQueryString();

    $data = [
        'cheques' => $cheques,
        'perPageOptions' => $perPageOptions,
        'perPage' => $perPage,
        'sort' => $sort,
        'direction' => $direction,
    ];

    if ($request->ajax()) {
        return view('cheque-management.partials.cheques-table', $data);
    }

    return view('cheque-management.index', $data);
})->name('cheque-management');

Route::get('/cheque-management/create', function () {
    return view('cheque-management.create', [
        'bankAccounts' => \App\Models\BankAccount::where('is_active', true)->orderBy('bank_name')->get(),
    ]);
})->name('cheque-management.create');

Route::post('/cheque-management', function (\Illuminate\Http\Request $request) {
    $validated = $request->validate([
        'bank_account_id'   => ['required', 'exists:bank_accounts,id'],
        'cheque_date'       => ['required', 'date'],
        'check_number'      => ['required', 'string', 'max:50'],
        'pay_to_order_of'   => ['required', 'string', 'max:255'],
        'amount'            => ['required', 'numeric', 'min:0'],
        'amount_in_words'   => ['nullable', 'string', 'max:255'],
        'nature_of_payment' => ['nullable', 'string', 'max:255'],
    ]);

    $bankAccount = \App\Models\BankAccount::findOrFail($validated['bank_account_id']);

    // Guard: a cheque number may not be reused on the same bank account.
    $exists = \App\Models\Cheque::where('bank_account_id', $bankAccount->id)
        ->where('check_number', $validated['check_number'])
        ->exists();

    if ($exists) {
        return response()->json([
            'message' => 'Error in adding cheque: Check No. ' . $validated['check_number']
                . ' has already been used for ' . $bankAccount->label() . '.',
        ], 422);
    }

    $cheque = \App\Models\Cheque::create([
        'bank_account_id'   => $bankAccount->id,
        'account_name'      => $bankAccount->account_name,
        'cheque_date'       => $validated['cheque_date'],
        'check_number'      => $validated['check_number'],
        'pay_to_order_of'   => $validated['pay_to_order_of'],
        'amount'            => $validated['amount'],
        'amount_in_words'   => ($validated['amount_in_words'] ?? null) ?: \App\Models\Cheque::spellAmount($validated['amount']),
        'nature_of_payment' => $validated['nature_of_payment'] ?? null,
        'status'            => 'Issued',
        'created_by'        => $request->user()?->name,
    ]);

    \App\Models\ActivityLog::record('Cheque Management - Create Cheque - No. ' . $cheque->check_number . ' - ' . $cheque->pay_to_order_of);

    return response()->json([
        'message'  => 'Cheque saved successfully.',
        'redirect' => route('cheque-management', [], false),
    ]);
})->name('cheque-management.store');

Route::post('/cheque-management/bank-accounts', function (\Illuminate\Http\Request $request) {
    $validated = $request->validate([
        'bank_name'      => ['required', 'string', 'max:255'],
        'account_number' => ['required', 'string', 'max:100', 'unique:bank_accounts,account_number'],
        'account_name'   => ['required', 'string', 'max:255'],
    ]);

    $account = \App\Models\BankAccount::create($validated + ['is_active' => true]);

    \App\Models\ActivityLog::record('Cheque Management - Add Bank Account - ' . $account->bank_name . ' · ' . $account->account_number);

    return response()->json([
        'id'             => $account->id,
        'bank_name'      => $account->bank_name,
        'account_number' => $account->account_number,
        'account_name'   => $account->account_name,
        'label'          => $account->bank_name . ' · ' . $account->account_number,
    ]);
})->name('cheque-management.bank-accounts.store');

Route::get('/cheque-management/report', function (\Illuminate\Http\Request $request) {
    $accounts = \App\Models\BankAccount::orderBy('bank_name')->get();

    // Smart defaults: open the report on the most recent cheque's account and
    // period so it lands on actual activity instead of an empty current month.
    $latest = \App\Models\Cheque::whereNull('archived_at')
        ->orderByDesc('cheque_date')
        ->orderByDesc('id')
        ->first();

    $accountId = (int) $request->input('bank_account_id', $latest?->bank_account_id ?? $accounts->first()?->id);
    $account = $accounts->firstWhere('id', $accountId) ?? $accounts->first();

    $month = (int) $request->input('month', $latest?->cheque_date?->month ?? now()->month);
    $year = (int) $request->input('year', $latest?->cheque_date?->year ?? now()->year);

    $cheques = \App\Models\Cheque::query()
        ->where('bank_account_id', $account?->id)
        ->whereNull('archived_at')
        ->whereMonth('cheque_date', $month)
        ->whereYear('cheque_date', $year)
        ->orderBy('cheque_date')
        ->orderBy('check_number')
        ->get();

    return view('cheque-management.report', compact('accounts', 'account', 'cheques', 'month', 'year'));
})->name('cheque-management.report');

Route::get('/cheque-management/{cheque}', function (\App\Models\Cheque $cheque) {
    return view('cheque-management.view', ['cheque' => $cheque->load('bankAccount')]);
})->name('cheque-management.view');

Route::get('/cheque-management/{cheque}/print', function (\App\Models\Cheque $cheque) {
    return view('cheque-management.print-cheque', ['cheque' => $cheque->load('bankAccount')]);
})->name('cheque-management.print');

Route::get('/cheque-management/{cheque}/duplicate', function (\App\Models\Cheque $cheque) {
    return view('cheque-management.duplicate', ['cheque' => $cheque->load('bankAccount')]);
})->name('cheque-management.duplicate');

Route::post('/cheque-management/{cheque}/cancel', function (\App\Models\Cheque $cheque) {
    if ($cheque->status !== 'Issued') {
        return response()->json(['message' => 'Only issued cheques can be cancelled.'], 422);
    }

    $cheque->update(['status' => 'Cancelled']);
    \App\Models\ActivityLog::record('Cheque Management - Cancel Cheque - No. ' . $cheque->check_number);

    return response()->json(['message' => 'Cheque cancelled successfully.']);
})->name('cheque-management.cancel');

Route::post('/cheque-management/{cheque}/archive', function (\App\Models\Cheque $cheque) {
    if ($cheque->status !== 'Cancelled') {
        return response()->json(['message' => 'Only cancelled cheques can be archived.'], 422);
    }

    $cheque->update(['archived_at' => now()]);
    \App\Models\ActivityLog::record('Cheque Management - Archive Cheque - No. ' . $cheque->check_number);

    return response()->json(['message' => 'Cheque archived successfully.']);
})->name('cheque-management.archive');

/**
 * Slugs for the 5 currently-buildable RAM reports (generated via the RAM
 * modal/xlsx pipeline). "Reports of Checks Issued" links out to the Cheque
 * Management report page instead (it carries a `url` in the list, not a slug).
 * "Report of Collection and Deposit" has no underlying data model yet, so its
 * row still shows "Coming Soon".
 */
// These helpers live in the route file. Route files are re-included on every
// app boot, so without this guard PHPUnit's multi-boot test process throws
// "Cannot redeclare ...". The guard declares them once per process.
if (! function_exists('collection_payment_rules')) {

/**
 * Validation rules for the shared collection payment inputs. Merge into each
 * collection form's $request->validate([...]) with the spread operator.
 */
function collection_payment_rules(): array
{
    return [
        'payment_method'         => ['required', 'in:cash,cheque,online,money_order'],
        'payer_bank_name'        => ['nullable', 'required_if:payment_method,cheque', 'string', 'max:255'],
        'payment_channel'        => ['nullable', 'required_if:payment_method,online', 'in:GCash,LandBank Link.BizPortal,Maya,Other'],
        'payment_reference'      => ['nullable', 'required_unless:payment_method,cash', 'string', 'max:255'],
        'payment_reference_date' => ['nullable', 'required_unless:payment_method,cash', 'date'],
    ];
}

/**
 * Map the request's payment inputs + the transaction amount into the
 * transaction_logs columns, nulling fields irrelevant to the chosen method.
 */
function collection_payment_log_fields(\Illuminate\Http\Request $request, float|int|string $amount): array
{
    $method = $request->input('payment_method', 'cash');

    return [
        'amount'                 => $amount,
        'payment_method'         => $method,
        'payment_channel'        => $method === 'online' ? $request->input('payment_channel') : null,
        'payer_bank_name'        => $method === 'cheque' ? $request->input('payer_bank_name') : null,
        'payment_reference'      => $method === 'cash' ? null : $request->input('payment_reference'),
        'payment_reference_date' => $method === 'cash' ? null : $request->input('payment_reference_date'),
        'recon_status'           => 'pending',
    ];
}

}

if (! function_exists('ram_report_slugs')) {

function ram_report_slugs(): array
{
    return ['treasurers-monthly', 'craaf', 'summary-ctc', 'raaf', 'abstract-ctc'];
}

/**
 * Resolves a validated from/to month+year payload into start/end Carbon
 * instances plus a human-readable period label, mirroring the ORAF
 * preview/export period resolution.
 */
function ram_resolve_period(array $validated): array
{
    $fromDate = \Illuminate\Support\Carbon::create($validated['from_year'], $validated['from_month'], 1)->startOfMonth();
    $toDate   = \Illuminate\Support\Carbon::create($validated['to_year'], $validated['to_month'], 1)->endOfMonth();

    $names = ['', 'January', 'February', 'March', 'April', 'May', 'June', 'July', 'August', 'September', 'October', 'November', 'December'];
    $fromLabel = $names[$validated['from_month']] . ' ' . $validated['from_year'];
    $toLabel   = $names[$validated['to_month']] . ' ' . $validated['to_year'];
    $label     = $fromLabel === $toLabel ? $fromLabel : "{$fromLabel} – {$toLabel}";

    return [$fromDate, $toDate, $label];
}

/**
 * The amount actually collected for a morphed transaction. Field name
 * differs per form: CTC/OR-RPT use `amount_paid`, OR uses `total`,
 * Marriage Certificate has no fee at all.
 */
function ram_transaction_amount($transaction): float
{
    if ($transaction === null) {
        return 0.0;
    }

    return (float) ($transaction->amount_paid ?? $transaction->total ?? 0);
}

/**
 * Beginning/received/issued/remaining breakdown for one form stock over a
 * period, used by both the Treasurer's Monthly Report and (via the same
 * underlying numbers) CRAAF and RAAF's Section C. "Issued" is read straight
 * off TransactionLog (one row = one consumed serial, regardless of later
 * cancellation, matching how FormBatch::usedQty() already treats it
 * elsewhere). For form stocks with no ORAF batches on record, the on-hand
 * figure falls back to the static `qty` column since there is no historical
 * batch trail to reconstruct a prior balance from.
 */
function ram_form_stock_breakdown(\App\Models\FormStock $formStock, \Illuminate\Support\Carbon $from, \Illuminate\Support\Carbon $to): array
{
    $batches = $formStock->batches;

    $receivedBefore = $batches->filter(fn ($b) => $b->purchase_date && $b->purchase_date->lt($from))->sum(fn ($b) => $b->startingQty());
    $receivedSince  = $batches->filter(fn ($b) => $b->purchase_date && $b->purchase_date->between($from, $to))->sum(fn ($b) => $b->startingQty());

    $issuedBefore = \App\Models\TransactionLog::where('form_type', $formStock->form_code)->where('transacted_at', '<', $from)->count();
    $issuedSince  = \App\Models\TransactionLog::where('form_type', $formStock->form_code)->whereBetween('transacted_at', [$from, $to])->count();

    if ($batches->isEmpty()) {
        $onHandLast  = $formStock->qty;
        $serialRange = '—';
    } else {
        $onHandLast = max(0, $receivedBefore - $issuedBefore);

        $batchesBefore = $batches->filter(fn ($b) => $b->purchase_date && $b->purchase_date->lt($from))->values();
        $serialRange = $batchesBefore->isEmpty()
            ? '—'
            : $batchesBefore->first()->starting_serial_number . ' – ' . $batchesBefore->last()->displayEndingSerialNumber();
    }

    $remaining = max(0, $onHandLast + $receivedSince - $issuedSince);

    $remarks = $batches->filter(fn ($b) => $b->purchase_date && $b->purchase_date->between($from, $to))
        ->pluck('added_by')->filter()->unique()->implode(', ');

    return [
        'form'         => $formStock->form_name,
        'on_hand_qty'  => $onHandLast,
        'serial_range' => $serialRange,
        'received'     => $receivedSince,
        'issued'       => $issuedSince,
        'remaining'    => $remaining,
        'remarks'      => $remarks ?: '—',
    ];
}

/**
 * Abstract of Community Tax Certificate — per-transaction listing for the
 * period (Individual + Corporation Cedula). "Tax" is split into CTC A (Basic
 * Community Tax) and CTC B (Additional Community Tax), matching the reference
 * template's columns: Date, Name of Taxpayer, CTC No., CTC A, CTC B, Penalty,
 * Total.
 */
function ram_build_abstract_ctc(\Illuminate\Support\Carbon $from, \Illuminate\Support\Carbon $to): array
{
    $logs = \App\Models\TransactionLog::whereIn('form_type', ['BIR0016', 'BIR0017'])
        ->whereBetween('transacted_at', [$from, $to])
        ->with('transaction')
        ->orderBy('transacted_at')
        ->get()
        ->filter(fn ($log) => $log->transaction !== null);

    $rows = [];
    $totalCtcA = 0;
    $totalCtcB = 0;
    $totalInterest = 0;
    $totalAmount = 0;

    foreach ($logs as $log) {
        $t = $log->transaction;
        $ctcA = (float) $t->a_community_tax_due;                          // Basic Community Tax
        $ctcB = max(0, (float) $t->total_community_tax_due - $ctcA);      // Additional Community Tax
        $interest = (float) $t->interest;
        $amount = (float) $t->amount_paid;

        $rows[] = [
            $log->transacted_at->format('M d, Y'),
            $log->payee,
            $log->serial_number,
            number_format($ctcA, 2),
            number_format($ctcB, 2),
            number_format($interest, 2),
            number_format($amount, 2),
        ];

        $totalCtcA += $ctcA;
        $totalCtcB += $ctcB;
        $totalInterest += $interest;
        $totalAmount += $amount;
    }

    return [
        'title' => 'Abstract of Community Tax Certificate',
        'sections' => [[
            'heading' => null,
            'columns' => [
                ['label' => 'Date', 'align' => 'left'],
                ['label' => 'Name of Taxpayer', 'align' => 'left'],
                ['label' => 'CTC No.', 'align' => 'left'],
                ['label' => 'CTC A', 'align' => 'right'],
                ['label' => 'CTC B', 'align' => 'right'],
                ['label' => 'Penalty', 'align' => 'right'],
                ['label' => 'Total', 'align' => 'right'],
            ],
            'rows' => $rows,
            'totals' => ['', '', 'Total', number_format($totalCtcA, 2), number_format($totalCtcB, 2), number_format($totalInterest, 2), number_format($totalAmount, 2)],
        ]],
    ];
}

/**
 * Summary of Community Tax Certificate — rollup by accountable officer
 * (the `treasurer_name` recorded on each CTC transaction). "Pages" follows
 * the CTC booklet convention of 1 certificate = 1 page.
 */
function ram_build_summary_ctc(\Illuminate\Support\Carbon $from, \Illuminate\Support\Carbon $to): array
{
    $logs = \App\Models\TransactionLog::whereIn('form_type', ['BIR0016', 'BIR0017'])
        ->whereBetween('transacted_at', [$from, $to])
        ->with('transaction')
        ->orderBy('transacted_at')
        ->get()
        ->filter(fn ($log) => $log->transaction !== null);

    $groups = $logs->groupBy(fn ($log) => $log->transaction->treasurer_name ?: 'Unassigned');

    $rows = [];
    $grandQty = 0;
    $grandAmount = 0;

    foreach ($groups as $officer => $group) {
        $serials = $group->pluck('serial_number');
        $qty = $group->count();
        $amount = $group->sum(fn ($log) => (float) $log->transaction->amount_paid);

        $rows[] = [
            $qty,
            $serials->first() . ' – ' . $serials->last(),
            $qty,
            number_format($amount, 2),
            $officer,
        ];

        $grandQty += $qty;
        $grandAmount += $amount;
    }

    return [
        'title' => 'Summary of Community Tax Certificate',
        'sections' => [[
            'heading' => null,
            'columns' => [
                ['label' => 'Pages', 'align' => 'right'],
                ['label' => 'CTC No. Range', 'align' => 'left'],
                ['label' => 'Qty', 'align' => 'right'],
                ['label' => 'Amount', 'align' => 'right'],
                ['label' => 'Accountable Officer', 'align' => 'left'],
            ],
            'rows' => $rows,
            'totals' => ['', 'GRAND TOTAL', $grandQty, number_format($grandAmount, 2), ''],
        ]],
    ];
}

/**
 * Treasurer's Monthly Report of Accountability for Accountable Forms — one
 * row per form stock (all 8 accountable forms in the system), system-wide
 * (this system tracks a single Treasury custody, not per-collector
 * inventory). When $withTotals is true, a B-TOTAL row is appended — used by
 * CRAAF to consolidate the same breakdown.
 */
function ram_build_treasurers_monthly(\Illuminate\Support\Carbon $from, \Illuminate\Support\Carbon $to, bool $withTotals = false): array
{
    $rows = [];
    $totalReceived = 0;
    $totalIssued = 0;
    $totalRemaining = 0;

    foreach (\App\Models\FormStock::orderBy('form_name')->get() as $formStock) {
        $b = ram_form_stock_breakdown($formStock, $from, $to);

        $rows[] = [
            $b['form'],
            $b['on_hand_qty'] . ' (' . $b['serial_range'] . ')',
            $b['received'],
            $b['issued'],
            $b['remaining'],
            $b['remarks'],
        ];

        $totalReceived += $b['received'];
        $totalIssued += $b['issued'];
        $totalRemaining += $b['remaining'];
    }

    $section = [
        'heading' => null,
        'columns' => [
            ['label' => 'Forms', 'align' => 'left'],
            ['label' => 'On Hand Last Report', 'align' => 'left'],
            ['label' => 'Received Since', 'align' => 'right'],
            ['label' => 'Issued Since', 'align' => 'right'],
            ['label' => 'Remaining on Hand', 'align' => 'right'],
            ['label' => 'Remarks', 'align' => 'left'],
        ],
        'rows' => $rows,
    ];

    if ($withTotals) {
        $section['totals'] = ['B-TOTAL', '', $totalReceived, $totalIssued, $totalRemaining, ''];
    }

    return [
        'title' => "Treasurer's Monthly Report of Accountability for Accountable Forms",
        'sections' => [$section],
    ];
}

/**
 * Form label as it appears in the FORMS column of the Treasurer's Monthly /
 * CRAAF reports, matching the government template wording (e.g.
 * "BIR F. 0016 (CTC)"); other forms fall back to their stored form name.
 */
function ram_form_label(\App\Models\FormStock $formStock): string
{
    return match ($formStock->form_code) {
        'BIR0016' => 'BIR F. 0016 (CTC)',
        'BIR0017' => 'BIR F. 0017 (CORP)',
        default => $formStock->form_name,
    };
}

/**
 * Inclusive serial range in the reference file's compact form — the start
 * serial in full, a hyphen, then only the trailing digits of the end serial
 * that differ from the start (e.g. "13474472" + "13474500" => "13474472-500",
 * matching how the municipality writes ranges by hand).
 */
function ram_serial_range_label(string $start, string $end): string
{
    if ($start === '' || $end === '') {
        return '—';
    }

    $i = 0;
    $min = min(strlen($start), strlen($end));
    while ($i < $min && $start[$i] === $end[$i]) {
        $i++;
    }

    $endShort = substr($end, $i);

    return $start . '-' . ($endShort === '' ? $end : $endShort);
}

/**
 * Per-collector accountability rows shared by the Treasurer's Monthly Report
 * and CRAAF, matching the real government Excel templates
 * (Treasurers_Monthly_Report and CRAAP reference files): one row per ORAF
 * batch, grouped by form (the form label shows only on the group's first
 * row), with the collector in Remarks (form_batches.assigned_to, falling
 * back to who added the batch / "STOCKS"), and a Quantity + Inclusive Serial
 * No. pair under each of On Hand Last Report / Received Since / Issued Since
 * / Remaining on Hand. Bucketing rules, derived from how the reference files
 * are kept:
 *   - On Hand Last Report: batches purchased before the period (full range).
 *   - Received Since: batches purchased within the period (full range).
 *   - Issued Since: serials of the batch issued within the period (matched to
 *     the batch by serial-number range against TransactionLog), assumed taken
 *     in order from the batch start.
 *   - Remaining on Hand: the unused tail as of the period end, or "NONE".
 * Forms with no batch trail are skipped (there is nothing to attribute to a
 * collector). Returns [$rows, $totals] with $totals summing the 4 Quantity
 * columns; the caller supplies the totals label ("Total" vs "B-TOTAL").
 */
function ram_per_collector_rows(\Illuminate\Support\Carbon $from, \Illuminate\Support\Carbon $to, string $totalLabel): array
{
    $rows = [];
    $totalOnHand = 0;
    $totalReceived = 0;
    $totalIssued = 0;
    $totalRemaining = 0;

    $trailingNumber = function (?string $serial): int {
        preg_match('/(\d+)$/', (string) $serial, $m);
        return (int) ($m[1] ?? 0);
    };

    foreach (\App\Models\FormStock::orderBy('form_name')->get() as $formStock) {
        // Only batches that exist as of the period end can be accounted for.
        $batches = $formStock->batches()
            ->oldest('purchase_date')
            ->get()
            ->filter(fn ($b) => ! $b->purchase_date || $b->purchase_date->lte($to))
            ->values();
        if ($batches->isEmpty()) {
            continue;
        }

        // Issued serials for this form recorded on or before the period end,
        // matched to a batch by serial-number range and split into "issued
        // before the period" vs "issued within the period".
        $logs = \App\Models\TransactionLog::where('form_type', $formStock->form_code)
            ->where('transacted_at', '<=', $to)
            ->get()
            ->map(fn ($l) => ['n' => $trailingNumber($l->serial_number), 'within' => $l->transacted_at->betweenIncluded($from, $to)]);

        $first = true;
        foreach ($batches as $batch) {
            [$startNum, $endNum] = $batch->serialRange();
            $prefix = $batch->serialPrefix();
            preg_match('/(\d+)$/', $batch->starting_serial_number, $m);
            $pad = strlen($m[1] ?? '');
            $fmt = fn (int $n) => $prefix . str_pad((string) $n, $pad, '0', STR_PAD_LEFT);

            $fullRange = ram_serial_range_label($batch->starting_serial_number, $batch->displayEndingSerialNumber());

            $purchasedBefore = $batch->purchase_date && $batch->purchase_date->lt($from);
            $purchasedWithin = $batch->purchase_date && $batch->purchase_date->between($from, $to);

            $starting = $batch->startingQty();

            $inRange = $logs->filter(fn ($x) => $x['n'] >= $startNum && $x['n'] <= $endNum);
            $issuedToEnd = $inRange->count();
            $issuedWithin = $inRange->where('within', true)->count();
            $remaining = max(0, $starting - $issuedToEnd);

            // Serials are assumed consumed in order from the batch start; the
            // in-period issuances are the most recent slice of that run.
            $issuedRange = $issuedWithin > 0
                ? ram_serial_range_label($fmt($startNum + $issuedToEnd - $issuedWithin), $fmt($startNum + $issuedToEnd - 1))
                : '';
            $remainingRange = $remaining > 0 ? ram_serial_range_label($fmt($startNum + $issuedToEnd), $fmt($endNum)) : 'NONE';

            $rows[] = [
                $first ? ram_form_label($formStock) : '',
                $purchasedBefore ? $starting : '',
                $purchasedBefore ? $fullRange : '',
                $purchasedWithin ? $starting : '',
                $purchasedWithin ? $fullRange : '',
                $issuedWithin > 0 ? $issuedWithin : '',
                $issuedRange,
                $remaining > 0 ? $remaining : '',
                $remainingRange,
                $batch->assigned_to ?: ($batch->added_by ?: 'STOCKS'),
            ];

            $first = false;
            $totalOnHand += $purchasedBefore ? $starting : 0;
            $totalReceived += $purchasedWithin ? $starting : 0;
            $totalIssued += $issuedWithin;
            $totalRemaining += $remaining;
        }
    }

    $totals = [$totalLabel, $totalOnHand, '', $totalReceived, '', $totalIssued, '', $totalRemaining, '', ''];

    return [$rows, $totals];
}

/**
 * The two-row grouped section shape (groups + leaf columns + rows + totals)
 * shared by the Treasurer's Monthly Report and CRAAF, so the preview header
 * and the dedicated export render identically for both.
 */
function ram_per_collector_section(array $rows, array $totals): array
{
    return [
        'heading' => null,
        'groups' => [
            ['label' => 'Forms', 'colspan' => 1],
            ['label' => 'On Hand Last Report', 'colspan' => 2, 'subcolumns' => ['Quantity', 'Inclusive Serial No.']],
            ['label' => 'Received Since', 'colspan' => 2, 'subcolumns' => ['Quantity', 'Inclusive Serial No.']],
            ['label' => 'Issued Since', 'colspan' => 2, 'subcolumns' => ['Quantity', 'Inclusive Serial No.']],
            ['label' => 'Remaining on Hand', 'colspan' => 2, 'subcolumns' => ['Quantity', 'Inclusive Serial No.']],
            ['label' => 'Remarks', 'colspan' => 1],
        ],
        'columns' => [
            ['label' => 'Forms', 'align' => 'left'],
            ['label' => 'Quantity', 'align' => 'right'],
            ['label' => 'Inclusive Serial No.', 'align' => 'left'],
            ['label' => 'Quantity', 'align' => 'right'],
            ['label' => 'Inclusive Serial No.', 'align' => 'left'],
            ['label' => 'Quantity', 'align' => 'right'],
            ['label' => 'Inclusive Serial No.', 'align' => 'left'],
            ['label' => 'Quantity', 'align' => 'right'],
            ['label' => 'Inclusive Serial No.', 'align' => 'left'],
            ['label' => 'Remarks', 'align' => 'left'],
        ],
        'rows' => $rows,
        'totals' => $totals,
    ];
}

/**
 * Treasurer's Monthly Report of Accountability for Accountable Forms — a
 * per-collector listing (one row per ORAF batch, grouped by form, collector
 * in Remarks) matching the Treasurers_Monthly_Report reference file. Used
 * for both the preview modal and the dedicated export
 * (export_treasurers_monthly_xlsx()) so what's previewed/printed matches the
 * download. Separate from ram_build_treasurers_monthly() above, which RAAF's
 * Section C still uses unchanged (simpler flat-column shape).
 */
function ram_build_treasurers_monthly_detailed(\Illuminate\Support\Carbon $from, \Illuminate\Support\Carbon $to): array
{
    [$rows, $totals] = ram_per_collector_rows($from, $to, 'Total');

    return [
        'title' => "Treasurer's Monthly Report of Accountability for Accountable Forms",
        'sections' => [ram_per_collector_section($rows, $totals)],
    ];
}

/**
 * Streams the Treasurer's Monthly Report as a styled .xlsx replicating the
 * real government template cell-for-cell: merged title block, a 3-column
 * officer/designation/province row, the two-row merged group header built
 * by ram_build_treasurers_monthly_detailed(), and an unbolded right-aligned
 * Total row. "Province or City" is hardcoded — it's always this
 * municipality and isn't user-entered elsewhere in the system.
 */
function export_treasurers_monthly_xlsx(array $built, string $periodLabel, ?string $officerName, ?string $designation, string $filename): \Symfony\Component\HttpFoundation\StreamedResponse
{
    $spreadsheet = new \PhpOffice\PhpSpreadsheet\Spreadsheet();
    $sheet = $spreadsheet->getActiveSheet();
    $center = \PhpOffice\PhpSpreadsheet\Style\Alignment::HORIZONTAL_CENTER;
    $right = \PhpOffice\PhpSpreadsheet\Style\Alignment::HORIZONTAL_RIGHT;
    $left = \PhpOffice\PhpSpreadsheet\Style\Alignment::HORIZONTAL_LEFT;
    $thin = ['borderStyle' => \PhpOffice\PhpSpreadsheet\Style\Border::BORDER_THIN];

    // OOXML stored widths taken verbatim from the reference file so Excel's
    // Column-Width dialog shows exactly A=17.3, quantity=5.8, serial/remarks=19.2
    // (Excel displays ~0.7-1.4 less than the stored value due to cell padding).
    foreach (['A' => 18.0, 'B' => 6.5, 'C' => 19.8984375, 'D' => 6.5, 'E' => 19.8984375, 'F' => 6.5, 'G' => 19.8984375, 'H' => 6.5, 'I' => 19.8984375, 'J' => 19.8984375] as $col => $width) {
        $sheet->getColumnDimension($col)->setWidth($width);
    }
    $spreadsheet->getDefaultStyle()->getFont()->setName('Roboto');

    // Print at actual size with NO fit-to-page scaling, mirroring the
    // reference file: the column widths above already span the
    // folio-landscape printable width, so no scaling is needed. Fit-to-page
    // is explicitly disabled so Excel does not shrink the sheet, and the
    // sheet is centered horizontally on the page.
    $sheet->getPageSetup()
        ->setOrientation(\PhpOffice\PhpSpreadsheet\Worksheet\PageSetup::ORIENTATION_LANDSCAPE)
        ->setPaperSize(\PhpOffice\PhpSpreadsheet\Worksheet\PageSetup::PAPERSIZE_FOLIO)
        ->setFitToHeight(0)
        ->setFitToWidth(0)
        ->setFitToPage(false)
        ->setHorizontalCentered(true);
    $sheet->getPageMargins()->setTop(0.5)->setBottom(0.2)->setLeft(0.2)->setRight(0.2);

    $vCenter = \PhpOffice\PhpSpreadsheet\Style\Alignment::VERTICAL_CENTER;
    $medium = ['borderStyle' => \PhpOffice\PhpSpreadsheet\Style\Border::BORDER_MEDIUM];

    $sheet->setCellValue('A1', strtoupper($built['title']));
    $sheet->mergeCells('A1:J1');
    $sheet->getStyle('A1')->getFont()->setBold(true)->setSize(11);
    $sheet->getStyle('A1')->getAlignment()->setHorizontal($center);

    $sheet->setCellValue('A2', $periodLabel);
    $sheet->mergeCells('A2:J2');
    $sheet->getStyle('A2')->getFont()->setBold(true)->setSize(11);
    $sheet->getStyle('A2')->getAlignment()->setHorizontal($center);

    // Officer / Designation / Province row (values on row 4, italic labels on row 5).
    $officerCols = [
        ['A4:B4', 'A5:B5', $officerName ?: '', 'Name of Officer'],
        ['D4:F4', 'D5:F5', $designation ?: '', 'Official Designation'],
        ['H4:J4', 'H5:J5', 'PRIETO DIAZ, SORSOGON', 'Province or City'],
    ];
    foreach ($officerCols as [$valueRange, $labelRange, $value, $label]) {
        $sheet->mergeCells($valueRange);
        $sheet->setCellValue(explode(':', $valueRange)[0], $value);
        $sheet->getStyle($valueRange)->applyFromArray([
            'font' => ['bold' => true, 'size' => 11],
            'alignment' => ['horizontal' => $center, 'vertical' => $vCenter],
            'borders' => ['bottom' => $medium],
        ]);

        $sheet->mergeCells($labelRange);
        $sheet->setCellValue(explode(':', $labelRange)[0], $label);
        $sheet->getStyle($labelRange)->getFont()->setSize(8)->setItalic(true);
        $sheet->getStyle($labelRange)->getAlignment()->setHorizontal($center);
    }
    $sheet->setCellValue('G4', 'of');
    $sheet->getStyle('G4')->getFont()->setSize(11);
    $sheet->getStyle('G4')->getAlignment()->setHorizontal($center)->setVertical($vCenter);

    // Two-row grouped column header (row 7 group labels over row 8 sub-headers),
    // matching the reference file's exact wording (incl. its "Recieved" spelling).
    $sheet->mergeCells('A7:A8');
    $sheet->setCellValue('A7', 'FORMS');

    $groupCols = ['B' => 'On hand last Report', 'D' => 'Recieved Since', 'F' => 'Issued Since', 'H' => 'Remaining on Hand'];
    foreach ($groupCols as $startCol => $label) {
        $endCol = chr(ord($startCol) + 1);
        $sheet->mergeCells("{$startCol}7:{$endCol}7");
        $sheet->setCellValue("{$startCol}7", $label);
        $sheet->setCellValue("{$startCol}8", 'Quantity');
        $sheet->setCellValue("{$endCol}8", 'Inclusive Serial Nos.');
    }

    $sheet->mergeCells('J7:J8');
    $sheet->setCellValue('J7', 'REMARKS');

    $sheet->getStyle('A7:J8')->applyFromArray([
        'font' => ['bold' => true, 'size' => 9],
        'alignment' => ['horizontal' => $center, 'vertical' => $vCenter, 'wrapText' => true],
        'borders' => ['allBorders' => $thin],
    ]);

    $quantityCols = [1, 3, 5, 7];

    $section = $built['sections'][0];
    $row = 9;
    foreach ($section['rows'] as $dataRow) {
        foreach ($dataRow as $i => $value) {
            $col = \PhpOffice\PhpSpreadsheet\Cell\Coordinate::stringFromColumnIndex($i + 1);
            $sheet->setCellValue("{$col}{$row}", $value);
            $isQuantity = in_array($i, $quantityCols, true);
            $isRemarks = $i === 9;
            $sheet->getStyle("{$col}{$row}")->getAlignment()->applyFromArray([
                'horizontal' => ($isQuantity || $isRemarks) ? $center : $left,
                'vertical' => $vCenter,
            ]);
        }
        $sheet->getStyle("A{$row}:J{$row}")->applyFromArray([
            'font' => ['name' => 'Roboto', 'size' => 8],
            'borders' => ['allBorders' => $thin],
        ]);
        // Form-group label cell (column A) is bold in the reference.
        $sheet->getStyle("A{$row}")->getFont()->setBold(true);
        $row++;
    }

    if (! empty($section['totals'])) {
        foreach ($section['totals'] as $i => $value) {
            $col = \PhpOffice\PhpSpreadsheet\Cell\Coordinate::stringFromColumnIndex($i + 1);
            $sheet->setCellValue("{$col}{$row}", $value);
            $isQuantity = in_array($i, $quantityCols, true);
            $sheet->getStyle("{$col}{$row}")->getAlignment()->setHorizontal($isQuantity ? $center : $left);
        }
        $sheet->getStyle("A{$row}")->getAlignment()->setHorizontal($right);
        $sheet->getStyle("A{$row}:J{$row}")->applyFromArray([
            'font' => ['name' => 'Roboto', 'size' => 8, 'bold' => true],
            'borders' => ['allBorders' => $thin],
        ]);
    }

    $writer = new \PhpOffice\PhpSpreadsheet\Writer\Xlsx($spreadsheet);

    return response()->streamDownload(function () use ($writer) {
        $writer->save('php://output');
    }, $filename, [
        'Content-Type' => 'application/vnd.openxmlformats-officedocument.spreadsheetml.sheet',
    ]);
}

/**
 * Consolidated Report of Accountability for Accountable Forms (CRAAF) — the
 * same per-collector batch listing as the Treasurer's Monthly Report, with a
 * consolidated B-TOTAL row, matching the CRAAP reference file. The
 * "Brgy A.F. / Checks Issued / Cash Tickets" line items called out in the
 * client brief are not yet modelled in this system (no barangay-A.F. form,
 * no cheque data source, no cash-ticket form), so they do not appear here —
 * only forms that have an actual ORAF batch trail are listed.
 */
function ram_build_craaf(\Illuminate\Support\Carbon $from, \Illuminate\Support\Carbon $to): array
{
    [$rows, $totals] = ram_per_collector_rows($from, $to, 'B-TOTAL');

    return [
        'title' => 'Consolidated Report of Accountability for Accountable Forms (CRAAF)',
        'sections' => [ram_per_collector_section($rows, $totals)],
    ];
}

/**
 * Report of Accountability for Accountable Forms — Section C reuses the
 * same system-wide form breakdown (renamed columns to match RAAF's
 * Beginning/Receipt/Issued/Ending wording); Section D summarizes cash
 * collections for the period. There is no remittance/deposit or cash
 * carry-over ledger in this system, so Beginning Balance and
 * Remittances/Deposit are shown as 0 rather than fabricated.
 */
function ram_build_raaf(\Illuminate\Support\Carbon $from, \Illuminate\Support\Carbon $to): array
{
    $sectionC = ram_build_treasurers_monthly($from, $to)['sections'][0];

    $collections = \App\Models\TransactionLog::whereBetween('transacted_at', [$from, $to])
        ->with('transaction')
        ->get()
        ->sum(fn ($log) => ram_transaction_amount($log->transaction));

    return [
        'title' => 'Report of Accountability for Accountable Forms (RAAF)',
        'sections' => [
            [
                'heading' => 'C. Accountability for Accountable Forms',
                'columns' => [
                    ['label' => 'Forms', 'align' => 'left'],
                    ['label' => 'Beginning Balance', 'align' => 'left'],
                    ['label' => 'Receipt', 'align' => 'right'],
                    ['label' => 'Issued', 'align' => 'right'],
                    ['label' => 'Ending Balance', 'align' => 'right'],
                    ['label' => 'Remarks', 'align' => 'left'],
                ],
                'rows' => $sectionC['rows'],
            ],
            [
                'heading' => 'D. Summary of Collections and Remittances/Deposit',
                'columns' => [
                    ['label' => 'Beginning Balance', 'align' => 'right'],
                    ['label' => 'Collections', 'align' => 'right'],
                    ['label' => 'Remittances/Deposit', 'align' => 'right'],
                    ['label' => 'Balance', 'align' => 'right'],
                ],
                'rows' => [[
                    number_format(0, 2),
                    number_format($collections, 2),
                    number_format(0, 2),
                    number_format($collections, 2),
                ]],
            ],
        ],
    ];
}

/**
 * Dispatches to the report-specific builder for a given RAM slug.
 */
function ram_build_report(string $slug, \Illuminate\Support\Carbon $from, \Illuminate\Support\Carbon $to): array
{
    return match ($slug) {
        'abstract-ctc' => ram_build_abstract_ctc($from, $to),
        'summary-ctc' => ram_build_summary_ctc($from, $to),
        'treasurers-monthly' => ram_build_treasurers_monthly_detailed($from, $to),
        'craaf' => ram_build_craaf($from, $to),
        'raaf' => ram_build_raaf($from, $to),
    };
}

/**
 * Streams a RAM report as a styled .xlsx, formatted to mirror the official
 * government template structure: centered title block, period line,
 * accountable officer row, then one or more sections (each with its own
 * heading, bordered column-header row, data rows, and an optional bold
 * totals row).
 */
function export_ram_report_xlsx(array $built, string $periodLabel, ?string $officerName, ?string $designation, string $filename): \Symfony\Component\HttpFoundation\StreamedResponse
{
    $spreadsheet = new \PhpOffice\PhpSpreadsheet\Spreadsheet();
    $sheet = $spreadsheet->getActiveSheet();

    $maxCols = max(array_map(fn ($s) => count($s['columns']), $built['sections']));
    $lastColLetter = \PhpOffice\PhpSpreadsheet\Cell\Coordinate::stringFromColumnIndex($maxCols);

    $row = 1;

    $sheet->setCellValue("A{$row}", strtoupper($built['title']));
    $sheet->mergeCells("A{$row}:{$lastColLetter}{$row}");
    $sheet->getStyle("A{$row}")->getFont()->setBold(true)->setSize(13);
    $sheet->getStyle("A{$row}")->getAlignment()->setHorizontal(\PhpOffice\PhpSpreadsheet\Style\Alignment::HORIZONTAL_CENTER);
    $row++;

    $sheet->setCellValue("A{$row}", 'Province of Sorsogon, Municipality of Prieto-Diaz');
    $sheet->mergeCells("A{$row}:{$lastColLetter}{$row}");
    $sheet->getStyle("A{$row}")->getAlignment()->setHorizontal(\PhpOffice\PhpSpreadsheet\Style\Alignment::HORIZONTAL_CENTER);
    $row++;

    $sheet->setCellValue("A{$row}", 'For the period of ' . strtoupper($periodLabel));
    $sheet->mergeCells("A{$row}:{$lastColLetter}{$row}");
    $sheet->getStyle("A{$row}")->getAlignment()->setHorizontal(\PhpOffice\PhpSpreadsheet\Style\Alignment::HORIZONTAL_CENTER);
    $row += 2;

    if ($officerName || $designation) {
        $sheet->setCellValue("A{$row}", 'Accountable Officer: ' . strtoupper($officerName ?: '---'));
        $sheet->setCellValue('A' . ($row + 1), 'Designation: ' . strtoupper($designation ?: '---'));
        $row += 3;
    }

    foreach ($built['sections'] as $section) {
        if ($section['heading']) {
            $sheet->setCellValue("A{$row}", $section['heading']);
            $sheet->getStyle("A{$row}")->getFont()->setBold(true);
            $row++;
        }

        $colLetters = [];
        foreach ($section['columns'] as $i => $col) {
            $letter = \PhpOffice\PhpSpreadsheet\Cell\Coordinate::stringFromColumnIndex($i + 1);
            $colLetters[] = $letter;
            $sheet->setCellValue("{$letter}{$row}", $col['label']);
        }
        $headerRange = "{$colLetters[0]}{$row}:" . end($colLetters) . $row;
        $sheet->getStyle($headerRange)->applyFromArray([
            'font' => ['bold' => true, 'color' => ['rgb' => 'FFFFFF']],
            'fill' => ['fillType' => \PhpOffice\PhpSpreadsheet\Style\Fill::FILL_SOLID, 'startColor' => ['rgb' => '2C4A6E']],
            'borders' => ['allBorders' => ['borderStyle' => \PhpOffice\PhpSpreadsheet\Style\Border::BORDER_THIN]],
        ]);
        $row++;

        foreach ($section['rows'] as $dataRow) {
            foreach ($dataRow as $i => $value) {
                $letter = \PhpOffice\PhpSpreadsheet\Cell\Coordinate::stringFromColumnIndex($i + 1);
                $sheet->setCellValue("{$letter}{$row}", $value);
                $align = $section['columns'][$i]['align'] ?? 'left';
                $sheet->getStyle("{$letter}{$row}")->getAlignment()->setHorizontal(
                    $align === 'right' ? \PhpOffice\PhpSpreadsheet\Style\Alignment::HORIZONTAL_RIGHT : \PhpOffice\PhpSpreadsheet\Style\Alignment::HORIZONTAL_LEFT
                );
            }
            $dataRange = "{$colLetters[0]}{$row}:" . end($colLetters) . $row;
            $sheet->getStyle($dataRange)->applyFromArray([
                'borders' => ['allBorders' => ['borderStyle' => \PhpOffice\PhpSpreadsheet\Style\Border::BORDER_THIN]],
            ]);
            $row++;
        }

        if (! empty($section['totals'])) {
            foreach ($section['totals'] as $i => $value) {
                $letter = \PhpOffice\PhpSpreadsheet\Cell\Coordinate::stringFromColumnIndex($i + 1);
                $sheet->setCellValue("{$letter}{$row}", $value);
            }
            $totalsRange = "{$colLetters[0]}{$row}:" . end($colLetters) . $row;
            $sheet->getStyle($totalsRange)->applyFromArray([
                'font' => ['bold' => true],
                'borders' => ['allBorders' => ['borderStyle' => \PhpOffice\PhpSpreadsheet\Style\Border::BORDER_THIN]],
            ]);
            $row++;
        }

        $row++;
    }

    foreach (range('A', $lastColLetter) as $col) {
        $sheet->getColumnDimension($col)->setAutoSize(true);
    }

    $writer = new \PhpOffice\PhpSpreadsheet\Writer\Xlsx($spreadsheet);

    return response()->streamDownload(function () use ($writer) {
        $writer->save('php://output');
    }, $filename, [
        'Content-Type' => 'application/vnd.openxmlformats-officedocument.spreadsheetml.sheet',
    ]);
}

/**
 * Dedicated .xlsx export for the Abstract of Community Tax Certificate,
 * matching the municipality's reference template: centered letterhead block,
 * a bordered 7-column table (Date, Name, CTC No., CTC A, CTC B, Penalty,
 * Total), and a Sub Total / Add / GRAND TOTAL footer. Portrait, horizontally
 * centered, 0.7" margins, with the reference column widths.
 */
function export_abstract_ctc_xlsx(array $built, string $periodLabel, string $filename): \Symfony\Component\HttpFoundation\StreamedResponse
{
    $A = \PhpOffice\PhpSpreadsheet\Style\Alignment::class;
    $B = \PhpOffice\PhpSpreadsheet\Style\Border::class;
    $C = \PhpOffice\PhpSpreadsheet\Cell\Coordinate::class;
    $NUM = \PhpOffice\PhpSpreadsheet\Cell\DataType::TYPE_NUMERIC;

    $spreadsheet = new \PhpOffice\PhpSpreadsheet\Spreadsheet();
    $sheet = $spreadsheet->getActiveSheet();
    // Keep the Normal-style font at the default (Calibri 11) so column widths
    // render at the reference's physical inches (Excel sizes columns by the
    // default font's digit width). Roboto is applied per-cell below.

    $section = $built['sections'][0];
    $columns = $section['columns'];
    $lastCol = $C::stringFromColumnIndex(count($columns));   // "G"

    // ── Letterhead block (merged A:lastCol, centered) ──
    $lines = [
        ['Republic of the Philippines', false, false],
        ['Province of Sorsogon', false, false],
        ['MUNICIPALITY OF PRIETO DIAZ', false, false],
        ['', false, false],
        ['Office of the Municipal Treasurer', false, true],
        ['ABSTRACT OF COMMUNITY TAX CERTIFICATE', true, false],
        ['For the month of ' . $periodLabel, false, true],
    ];
    $row = 1;
    foreach ($lines as [$text, $bold, $italic]) {
        $sheet->setCellValue("A{$row}", $text);
        $sheet->mergeCells("A{$row}:{$lastCol}{$row}");
        $sheet->getStyle("A{$row}")->getFont()->setName('Roboto')->setSize(10)->setBold($bold)->setItalic($italic);
        $sheet->getStyle("A{$row}")->getAlignment()->setHorizontal($A::HORIZONTAL_CENTER);
        $row++;
    }
    $row++; // blank spacer

    // ── Table header ──
    foreach ($columns as $i => $col) {
        $sheet->setCellValue($C::stringFromColumnIndex($i + 1) . $row, $col['label']);
    }
    $sheet->getStyle("A{$row}:{$lastCol}{$row}")->applyFromArray([
        'font' => ['bold' => true, 'size' => 9, 'name' => 'Roboto'],
        'alignment' => ['horizontal' => $A::HORIZONTAL_LEFT, 'vertical' => $A::VERTICAL_CENTER],
        'borders' => ['allBorders' => ['borderStyle' => $B::BORDER_THIN]],
    ]);
    $sheet->getRowDimension($row)->setRowHeight(15);
    $row++;

    // ── Data rows (numeric columns written as real numbers) ──
    foreach ($section['rows'] as $dataRow) {
        foreach ($dataRow as $i => $value) {
            $letter = $C::stringFromColumnIndex($i + 1);
            if (($columns[$i]['align'] ?? 'left') === 'right') {
                $sheet->setCellValueExplicit("{$letter}{$row}", (float) str_replace(',', '', $value), $NUM);
                $sheet->getStyle("{$letter}{$row}")->getAlignment()->setHorizontal($A::HORIZONTAL_RIGHT);
            } else {
                $sheet->setCellValue("{$letter}{$row}", $value);
                $sheet->getStyle("{$letter}{$row}")->getAlignment()->setHorizontal($A::HORIZONTAL_LEFT);
            }
        }
        $sheet->getStyle("A{$row}:{$lastCol}{$row}")->applyFromArray([
            'font' => ['size' => 8, 'name' => 'Roboto'],
            'alignment' => ['vertical' => $A::VERTICAL_CENTER],
            'borders' => ['allBorders' => ['borderStyle' => $B::BORDER_THIN]],
        ]);
        $sheet->getRowDimension($row)->setRowHeight(15);
        $row++;
    }

    // ── Footer: Sub Total / Add / GRAND TOTAL ──
    $totals = $section['totals'];   // ['', '', 'Total', ctcA, ctcB, penalty, total]
    $footer = function (string $label) use ($sheet, &$row, $lastCol, $A, $B) {
        $sheet->setCellValue("A{$row}", $label);
        $sheet->mergeCells("A{$row}:B{$row}");
        $sheet->getStyle("A{$row}:{$lastCol}{$row}")->applyFromArray([
            'font' => ['bold' => true, 'size' => 8, 'name' => 'Roboto'],
            'alignment' => ['vertical' => $A::VERTICAL_CENTER],
            'borders' => ['allBorders' => ['borderStyle' => $B::BORDER_THIN]],
        ]);
        $sheet->getStyle("A{$row}")->getAlignment()->setHorizontal($A::HORIZONTAL_CENTER);
    };

    $subTotalRow = $row;
    $footer('Sub Total:');
    foreach ([3, 4, 5, 6] as $i) {   // D, E, F, G
        $sheet->setCellValueExplicit($C::stringFromColumnIndex($i + 1) . $row, (float) str_replace(',', '', $totals[$i]), $NUM);
    }
    $row++;

    $addRow = $row;
    $footer('Add:');
    $sheet->setCellValueExplicit("{$lastCol}{$row}", 0, $NUM);
    $row++;

    $footer('GRAND TOTAL:');
    $sheet->setCellValue("{$lastCol}{$row}", "=SUM({$lastCol}{$subTotalRow}:{$lastCol}{$addRow})");

    // ── Column widths (reference template) ──
    $sheet->getColumnDimension('A')->setWidth(15.33203125);
    $sheet->getColumnDimension('B')->setWidth(33.109375);
    $sheet->getColumnDimension('C')->setWidth(11.77734375);
    foreach (['D', 'E', 'F', 'G'] as $col) {
        $sheet->getColumnDimension($col)->setWidth(7.44140625);
    }

    // ── Page setup: portrait, horizontally centered, 0.7" margins ──
    $sheet->getPageSetup()->setOrientation(\PhpOffice\PhpSpreadsheet\Worksheet\PageSetup::ORIENTATION_PORTRAIT);
    $sheet->getPageSetup()->setHorizontalCentered(true);
    $sheet->getPageMargins()->setLeft(0.7)->setRight(0.7)->setTop(0.7)->setBottom(0.7)->setHeader(0.3)->setFooter(0.3);

    $writer = new \PhpOffice\PhpSpreadsheet\Writer\Xlsx($spreadsheet);

    return response()->streamDownload(function () use ($writer) {
        $writer->save('php://output');
    }, $filename, [
        'Content-Type' => 'application/vnd.openxmlformats-officedocument.spreadsheetml.sheet',
    ]);
}

/**
 * Streams an activity-log style export (Name / Activity Log / Date / Time)
 * as a styled .xlsx: green header row with white bold text and filter
 * dropdowns, alternating light-green row stripes, auto-sized columns.
 */
function export_activity_log_xlsx(\Illuminate\Support\Collection $rows, string $filename): \Symfony\Component\HttpFoundation\StreamedResponse
{
    $spreadsheet = new \PhpOffice\PhpSpreadsheet\Spreadsheet();
    $sheet = $spreadsheet->getActiveSheet();

    $headers = ['Name', 'Activity Log', 'Date', 'Time'];
    $sheet->fromArray($headers, null, 'A1');
    $sheet->getStyle('A1:D1')->applyFromArray([
        'font' => ['bold' => true, 'color' => ['rgb' => 'FFFFFF']],
        'fill' => [
            'fillType' => \PhpOffice\PhpSpreadsheet\Style\Fill::FILL_SOLID,
            'startColor' => ['rgb' => '538135'],
        ],
    ]);

    $rowNum = 2;
    foreach ($rows as $row) {
        $sheet->fromArray([
            $row->user_name,
            $row->action,
            $row->created_at->format('d-M-y'),
            $row->created_at->format('g:i A'),
        ], null, "A{$rowNum}");

        if ($rowNum % 2 === 0) {
            $sheet->getStyle("A{$rowNum}:D{$rowNum}")->applyFromArray([
                'fill' => [
                    'fillType' => \PhpOffice\PhpSpreadsheet\Style\Fill::FILL_SOLID,
                    'startColor' => ['rgb' => 'E2EFDA'],
                ],
            ]);
        }

        $rowNum++;
    }

    $lastRow = max($rowNum - 1, 1);
    $sheet->setAutoFilter("A1:D{$lastRow}");
    $sheet->freezePane('A2');

    foreach (['A', 'B', 'C', 'D'] as $column) {
        $sheet->getColumnDimension($column)->setAutoSize(true);
    }

    $writer = new \PhpOffice\PhpSpreadsheet\Writer\Xlsx($spreadsheet);

    return response()->streamDownload(function () use ($writer) {
        $writer->save('php://output');
    }, $filename, [
        'Content-Type' => 'application/vnd.openxmlformats-officedocument.spreadsheetml.sheet',
    ]);
}

/**
 * Shared listing query for the User Management table partial, reused by the
 * index route and by every mutating route that re-renders the table.
 */
function um_user_list_data(\Illuminate\Http\Request $request): array
{
    $perPageOptions = [10, 25, 50, 100];
    $perPage = in_array((int) $request->input('per_page'), $perPageOptions) ? (int) $request->input('per_page') : 10;

    $sortable = ['name', 'email', 'mobile', 'status', 'created_at', 'added_by'];
    $sort = in_array($request->input('sort'), $sortable) ? $request->input('sort') : 'created_at';
    $direction = $request->input('direction') === 'asc' ? 'asc' : 'desc';

    $users = \App\Models\User::query()
        ->with('roles')
        ->where('status', '!=', \App\Models\User::STATUS_ARCHIVED)
        ->when($request->input('search'), function ($query, $search) {
            $query->where(function ($q) use ($search) {
                $q->where('name', 'like', "%{$search}%")
                    ->orWhere('email', 'like', "%{$search}%");
            });
        })
        ->orderBy($sort, $direction)
        ->paginate($perPage)
        ->withQueryString();

    return [
        'users' => $users,
        'perPageOptions' => $perPageOptions,
        'perPage' => $perPage,
        'sort' => $sort,
        'direction' => $direction,
    ];
}

} // end function_exists guard for RAM/UM route-file helpers

Route::get('/user-management', function (\Illuminate\Http\Request $request) {
    $data = um_user_list_data($request);
    $data['roles'] = \App\Models\Role::all();

    if ($request->ajax()) {
        return view('user-management.partials.users-table', $data);
    }

    return view('user-management.index', $data);
})->name('user-management');

Route::post('/user-management/users', function (\Illuminate\Http\Request $request) {
    $validated = $request->validate([
        'username' => ['nullable', 'string', 'max:255', 'unique:users,username'],
        'name' => ['required', 'string', 'max:255'],
        'email' => ['required', 'email', 'max:255', 'unique:users,email'],
        'mobile' => ['nullable', 'string', 'max:32'],
        'password' => ['required', 'string', 'min:8', 'confirmed'],
        'status' => ['required', \Illuminate\Validation\Rule::in([\App\Models\User::STATUS_ACTIVATED, \App\Models\User::STATUS_DISABLED])],
        'role' => ['required', 'exists:roles,id'],
    ]);

    $user = \App\Models\User::create([
        'username' => $validated['username'] ?? null,
        'name' => $validated['name'],
        'email' => $validated['email'],
        'mobile' => $validated['mobile'] ?? null,
        'password' => $validated['password'],
        'status' => $validated['status'],
        'added_by' => $request->user()?->name,
    ]);

    $user->roles()->sync([$validated['role']]);

    \App\Models\ActivityLog::record('User Management - Add User - ' . $user->name);

    return view('user-management.partials.users-table', um_user_list_data($request));
})->name('user-management.users.store');

Route::put('/user-management/users/{user}', function (\Illuminate\Http\Request $request, \App\Models\User $user) {
    $validated = $request->validate([
        'username' => ['nullable', 'string', 'max:255', \Illuminate\Validation\Rule::unique('users', 'username')->ignore($user->id)],
        'name' => ['required', 'string', 'max:255'],
        'email' => ['required', 'email', 'max:255', \Illuminate\Validation\Rule::unique('users', 'email')->ignore($user->id)],
        'mobile' => ['nullable', 'string', 'max:32'],
        'status' => ['required', \Illuminate\Validation\Rule::in([\App\Models\User::STATUS_ACTIVATED, \App\Models\User::STATUS_DISABLED])],
        'role' => ['required', 'exists:roles,id'],
    ]);

    $user->update([
        'username' => $validated['username'] ?? null,
        'name' => $validated['name'],
        'email' => $validated['email'],
        'mobile' => $validated['mobile'] ?? null,
        'status' => $validated['status'],
    ]);

    $user->roles()->sync([$validated['role']]);

    \App\Models\ActivityLog::record('User Management - Edit User - ' . $user->name);

    return view('user-management.partials.users-table', um_user_list_data($request));
})->name('user-management.users.update');

Route::post('/user-management/users/{user}/disable', function (\Illuminate\Http\Request $request, \App\Models\User $user) {
    $user->update(['status' => \App\Models\User::STATUS_DISABLED]);

    \App\Models\ActivityLog::record('User Management - Disable User - ' . $user->name);

    return view('user-management.partials.users-table', um_user_list_data($request));
})->name('user-management.users.disable');

Route::post('/user-management/users/{user}/activate', function (\Illuminate\Http\Request $request, \App\Models\User $user) {
    $user->update(['status' => \App\Models\User::STATUS_ACTIVATED]);

    \App\Models\ActivityLog::record('User Management - Activate User - ' . $user->name);

    return view('user-management.partials.users-table', um_user_list_data($request));
})->name('user-management.users.activate');

Route::post('/user-management/users/{user}/archive', function (\Illuminate\Http\Request $request, \App\Models\User $user) {
    $user->update(['status' => \App\Models\User::STATUS_ARCHIVED, 'archived_at' => now()]);

    \App\Models\ActivityLog::record('User Management - Archive User - ' . $user->name);

    return view('user-management.partials.users-table', um_user_list_data($request));
})->name('user-management.users.archive');

Route::post('/user-management/users/{user}/verify-password', function (\Illuminate\Http\Request $request, \App\Models\User $user) {
    $validated = $request->validate([
        'password' => ['required', 'string'],
    ]);

    if (! \Illuminate\Support\Facades\Hash::check($validated['password'], $request->user()->password)) {
        return response()->json(['message' => 'Incorrect password. Please try again.'], 422);
    }

    return response()->json(['verified' => true]);
})->name('user-management.users.verify-password');

Route::post('/user-management/users/{user}/reset-password', function (\Illuminate\Http\Request $request, \App\Models\User $user) {
    $validated = $request->validate([
        'password' => ['required', 'string', 'min:8'],
    ]);

    $user->update(['password' => $validated['password']]);

    \App\Models\ActivityLog::record('User Management - Reset Password - ' . $user->name);

    return response()->json([
        'email' => $user->email,
    ]);
})->name('user-management.users.reset-password');

Route::get('/user-management/logs', function (\Illuminate\Http\Request $request) {
    $perPageOptions = [10, 25, 50, 100];
    $perPage = in_array((int) $request->input('per_page'), $perPageOptions) ? (int) $request->input('per_page') : 10;

    $sortable = ['user_name', 'action', 'created_at'];
    $sort = in_array($request->input('sort'), $sortable) ? $request->input('sort') : 'created_at';
    $direction = $request->input('direction') === 'asc' ? 'asc' : 'desc';

    $logs = \App\Models\ActivityLog::query()
        ->join('users', 'activity_logs.user_id', '=', 'users.id')
        ->select('activity_logs.*', 'users.name as user_name')
        ->when($request->input('search'), function ($query, $search) {
            $query->where(function ($q) use ($search) {
                $q->where('users.name', 'like', "%{$search}%")
                    ->orWhere('activity_logs.action', 'like', "%{$search}%");
            });
        })
        ->orderBy($sort === 'user_name' ? 'users.name' : "activity_logs.{$sort}", $direction)
        ->paginate($perPage)
        ->withQueryString();

    $data = [
        'logs' => $logs,
        'perPageOptions' => $perPageOptions,
        'perPage' => $perPage,
        'sort' => $sort,
        'direction' => $direction,
    ];

    if ($request->ajax()) {
        return view('user-management.partials.logs-table', $data);
    }

    return view('user-management.logs', $data);
})->name('user-management.logs');

Route::get('/user-management/logs/export', function (\Illuminate\Http\Request $request) {
    $logs = \App\Models\ActivityLog::query()
        ->join('users', 'activity_logs.user_id', '=', 'users.id')
        ->select('activity_logs.*', 'users.name as user_name')
        ->when($request->input('search'), function ($query, $search) {
            $query->where(function ($q) use ($search) {
                $q->where('users.name', 'like', "%{$search}%")
                    ->orWhere('activity_logs.action', 'like', "%{$search}%");
            });
        })
        ->orderBy('activity_logs.created_at', 'desc')
        ->get();

    return export_activity_log_xlsx($logs, 'user-management-logs.xlsx');
})->name('user-management.logs.export');

Route::get('/user-management/roles-permissions', function (\Illuminate\Http\Request $request) {
    $roles = \App\Models\Role::with('permissions')->get();

    $activeRole = $request->input('role', 'all');
    if (! in_array($activeRole, array_merge(['all'], $roles->pluck('slug')->all()))) {
        $activeRole = 'all';
    }

    return view('user-management.roles-permissions', [
        'roles' => $roles,
        'activeRole' => $activeRole,
        'modules' => \App\Models\RoleModulePermission::MODULES,
    ]);
})->name('user-management.roles-permissions');

Route::post('/user-management/roles-permissions', function (\Illuminate\Http\Request $request) {
    $validated = $request->validate([
        'permissions' => ['required', 'array'],
        'permissions.*.*.*' => ['sometimes', 'boolean'],
    ]);

    $fields = ['view', 'add', 'generate_report', 'print', 'export', 'request_admin_cancellation', 'reset_password', 'change_permission'];
    $updatedRoleIds = [];

    foreach ($validated['permissions'] as $roleId => $modules) {
        foreach ($modules as $module => $values) {
            if (! array_key_exists($module, \App\Models\RoleModulePermission::MODULES)) {
                continue;
            }

            $updates = \Illuminate\Support\Arr::only($values, $fields);

            if (empty($updates)) {
                continue;
            }

            \App\Models\RoleModulePermission::where('role_id', $roleId)
                ->where('module', $module)
                ->update($updates);

            $updatedRoleIds[] = $roleId;
        }
    }

    $updatedRoleNames = \App\Models\Role::whereIn('id', array_unique($updatedRoleIds))->pluck('name')->implode(', ');

    \App\Models\ActivityLog::record('User Management - Change Permission - ' . ($updatedRoleNames ?: 'No changes'));

    return response()->json(['message' => 'Permissions updated successfully.']);
})->name('user-management.roles-permissions.update');
Route::get('/records', function (\Illuminate\Http\Request $request) {
    $perPageOptions = [10, 25, 50, 100];
    $perPage = in_array((int) $request->input('per_page'), $perPageOptions) ? (int) $request->input('per_page') : 10;

    $sortable = ['user_name', 'action', 'created_at'];
    $sort = in_array($request->input('sort'), $sortable) ? $request->input('sort') : 'created_at';
    $direction = $request->input('direction') === 'asc' ? 'asc' : 'desc';

    $modules = \App\Models\ActivityLog::query()
        ->pluck('action')
        ->map(fn ($action) => trim(explode(' - ', $action)[0]))
        ->unique()
        ->sort()
        ->values();

    $module = $request->input('module');
    if (! $modules->contains($module)) {
        $module = null;
    }

    $records = \App\Models\ActivityLog::query()
        ->join('users', 'activity_logs.user_id', '=', 'users.id')
        ->select('activity_logs.*', 'users.name as user_name')
        ->when($module, function ($query, $module) {
            $query->where('activity_logs.action', 'like', "{$module} - %");
        })
        ->when($request->input('search'), function ($query, $search) {
            $query->where(function ($q) use ($search) {
                $q->where('users.name', 'like', "%{$search}%")
                    ->orWhere('activity_logs.action', 'like', "%{$search}%");
            });
        })
        ->orderBy($sort === 'user_name' ? 'users.name' : "activity_logs.{$sort}", $direction)
        ->paginate($perPage)
        ->withQueryString();

    $data = [
        'records' => $records,
        'perPageOptions' => $perPageOptions,
        'perPage' => $perPage,
        'sort' => $sort,
        'direction' => $direction,
        'modules' => $modules,
        'module' => $module,
    ];

    if ($request->ajax()) {
        return view('records.partials.records-table', $data);
    }

    return view('records.index', $data);
})->name('records');

Route::get('/records/export', function (\Illuminate\Http\Request $request) {
    $modules = \App\Models\ActivityLog::query()
        ->pluck('action')
        ->map(fn ($action) => trim(explode(' - ', $action)[0]))
        ->unique();

    $module = $modules->contains($request->input('module')) ? $request->input('module') : null;

    $records = \App\Models\ActivityLog::query()
        ->join('users', 'activity_logs.user_id', '=', 'users.id')
        ->select('activity_logs.*', 'users.name as user_name')
        ->when($module, function ($query, $module) {
            $query->where('activity_logs.action', 'like', "{$module} - %");
        })
        ->when($request->input('search'), function ($query, $search) {
            $query->where(function ($q) use ($search) {
                $q->where('users.name', 'like', "%{$search}%")
                    ->orWhere('activity_logs.action', 'like', "%{$search}%");
            });
        })
        ->orderBy('activity_logs.created_at', 'desc')
        ->get();

    return export_activity_log_xlsx($records, 'records.xlsx');
})->name('records.export');

Route::get('/archive-records/users/{user}', function (\App\Models\User $user) {
    $user->load('roles');
    return view('archive-records.user-view', compact('user'));
})->name('archives.users.view');

Route::post('/archive-records/transactions/{log}/unarchive', function (\App\Models\TransactionLog $log) {
    if (! $log->archived_at) {
        return response()->json(['message' => 'This transaction is not archived.'], 422);
    }

    $log->update(['archived_at' => null]);

    \App\Models\ActivityLog::record('Collection Management - Unarchive Transaction - ' . \App\Models\TransactionLog::formName($log->form_type) . ' - ' . $log->payee . ' - ' . $log->serial_number);

    return response()->json(['message' => 'Transaction unarchived successfully.']);
})->name('archives.transactions.unarchive');

Route::post('/archive-records/users/{user}/unarchive', function (\App\Models\User $user) {
    if ($user->status !== \App\Models\User::STATUS_ARCHIVED) {
        return response()->json(['message' => 'This user is not archived.'], 422);
    }

    $user->update(['status' => \App\Models\User::STATUS_ACTIVATED, 'archived_at' => null]);

    \App\Models\ActivityLog::record('User Management - Unarchive User - ' . $user->name);

    return response()->json(['message' => "{$user->name} has been unarchived and reactivated."]);
})->name('archives.users.unarchive');

Route::get('/archive-records', function (\Illuminate\Http\Request $request) {
    $tab = in_array($request->input('tab'), ['collection-management', 'user-management'])
        ? $request->input('tab')
        : 'collection-management';

    $perPageOptions = [10, 25, 50, 100];
    $perPage = in_array((int) $request->input('per_page'), $perPageOptions)
        ? (int) $request->input('per_page') : 10;

    if ($tab === 'collection-management') {
        $sortable  = ['serial_number', 'payee', 'transacted_at', 'form_type', 'archived_at'];
        $sort      = in_array($request->input('sort'), $sortable) ? $request->input('sort') : 'archived_at';
        $direction = $request->input('direction') === 'asc' ? 'asc' : 'desc';

        $formTypeOptions = ['Form 58', 'BIR0017', 'Form 53', 'Form 28A', 'BIR0016', 'Form 10', 'Form 5IC', 'Form 56'];
        $formTypes = array_intersect((array) $request->input('form_type', []), $formTypeOptions);
        $dateStart = $request->input('date_start');
        $dateEnd   = $request->input('date_end');

        $transactions = \App\Models\TransactionLog::query()
            ->whereNotNull('archived_at')
            ->when($request->input('search'), function ($q, $search) {
                $q->where(function ($q) use ($search) {
                    $q->where('serial_number', 'like', "%{$search}%")
                      ->orWhere('payee', 'like', "%{$search}%");
                });
            })
            ->when($formTypes, fn ($q, $ft) => $q->whereIn('form_type', $ft))
            ->when($dateStart, fn ($q, $d) => $q->whereDate('archived_at', '>=', $d))
            ->when($dateEnd,   fn ($q, $d) => $q->whereDate('archived_at', '<=', $d))
            ->orderBy($sort, $direction)
            ->paginate($perPage)
            ->withQueryString();

        $data = [
            'tab'            => $tab,
            'transactions'   => $transactions,
            'perPageOptions' => $perPageOptions,
            'perPage'        => $perPage,
            'sort'           => $sort,
            'direction'      => $direction,
        ];

        if ($request->ajax()) {
            return view('archive-records.partials.transactions-table', $data);
        }

        return view('archive-records.index', $data);
    }

    // tab === 'user-management'
    $sortable  = ['name', 'email', 'archived_at'];
    $sort      = in_array($request->input('sort'), $sortable) ? $request->input('sort') : 'archived_at';
    $direction = $request->input('direction') === 'asc' ? 'asc' : 'desc';

    $users = \App\Models\User::query()
        ->with('roles')
        ->where('status', \App\Models\User::STATUS_ARCHIVED)
        ->when($request->input('search'), function ($q, $search) {
            $q->where(function ($q) use ($search) {
                $q->where('name', 'like', "%{$search}%")
                  ->orWhere('email', 'like', "%{$search}%");
            });
        })
        ->orderBy($sort, $direction)
        ->paginate($perPage)
        ->withQueryString();

    $data = [
        'tab'            => $tab,
        'users'          => $users,
        'perPageOptions' => $perPageOptions,
        'perPage'        => $perPage,
        'sort'           => $sort,
        'direction'      => $direction,
    ];

    if ($request->ajax()) {
        return view('archive-records.partials.users-table', $data);
    }

    return view('archive-records.index', $data);
})->name('archives');