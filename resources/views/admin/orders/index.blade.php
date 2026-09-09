@extends('layouts.admin')
@section('title', 'Orders — VarStore')
@section('content')<div class="admin-wrap">
        <div class="admin-head">
            <div>
                <p class="eyebrow">Admin studio</p>
                <h1>Orders</h1>
                <p>Track incoming customer requests.</p>
            </div>
        </div>
        @if (session('success'))
            <div class="notice">{{ session('success') }}</div>
        @endif
        <div class="admin-tabs">
            <a href="{{ route('admin.products.index') }}">Products</a><a class="tab-active"
                href="{{ route('admin.orders.index') }}">Orders</a>
        </div>
        <div class="admin-table">
            <div class="admin-table-head order-head">
                <span>Order</span><span>Customer</span><span>Items</span><span>Total</span><span>Status</span></div>
            @forelse($orders as $order)
                <div class="admin-row order-row">
                    <span><b>#{{ $order->id }}</b><small>{{ $order->created_at->format('d M Y, H:i') }}</small></span><span><b>{{ $order->customer_name }}</b><small>{{ $order->customer_phone }}</small></span><span>{{ $order->items->sum('quantity') }}
                        item(s)</span><span>
                        <b>Rp {{ number_format($order->total, 0, ',', '.') }}</b>
                        @if($order->discount_amount > 0)
                            <small style="color: #16a34a; font-weight: 700; display: block;">
                                Hemat: -Rp {{ number_format($order->discount_amount, 0, ',', '.') }}
                                @if($order->coupon_code) ({{ $order->coupon_code }}) @endif
                            </small>
                        @endif
                    </span>
                    <form method="POST" action="{{ route('admin.orders.update', $order) }}">@csrf @method('PATCH')<select
                            name="status" onchange="this.form.submit()">
                            <option value="pending" @selected($order->status === 'pending')>Pending</option>
                            <option value="processed" @selected($order->status === 'processed')>Processed</option>
                            <option value="completed" @selected($order->status === 'completed')>Completed</option>
                            <option value="cancelled" @selected($order->status === 'cancelled')>Cancelled</option>
                        </select></form>
            </div>@empty<div class="empty-state">No orders yet.</div>
            @endforelse
        </div>{{ $orders->links() }}
</div>@endsection
