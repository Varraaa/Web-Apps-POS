<?php

namespace App\Http\Controllers;

use App\Models\CashierShift;
use App\Models\Customer;
use App\Models\Discount;
use App\Models\Order;
use App\Models\PosHeldBill;
use App\Models\Product;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\View\View;

class AdminPosController extends Controller
{
    /**
     * Render the POS Cashier Terminal register.
     */
    public function index(Request $request): View
    {
        $search = $request->string('search')->trim()->toString();
        $category = $request->string('category')->trim()->toString();

        $query = Product::query()
            ->when($search, fn ($q) => $q->where(fn ($sub) => $sub->where('name', 'like', "%{$search}%")->orWhere('color', 'like', "%{$search}%")))
            ->when($category, fn ($q) => $q->where('category', $category));

        $products = $query->orderBy('name')->get();

        $categories = Product::distinct()->pluck('category')->filter()->values();

        $coupons = Discount::where('is_active', true)
            ->where(fn ($q) => $q->whereNull('expires_at')->orWhere('expires_at', '>', now()))
            ->get();

        $todayOrders = Order::where('source', 'pos')
            ->whereDate('created_at', now()->today())
            ->latest()
            ->take(8)
            ->get();

        $todaySales = (float) Order::where('source', 'pos')
            ->whereDate('created_at', now()->today())
            ->whereIn('status', ['completed', 'processed'])
            ->sum('total');

        $todayCount = Order::where('source', 'pos')
            ->whereDate('created_at', now()->today())
            ->count();

        $currentShift = CashierShift::active()->first();

        return view('admin.pos.index', compact(
            'products',
            'categories',
            'coupons',
            'todayOrders',
            'todaySales',
            'todayCount',
            'currentShift',
            'search',
            'category'
        ));
    }

