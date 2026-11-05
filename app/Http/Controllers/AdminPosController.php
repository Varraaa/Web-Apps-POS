<?php

namespace App\Http\Controllers;

use App\Models\Customer;
use App\Models\Discount;
use App\Models\Order;
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

        return view('admin.pos.index', compact(
            'products',
            'categories',
            'coupons',
            'todayOrders',
            'todaySales',
            'todayCount',
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
            'customer_name' => ['nullable', 'string', 'max:255'],
            'customer_phone' => ['nullable', 'string', 'max:30'],
            'payment_method' => ['required', 'string', 'in:cash,qris,transfer,whatsapp'],
            'amount_paid' => ['nullable', 'numeric', 'min:0'],
            'coupon_code' => ['nullable', 'string', 'max:30'],
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

                    $unitPrice = $product->final_price;
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
                $discountAmount = 0.0;
                $couponCode = null;

                if (! empty($data['coupon_code'])) {
                    $coupon = Discount::where('code', strtoupper(trim($data['coupon_code'])))->first();
                    if ($coupon && $coupon->isValid($subtotal)) {
                        $discountAmount = $coupon->calculateDiscount($subtotal);
                        $couponCode = $coupon->code;
                    }
                }

                $totalAfterCoupon = max(0.0, $subtotal - $discountAmount);

                // Calculate Loyalty Points Redemption
                $pointsRedeemed = 0;
                $pointsDiscountAmount = 0.0;

                if ($customer && ! empty($data['redeem_points']) && $customer->points > 0) {
                    $maxRedeemablePoints = (int) floor($totalAfterCoupon / 100);
                    $pointsRedeemed = min($customer->points, $maxRedeemablePoints);
                    $pointsDiscountAmount = $pointsRedeemed * 100;
                }

                $total = max(0.0, $totalAfterCoupon - $pointsDiscountAmount);
                $amountPaid = isset($data['amount_paid']) ? (float) $data['amount_paid'] : $total;

                if ($paymentMethod === 'cash' && $amountPaid < $total) {
                    throw new \RuntimeException('Jumlah uang tunai yang dibayarkan kurang dari total tagihan.');
                }

                $changeAmount = ($paymentMethod === 'cash') ? max(0.0, $amountPaid - $total) : 0.0;

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
                    'points_earned' => $pointsEarned,
                    'points_redeemed' => $pointsRedeemed,
                    'points_discount_amount' => $pointsDiscountAmount,
                    'total' => $total,
                    'payment_method' => $paymentMethod,
                    'amount_paid' => $amountPaid,
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
     * View thermal receipt for POS order.
     */
    public function receipt(Order $order): View
    {
        $order->load(['items.product', 'user']);

        return view('admin.pos.receipt', compact('order'));
    }
}
