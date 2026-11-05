<?php

namespace App\Http\Controllers;

use App\Models\Product;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;

class WishlistController extends Controller
{
    /**
     * Display customer wishlist.
     */
    public function index(Request $request): View
    {
        $wishlistIds = $request->session()->get('wishlist', []);
        $products = Product::whereIn('id', $wishlistIds)->get();

        return view('store.wishlist', compact('products'));
    }

    /**
     * Toggle a product in/out of customer wishlist.
     */
    public function toggle(Request $request, Product $product): JsonResponse|RedirectResponse
    {
        $wishlist = $request->session()->get('wishlist', []);

        if (in_array($product->id, $wishlist, true)) {
            $wishlist = array_values(array_diff($wishlist, [$product->id]));
            $liked = false;
            $message = "Produk \"{$product->name}\" dihapus dari wishlist.";
        } else {
            $wishlist[] = $product->id;
            $liked = true;
            $message = "Produk \"{$product->name}\" disimpan ke wishlist!";
        }

        $request->session()->put('wishlist', $wishlist);

        if ($request->wantsJson()) {
            return response()->json([
                'success' => true,
                'liked' => $liked,
                'count' => count($wishlist),
                'message' => $message,
            ]);
        }

        return back()->with('success', $message);
    }

    /**
     * Move an item from wishlist to active cart.
     */
    public function moveToCart(Request $request, Product $product): RedirectResponse
    {
        $wishlist = $request->session()->get('wishlist', []);
        $wishlist = array_values(array_diff($wishlist, [$product->id]));
        $request->session()->put('wishlist', $wishlist);

        $cart = $request->session()->get('cart', []);
        $cart[$product->id] = [
            'product_id' => $product->id,
            'quantity' => min(($cart[$product->id]['quantity'] ?? 0) + 1, $product->stock),
        ];
        $request->session()->put('cart', $cart);

        return redirect()->route('cart.index')->with('success', "Produk \"{$product->name}\" dipindahkan ke tas belanja!");
    }
}