    /**
     * Process POS transaction checkout.
     */
    public function checkout(Request $request): JsonResponse|RedirectResponse
    {
        $data = $request->validate([
            'customer_id' => ['nullable', 'exists:customers,id'],
            'redeem_points' => ['nullable', 'boolean'],
            'use_store_credit' => ['nullable', 'boolean'],
            'customer_name' => ['nullable', 'string', 'max:255'],
            'customer_phone' => ['nullable', 'string', 'max:30'],
            'payment_method' => ['required', 'string', 'in:cash,qris,transfer,whatsapp,split'],
            'is_split_payment' => ['nullable', 'boolean'],
            'primary_payment_method' => ['nullable', 'string', 'in:cash,qris,transfer'],
            'secondary_payment_method' => ['nullable', 'string', 'in:cash,qris,transfer'],
            'secondary_amount_paid' => ['nullable', 'numeric', 'min:0'],
            'amount_paid' => ['nullable', 'numeric', 'min:0'],
            'coupon_code' => ['nullable', 'string', 'max:30'],
            'custom_discount_type' => ['nullable', 'string', 'in:fixed,percent'],
            'custom_discount_value' => ['nullable', 'numeric', 'min:0'],
            'custom_discount_reason' => ['nullable', 'string', 'max:255'],
            'notes' => ['nullable', 'string', 'max:500'],
            'items' => ['required', 'array', 'min:1'],
            'items.*.product_id' => ['required', 'exists:products,id'],
            'items.*.quantity' => ['required', 'integer', 'min:1'],
        ]);

        $customerId = $data['customer_id'] ?? null;
        $customer = $customerId ? Customer::find($customerId) : null;
        $customerName = $customer ? $customer->name : (trim($data['customer_name'] ?? '') ?: 'Pelanggan Walk-In');
        $customerPhone = $customer ? ($customer->phone ?: '-') : (trim($data['customer_phone'] ?? '') ?: '-');
        $notes = $data['notes'] ?? null;
        $paymentMethod = $data['payment_method'];

        try {
            $order = DB::transaction(function () use ($data, $customer, $customerName, $customerPhone, $notes, $paymentMethod, $request): Order {
                $productIds = collect($data['items'])->pluck('product_id')->all();
                $products = Product::whereIn('id', $productIds)->lockForUpdate()->get()->keyBy('id');

                $subtotal = 0.0;
                $orderItemsData = [];

                foreach ($data['items'] as $itemInput) {
                    /** @var Product|null $product */
                    $product = $products->get($itemInput['product_id']);

                    if (! $product) {
                        throw new \RuntimeException('Produk tidak ditemukan.');
                    }

                    $qty = (int) $itemInput['quantity'];
                    if ($product->stock < $qty) {
                        throw new \RuntimeException("Stok produk \"{$product->name}\" tidak mencukupi (sisa: {$product->stock}).");
                    }

                    $unitPrice = $product->getPriceForQuantity($qty);
                    $lineTotal = $unitPrice * $qty;
                    $subtotal += $lineTotal;

                    // Deduct stock
                    $product->decreaseStock($qty);

                    $orderItemsData[] = [
                        'product_id' => $product->id,
                        'product_name' => $product->name,
                        'quantity' => $qty,
                        'price' => $unitPrice,
                    ];
                }

                // Calculate coupon discount
                $couponDiscountAmount = 0.0;
                $couponCode = null;

                if (! empty($data['coupon_code'])) {
                    $coupon = Discount::where('code', strtoupper(trim($data['coupon_code'])))->first();
                    if ($coupon && $coupon->isValid($subtotal)) {
                        $couponDiscountAmount = $coupon->calculateDiscount($subtotal);
                        $couponCode = $coupon->code;
                    }
                }

                // Calculate spontaneous / custom manager discount
                $customDiscountAmount = 0.0;
                $customDiscountType = $data['custom_discount_type'] ?? null;
                $customDiscountValue = isset($data['custom_discount_value']) ? (float) $data['custom_discount_value'] : null;
                $customDiscountReason = $data['custom_discount_reason'] ?? null;

                if ($customDiscountValue > 0) {
                    if ($customDiscountType === 'percent') {
                        $customDiscountAmount = round(($customDiscountValue / 100) * $subtotal);
                    } else {
                        $customDiscountAmount = min($customDiscountValue, $subtotal);
                        $customDiscountType = 'fixed';
                    }
                }

                $discountAmount = $couponDiscountAmount + $customDiscountAmount;
                $totalAfterCoupon = max(0.0, $subtotal - $discountAmount);

                // Calculate Loyalty Points Redemption
                $pointsRedeemed = 0;
                $pointsDiscountAmount = 0.0;

                if ($customer && ! empty($data['redeem_points']) && $customer->points > 0) {
                    $maxRedeemablePoints = (int) floor($totalAfterCoupon / 100);
                    $pointsRedeemed = min($customer->points, $maxRedeemablePoints);
                    $pointsDiscountAmount = $pointsRedeemed * 100;
                }

                $totalAfterPoints = max(0.0, $totalAfterCoupon - $pointsDiscountAmount);

                // Calculate Store Credit Wallet Deduction
                $storeCreditUsed = 0.0;
                if ($customer && ! empty($data['use_store_credit']) && (float) $customer->store_credit > 0) {
                    $storeCreditUsed = min((float) $customer->store_credit, $totalAfterPoints);
                    $customer->deductStoreCredit($storeCreditUsed);
                }

                $total = max(0.0, $totalAfterPoints - $storeCreditUsed);

                $isSplit = ($paymentMethod === 'split') || (! empty($data['is_split_payment']));
                $primaryMethod = $isSplit ? ($data['primary_payment_method'] ?? 'cash') : $paymentMethod;
                $secondaryMethod = $isSplit ? ($data['secondary_payment_method'] ?? 'qris') : null;
                $secondaryAmountPaid = $isSplit ? (float) ($data['secondary_amount_paid'] ?? 0) : null;
                $primaryAmountPaid = isset($data['amount_paid']) ? (float) $data['amount_paid'] : ($isSplit ? max(0, $total - ($secondaryAmountPaid ?? 0)) : $total);

                $totalPaid = $isSplit ? ($primaryAmountPaid + (float) $secondaryAmountPaid) : $primaryAmountPaid;

                if ($isSplit) {
                    if ($totalPaid < $total) {
                        throw new \RuntimeException('Total pembayaran campuran (split payment) kurang dari total tagihan.');
                    }
                    $changeAmount = ($primaryMethod === 'cash' || $secondaryMethod === 'cash') ? max(0.0, $totalPaid - $total) : 0.0;
                    $paymentBreakdown = [
                        ['method' => $primaryMethod, 'amount' => $primaryAmountPaid],
                        ['method' => $secondaryMethod, 'amount' => $secondaryAmountPaid],
                    ];
                } else {
                    if ($primaryMethod === 'cash' && $primaryAmountPaid < $total) {
                        throw new \RuntimeException('Jumlah uang tunai yang dibayarkan kurang dari total tagihan.');
                    }
                    $changeAmount = ($primaryMethod === 'cash') ? max(0.0, $primaryAmountPaid - $total) : 0.0;
                    $paymentBreakdown = null;
                }

                // Points earned for this purchase: 1 point per Rp 10.000 spent
                $pointsEarned = $customer ? (int) floor($total / 10000) : 0;

                $order = Order::create([
                    'user_id' => $request->user()?->id,
                    'customer_id' => $customer?->id,
                    'customer_name' => $customerName,
                    'customer_phone' => $customerPhone,
                    'shipping_address' => 'Transaksi Kasir POS / Di Tempat',
                    'subtotal' => $subtotal,
                    'discount_amount' => $discountAmount,
                    'coupon_code' => $couponCode,
                    'custom_discount_type' => $customDiscountType,
                    'custom_discount_value' => $customDiscountValue,
                    'custom_discount_amount' => $customDiscountAmount,
                    'custom_discount_reason' => $customDiscountReason,
                    'points_earned' => $pointsEarned,
                    'points_redeemed' => $pointsRedeemed,
                    'points_discount_amount' => $pointsDiscountAmount,
                    'store_credit_used' => $storeCreditUsed,
                    'total' => $total,
                    'payment_method' => $primaryMethod,
                    'is_split_payment' => $isSplit,
                    'secondary_payment_method' => $secondaryMethod,
                    'secondary_amount_paid' => $secondaryAmountPaid,
                    'payment_breakdown' => $paymentBreakdown,
                    'amount_paid' => $primaryAmountPaid,
                    'change_amount' => $changeAmount,
                    'cashier_name' => $request->user()?->name ?? 'Kasir VarStore',
                    'source' => 'pos',
                    'status' => 'completed',
                    'notes' => $notes,
                ]);

                foreach ($orderItemsData as $itemData) {
                    $order->items()->create($itemData);
                }

                if ($customer) {
                    $customer->recordTransaction($total, $pointsEarned, $pointsRedeemed);
                }

                return $order;
            });

            if ($request->wantsJson()) {
                return response()->json([
                    'success' => true,
                    'message' => 'Transaksi POS berhasil diselesaikan!',
                    'order_id' => $order->id,
                    'invoice_number' => $order->invoice_number,
                    'receipt_url' => route('admin.pos.receipt', $order),
                ]);
            }

            return redirect()->route('admin.pos.receipt', $order)
                ->with('success', "Transaksi POS #{$order->invoice_number} berhasil diselesaikan!");
        } catch (\Throwable $e) {
            if ($request->wantsJson()) {
                return response()->json([
                    'success' => false,
                    'message' => $e->getMessage(),
                ], 422);
            }

            return back()->withInput()->with('error', $e->getMessage());
        }
    }

