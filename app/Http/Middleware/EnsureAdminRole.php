<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class EnsureAdminRole
{
    public function handle(Request $request, Closure $next): Response
    {
        if (!$request->user()?->hasRole('AdminRole')) {
            return redirect()->route('no_permission');
        }

        return $next($request);
    }
}
