<?php

namespace App\Http\Controllers;

use App\Models\Order;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;

class AdminOrderController extends Controller
{
    /**
     * Display a listing of orders with filtering and search.
     */
    public function index(Request $request): View
    {
        $status = $request->string('status')->trim()->toString();
        $source = $request->string('source')->trim()->toString();
        $search = $request->string('search')->trim()->toString();

        $query = Order::with(['items.product', 'user'])->latest();

        if ($status && in_array($status, ['pending', 'processed', 'completed', 'cancelled'])) {
            $query->where('status', $status);
        }

        if ($source && in_array($source, ['pos', 'online'])) {
            $query->where('source', $source);
        }

        if ($search) {
            $query->where(function ($q) use ($search): void {
                $q->where('invoice_number', 'like', "%{$search}%")
                    ->orWhere('customer_name', 'like', "%{$search}%")
                    ->orWhere('customer_phone', 'like', "%{$search}%")
                    ->orWhere('id', $search);
            });
        }

        $orders = $query->paginate(15)->withQueryString();

        $counts = [
            'all' => Order::count(),
            'pos' => Order::where('source', 'pos')->count(),
            'online' => Order::where('source', 'online')->count(),
            'pending' => Order::where('status', 'pending')->count(),
            'processed' => Order::where('status', 'processed')->count(),
            'completed' => Order::where('status', 'completed')->count(),
            'cancelled' => Order::where('status', 'cancelled')->count(),
        ];

        return view('admin.orders.index', compact('orders', 'counts', 'status', 'source', 'search'));
    }

    /**
     * Get order details for modal preview.
     */
    public function show(Order $order): JsonResponse|View
    {
        $order->load(['items.product', 'user']);

        if (request()->wantsJson()) {
            return response()->json([
                'success' => true,
                'order' => $order,
                'items' => $order->items,
                'formatted_total' => $order->formatted_total,
                'formatted_subtotal' => $order->formatted_subtotal,
                'formatted_discount' => $order->formatted_discount,
                'receipt_url' => route('admin.pos.receipt', $order),
                'invoice_url' => route('admin.orders.invoice', $order),
            ]);
        }

        return view('admin.orders.show', compact('order'));
    }

    /**
     * View commercial A4 invoice.
     */
    public function invoice(Order $order): View
    {
        $order->load(['items.product', 'user']);

        return view('admin.orders.invoice', compact('order'));
    }

    /**
     * View warehouse packing slip / shipping label.
     */
    public function packingSlip(Order $order): View
    {
        $order->load(['items.product', 'user']);

        return view('admin.orders.packing-slip', compact('order'));
    }

    /**
     * Update order status.
     */
    public function update(Request $request, Order $order): RedirectResponse
    {
        $validated = $request->validate([
            'status' => ['required', 'in:pending,processed,completed,cancelled'],
        ]);

        $order->update($validated);

        return back()->with('success', "Status pesanan #{$order->invoice_number} berhasil diubah menjadi {$order->status_label}.");
    }
}
