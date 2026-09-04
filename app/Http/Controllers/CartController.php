<?php

namespace App\Http\Controllers;

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
        return view('store.cart', ['cart' => $this->cart($request)]);
    }

    public function add(Request $request, Product $product): RedirectResponse
    {
        $quantity = min(max($request->integer('quantity', 1), 1), $product->stock);
        $cart = $request->session()->get('cart', []);
        $cart[$product->id] = ['product_id' => $product->id, 'quantity' => min(($cart[$product->id]['quantity'] ?? 0) + $quantity, $product->stock)];
        $request->session()->put('cart', $cart);

        return back()->with('success', 'Produk ditambahkan ke tas.');
    }

    public function remove(Request $request, Product $product): RedirectResponse
    {
        $cart = $request->session()->get('cart', []);
        unset($cart[$product->id]);
        $request->session()->put('cart', $cart);

        return back();
    }

    public function checkout(Request $request): RedirectResponse
    {
        $data = $request->validate(['customer_name' => ['required', 'string', 'max:255'], 'customer_phone' => ['required', 'string', 'max:30'], 'shipping_address' => ['required', 'string', 'max:1000']]);
        $cart = $this->cart($request);
        abort_if($cart['items']->isEmpty(), 422, 'Tas belanja masih kosong.');
        $order = DB::transaction(function () use ($data, $cart, $request): Order {
            $order = Order::create([...$data, 'user_id' => $request->user()?->id, 'total' => $cart['total'], 'status' => 'pending']);
            foreach ($cart['items'] as $item) {
                $order->items()->create(['product_id' => $item['product']->id, 'product_name' => $item['product']->name, 'quantity' => $item['quantity'], 'price' => $item['product']->price]);
            }

            return $order;
        });
        $request->session()->forget('cart');
        $productNames = $order->items->pluck('product_name')->join(', ');
        $lines = collect(['Halo VarStore, saya ingin konfirmasi pesanan ('.$productNames.').', 'Nama: '.$order->customer_name, 'Telepon: '.$order->customer_phone, 'Alamat: '.$order->shipping_address, 'Total: Rp '.number_format($order->total, 0, ',', '.')])->implode("\n");

        return redirect()->away('https://wa.me/6281297577567?text='.urlencode($lines));
    }

    private function cart(Request $request): array
    {
        $raw = $request->session()->get('cart', []);
        $products = Product::whereIn('id', array_keys($raw))->get()->keyBy('id');
        $items = collect($raw)->map(function (array $item) use ($products): ?array {
            $product = $products->get($item['product_id']);

            return $product ? ['product' => $product, 'quantity' => $item['quantity'], 'subtotal' => $product->price * $item['quantity']] : null;
        })->filter()->values();

        return ['items' => $items, 'total' => $items->sum('subtotal')];
    }
}
