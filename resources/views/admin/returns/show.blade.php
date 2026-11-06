@extends('layouts.admin')

@section('title', 'Detail Retur #' . $orderReturn->return_number)

@section('content')
<div class="max-w-4xl mx-auto space-y-6">
    <!-- Top Bar -->
    <div class="flex items-center justify-between">
        <a href="{{ route('admin.returns.index') }}" class="text-xs font-bold text-slate-500 hover:text-slate-900 inline-flex items-center gap-1.5">
            ← Kembali ke Daftar Retur
        </a>
        <div class="flex items-center gap-2">
            <a href="{{ route('admin.returns.slip', $orderReturn) }}" target="_blank" class="pos-btn-pay text-xs py-2 px-3.5 inline-flex items-center gap-1.5">
                <span>🖨</span> Cetak Slip Retur Resmi
            </a>
        </div>
    </div>

    <!-- Return Audit Card -->
    <div class="bg-white rounded-2xl border border-slate-200 overflow-hidden shadow-xs">
        <div class="p-6 border-b border-slate-200 flex flex-col sm:flex-row justify-between items-start sm:items-center gap-4 bg-slate-50/50">
            <div>
                <span class="text-xs font-bold text-slate-500 uppercase tracking-wider block">BERITA ACARA RETUR & REFUND</span>
                <h1 class="text-xl font-black text-slate-900 font-mono mt-0.5">#{{ $orderReturn->return_number }}</h1>
                <span class="text-xs text-slate-500">Diproses pada {{ $orderReturn->created_at->format('d F Y - H:i:s') }} WIB oleh {{ $orderReturn->processed_by ?? 'Kasir Utama' }}</span>
            </div>
            <span class="px-3 py-1 rounded-full text-xs font-bold bg-emerald-100 text-emerald-800">
                Status: {{ ucfirst($orderReturn->status) }}
            </span>
        </div>

        <div class="p-6 grid grid-cols-1 md:grid-cols-2 gap-6">
            <!-- Product Information -->
            <div class="p-4 rounded-xl border border-slate-200 bg-slate-50/70 space-y-3">
                <span class="text-[11px] font-bold text-slate-500 uppercase tracking-wider block">Produk yang Diretur</span>
                <div class="flex items-center gap-3">
                    @if($orderReturn->product->image)
                        <img src="{{ asset('storage/' . $orderReturn->product->image) }}" class="w-14 h-14 rounded-lg object-cover border border-slate-200">
                    @else
                        <div class="w-14 h-14 rounded-lg bg-slate-200 flex items-center justify-center text-slate-500 text-lg">👕</div>
                    @endif
                    <div>
                        <h3 class="font-bold text-slate-900 text-sm">{{ $orderReturn->product->name }}</h3>
                        <span class="text-xs text-slate-500">SKU: {{ $orderReturn->product->sku ?? '-' }} · Kategori: {{ $orderReturn->product->category }}</span>
                        <div class="text-xs font-semibold text-slate-700 mt-1">Kuantitas: <strong class="text-slate-900">{{ $orderReturn->quantity }} item</strong></div>
                    </div>
                </div>

                <div class="pt-3 border-t border-slate-200 text-xs space-y-1.5">
                    <div class="flex justify-between">
                        <span class="text-slate-500">Kondisi Fisik:</span>
                        <strong class="text-slate-900">{{ $orderReturn->condition_label }}</strong>
                    </div>
                    <div class="flex justify-between">
                        <span class="text-slate-500">Alasan Retur:</span>
                        <strong class="text-slate-900">{{ $orderReturn->reason_label }}</strong>
                    </div>
                    <div class="flex justify-between">
                        <span class="text-slate-500">Status Masuk Stok:</span>
                        <strong class="{{ $orderReturn->restock_item ? 'text-emerald-600' : 'text-rose-600' }}">
                            {{ $orderReturn->restock_item ? 'Dikembalikan ke Stok Aktif' : 'Afkir / Tidak Dijual Lagi' }}
                        </strong>
                    </div>
                </div>
            </div>

            <!-- Settlement & Financial Details -->
            <div class="p-4 rounded-xl border border-slate-200 bg-slate-50/70 space-y-3">
                <span class="text-[11px] font-bold text-slate-500 uppercase tracking-wider block">Kompensasi & Pengembalian Dana</span>
                
                <div class="bg-white p-4 rounded-xl border border-slate-200 text-center">
                    <span class="text-xs text-slate-500 block">Total Nominal Refund</span>
                    <h2 class="text-3xl font-black text-rose-600 my-1">{{ $orderReturn->formatted_refund_amount }}</h2>
                    <span class="inline-block text-xs font-bold px-2 py-0.5 rounded bg-rose-50 text-rose-700 border border-rose-200">
                        {{ $orderReturn->refund_type_label }}
                    </span>
                </div>

                <div class="pt-2 text-xs space-y-1.5">
                    <div class="flex justify-between">
                        <span class="text-slate-500">Invoice Asal:</span>
                        <a href="{{ route('admin.orders.invoice', $orderReturn->order) }}" target="_blank" class="font-mono font-bold text-blue-600 hover:underline">
                            #{{ $orderReturn->order->invoice_number }}
                        </a>
                    </div>
                    <div class="flex justify-between">
                        <span class="text-slate-500">Nama Pelanggan:</span>
                        <strong class="text-slate-900">{{ $orderReturn->order->customer_name }}</strong>
                    </div>
                    @if($orderReturn->notes)
                        <div class="pt-2 border-t border-slate-200">
                            <span class="text-slate-500 block">Catatan Tambahan Kasir:</span>
                            <p class="text-slate-700 italic mt-0.5">"{{ $orderReturn->notes }}"</p>
                        </div>
                    @endif
                </div>
            </div>
        </div>
    </div>
</div>
@endsection
