<?php

namespace App\Http\Controllers;

use App\Services\DashboardMetrics;
use Illuminate\Http\Request;

class DashboardController extends Controller
{
    public function index(Request $request)
    {
        $range = in_array($request->input('range'), ['today', 'week', 'month'], true)
            ? $request->input('range') : 'month';

        $m = ($request->filled('from') && $request->filled('to'))
            ? DashboardMetrics::forDates($request->input('from'), $request->input('to'))
            : DashboardMetrics::forRange($range);

        return view('dashboard', [
            'range'          => $range,
            'cash'           => $m->cashPosition(),
            'collections'    => $m->collections(),
            'disbursed'      => $m->disbursed(),
            'exceptions'     => $m->exceptions(),
            'trend'          => $m->trend(),
            'payments'       => $m->paymentMethods(),
            'reconciliation' => $m->reconciliation(),
            'forms'          => $m->formsUtilization(),
            'activity'       => $m->recentActivity(),
        ]);
    }
}
