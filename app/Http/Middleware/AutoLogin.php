<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Symfony\Component\HttpFoundation\Response;

class AutoLogin
{
    /**
     * Gate every request behind the login page: guests are redirected to
     * /login, authenticated users whose account is no longer active are
     * logged out, and authenticated users are kept off the login page.
     */
    public function handle(Request $request, Closure $next): Response
    {
        if (Auth::check() && ! Auth::user()->isActivated()) {
            Auth::logout();
            $request->session()->invalidate();
            $request->session()->regenerateToken();
        }

        if (Auth::check()) {
            if ($request->routeIs('login')) {
                return redirect()->route('home');
            }

            return $next($request);
        }

        if ($request->routeIs('login', 'login.attempt') || $request->is('up')) {
            return $next($request);
        }

        return redirect()->route('login');
    }
}
