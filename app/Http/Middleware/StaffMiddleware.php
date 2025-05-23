<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Symfony\Component\HttpFoundation\Response;
class StaffMiddleware
{
    public function handle(Request $request, Closure $next): Response
    {
        if (!Auth::check() || !in_array(Auth::user()->usertype, ['admin', 'staff'])) {
            return redirect('/guest')->with('error', 'Access denied. Admins and staff only.');
        }
        

        return $next($request);
    }
}
