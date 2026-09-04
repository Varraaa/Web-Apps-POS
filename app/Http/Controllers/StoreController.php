<?php

namespace App\Http\Controllers;

use App\Models\Product;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;

class StoreController extends Controller
{
    public function index(Request $request): View
    {
        $featured = Product::where('is_featured', true)->where('stock', '>', 0)->latest()->take(3)->get();

        return view('store.home', compact('featured'));
    }

    public function about(): View
    {
        return view('store.about');
    }

    public function collection(Request $request): View
    {
        $category = $request->string('category')->trim()->toString();
        $search = $request->string('search')->trim()->toString();
        $products = Product::query()->when($category, fn ($query) => $query->where('category', $category))->when($search, fn ($query) => $query->where(fn ($query) => $query->where('name', 'like', "%{$search}%")->orWhere('description', 'like', "%{$search}%")))->latest()->paginate(12)->withQueryString();

        return view('store.collection', compact('products', 'category', 'search'));
    }

    public function show(Product $product): View
    {
        return view('store.show', ['product' => $product, 'related' => Product::where('category', $product->category)->whereKeyNot($product->id)->take(4)->get()]);
    }

    public function buy(Product $product, Request $request): RedirectResponse
    {
        abort_if($product->stock < 1, 422, 'Produk sedang habis.');

        $quantity = min(max($request->integer('quantity', 1), 1), $product->stock);
        // Pesan otomatis membuat pembeli dapat langsung mengonfirmasi produk di WhatsApp.
        $message = collect([
            'Halo VarStore, saya ingin konfirmasi pesanan ('.$product->name.').',
            'Kategori: '.$product->category,
            'Jumlah: '.$quantity,
            'Harga: Rp '.number_format($product->price * $quantity, 0, ',', '.'),
            'Mohon informasi langkah selanjutnya.',
        ])->implode("\n");

        return redirect()->away('https://wa.me/6281297577567?text='.urlencode($message));
    }
}
