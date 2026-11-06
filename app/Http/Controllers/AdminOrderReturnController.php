<?php

namespace App\Http\Controllers;

use App\Models\Order;
use App\Models\OrderItem;
use App\Models\OrderReturn;
use App\Models\Product;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\View\View;

class AdminOrderReturnController extends Controller
{
    /**
     * Display a listing of returns.
     */
    public function index(Request $request): View
    {
        $status = $request->string('status')->trim()->toString();
        $search = $request->string('search')->trim()->toString();

        $query = OrderReturn::with(['order', 'product', 'orderItem'])->latest();

        if ($status && in_array($status, ['approved', 'pending', 'rejected'])) {
            $query->where('status', $status);
        }

        if ($search) {
            $query->where(function ($q) use ($search): void {
                $q->where('return_number', 'like', "%{$search}%")
                    ->orWhereHas('order', function ($oq) use ($search): void {
                        $oq->where('invoice_number', 'like', "%{$search}%")
                            ->orWhere('customer_name', 'like', "%{$search}%");
                    })
                    ->orWhereHas('product', function ($pq) use ($search): void {
                        $pq->where('name', 'like', "%{$search}%");
                    });
            });
        }

        $returns = $query->paginate(15)->withQueryString();

        $counts = [
            'all' => OrderReturn::count(),
            'approved' => OrderReturn::where('status', 'approved')->count(),
            'pending' => OrderReturn::where('status', 'pending')->count(),
            'rejected' => OrderReturn::where('status', 'rejected')->count(),
            'total_refunded' => OrderReturn::where('status', 'approved')->sum('refund_amount'),
        ];

        return view('admin.returns.index', compact('returns', 'counts', 'status', 'search'));
    }

    /**
     * Store a newly created return in storage.
     */
    public function store(Request $request, Order $order): RedirectResponse
    {
        $validated = $request->validate([
            'order_item_id' => ['required', 'exists:order_items,id'],
            'quantity' => ['required', 'integer', 'min:1'],
            'refund_amount' => ['required', 'numeric', 'min:0'],
            'reason' => ['required', 'string', 'max:50'],
            'condition' => ['required', 'string', 'max:50'],
            'refund_type' => ['required', 'in:cash,store_credit,exchange'],
            'restock_item' => ['nullable', 'boolean'],
            'notes' => ['nullable', 'string', 'max:500'],
        ]);

        $orderItem = OrderItem::where('order_id', $order->id)->findOrFail($validated['order_item_id']);

        if ($validated['quantity'] > $orderItem->quantity) {
            return back()->with('error', "Jumlah retur melebihi kuantitas pembelian ({$orderItem->quantity} item).");
        }

        $restock = $request->boolean('restock_item', true);

        return DB::transaction(function () use ($order, $orderItem, $validated, $restock, $request): RedirectResponse {
            $orderReturn = OrderReturn::create([
                'order_id' => $order->id,
                'order_item_id' => $orderItem->id,
                'product_id' => $orderItem->product_id,
                'quantity' => $validated['quantity'],
                'refund_amount' => $validated['refund_amount'],
                'reason' => $validated['reason'],
                'condition' => $validated['condition'],
                'refund_type' => $validated['refund_type'],
                'restock_item' => $restock,
                'notes' => $validated['notes'] ?? null,
                'status' => 'approved',
                'processed_by' => $request->user()?->name ?? 'Kasir / Admin',
            ]);

            // Restock product if allowed and in sellable condition
            if ($restock && in_array($validated['condition'], ['intact_with_tags', 'opened_good'])) {
                Product::where('id', $orderItem->product_id)->increment('stock', $validated['quantity']);
            }

            return redirect()->route('admin.returns.show', $orderReturn)
                ->with('success', "Retur barang #{$orderReturn->return_number} berhasil diproses!");
        });
    }

    /**
     * Display the specified return.
     */
    public function show(OrderReturn $orderReturn): View
    {
        $orderReturn->load(['order.customer', 'product', 'orderItem']);

        return view('admin.returns.show', compact('orderReturn'));
    }

    /**
     * Display printable return slip.
     */
    public function slip(OrderReturn $orderReturn): View
    {
        $orderReturn->load(['order.customer', 'product', 'orderItem']);

        return view('admin.returns.slip', compact('orderReturn'));
    }
}
