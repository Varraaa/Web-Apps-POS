<?php

namespace App\Http\Controllers;

use App\Models\Discount;
use App\Models\Order;
use App\Models\OrderItem;
use App\Models\Product;
use Illuminate\Support\Facades\DB;
use Illuminate\View\View;

class AdminDashboardController extends Controller
{
    /**
     * Render the elevated POS Admin Dashboard with real-time store analytics.
     */
    public function index(): View
    {
        $today = now()->today();

        $todaySales = (float) Order::whereDate('created_at', $today)
            ->whereIn('status', ['processed', 'completed'])
            ->sum('total');

        $todayOrdersCount = Order::whereDate('created_at', $today)->count();

        $todayPosSales = (float) Order::where('source', 'pos')
            ->whereDate('created_at', $today)
            ->whereIn('status', ['processed', 'completed'])
            ->sum('total');

        $totalRevenue = (float) Order::whereIn('status', ['processed', 'completed'])->sum('total');
        $ordersCount = Order::count();
        $productsCount = Product::count();

        $activeVouchersCount = Discount::where('is_active', true)
            ->where(fn ($q) => $q->whereNull('expires_at')->orWhere('expires_at', '>', now()))
            ->count();

        // Payment Method breakdown
        $paymentStats = Order::select('payment_method', DB::raw('count(*) as count'), DB::raw('sum(total) as revenue'))
            ->groupBy('payment_method')
            ->get()
            ->keyBy('payment_method');

        // Top Selling Products
        $topProducts = OrderItem::select('product_name', DB::raw('sum(quantity) as total_qty'), DB::raw('sum(price * quantity) as total_sales'))
            ->groupBy('product_name')
            ->orderByDesc('total_qty')
            ->take(5)
            ->get();

        // Low stock alerts
        $lowStockProducts = Product::where('stock', '<=', 5)
            ->orderBy('stock')
            ->take(6)
            ->get();

        // Recent transactions
        $recentOrders = Order::with('items')->latest()->take(7)->get();

        return view('admin.dashboard', compact(
            'todaySales',
            'todayOrdersCount',
            'todayPosSales',
            'totalRevenue',
            'ordersCount',
            'productsCount',
            'activeVouchersCount',
            'paymentStats',
            'topProducts',
            'lowStockProducts',
            'recentOrders'
        ));
    }
}
