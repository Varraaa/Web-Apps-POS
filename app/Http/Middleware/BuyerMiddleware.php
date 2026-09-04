<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class BuyerMiddleware
{
    public function handle(Request $request, Closure $next): Response
    {
        // Admin hanya boleh bekerja di workspace admin, bukan storefront pembeli.
        if ($request->user()?->isAdmin()) {
            return redirect()->route('admin.dashboard');
        }

        return $next($request);
    }
}
