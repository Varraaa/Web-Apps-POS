<?php

namespace App\Http\Controllers;

use App\Models\Order;
use Illuminate\Http\Request;
use Illuminate\View\View;

class OrderTrackingController extends Controller
{
    /**
     * Show tracking and customer order history portal.
     */
    public function index(Request $request): View
    {
        $query = $request->string('q')->trim()->toString();
        $order = null;
        $orderList = collect();
        $searched = false;

        if ($query) {
            $searched = true;

            // First check if query is exact invoice number or ID
            $exactOrder = Order::with(['items.product'])
                ->where('invoice_number', $query)
                ->orWhere('id', $query)
                ->first();

            if ($exactOrder) {
                $order = $exactOrder;
            } else {
                // If phone number or name, find matching orders
                $orderList = Order::with(['items.product'])
                    ->where(function ($q) use ($query): void {
                        $q->where('customer_phone', 'like', "%{$query}%")
                            ->orWhere('customer_name', 'like', "%{$query}%");
                    })
                    ->latest()
                    ->get();

                if ($orderList->count() === 1) {
                    $order = $orderList->first();
                }
            }
        }

        return view('store.tracking', compact('order', 'orderList', 'query', 'searched'));
    }

    /**
     * View official digital customer invoice.
     */
    public function invoice(Order $order, Request $request): View
    {
        $phone = $request->query('phone');
        $user = $request->user();

        // Allow access if admin, or matches order user_id, or matches customer_phone
        $isAuthorized = ($user && $user->role === 'admin')
            || ($user && $user->id === $order->user_id)
            || ($phone && ($order->customer_phone === $phone || str_ends_with($order->customer_phone, substr($phone, -8))));

        if (! $isAuthorized && ! empty($order->customer_phone)) {
            abort(403, 'Akses invoice memerlukan verifikasi nomor telepon pemesan.');
        }

        $order->load(['items.product', 'user']);

        return view('admin.orders.invoice', compact('order'));
    }
}
