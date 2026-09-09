<?php

namespace App\Http\Controllers;

use App\Models\Discount;
use App\Models\Product;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;

class StoreController extends Controller
{
    public function index(Request $request): View
    {
        $featured = Product::where('is_featured', true)
            ->where('stock', '>', 0)
            ->latest()
            ->take(6)
            ->get();

        if ($featured->isEmpty()) {
            $featured = Product::where('stock', '>', 0)->latest()->take(6)->get();
        }

        $saleProducts = Product::where('discount_percent', '>', 0)
            ->where('stock', '>', 0)
            ->latest()
            ->take(4)
            ->get();

        $activeCoupons = Discount::where('is_active', true)
            ->where(fn ($q) => $q->whereNull('expires_at')->orWhere('expires_at', '>', now()))
            ->take(3)
            ->get();

        return view('store.home', compact('featured', 'saleProducts', 'activeCoupons'));
    }

    public function about(): View
    {
        return view('store.about');
    }

    public function collection(Request $request): View
    {
        $category = $request->string('category')->trim()->toString();
        $search = $request->string('search')->trim()->toString();
        $saleOnly = $request->boolean('sale');
        $sort = $request->string('sort', 'latest')->toString();

        $query = Product::query()
            ->when($category, fn ($q) => $q->where('category', $category))
            ->when($search, fn ($q) => $q->where(fn ($sub) => $sub->where('name', 'like', "%{$search}%")->orWhere('description', 'like', "%{$search}%")))
            ->when($saleOnly, fn ($q) => $q->where('discount_percent', '>', 0));

        match ($sort) {
            'price_asc' => $query->orderBy('price', 'asc'),
            'price_desc' => $query->orderBy('price', 'desc'),
            'discount' => $query->orderBy('discount_percent', 'desc'),
            default => $query->latest(),
        };

        $products = $query->paginate(12)->withQueryString();

        $categoryCounts = Product::selectRaw('category, count(*) as total')
            ->groupBy('category')
            ->pluck('total', 'category')
            ->toArray();

        $saleCount = Product::where('discount_percent', '>', 0)->count();

        $activeCoupons = Discount::where('is_active', true)
            ->where(fn ($q) => $q->whereNull('expires_at')->orWhere('expires_at', '>', now()))
            ->take(2)
            ->get();

        return view('store.collection', compact(
            'products',
            'category',
            'search',
            'saleOnly',
            'sort',
            'categoryCounts',
            'saleCount',
            'activeCoupons'
        ));
    }

    public function show(Product $product): View
    {
        $related = Product::where('category', $product->category)
            ->whereKeyNot($product->id)
            ->take(4)
            ->get();

        $activeCoupons = Discount::where('is_active', true)->take(2)->get();

        return view('store.show', compact('product', 'related', 'activeCoupons'));
    }

    public function buy(Product $product, Request $request): RedirectResponse
    {
        abort_if($product->stock < 1, 422, 'Produk sedang habis.');

        $quantity = min(max($request->integer('quantity', 1), 1), $product->stock);
        $unitPrice = $product->final_price;
        $total = $unitPrice * $quantity;

        $lines = [
            'Halo VarStore Concierge, saya ingin memesan langsung:',
            "- Produk: {$product->name}",
            "- Kategori: {$product->category}",
            "- Warna / Ukuran: {$product->color} ({$product->size})",
            "- Jumlah: {$quantity} pcs",
        ];

        if ($product->has_discount) {
            $lines[] = '- Harga Normal: Rp '.number_format($product->price, 0, ',', '.');
            $lines[] = "- Promo Spesial: Diskon {$product->discount_percent}% (Hemat Rp ".number_format($product->discount_savings * $quantity, 0, ',', '.').')';
        }

        $lines[] = 'TOTAL HARGA: Rp '.number_format($total, 0, ',', '.');
        $lines[] = "\nMohon konfirmasi pesanan dan ketersediaan stok. Terima kasih!";

        $message = implode("\n", $lines);

        return redirect()->away('https://wa.me/6281297577567?text='.urlencode($message));
    }
}
