<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class AdminMiddleware
{
    public function handle(Request $request, Closure $next): Response
    {
        if (!auth()->check()) {
            return redirect()->route('login')->with('status', 'Please login to access admin');
        }

        $u = auth()->user();
        if (!($u->is_admin || ($u->role ?? null) === 'admin')) {
            abort(403, 'Unauthorized');
        }

        return $next($request);
    }
}