    /**
     * List currently held bills.
     */
    public function listHeldBills(): JsonResponse
    {
        $bills = PosHeldBill::with('customer')->latest()->get();

        return response()->json([
            'count' => $bills->count(),
            'bills' => $bills->map(fn ($b) => [
                'id' => $b->id,
                'customer_name' => $b->customer_name,
                'customer_id' => $b->customer_id,
                'reference_note' => $b->reference_note,
                'items_count' => collect($b->items)->sum('quantity'),
                'subtotal' => (float) $b->subtotal,
                'formatted_subtotal' => $b->formatted_subtotal,
                'time_ago' => $b->created_at->diffForHumans(),
            ]),
        ]);
    }

    /**
     * Hold active cart.
     */
    public function holdBill(Request $request): JsonResponse
    {
        $validated = $request->validate([
            'customer_name' => ['nullable', 'string', 'max:255'],
            'customer_id' => ['nullable', 'exists:customers,id'],
            'reference_note' => ['nullable', 'string', 'max:255'],
            'coupon_code' => ['nullable', 'string', 'max:30'],
            'items' => ['required', 'array', 'min:1'],
        ]);

        $subtotal = collect($validated['items'])->sum(fn ($i) => (float) $i['price'] * (int) $i['quantity']);

        $held = PosHeldBill::create([
            'user_id' => $request->user()?->id,
            'customer_id' => $validated['customer_id'] ?? null,
            'customer_name' => $validated['customer_name'] ?? 'Pelanggan Walk-In',
            'reference_note' => $validated['reference_note'] ?? null,
            'items' => $validated['items'],
            'coupon_code' => $validated['coupon_code'] ?? null,
            'subtotal' => $subtotal,
        ]);

        return response()->json([
            'success' => true,
            'message' => 'Tagihan transaksi berhasil diparkir/disimpan.',
            'held_id' => $held->id,
        ]);
    }

