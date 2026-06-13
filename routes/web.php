<?php

use Illuminate\Support\Facades\Route;

Route::get('/', function () { return view('index'); })->name('home');

Route::get('/collections', function (\Illuminate\Http\Request $request) {
    $perPageOptions = [10, 25, 50, 100];
    $perPage = in_array((int) $request->input('per_page'), $perPageOptions) ? (int) $request->input('per_page') : 10;

    $transactions = \App\Models\TransactionLog::query()
        ->when($request->input('search'), function ($query, $search) {
            $query->where('serial_number', 'like', "%{$search}%")
                ->orWhere('payee', 'like', "%{$search}%");
        })
        ->orderByDesc('transacted_at')
        ->paginate($perPage)
        ->withQueryString();

    $data = [
        'transactions' => $transactions,
        'perPageOptions' => $perPageOptions,
        'perPage' => $perPage,
    ];

    if ($request->ajax()) {
        return view('collection-management.partials.transactions-table', $data);
    }

    return view('collection-management.index', $data);
})->name('collections');
Route::get ('/collections/transaction-entry', function() { return view('collection-management.transaction-entry.index'); })->name('transaction-entry');

Route::get('/official-receipts-accountable-forms', function () { return view('official-receipt-accountable-forms.index'); })->name('official-receipts-accountable-forms');
Route::get('/reporting-abstract', function () { return view('reporting-abstract.index'); })->name('reporting-abstract');
Route::get('/bank-deposit-reconciliation', function () { return view('bank-deposit-reconciliation.index'); })->name('bank-deposit-reconciliation');
Route::get('/cheque-management', function () { return view('cheque-management.index'); })->name('cheque-management');
Route::get('/user-management', function () { return view('user-management.index'); })->name('user-management');
Route::get('/records', function () { return view('records.index'); })->name('records');
Route::get('archive-records', function () { return view('archive-records.index'); })->name('archives');