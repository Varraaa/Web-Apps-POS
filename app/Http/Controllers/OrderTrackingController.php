<?php

namespace App\Http\Controllers;

use App\Models\Order;
use Illuminate\Http\Request;
use Illuminate\View\View;

class OrderTrackingController extends Controller
{
    /**
     * Show tracking search portal.
     */
    public function index(Request $request): View
    {
        $query = $request->string('q')->trim()->toString();
        $order = null;
        $searched = false;

        if ($query) {
            $searched = true;
            $order = Order::with(['items.product'])
                ->where(function ($q) use ($query): void {
                    $q->where('invoice_number', $query)
                        ->orWhere('id', $query)
                        ->orWhere('customer_phone', $query);
                })
                ->latest()
                ->first();
        }

        return view('store.tracking', compact('order', 'query', 'searched'));
    }
}
