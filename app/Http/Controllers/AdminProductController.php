<?php

namespace App\Http\Controllers;

use App\Models\Product;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;

class AdminProductController extends Controller
{
    public function index(): View
    {
        return view('admin.products.index', ['products' => Product::latest()->paginate(12)]);
    }

    public function create(): View
    {
        return view('admin.products.form');
    }

    public function store(Request $request): RedirectResponse
    {
        Product::create($this->validated($request));

        return redirect()->route('admin.products.index')->with('success', 'Produk berhasil ditambahkan.');
    }

    public function edit(Product $product): View
    {
        return view('admin.products.form', compact('product'));
    }

    public function update(Request $request, Product $product): RedirectResponse
    {
        $product->update($this->validated($request, $product));

        return redirect()->route('admin.products.index')->with('success', 'Produk berhasil diperbarui.');
    }

    public function destroy(Product $product): RedirectResponse
    {
        $product->delete();

        return back()->with('success', 'Produk berhasil dihapus.');
    }

    private function validated(Request $request, ?Product $product = null): array
    {
        return $request->validate(['name' => ['required', 'string', 'max:255'], 'category' => ['required', 'string', 'max:60'], 'description' => ['required', 'string'], 'price' => ['required', 'numeric', 'min:0'], 'stock' => ['required', 'integer', 'min:0'], 'size' => ['nullable', 'string', 'max:80'], 'color' => ['nullable', 'string', 'max:50'], 'image_url' => ['required', 'url', 'max:500'], 'is_featured' => ['boolean']]);
    }
}
