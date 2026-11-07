<?php

namespace App\Http\Controllers;

use App\Models\Product;
use App\Models\StockAdjustment;
use Illuminate\Http\JsonResponse;
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

    /**
     * Display printable purchase order replenishment sheet for low stock items.
     */
    public function reorderSheet(Request $request): View
    {
        $threshold = (int) $request->input('threshold', 5);
        $category = $request->string('category')->trim()->toString();
        $targetBuffer = (int) $request->input('target_buffer', 20);

        $query = Product::where('stock', '<=', $threshold);

        if ($category) {
            $query->where('category', $category);
        }

        $lowStockProducts = $query->orderBy('stock')->orderBy('name')->get();

        $items = $lowStockProducts->map(function (Product $product) use ($targetBuffer) {
            $recommendedQty = max(1, $targetBuffer - $product->stock);
            $unitCost = (float) ($product->wholesale_price ?: ($product->price * 0.70));
            $estimatedCost = $recommendedQty * $unitCost;

            return [
                'product' => $product,
                'current_stock' => $product->stock,
                'recommended_qty' => $recommendedQty,
                'unit_cost' => $unitCost,
                'estimated_cost' => $estimatedCost,
                'status_level' => $product->stock <= 0 ? 'Habis (Out of Stock)' : ($product->stock <= 2 ? 'Kritis (Critical)' : 'Menipis (Low)'),
            ];
        });

        $totalEstimatedCost = $items->sum('estimated_cost');
        $totalRecommendedUnits = $items->sum('recommended_qty');
        $categories = Product::distinct()->pluck('category')->filter()->values();
        $poNumber = 'PO-'.now()->format('Ymd').'-'.strtoupper(substr(md5(now()->toDateString()), 0, 4));

        return view('admin.products.reorder-sheet', compact(
            'items',
            'threshold',
            'targetBuffer',
            'category',
            'categories',
            'totalEstimatedCost',
            'totalRecommendedUnits',
            'poNumber'
        ));
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

    /**
     * Show printable clothing hangtag barcode label.
     */
    public function barcodeLabel(Product $product): View
    {
        return view('admin.products.barcode-label', compact('product'));
    }

    /**
     * Adjust physical product inventory stock (Opname Stok).
     */
    public function adjustStock(Request $request, Product $product): JsonResponse|RedirectResponse
    {
        $validated = $request->validate([
            'type' => ['required', 'in:in,out,damage,loss,opname_correction'],
            'quantity' => ['required', 'integer', 'min:0'],
            'reason' => ['required', 'string', 'max:255'],
        ]);

        $type = $validated['type'];
        $qty = (int) $validated['quantity'];
        $stockBefore = (int) $product->stock;

        $stockAfter = match ($type) {
            'in' => $stockBefore + $qty,
            'out', 'damage', 'loss' => max(0, $stockBefore - $qty),
            'opname_correction' => $qty,
        };

        $adjustment = StockAdjustment::create([
            'product_id' => $product->id,
            'type' => $type,
            'quantity' => $qty,
            'stock_before' => $stockBefore,
            'stock_after' => $stockAfter,
            'reason' => $validated['reason'],
            'adjusted_by' => $request->user()?->name ?? 'Kasir / Store Staff',
        ]);

        $product->update(['stock' => $stockAfter]);

        $message = "Stok untuk {$product->name} berhasil disesuaikan ({$adjustment->type_label}): dari {$stockBefore} pcs menjadi {$stockAfter} pcs.";

        if ($request->wantsJson()) {
            return response()->json([
                'success' => true,
                'message' => $message,
                'stock_before' => $stockBefore,
                'stock_after' => $stockAfter,
                'product_id' => $product->id,
            ]);
        }

        return back()->with('success', $message);
    }

    /**
     * Retrieve stock audit history for a product.
     */
    public function stockHistory(Product $product): JsonResponse
    {
        $history = $product->stockAdjustments()->take(20)->get();

        return response()->json([
            'product' => [
                'id' => $product->id,
                'name' => $product->name,
                'sku' => $product->sku,
                'current_stock' => $product->stock,
            ],
            'history' => $history->map(fn ($h) => [
                'id' => $h->id,
                'type' => $h->type,
                'type_label' => $h->type_label,
                'quantity' => $h->quantity,
                'stock_before' => $h->stock_before,
                'stock_after' => $h->stock_after,
                'reason' => $h->reason,
                'adjusted_by' => $h->adjusted_by,
                'created_at' => $h->created_at->format('d M Y, H:i'),
                'time_ago' => $h->created_at->diffForHumans(),
            ]),
        ]);
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
            'wholesale_min_qty' => ['nullable', 'integer', 'min:2'],
            'wholesale_price' => ['nullable', 'numeric', 'min:0'],
            'size' => ['nullable', 'string', 'max:80'],
            'color' => ['nullable', 'string', 'max:50'],
            'image_url' => ['required', 'url', 'max:500'],
            'is_featured' => ['boolean'],
        ]);

        $validated['discount_percent'] = (int) ($validated['discount_percent'] ?? 0);
        $validated['wholesale_min_qty'] = ! empty($validated['wholesale_min_qty']) ? (int) $validated['wholesale_min_qty'] : null;
        $validated['wholesale_price'] = ! empty($validated['wholesale_price']) ? (float) $validated['wholesale_price'] : null;
        $validated['is_featured'] = $request->boolean('is_featured');

        return $validated;
    }
}
