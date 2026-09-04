<?php

namespace App\Http\Controllers;

use App\Models\Order;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;

class AdminOrderController extends Controller
{
    public function index(): View
    {
        return view('admin.orders.index', ['orders' => Order::with('items')->latest()->paginate(15)]);
    }

    public function update(Request $request, Order $order): RedirectResponse
    {
        $order->update($request->validate(['status' => ['required', 'in:pending,processed,completed,cancelled']]));

        return back()->with('success', 'Status pesanan diperbarui.');
    }
}