    /**
     * Recall and remove held bill.
     */
    public function recallHeldBill(PosHeldBill $heldBill): JsonResponse
    {
        $data = [
            'id' => $heldBill->id,
            'customer_name' => $heldBill->customer_name,
            'customer_id' => $heldBill->customer_id,
            'customer' => $heldBill->customer,
            'reference_note' => $heldBill->reference_note,
            'coupon_code' => $heldBill->coupon_code,
            'items' => $heldBill->items,
        ];

        $heldBill->delete();

        return response()->json([
            'success' => true,
            'bill' => $data,
        ]);
    }

    /**
     * Delete held bill.
     */
    public function deleteHeldBill(PosHeldBill $heldBill): JsonResponse
    {
        $heldBill->delete();

        return response()->json([
            'success' => true,
            'message' => 'Tagihan tersimpan berhasil dihapus.',
        ]);
    }

    /**
     * View thermal receipt for POS order.
     */
    public function receipt(Order $order): View
    {
        $order->load(['items.product', 'user']);

        return view('admin.pos.receipt', compact('order'));
    }

    /**
     * Get current shift status.
     */
    public function getCurrentShift(): JsonResponse
    {
        $shift = CashierShift::active()->first();

        if (! $shift) {
            return response()->json(['active' => false]);
        }

        // Calculate sales since shift opened
        $orders = Order::where('source', 'pos')
            ->where('created_at', '>=', $shift->opened_at)
            ->whereIn('status', ['completed', 'processed'])
            ->get();

        $cashSales = (float) $orders->where('payment_method', 'cash')->sum('total');
        $nonCashSales = (float) $orders->where('payment_method', '!=', 'cash')->sum('total');
        $expectedCash = (float) ($shift->starting_cash + $cashSales);

        return response()->json([
            'active' => true,
            'shift' => [
                'id' => $shift->id,
                'cashier_name' => $shift->cashier_name,
                'opened_at' => $shift->opened_at->format('d M Y, H:i'),
                'opened_time' => $shift->opened_at->diffForHumans(),
                'starting_cash' => (float) $shift->starting_cash,
                'total_cash_sales' => $cashSales,
                'total_non_cash_sales' => $nonCashSales,
                'total_transactions' => $orders->count(),
                'expected_cash' => $expectedCash,
            ],
        ]);
    }

