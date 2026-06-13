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

Route::get('/official-receipts-accountable-forms', function () { return view('official-receipt-accountable-forms.index'); })->name('official-receipts-accountable-forms');
Route::get('/reporting-abstract', function () { return view('reporting-abstract.index'); })->name('reporting-abstract');
Route::get('/bank-deposit-reconciliation', function () { return view('bank-deposit-reconciliation.index'); })->name('bank-deposit-reconciliation');
Route::get('/cheque-management', function () { return view('cheque-management.index'); })->name('cheque-management');
Route::get('/user-management', function () { return view('user-management.index'); })->name('user-management');
Route::get('/records', function () { return view('records.index'); })->name('records');
Route::get('archive-records', function () { return view('archive-records.index'); })->name('archives');