<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class AdminMiddleware
{
    public function handle(Request $request, Closure $next): Response
    {
        // Hanya admin toko yang boleh mengelola katalog produk dan pesanan.
        abort_unless($request->user()?->isAdmin(), 403);

        return $next($request);
    }
}
