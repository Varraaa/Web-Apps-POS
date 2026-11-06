@extends('layouts.admin')

@section('title', 'Retur & Penukaran Barang')

@section('content')
<div class="space-y-6">
    <!-- Header -->
    <div class="flex flex-col sm:flex-row justify-between items-start sm:items-center gap-4 bg-white p-6 rounded-2xl border border-slate-200 shadow-xs">
        <div>
            <h1 class="text-xl font-black text-slate-900 tracking-tight">↩ Retur & Penukaran Barang</h1>
            <p class="text-xs text-slate-500 mt-1">Audit kondisi barang retur, pengembalian dana kasir, dan penyesuaian otomatis stok etalase.</p>
        </div>
        <div class="flex items-center gap-2">
            <a href="{{ route('admin.orders.index') }}" class="pos-btn-pay text-xs py-2 px-3.5 inline-flex items-center gap-1.5">
                <span>➕</span> Proses Retur dari Pesanan
            </a>
        </div>
    </div>

    <!-- Metric Cards -->
    <div class="grid grid-cols-2 md:grid-cols-4 gap-4">
        <div class="bg-white p-4 rounded-xl border border-slate-200">
            <span class="text-[11px] font-bold text-slate-500 uppercase tracking-wider block">Total Retur</span>
            <div class="text-2xl font-black text-slate-900 mt-1">{{ number_format($counts['all']) }}</div>
            <small class="text-[10px] text-slate-400">Semua riwayat retur</small>
        </div>
        <div class="bg-white p-4 rounded-xl border border-slate-200">
            <span class="text-[11px] font-bold text-emerald-600 uppercase tracking-wider block">Disetujui</span>
            <div class="text-2xl font-black text-emerald-600 mt-1">{{ number_format($counts['approved']) }}</div>
            <small class="text-[10px] text-slate-400">Barang diterima & dana kembali</small>
        </div>
        <div class="bg-white p-4 rounded-xl border border-slate-200">
            <span class="text-[11px] font-bold text-amber-600 uppercase tracking-wider block">Tertunda</span>
            <div class="text-2xl font-black text-amber-600 mt-1">{{ number_format($counts['pending']) }}</div>
            <small class="text-[10px] text-slate-400">Menunggu inspeksi fisik</small>
        </div>
        <div class="bg-white p-4 rounded-xl border border-slate-200">
            <span class="text-[11px] font-bold text-rose-600 uppercase tracking-wider block">Total Refund Dana</span>
            <div class="text-xl font-black text-rose-600 mt-1">Rp {{ number_format($counts['total_refunded'], 0, ',', '.') }}</div>
            <small class="text-[10px] text-slate-400">Total nilai retur toko</small>
        </div>
    </div>

    <!-- Filter & Search Toolbar -->
    <div class="bg-white p-4 rounded-xl border border-slate-200 flex flex-col md:flex-row gap-4 items-center justify-between">
        <div class="flex items-center gap-1.5 overflow-x-auto w-full md:w-auto pb-1 md:pb-0">
            <a href="{{ route('admin.returns.index') }}" class="text-xs px-3 py-1.5 rounded-lg font-bold transition-all {{ empty($status) ? 'bg-slate-900 text-white' : 'bg-slate-100 text-slate-700 hover:bg-slate-200' }}">
                Semua ({{ $counts['all'] }})
            </a>
            <a href="{{ route('admin.returns.index', ['status' => 'approved']) }}" class="text-xs px-3 py-1.5 rounded-lg font-bold transition-all {{ $status === 'approved' ? 'bg-emerald-600 text-white' : 'bg-slate-100 text-slate-700 hover:bg-slate-200' }}">
                Disetujui ({{ $counts['approved'] }})
            </a>
            <a href="{{ route('admin.returns.index', ['status' => 'pending']) }}" class="text-xs px-3 py-1.5 rounded-lg font-bold transition-all {{ $status === 'pending' ? 'bg-amber-600 text-white' : 'bg-slate-100 text-slate-700 hover:bg-slate-200' }}">
                Tertunda ({{ $counts['pending'] }})
            </a>
        </div>

        <form method="GET" action="{{ route('admin.returns.index') }}" class="w-full md:w-72">
            @if($status)
                <input type="hidden" name="status" value="{{ $status }}">
            @endif
            <div class="relative">
                <input type="text" name="search" value="{{ $search }}" placeholder="Cari No. Retur / Invoice / Produk..." class="w-full text-xs pl-8 pr-3 py-2 rounded-lg border border-slate-200 focus:outline-none focus:ring-2 focus:ring-slate-900">
                <span class="absolute left-2.5 top-2.5 text-slate-400 text-xs">🔍</span>
            </div>
        </form>
    </div>

    <!-- Table -->
    <div class="bg-white rounded-2xl border border-slate-200 overflow-hidden shadow-xs">
        <div class="overflow-x-auto">
            <table class="w-full text-left text-xs">
                <thead class="bg-slate-50 border-b border-slate-200 text-slate-600 uppercase font-black text-[10px] tracking-wider">
                    <tr>
                        <th class="py-3 px-4">No. Retur & Waktu</th>
                        <th class="py-3 px-4">Invoice Terkait</th>
                        <th class="py-3 px-4">Produk & Qty</th>
                        <th class="py-3 px-4">Kondisi & Alasan</th>
                        <th class="py-3 px-4">Refund / Metode</th>
                        <th class="py-3 px-4">Restock</th>
                        <th class="py-3 px-4 text-right">Aksi</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-slate-100">
                    @forelse($returns as $ret)
                        <tr class="hover:bg-slate-50/80 transition-colors">
                            <td class="py-3.5 px-4">
                                <a href="{{ route('admin.returns.show', $ret) }}" class="font-bold text-slate-900 font-mono hover:underline">
                                    {{ $ret->return_number }}
                                </a>
                                <span class="block text-[10px] text-slate-400 mt-0.5">{{ $ret->created_at->format('d/m/Y H:i') }}</span>
                            </td>
                            <td class="py-3.5 px-4">
                                <span class="font-mono font-semibold text-slate-800">#{{ $ret->order->invoice_number }}</span>
                                <small class="block text-[10px] text-slate-500">{{ $ret->order->customer_name }}</small>
                            </td>
                            <td class="py-3.5 px-4">
                                <strong class="text-slate-900 block">{{ $ret->product->name }}</strong>
                                <span class="text-[10px] text-slate-500">{{ $ret->quantity }} item ditukar/retur</span>
                            </td>
                            <td class="py-3.5 px-4">
                                <span class="inline-block px-2 py-0.5 rounded text-[10px] font-bold bg-slate-100 text-slate-700 mb-1">
                                    {{ $ret->condition_label }}
                                </span>
                                <small class="block text-[10px] text-slate-500 italic">"{{ $ret->reason_label }}"</small>
                            </td>
                            <td class="py-3.5 px-4">
                                <strong class="text-rose-600 block font-bold">{{ $ret->formatted_refund_amount }}</strong>
                                <span class="text-[10px] text-slate-600 font-semibold">{{ $ret->refund_type_label }}</span>
                            </td>
                            <td class="py-3.5 px-4">
                                @if($ret->restock_item)
                                    <span class="inline-flex items-center gap-1 px-2 py-0.5 rounded text-[10px] font-bold bg-emerald-100 text-emerald-800">
                                        ✓ Masuk Stok
                                    </span>
                                @else
                                    <span class="inline-flex items-center gap-1 px-2 py-0.5 rounded text-[10px] font-bold bg-rose-100 text-rose-800">
                                        ✗ Afkir / Rusak
                                    </span>
                                @endif
                            </td>
                            <td class="py-3.5 px-4 text-right">
                                <div class="inline-flex items-center gap-1.5">
                                    <a href="{{ route('admin.returns.show', $ret) }}" class="p-1.5 rounded-lg border border-slate-200 text-slate-600 hover:bg-slate-100" title="Lihat Rincian">
                                        👁
                                    </a>
                                    <a href="{{ route('admin.returns.slip', $ret) }}" target="_blank" class="p-1.5 rounded-lg border border-slate-200 text-slate-600 hover:bg-slate-100" title="Cetak Slip Retur">
                                        🖨
                                    </a>
                                </div>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="7" class="py-12 text-center text-slate-400">
                                <p class="text-sm font-semibold">Belum ada riwayat retur barang.</p>
                                <span class="text-xs">Retur dapat dibuat langsung dari detail pesanan di halaman Pesanan & Transaksi.</span>
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>

        @if($returns->hasPages())
            <div class="p-4 border-t border-slate-200">
                {{ $returns->links() }}
            </div>
        @endif
    </div>
</div>
@endsection
