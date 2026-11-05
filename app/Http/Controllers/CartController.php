<?php

namespace App\Http\Controllers;

use App\Models\Discount;
use App\Models\Order;
use App\Models\Product;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\View\View;

class CartController extends Controller
{
    public function index(Request $request): View
    {
        $cart = $this->cart($request);
        $availableCoupons = Discount::where('is_active', true)
            ->where(fn ($q) => $q->whereNull('expires_at')->orWhere('expires_at', '>', now()))
            ->get();

        return view('store.cart', [
            'cart' => $cart,
            'availableCoupons' => $availableCoupons,
        ]);
    }

    public function add(Request $request, Product $product): RedirectResponse
    {
        $quantity = min(max($request->integer('quantity', 1), 1), $product->stock);
        $cart = $request->session()->get('cart', []);
        $cart[$product->id] = [
            'product_id' => $product->id,
            'quantity' => min(($cart[$product->id]['quantity'] ?? 0) + $quantity, $product->stock),
        ];
        $request->session()->put('cart', $cart);

        return back()->with('success', 'Produk "'.$product->name.'" ditambahkan ke tas.');
    }

    public function remove(Request $request, Product $product): RedirectResponse
    {
        $cart = $request->session()->get('cart', []);
        unset($cart[$product->id]);
        $request->session()->put('cart', $cart);

        return back()->with('success', 'Produk dihapus dari tas.');
    }

    public function update(Request $request, Product $product): RedirectResponse
    {
        $quantity = $request->integer('quantity', 1);
        $cart = $request->session()->get('cart', []);

        if ($quantity <= 0) {
            unset($cart[$product->id]);
            $request->session()->put('cart', $cart);

            return back()->with('success', 'Produk dihapus dari tas.');
        }

        $cart[$product->id] = [
            'product_id' => $product->id,
            'quantity' => min(max($quantity, 1), $product->stock),
        ];
        $request->session()->put('cart', $cart);

        return back()->with('success', 'Kuantitas barang diperbarui.');
    }

    public function applyCoupon(Request $request): RedirectResponse
    {
        $code = trim((string) $request->input('code'));
        if ($code === '') {
            return back()->with('error', 'Silakan masukkan kode voucher.');
        }

        $coupon = Discount::where('code', strtoupper($code))->first();
        if (! $coupon || ! $coupon->isValid()) {
            return back()->with('error', 'Kode voucher "'.$code.'" tidak valid atau sudah berakhir.');
        }

        $cart = $this->cart($request);
        if ($cart['items']->isEmpty()) {
            return back()->with('error', 'Tas belanja masih kosong. Tambahkan produk sebelum menerapkan voucher.');
        }

        if ($cart['subtotal'] < (float) $coupon->min_spend) {
            $minFormatted = 'Rp '.number_format($coupon->min_spend, 0, ',', '.');

            return back()->with('error', "Minimal belanja untuk voucher {$coupon->code} adalah {$minFormatted}.");
        }

        $request->session()->put('coupon', $coupon->code);

        return back()->with('success', "Voucher {$coupon->code} berhasil diterapkan! Anda hemat {$coupon->formatted_discount}.");
    }

    public function removeCoupon(Request $request): RedirectResponse
    {
        $request->session()->forget('coupon');

        return back()->with('success', 'Voucher berhasil dibatalkan.');
    }

    public function checkout(Request $request): RedirectResponse
    {
        $data = $request->validate([
            'customer_name' => ['required', 'string', 'max:255'],
            'customer_phone' => ['required', 'string', 'max:30'],
            'shipping_address' => ['required', 'string', 'max:1000'],
        ]);

        $cart = $this->cart($request);
        abort_if($cart['items']->isEmpty(), 422, 'Tas belanja masih kosong.');

        $order = DB::transaction(function () use ($data, $cart, $request): Order {
            $order = Order::create([
                ...$data,
                'user_id' => $request->user()?->id,
                'subtotal' => $cart['subtotal'],
                'discount_amount' => $cart['discount_amount'],
                'coupon_code' => $cart['coupon']?->code,
                'total' => $cart['total'],
                'status' => 'pending',
            ]);

            foreach ($cart['items'] as $item) {
                $order->items()->create([
                    'product_id' => $item['product']->id,
                    'product_name' => $item['product']->name,
                    'quantity' => $item['quantity'],
                    'price' => $item['price'],
                ]);
            }

            return $order;
        });

        $request->session()->forget(['cart', 'coupon']);

        $itemDetails = $order->items->map(function ($item): string {
            return "- {$item->product_name} ({$item->quantity}x) @ Rp ".number_format($item->price, 0, ',', '.');
        })->join("\n");

        $lines = [
            'Halo VarStore Concierge, saya ingin konfirmasi pesanan:',
            "No. Order: #{$order->id}",
            "Nama: {$order->customer_name}",
            "Telepon: {$order->customer_phone}",
            "Alamat Pengiriman:\n{$order->shipping_address}",
            "\nDetail Barang:\n{$itemDetails}",
            "\nSubtotal: Rp ".number_format($order->subtotal ?? $order->total, 0, ',', '.'),
        ];

        if ($order->discount_amount > 0) {
            $lines[] = "Diskon Promo ({$order->coupon_code}): - Rp ".number_format($order->discount_amount, 0, ',', '.');
        }

        $lines[] = 'TOTAL PEMBAYARAN: Rp '.number_format($order->total, 0, ',', '.');
        $lines[] = "\nMohon konfirmasi ketersediaan dan rekening pembayaran. Terima kasih!";

        $message = implode("\n", $lines);

        return redirect()->away('https://wa.me/6281297577567?text='.urlencode($message));
    }

    private function cart(Request $request): array
    {
        $raw = $request->session()->get('cart', []);
        $products = Product::whereIn('id', array_keys($raw))->get()->keyBy('id');

        $items = collect($raw)->map(function (array $item) use ($products): ?array {
            /** @var Product|null $product */
            $product = $products->get($item['product_id']);
            if (! $product) {
                return null;
            }

            $unitPrice = $product->getPriceForQuantity($item['quantity']);

            return [
                'product' => $product,
                'quantity' => $item['quantity'],
                'price' => $unitPrice,
                'original_price' => (float) $product->price,
                'subtotal' => $unitPrice * $item['quantity'],
                'is_wholesale' => $product->has_wholesale_price && $item['quantity'] >= $product->wholesale_min_qty,
                'savings' => $product->has_discount ? ($product->discount_savings * $item['quantity']) : 0,
            ];
        })->filter()->values();

        $subtotal = (float) $items->sum('subtotal');
        $couponCode = $request->session()->get('coupon');
        $coupon = null;
        $discountAmount = 0.0;

        if ($couponCode) {
            $coupon = Discount::where('code', $couponCode)->first();
            if ($coupon && $coupon->isValid($subtotal)) {
                $discountAmount = $coupon->calculateDiscount($subtotal);
            } else {
                $request->session()->forget('coupon');
                $coupon = null;
            }
        }

        $total = max(0.0, $subtotal - $discountAmount);

        return [
            'items' => $items,
            'subtotal' => $subtotal,
            'discount_amount' => $discountAmount,
            'coupon' => $coupon,
            'total' => $total,
        ];
    }
}
