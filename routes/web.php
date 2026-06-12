<?php

use Illuminate\Support\Facades\Route;

Route::get('/', function () {
    return view('index');
})->name('home');

Route::get('/collections', function () {
    return view('collection-management.index');
})->name('collections');

Route::get('/official-receipts-accountable-forms', function () {
    return view('official-receipt-accountable-forms.index');
})->name('official-receipts-accountable-forms');

Route::get('/reporting-abstract', function () {
    return view('reporting-abstract.index');
})->name('reporting-abstract');

Route::get('/bank-deposit-reconciliation', function () {
    return view('bank-deposit-reconciliation.index');
})->name('bank-deposit-reconciliation');

Route::get('/cheque-management', function () {
    return view('cheque-management.index');
})->name('cheque-management');

Route::get('/user-management', function () {
    return view('user-management.index');
})->name('user-management');

Route::get('/records', function () {
    return view('records.index');
})->name('records');

Route::get('archive-records', function () {
    return view('archive-records.index');
})->name('archives');