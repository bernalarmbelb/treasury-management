<?php

namespace App\Http\Controllers;

use App\Services\DashboardMetrics;
use Illuminate\Http\Request;

class DashboardController extends Controller
{
    public function index(Request $request)
    {
        $now = now();

        $month = (int) ($request->input('month') ?: $now->month);
        $month = ($month >= 1 && $month <= 12) ? $month : $now->month;

        $year = (int) ($request->input('year') ?: $now->year);
        $year = ($year >= 2020 && $year <= $now->year) ? $year : $now->year;

        $from = \Illuminate\Support\Carbon::create($year, $month, 1)->startOfMonth();
        $to = $from->copy()->endOfMonth();

        $m = DashboardMetrics::forDates($from->toDateString(), $to->toDateString());

        return view('dashboard', [
            'month'          => $month,
            'year'           => $year,
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
