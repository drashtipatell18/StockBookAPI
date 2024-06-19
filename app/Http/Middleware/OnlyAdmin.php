<?php

// app/Http/Middleware/OnlyAdmin.php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Symfony\Component\HttpFoundation\Response;

class OnlyAdmin
{
    /**
     * Handle an incoming request.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \Closure  $next
     * @param  string  $role
     * @return \Symfony\Component\HttpFoundation\Response
     */
    public function handle(Request $request, Closure $next, $role = 'admin')
    {
        if (Auth::check() && Auth::user()->role->role_name == $role) {
            return $next($request);
        }
        return response()->json(['success' => false, 'message' => 'Unauthorized'], 403);
    }
}
