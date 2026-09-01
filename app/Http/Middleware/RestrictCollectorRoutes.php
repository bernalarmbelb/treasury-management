<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class RestrictCollectorRoutes
{
    /**
     * Collector accounts have no business in these three modules — Bank
     * Deposit & Reconciliation, Cheque Management, and User Management are
     * fully off-limits, not just nav-hidden. Matches every named route in
     * those modules (all share the bare module name or a "module.*" name).
     */
    public function handle(Request $request, Closure $next): Response
    {
        $user = $request->user();

        if ($user && $user->hasRole('collector') && $request->routeIs(
            'bank-deposit-reconciliation*',
            'cheque-management*',
            'user-management*',
        )) {
            abort(403, 'This module is not available to Collector accounts.');
        }

        return $next($request);
    }
}
