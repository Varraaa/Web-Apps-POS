@extends('layouts.admin')
@section('title', 'Overview & POS Analytics — VarStore')

@section('content')
<div class="space-y-8">
    <!-- Header with Quick Action Bar -->
    <div class="flex flex-col md:flex-row md:items-center justify-between gap-4 bg-white p-6 rounded-2xl border border-slate-200 shadow-sm">
        <div>
            <span class="inline-flex items-center gap-1.5 text-[11px] font-extrabold uppercase tracking-wider text-emerald-700 bg-emerald-50 px-2.5 py-1 rounded-full border border-emerald-200 mb-2">
                <span class="w-2 h-2 rounded-full bg-emerald-500 animate-pulse"></span>
                Sistem POS Aktif · {{ now()->format('l, d F Y') }}
            </span>
            <h1 class="text-2xl sm:text-3xl font-extrabold text-slate-900 tracking-tight">
                Halo, {{ auth()->user()->name ?? 'Administrator' }}!
            </h1>
            <p class="text-xs text-slate-500 mt-1">
                Berikut adalah ringkasan performa penjualan kasir POS dan operasional butik hari ini.
            </p>
        </div>

        <div class="flex flex-wrap items-center gap-3">
            <a href="{{ route('admin.pos.index') }}" class="pos-btn-pay text-xs px-5 py-3 shadow-md hover:shadow-lg">
                <span class="text-base">🖥</span>
                <span>Buka Terminal Kasir (F4)</span>
            </a>
            <a href="{{ route('admin.products.create') }}" class="pos-btn-ghost text-xs py-3 px-4">
                <span>+ Produk Baru</span>
            </a>
            <a href="{{ route('admin.discounts.create') }}" class="pos-btn-ghost text-xs py-3 px-4">
                <span>🏷 Buat Voucher</span>
            </a>
        </div>
    </div>

    <!-- 4 Key Stat Metric Cards -->
    <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-5">
        <!-- Card 1: Today's Sales -->
        <div class="bg-white p-5 rounded-2xl border border-slate-200 shadow-sm relative overflow-hidden flex flex-col justify-between">
            <div class="flex items-center justify-between">
                <span class="text-xs font-bold text-slate-500 uppercase tracking-wider">Penjualan Hari Ini</span>
                <span class="w-9 h-9 rounded-xl bg-emerald-50 text-emerald-600 flex items-center justify-center font-bold text-sm">
                    💵
                </span>
            </div>
            <div class="mt-4">
                <div class="text-2xl font-black text-slate-900">
                    Rp {{ number_format($todaySales, 0, ',', '.') }}
                </div>
                <div class="flex items-center gap-2 mt-1.5 text-[11px] text-slate-500">
                    <span class="font-bold text-emerald-600">POS: Rp {{ number_format($todayPosSales, 0, ',', '.') }}</span>
                    <span>·</span>
                    <span>{{ $todayOrdersCount }} pesanan</span>
                </div>
            </div>
        </div>

        <!-- Card 2: Total Revenue -->
        <div class="bg-white p-5 rounded-2xl border border-slate-200 shadow-sm relative overflow-hidden flex flex-col justify-between">
            <div class="flex items-center justify-between">
                <span class="text-xs font-bold text-slate-500 uppercase tracking-wider">Total Omset Toko</span>
                <span class="w-9 h-9 rounded-xl bg-blue-50 text-blue-600 flex items-center justify-center font-bold text-sm">
                    📈
                </span>
            </div>
            <div class="mt-4">
                <div class="text-2xl font-black text-slate-900">
                    Rp {{ number_format($totalRevenue, 0, ',', '.') }}
                </div>
                <div class="mt-1.5 text-[11px] text-slate-500">
                    Processed & Completed orders
                </div>
            </div>
        </div>

        <!-- Card 3: Total Orders -->
        <div class="bg-white p-5 rounded-2xl border border-slate-200 shadow-sm relative overflow-hidden flex flex-col justify-between">
            <div class="flex items-center justify-between">
                <span class="text-xs font-bold text-slate-500 uppercase tracking-wider">Total Transaksi</span>
                <span class="w-9 h-9 rounded-xl bg-purple-50 text-purple-600 flex items-center justify-center font-bold text-sm">
                    📋
                </span>
            </div>
            <div class="mt-4">
                <div class="text-2xl font-black text-slate-900">
                    {{ $ordersCount }} Transaksi
                </div>
                <div class="mt-1.5 text-[11px] text-slate-500">
                    Kasir POS & WhatsApp Concierge
                </div>
            </div>
        </div>

        <!-- Card 4: Catalog & Vouchers -->
        <div class="bg-white p-5 rounded-2xl border border-slate-200 shadow-sm relative overflow-hidden flex flex-col justify-between">
            <div class="flex items-center justify-between">
                <span class="text-xs font-bold text-slate-500 uppercase tracking-wider">Produk & Promo</span>
                <span class="w-9 h-9 rounded-xl bg-amber-50 text-amber-600 flex items-center justify-center font-bold text-sm">
                    🏷
                </span>
            </div>
            <div class="mt-4">
                <div class="text-2xl font-black text-slate-900">
                    {{ $productsCount }} Produk
                </div>
                <div class="flex items-center gap-2 mt-1.5 text-[11px] text-slate-500">
                    <span class="font-bold text-amber-600">{{ $activeVouchersCount }} Voucher Aktif</span>
                    <span>·</span>
                    <a href="{{ route('admin.discounts.index') }}" class="hover:underline text-slate-800 font-semibold">Kelola ↗</a>
                </div>
            </div>
        </div>
    </div>

    <!-- Main Content Grid -->
    <div class="grid grid-cols-1 lg:grid-cols-12 gap-6">
        <!-- Recent Transactions (8 cols) -->
        <section class="lg:col-span-8 bg-white rounded-2xl border border-slate-200 shadow-sm p-6">
            <div class="flex items-center justify-between mb-6 pb-4 border-b border-slate-100">
                <div>
                    <h2 class="text-base font-extrabold text-slate-900">Transaksi Terbaru</h2>
                    <p class="text-xs text-slate-500">Aktivitas pesanan terkini yang masuk ke sistem.</p>
                </div>
                <a href="{{ route('admin.orders.index') }}" class="text-xs font-extrabold text-slate-800 hover:underline">
                    Semua Transaksi ({{ $ordersCount }}) ↗
                </a>
            </div>

            <div class="divide-y divide-slate-100">
                @forelse($recentOrders as $order)
                    <div class="py-3.5 flex items-center justify-between gap-4 text-xs">
                        <div class="flex items-center gap-3">
                            <span class="w-8 h-8 rounded-lg bg-slate-100 text-slate-700 flex items-center justify-center font-bold text-[11px]">
                                {{ $order->isPos() ? 'POS' : 'WA' }}
                            </span>
                            <div>
                                <strong class="text-slate-900 block font-mono text-xs">#{{ $order->invoice_number }}</strong>
                                <span class="text-slate-500 text-[11px]">
                                    {{ $order->customer_name }} · {{ $order->items->sum('quantity') }} pcs
                                </span>
                            </div>
                        </div>

                        <div class="text-right">
                            <strong class="text-slate-900 block text-xs">{{ $order->formatted_total }}</strong>
                            <small class="text-slate-400 text-[10px]">{{ $order->created_at->diffForHumans() }}</small>
                        </div>

                        <div class="flex items-center gap-2">
                            <span class="text-[10px] font-extrabold px-2 py-0.5 rounded-full
                                {{ $order->status === 'completed' ? 'bg-emerald-100 text-emerald-800' : '' }}
                                {{ $order->status === 'processed' ? 'bg-blue-100 text-blue-800' : '' }}
                                {{ $order->status === 'pending' ? 'bg-amber-100 text-amber-800' : '' }}
                                {{ $order->status === 'cancelled' ? 'bg-rose-100 text-rose-800' : '' }}">
                                {{ ucfirst($order->status) }}
                            </span>
                            <a href="{{ route('admin.pos.receipt', $order) }}" target="_blank" 
                               class="pos-btn-ghost text-[10px] py-1 px-2" title="Cetak Struk">
                                Struk
                            </a>
                        </div>
                    </div>
                @empty
                    <div class="text-center py-12 text-slate-400">
                        Belum ada transaksi tercatat.
                    </div>
                @endforelse
            </div>
        </section>

        <!-- Right Side: Low Stock Watch & Payment Breakdown (4 cols) -->
        <div class="lg:col-span-4 space-y-6">
            <!-- Low Stock Watch -->
            <section class="bg-white rounded-2xl border border-slate-200 shadow-sm p-6">
                <div class="flex items-center justify-between mb-4 pb-3 border-b border-slate-100">
                    <div>
                        <h2 class="text-sm font-extrabold text-slate-900 flex items-center gap-1.5">
                            <span class="text-amber-500">⚠</span> Peringatan Stok Rendah
                        </h2>
                        <p class="text-[11px] text-slate-500">Produk yang butuh restock segera.</p>
                    </div>
                    <a href="{{ route('admin.products.index') }}" class="text-[11px] font-bold text-slate-700 hover:underline">
                        Katalog ↗
                    </a>
                </div>

                <div class="divide-y divide-slate-100 text-xs">
                    @forelse($lowStockProducts as $product)
                        <div class="py-2.5 flex items-center justify-between">
                            <div>
                                <strong class="text-slate-900 block truncate max-w-[170px]">{{ $product->name }}</strong>
                                <small class="text-slate-400 text-[10px]">{{ $product->category }}</small>
                            </div>
                            <span class="px-2 py-0.5 rounded text-[11px] font-extrabold {{ $product->stock <= 2 ? 'bg-rose-100 text-rose-700' : 'bg-amber-100 text-amber-700' }}">
                                {{ $product->stock }} left
                            </span>
                        </div>
                    @empty
                        <p class="text-xs text-slate-400 py-4 text-center">Semua level stok produk aman!</p>
                    @endforelse
                </div>
            </section>

            <!-- Top Selling Leaderboard -->
            <section class="bg-white rounded-2xl border border-slate-200 shadow-sm p-6">
                <h2 class="text-sm font-extrabold text-slate-900 mb-1">Produk Terlaris</h2>
                <p class="text-[11px] text-slate-500 mb-4">Item dengan penjualan unit tertinggi.</p>

                <div class="divide-y divide-slate-100 text-xs">
                    @forelse($topProducts as $idx => $item)
                        <div class="py-2.5 flex items-center justify-between">
                            <div class="flex items-center gap-2">
                                <span class="w-5 h-5 rounded-full bg-slate-100 text-slate-700 font-bold text-[10px] flex items-center justify-center">
                                    {{ $idx + 1 }}
                                </span>
                                <span class="font-semibold text-slate-800 truncate max-w-[160px]">{{ $item->product_name }}</span>
                            </div>
                            <strong class="text-slate-900">{{ $item->total_qty }} terjual</strong>
                        </div>
                    @empty
                        <p class="text-xs text-slate-400 py-4 text-center">Belum ada data barang terjual.</p>
                    @endforelse
                </div>
            </section>
        </div>
    </div>
</div>
@endsection