    /**
     * Open a new cashier shift.
     */
    public function openShift(Request $request): JsonResponse
    {
        $validated = $request->validate([
            'cashier_name' => ['required', 'string', 'max:100'],
            'starting_cash' => ['required', 'numeric', 'min:0'],
            'notes' => ['nullable', 'string', 'max:255'],
        ]);

        $active = CashierShift::active()->first();
        if ($active) {
            return response()->json([
                'success' => false,
                'message' => "Shift kasir atas nama {$active->cashier_name} sedang aktif. Harap tutup shift sebelumnya terlebih dahulu.",
            ], 422);
        }

        $shift = CashierShift::create([
            'user_id' => $request->user()?->id,
            'cashier_name' => $validated['cashier_name'],
            'opened_at' => now(),
            'starting_cash' => $validated['starting_cash'],
            'status' => 'open',
            'notes' => $validated['notes'] ?? null,
        ]);

        return response()->json([
            'success' => true,
            'message' => "Shift kasir untuk {$shift->cashier_name} berhasil dibuka!",
            'shift' => $shift,
        ]);
    }

    /**
     * Close active cashier shift and compute settlement.
     */
    public function closeShift(Request $request): JsonResponse
    {
        $shift = CashierShift::active()->first();
        if (! $shift) {
            return response()->json([
                'success' => false,
                'message' => 'Tidak ada shift kasir yang sedang aktif.',
            ], 422);
        }

        $validated = $request->validate([
            'actual_cash' => ['required', 'numeric', 'min:0'],
            'notes' => ['nullable', 'string', 'max:500'],
        ]);

        // Aggregate orders during this shift
        $orders = Order::where('source', 'pos')
            ->where('created_at', '>=', $shift->opened_at)
            ->whereIn('status', ['completed', 'processed'])
            ->get();

        $cashSales = (float) $orders->where('payment_method', 'cash')->sum('total');
        $nonCashSales = (float) $orders->where('payment_method', '!=', 'cash')->sum('total');
        $expectedCash = (float) ($shift->starting_cash + $cashSales);
        $actualCash = (float) $validated['actual_cash'];
        $difference = $actualCash - $expectedCash;

        $shift->update([
            'closed_at' => now(),
            'expected_cash' => $expectedCash,
            'actual_cash' => $actualCash,
            'difference' => $difference,
            'total_cash_sales' => $cashSales,
            'total_non_cash_sales' => $nonCashSales,
            'total_transactions' => $orders->count(),
            'status' => 'closed',
            'notes' => $validated['notes'] ?? $shift->notes,
        ]);

        return response()->json([
            'success' => true,
            'message' => "Shift kasir {$shift->cashier_name} berhasil ditutup. Laporan Z-Report siap dicetak.",
            'z_report_url' => route('admin.pos.shift.z-report', $shift),
            'shift' => $shift,
        ]);
    }

    /**
     * Printable Z-Report thermal closing slip.
     */
    public function zReport(CashierShift $shift): View
    {
        $orders = Order::where('source', 'pos')
            ->where('created_at', '>=', $shift->opened_at)
            ->when($shift->closed_at, fn ($q) => $q->where('created_at', '<=', $shift->closed_at))
            ->whereIn('status', ['completed', 'processed'])
            ->get();

        $byPayment = $orders->groupBy('payment_method')->map(fn ($group) => [
            'count' => $group->count(),
            'total' => (float) $group->sum('total'),
        ]);

        return view('admin.pos.z-report', compact('shift', 'orders', 'byPayment'));
    }
}
