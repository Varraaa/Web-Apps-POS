<?php

namespace App\Http\Controllers;

use App\Models\Product;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;

class AdminProductController extends Controller
{
    /**
     * Display a listing of products with search and category filter.
     */
    public function index(Request $request): View
    {
        $search = $request->string('search')->trim()->toString();
        $category = $request->string('category')->trim()->toString();
        $stockFilter = $request->string('stock')->trim()->toString();

        $query = Product::query()->latest();

        if ($search) {
            $query->where(function ($q) use ($search): void {
                $q->where('name', 'like', "%{$search}%")
                    ->orWhere('color', 'like', "%{$search}%")
                    ->orWhere('description', 'like', "%{$search}%");
            });
        }

        if ($category) {
            $query->where('category', $category);
        }

        if ($stockFilter === 'low') {
            $query->where('stock', '<=', 5);
        } elseif ($stockFilter === 'out') {
            $query->where('stock', '<=', 0);
        }

        $products = $query->paginate(12)->withQueryString();
        $categories = Product::distinct()->pluck('category')->filter()->values();

        return view('admin.products.index', compact('products', 'categories', 'search', 'category', 'stockFilter'));
    }

    public function create(): View
    {
        $categories = Product::distinct()->pluck('category')->filter()->values();

        return view('admin.products.form', compact('categories'));
    }

    public function store(Request $request): RedirectResponse
    {
        $data = $this->validated($request);
        Product::create($data);

        return redirect()->route('admin.products.index')->with('success', 'Produk berhasil ditambahkan.');
    }

    public function edit(Product $product): View
    {
        $categories = Product::distinct()->pluck('category')->filter()->values();

        return view('admin.products.form', compact('product', 'categories'));
    }

    public function update(Request $request, Product $product): RedirectResponse
    {
        $data = $this->validated($request, $product);
        $product->update($data);

        return redirect()->route('admin.products.index')->with('success', 'Produk berhasil diperbarui.');
    }

    public function destroy(Product $product): RedirectResponse
    {
        $product->delete();

        return back()->with('success', 'Produk berhasil dihapus.');
    }

    private function validated(Request $request, ?Product $product = null): array
    {
        $validated = $request->validate([
            'name' => ['required', 'string', 'max:255'],
            'category' => ['required', 'string', 'max:60'],
            'description' => ['required', 'string'],
            'price' => ['required', 'numeric', 'min:0'],
            'discount_percent' => ['nullable', 'integer', 'min:0', 'max:99'],
            'stock' => ['required', 'integer', 'min:0'],
            'size' => ['nullable', 'string', 'max:80'],
            'color' => ['nullable', 'string', 'max:50'],
            'image_url' => ['required', 'url', 'max:500'],
            'is_featured' => ['boolean'],
        ]);

        $validated['discount_percent'] = (int) ($validated['discount_percent'] ?? 0);
        $validated['is_featured'] = $request->boolean('is_featured');

        return $validated;
    }
}
