<?php

namespace App\Http\Controllers;

use App\Models\Order;
use App\Models\Product;
use App\Models\User;
use Illuminate\View\View;

class AdminDashboardController extends Controller
{
    public function index(): View
    {
        return view('admin.dashboard', [
            'productsCount' => Product::count(),
            'ordersCount' => Order::count(),
            'revenue' => Order::whereIn('status', ['processed', 'completed'])->sum('total'),
            'customersCount' => User::where('role', 'buyer')->count(),
            'successfulCustomersCount' => Order::whereIn('status', ['processed', 'completed'])->distinct('customer_phone')->count('customer_phone'),
            'recentOrders' => Order::latest()->take(6)->get(),
            'lowStockProducts' => Product::where('stock', '<=', 5)->orderBy('stock')->take(5)->get(),
        ]);
    }
}
