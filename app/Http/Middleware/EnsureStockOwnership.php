<?php

declare(strict_types=1);

namespace App\Http\Middleware;

use App\Features\Stock\Models\Stock;
use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class EnsureStockOwnership
{
    public function handle(Request $request, Closure $next): Response
    {
        $user  = $request->user();
        $stock = $request->route('stock');

        if (! $user) {
            return redirect()->route('login');
        }

        // Admin bypasses ownership check (read-only handled by policy)
        if ($user->isAdmin()) {
            return $next($request);
        }

        if (is_numeric($stock)) {
            $stock = Stock::find($stock);
        }

        if ($stock instanceof Stock && $stock->user_id !== $user->id) {
            abort(403, 'You do not own this stock item.');
        }

        return $next($request);
    }
}
