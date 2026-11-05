@extends('layouts.admin')
@section('title', 'Laporan & Rekapitulasi POS — VarStore')

@section('content')
<div class="space-y-6">
    <!-- Header & Period Filter Toolbar -->
    <div class="flex flex-col md:flex-row md:items-center justify-between gap-4 pb-4 border-b border-slate-200">
        <div>
            <div class="flex items-center gap-2">
                <span class="px-2.5 py-0.5 rounded-full text-xs font-bold bg-slate-900 text-white uppercase tracking-wider">
                    Analisis Finansial
                </span>
                <span class="text-xs text-slate-500 font-medium">{{ $periodLabel }}</span>
            </div>
            <h1 class="text-2xl font-black text-slate-900 tracking-tight mt-1">Laporan Penjualan & Kasir</h1>
            <p class="text-xs text-slate-500">Rekapitulasi performa omzet, transaksi kasir POS, dan tren produk terlaris.</p>
        </div>

        <div class="flex flex-wrap items-center gap-2">
            <!-- Period Filter Pills -->
            <div class="inline-flex bg-slate-100 p-1 rounded-xl text-xs font-semibold">
                <a href="{{ route('admin.reports.index', ['period' => 'today']) }}" 
                   class="px-3 py-1.5 rounded-lg transition {{ $period === 'today' ? 'bg-white text-slate-900 shadow-sm font-bold' : 'text-slate-600 hover:text-slate-900' }}">
                    Hari Ini
                </a>
                <a href="{{ route('admin.reports.index', ['period' => 'yesterday']) }}" 
                   class="px-3 py-1.5 rounded-lg transition {{ $period === 'yesterday' ? 'bg-white text-slate-900 shadow-sm font-bold' : 'text-slate-600 hover:text-slate-900' }}">
                    Kemarin
                </a>
                <a href="{{ route('admin.reports.index', ['period' => 'this_week']) }}" 
                   class="px-3 py-1.5 rounded-lg transition {{ $period === 'this_week' ? 'bg-white text-slate-900 shadow-sm font-bold' : 'text-slate-600 hover:text-slate-900' }}">
                    Minggu Ini
                </a>
                <a href="{{ route('admin.reports.index', ['period' => 'this_month']) }}" 
                   class="px-3 py-1.5 rounded-lg transition {{ $period === 'this_month' ? 'bg-white text-slate-900 shadow-sm font-bold' : 'text-slate-600 hover:text-slate-900' }}">
                    Bulan Ini
                </a>
                <a href="{{ route('admin.reports.index', ['period' => 'last_30_days']) }}" 
                   class="px-3 py-1.5 rounded-lg transition {{ $period === 'last_30_days' ? 'bg-white text-slate-900 shadow-sm font-bold' : 'text-slate-600 hover:text-slate-900' }}">
                    30 Hari
                </a>
            </div>

            <!-- Export CSV Button -->
            <a href="{{ route('admin.reports.export-csv', ['period' => $period, 'start_date' => request('start_date'), 'end_date' => request('end_date')]) }}" 
               class="inline-flex items-center gap-2 px-4 py-2 bg-emerald-600 hover:bg-emerald-700 text-white text-xs font-bold rounded-xl transition shadow-sm">
                <span>📥 Ekspor CSV</span>
            </a>
        </div>
    </div>

    <!-- 4 KPI Metrics Cards -->
    <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-4">
        <div class="bg-white p-5 rounded-2xl border border-slate-200/80 shadow-sm">
            <span class="text-[11px] font-bold text-slate-400 uppercase tracking-wider block">Total Omzet Bersih</span>
            <div class="text-2xl font-black text-slate-900 mt-1 font-mono">
                Rp {{ number_format($totalRevenue, 0, ',', '.') }}
            </div>
            <span class="text-[11px] text-emerald-600 font-semibold mt-1 inline-block">
                ✓ Berhasil dibukukan
            </span>
        </div>

        <div class="bg-white p-5 rounded-2xl border border-slate-200/80 shadow-sm">
            <span class="text-[11px] font-bold text-slate-400 uppercase tracking-wider block">Total Transaksi</span>
            <div class="text-2xl font-black text-slate-900 mt-1 font-mono">
                {{ number_format($totalOrders) }} <span class="text-sm font-normal text-slate-500">Bon</span>
            </div>
            <span class="text-[11px] text-slate-500 mt-1 inline-block">
                Periode terpilih
            </span>
        </div>

        <div class="bg-white p-5 rounded-2xl border border-slate-200/80 shadow-sm">
            <span class="text-[11px] font-bold text-slate-400 uppercase tracking-wider block">Rata-Rata Bon (AOV)</span>
            <div class="text-2xl font-black text-slate-900 mt-1 font-mono">
                Rp {{ number_format($averageOrderValue, 0, ',', '.') }}
            </div>
            <span class="text-[11px] text-slate-500 mt-1 inline-block">
                Per transaksi kasir
            </span>
        </div>

        <div class="bg-white p-5 rounded-2xl border border-slate-200/80 shadow-sm">
            <span class="text-[11px] font-bold text-slate-400 uppercase tracking-wider block">Total Diskon Diberikan</span>
            <div class="text-2xl font-black text-rose-600 mt-1 font-mono">
                Rp {{ number_format($totalDiscountsGiven, 0, ',', '.') }}
            </div>
            <span class="text-[11px] text-rose-500 mt-1 inline-block">
                Kupon promo + poin loyalty
            </span>
        </div>
    </div>

    <!-- 2-Column Insight Row -->
    <div class="grid grid-cols-1 lg:grid-cols-3 gap-6">
        <!-- Top 8 Selling Products (2 cols) -->
        <div class="lg:col-span-2 bg-white rounded-2xl border border-slate-200/80 shadow-sm overflow-hidden">
            <div class="p-5 border-b border-slate-100 flex items-center justify-between">
                <div>
                    <h3 class="font-extrabold text-slate-900 text-sm">Produk Paling Laris (Top Sellers)</h3>
                    <p class="text-xs text-slate-500">Daftar artikel pakaian dengan kuantitas penjualan tertinggi.</p>
                </div>
                <span class="text-xs font-semibold text-slate-400 font-mono">{{ $topProducts->count() }} Item</span>
            </div>

            <div class="overflow-x-auto">
                <table class="w-full text-left text-xs">
                    <thead class="bg-slate-50 text-slate-500 font-bold uppercase tracking-wider border-b border-slate-100 text-[10px]">
                        <tr>
                            <th class="py-3 px-4">#</th>
                            <th class="py-3 px-4">Nama Produk</th>
                            <th class="py-3 px-4 text-center">Qty Terjual</th>
                            <th class="py-3 px-4 text-right">Total Penjualan</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-slate-100 font-medium">
                        @forelse($topProducts as $idx => $prod)
                            <tr class="hover:bg-slate-50/70 transition">
                                <td class="py-3 px-4 text-slate-400 font-mono">{{ $idx + 1 }}</td>
                                <td class="py-3 px-4 font-bold text-slate-800">
                                    {{ $prod->product_name }}
                                </td>
                                <td class="py-3 px-4 text-center">
                                    <span class="inline-flex px-2 py-0.5 rounded-full font-bold bg-slate-100 text-slate-800 text-[11px]">
                                        {{ $prod->total_qty }} pcs
                                    </span>
                                </td>
                                <td class="py-3 px-4 text-right font-mono font-bold text-slate-900">
                                    Rp {{ number_format($prod->total_sales, 0, ',', '.') }}
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="4" class="py-8 text-center text-slate-400">
                                    Belum ada penjualan produk pada periode ini.
                                </td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>

        <!-- Payment Breakdown & Sources (1 col) -->
        <div class="space-y-6">
            <!-- Payment Methods Breakdown -->
            <div class="bg-white p-5 rounded-2xl border border-slate-200/80 shadow-sm">
                <h3 class="font-extrabold text-slate-900 text-sm mb-1">Metode Pembayaran</h3>
                <p class="text-xs text-slate-500 mb-4">Sebaran uang masuk berdasarkan metode bayar kasir.</p>

                <div class="space-y-3">
                    @forelse($paymentMethods as $pm)
                        @php
                            $pct = $totalRevenue > 0 ? ($pm->revenue / $totalRevenue) * 100 : 0;
                        @endphp
                        <div>
                            <div class="flex justify-between text-xs mb-1">
                                <span class="font-bold text-slate-800 uppercase">{{ $pm->payment_method }} ({{ $pm->count }}x)</span>
                                <span class="font-mono text-slate-900 font-semibold">Rp {{ number_format($pm->revenue, 0, ',', '.') }} ({{ round($pct) }}%)</span>
                            </div>
                            <div class="w-full bg-slate-100 h-2 rounded-full overflow-hidden">
                                <div class="bg-slate-900 h-full rounded-full" style="width: {{ $pct }}%"></div>
                            </div>
                        </div>
                    @empty
                        <p class="text-xs text-slate-400 text-center py-4">Belum ada data pembayaran.</p>
                    @endforelse
                </div>
            </div>

            <!-- Channel Breakdown -->
            <div class="bg-white p-5 rounded-2xl border border-slate-200/80 shadow-sm">
                <h3 class="font-extrabold text-slate-900 text-sm mb-1">Kanal Penjualan</h3>
                <p class="text-xs text-slate-500 mb-4">Perbandingan transaksi Offline POS vs Online Storefront.</p>

                <div class="space-y-3">
                    @forelse($sources as $src)
                        @php
                            $pct = $totalRevenue > 0 ? ($src->revenue / $totalRevenue) * 100 : 0;
                        @endphp
                        <div>
                            <div class="flex justify-between text-xs mb-1">
                                <span class="font-bold text-slate-800 uppercase">{{ $src->source ?? 'POS' }}</span>
                                <span class="font-mono text-slate-900 font-semibold">{{ $src->count }} Bon · {{ round($pct) }}%</span>
                            </div>
                            <div class="w-full bg-slate-100 h-2 rounded-full overflow-hidden">
                                <div class="bg-emerald-600 h-full rounded-full" style="width: {{ $pct }}%"></div>
                            </div>
                        </div>
                    @empty
                        <p class="text-xs text-slate-400 text-center py-4">Belum ada data kanal.</p>
                    @endforelse
                </div>
            </div>
        </div>
    </div>

    <!-- Transaction History Table -->
    <div class="bg-white rounded-2xl border border-slate-200/80 shadow-sm overflow-hidden">
        <div class="p-5 border-b border-slate-100 flex items-center justify-between">
            <div>
                <h3 class="font-extrabold text-slate-900 text-sm">Rincian Transaksi Selesai</h3>
                <p class="text-xs text-slate-500">Daftar bon transaksi yang tercatat dalam periode {{ $periodLabel }}.</p>
            </div>
            <span class="text-xs font-semibold text-slate-400 font-mono">{{ $recentOrders->total() }} Transaksi</span>
        </div>

        <div class="overflow-x-auto">
            <table class="w-full text-left text-xs">
                <thead class="bg-slate-50 text-slate-500 font-bold uppercase tracking-wider border-b border-slate-100 text-[10px]">
                    <tr>
                        <th class="py-3 px-4">No. Invoice</th>
                        <th class="py-3 px-4">Tanggal & Waktu</th>
                        <th class="py-3 px-4">Pelanggan</th>
                        <th class="py-3 px-4">Kasir / Sumber</th>
                        <th class="py-3 px-4">Metode Bayar</th>
                        <th class="py-3 px-4 text-right">Diskon</th>
                        <th class="py-3 px-4 text-right">Total Akhir</th>
                        <th class="py-3 px-4 text-center">Aksi</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-slate-100 font-medium">
                    @forelse($recentOrders as $order)
                        <tr class="hover:bg-slate-50/70 transition">
                            <td class="py-3 px-4 font-mono font-bold text-slate-900">
                                #{{ $order->invoice_number }}
                            </td>
                            <td class="py-3 px-4 text-slate-500">
                                <div>{{ $order->created_at->format('d M Y') }}</div>
                                <div class="text-[10px] text-slate-400 font-mono">{{ $order->created_at->format('H:i') }} WIB</div>
                            </td>
                            <td class="py-3 px-4">
                                <div class="font-bold text-slate-800">
                                    {{ $order->customer ? $order->customer->name : ($order->customer_name ?: 'Pelanggan Walk-In') }}
                                </div>
                                @if($order->customer)
                                    <span class="text-[10px] text-amber-700 font-semibold uppercase">★ Member {{ $order->customer->membership_tier }}</span>
                                @endif
                            </td>
                            <td class="py-3 px-4">
                                <div class="text-slate-800">{{ $order->cashier_name ?: 'Online' }}</div>
                                <span class="text-[10px] font-bold uppercase px-1.5 py-0.5 rounded {{ $order->source === 'pos' ? 'bg-emerald-100 text-emerald-800' : 'bg-blue-100 text-blue-800' }}">
                                    {{ $order->source ?? 'POS' }}
                                </span>
                            </td>
                            <td class="py-3 px-4 uppercase text-slate-700 font-bold">
                                {{ $order->payment_method }}
                            </td>
                            <td class="py-3 px-4 text-right font-mono text-rose-600">
                                @if($order->discount_amount > 0 || ($order->points_discount_amount ?? 0) > 0)
                                    -Rp {{ number_format($order->discount_amount + ($order->points_discount_amount ?? 0), 0, ',', '.') }}
                                @else
                                    -
                                @endif
                            </td>
                            <td class="py-3 px-4 text-right font-mono font-bold text-slate-900">
                                Rp {{ number_format($order->total, 0, ',', '.') }}
                            </td>
                            <td class="py-3 px-4 text-center">
                                <a href="{{ route('admin.pos.receipt', $order) }}" target="_blank" 
                                   class="inline-block px-2.5 py-1 bg-slate-100 hover:bg-slate-200 text-slate-700 rounded text-[11px] font-semibold transition" title="Lihat Struk">
                                    Struk ↗
                                </a>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="8" class="py-12 text-center text-slate-400">
                                Tidak ada transaksi yang tercatat pada rentang periode ini.
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>

        @if($recentOrders->hasPages())
            <div class="p-4 border-t border-slate-100">
                {{ $recentOrders->links() }}
            </div>
        @endif
    </div>
</div>
@endsection
