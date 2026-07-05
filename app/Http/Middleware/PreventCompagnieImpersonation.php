<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;

class PreventCompagnieImpersonation
{
    /**
     * Handle an incoming request.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \Closure(\Illuminate\Http\Request): (\Illuminate\Http\Response|\Illuminate\Http\RedirectResponse)  $next
     * @return \Illuminate\Http\Response|\Illuminate\Http\RedirectResponse
     */
    public function handle($request, Closure $next)
    {
        if (session()->has('original_compagnie_user')) {
            // Prevent access to sensitive routes while impersonating
            if ($request->is('compagnie/*') || $request->is('admin/*')) {
                abort(403, 'Cannot access compagnie routes while impersonating a user');
            }
        }

        return $next($request);
    }
}
