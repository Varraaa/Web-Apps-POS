@extends('layouts.store')
@section('title', 'Lacak Pesanan & Struk Digital — VarStore')

@section('content')
<div class="py-16 px-[clamp(20px,5vw,80px)] max-w-4xl mx-auto min-h-[70vh]">
    <div class="text-center mb-10 reveal-on-scroll">
        <span class="eyebrow-luxury justify-center">Client Concierge Services</span>
        <h1 class="text-3xl sm:text-5xl font-extrabold text-[#0d131a] tracking-tight mt-2">
            Lacak Pesanan & Struk Digital
        </h1>
        <p class="text-xs sm:text-sm text-[#64748b] mt-2 max-w-xl mx-auto">
            Masukkan Nomor Invoice (#POS-XXXX / #INV-XXXX) atau Nomor WhatsApp Anda untuk memeriksa status verifikasi dan pengiriman barang.
        </p>
    </div>

    <!-- Search Card -->
    <div class="bg-white p-6 sm:p-8 rounded-2xl border border-[#eeebe5] shadow-lg mb-10 reveal-on-scroll">
        <form method="GET" action="{{ route('tracking.index') }}" class="flex flex-col sm:flex-row gap-3">
            <div class="relative flex-1">
                <input type="text" name="q" value="{{ $query }}" 
                       placeholder="Contoh: POS-20261104-ABCDE atau 081297577567" 
                       class="w-full pl-11 pr-4 py-3.5 text-xs sm:text-sm rounded-xl border border-[#d4cdbf] bg-[#fbfaf8] focus:bg-white focus:border-[#0d131a] outline-none font-mono" required>
                <span class="absolute left-4 top-4 text-gray-400">🔍</span>
            </div>
            <button type="submit" class="btn-luxury-dark py-3.5 px-8 justify-center text-xs">
                <span>Cari Pesanan</span>
                <span>→</span>
            </button>
        </form>
    </div>

    <!-- Results Section -->
    @if($searched)
        @if($order)
            <div class="bg-white rounded-2xl border border-[#eeebe5] shadow-xl overflow-hidden reveal-on-scroll">
                <!-- Status Header Banner -->
                <div class="p-6 sm:p-8 bg-[#0d131a] text-white flex flex-col sm:flex-row sm:items-center justify-between gap-4">
                    <div>
                        <span class="text-[10px] uppercase tracking-widest text-[#dfbe95] font-bold">Terverifikasi di Sistem</span>
                        <h2 class="text-xl sm:text-2xl font-mono font-bold mt-1">#{{ $order->invoice_number }}</h2>
                        <span class="text-xs text-slate-400">Dipesan pada {{ $order->created_at->format('d M Y, H:i') }} WIB</span>
                    </div>

                    <div class="text-right">
                        <span class="inline-block px-3 py-1 rounded-full text-xs font-extrabold uppercase tracking-wider
                            {{ $order->status === 'completed' ? 'bg-emerald-500/20 text-emerald-300 border border-emerald-500/30' : '' }}
                            {{ $order->status === 'processed' ? 'bg-blue-500/20 text-blue-300 border border-blue-500/30' : '' }}
                            {{ $order->status === 'pending' ? 'bg-amber-500/20 text-amber-300 border border-amber-500/30' : '' }}
                            {{ $order->status === 'cancelled' ? 'bg-rose-500/20 text-rose-300 border border-rose-500/30' : '' }}">
                            {{ $order->status_label }}
                        </span>
                        <div class="text-xs text-slate-400 mt-1">
                            Metode: <strong class="text-white">{{ $order->payment_label }}</strong>
                        </div>
                    </div>
                </div>

                <!-- Fulfillment Progress Stepper -->
                <div class="p-6 sm:p-8 border-b border-[#eeebe5] bg-[#faf8f5]">
                    <div class="grid grid-cols-3 gap-2 relative">
                        <!-- Step 1 -->
                        <div class="text-center relative z-10">
                            <div class="w-8 h-8 rounded-full mx-auto flex items-center justify-center font-bold text-xs
                                {{ in_array($order->status, ['pending', 'processed', 'completed']) ? 'bg-[#0d131a] text-[#dfbe95]' : 'bg-slate-200 text-slate-500' }}">
                                ✓
                            </div>
                            <span class="block text-xs font-bold mt-2 text-[#0d131a]">Pesanan Masuk</span>
                            <small class="text-[10px] text-[#64748b] hidden sm:block">Diterima oleh sistem</small>
                        </div>

                        <!-- Step 2 -->
                        <div class="text-center relative z-10">
                            <div class="w-8 h-8 rounded-full mx-auto flex items-center justify-center font-bold text-xs
                                {{ in_array($order->status, ['processed', 'completed']) ? 'bg-[#0d131a] text-[#dfbe95]' : 'bg-slate-200 text-slate-500' }}">
                                {{ in_array($order->status, ['processed', 'completed']) ? '✓' : '2' }}
                            </div>
                            <span class="block text-xs font-bold mt-2 text-[#0d131a]">Diproses Kasir</span>
                            <small class="text-[10px] text-[#64748b] hidden sm:block">QC & pengemasan barang</small>
                        </div>

                        <!-- Step 3 -->
                        <div class="text-center relative z-10">
                            <div class="w-8 h-8 rounded-full mx-auto flex items-center justify-center font-bold text-xs
                                {{ $order->status === 'completed' ? 'bg-[#0d131a] text-[#dfbe95]' : 'bg-slate-200 text-slate-500' }}">
                                {{ $order->status === 'completed' ? '✓' : '3' }}
                            </div>
                            <span class="block text-xs font-bold mt-2 text-[#0d131a]">Selesai / Terkirim</span>
                            <small class="text-[10px] text-[#64748b] hidden sm:block">Transaksi tuntas</small>
                        </div>
                    </div>
                </div>

                <!-- Customer & Items Details -->
                <div class="p-6 sm:p-8 space-y-6">
                    <div class="grid grid-cols-1 sm:grid-cols-2 gap-4 text-xs">
                        <div class="p-4 rounded-xl bg-[#fbfaf8] border border-[#eeebe5]">
                            <span class="text-[10px] font-bold text-[#94a3b8] uppercase tracking-wider block mb-1">Informasi Penerima</span>
                            <strong class="text-sm text-[#0d131a] block">{{ $order->customer_name }}</strong>
                            <p class="text-slate-600 mt-1">{{ $order->customer_phone }}</p>
                        </div>

                        <div class="p-4 rounded-xl bg-[#fbfaf8] border border-[#eeebe5]">
                            <span class="text-[10px] font-bold text-[#94a3b8] uppercase tracking-wider block mb-1">Lokasi Pengiriman / Di Tempat</span>
                            <p class="text-slate-700 whitespace-pre-line">{{ $order->shipping_address }}</p>
                        </div>
                    </div>

                    <!-- Items List -->
                    <div>
                        <h4 class="text-xs font-bold text-[#0d131a] uppercase tracking-wider mb-3">Item Pesanan:</h4>
                        <div class="divide-y divide-[#eeebe5] border border-[#eeebe5] rounded-xl overflow-hidden">
                            @foreach($order->items as $item)
                                <div class="p-4 flex items-center justify-between text-xs bg-white">
                                    <div class="flex items-center gap-3">
                                        @if($item->product)
                                            <img src="{{ $item->product->image_url }}" alt="" class="w-10 h-12 object-cover rounded bg-[#eeebe5]">
                                        @endif
                                        <div>
                                            <strong class="text-[#0d131a] block text-xs">{{ $item->product_name }}</strong>
                                            <span class="text-[11px] text-[#64748b]">
                                                {{ $item->quantity }} pcs @ Rp {{ number_format($item->price, 0, ',', '.') }}
                                            </span>
                                        </div>
                                    </div>
                                    <strong class="text-xs text-[#0d131a]">
                                        Rp {{ number_format($item->quantity * $item->price, 0, ',', '.') }}
                                    </strong>
                                </div>
                            @endforeach
                        </div>
                    </div>

                    <!-- Totals -->
                    <div class="p-4 rounded-xl bg-[#fbfaf8] border border-[#eeebe5] space-y-2 text-xs">
                        <div class="flex justify-between text-slate-600">
                            <span>Subtotal:</span>
                            <span>Rp {{ number_format($order->subtotal ?? $order->total, 0, ',', '.') }}</span>
                        </div>
                        @if($order->discount_amount > 0)
                            <div class="flex justify-between text-emerald-600 font-bold">
                                <span>Diskon Promo ({{ $order->coupon_code }}):</span>
                                <span>- Rp {{ number_format($order->discount_amount, 0, ',', '.') }}</span>
                            </div>
                        @endif
                        <div class="flex justify-between text-sm font-extrabold text-[#0d131a] pt-2 border-t border-[#e2ded5]">
                            <span>Total Pembayaran:</span>
                            <span>{{ $order->formatted_total }}</span>
                        </div>
                    </div>

                    <!-- Quick Support & Receipt CTA -->
                    <div class="flex flex-col sm:flex-row items-center justify-between gap-4 pt-4 border-t border-[#eeebe5]">
                        <a href="https://wa.me/6281297577567?text={{ urlencode('Halo VarStore Concierge, saya ingin bertanya tentang pesanan #' . $order->invoice_number) }}" 
                           target="_blank" rel="noopener noreferrer" 
                           class="text-xs font-bold text-emerald-600 hover:underline flex items-center gap-1.5">
                            <span>💬</span> Hubungi WhatsApp Concierge Toko
                        </a>

                        <a href="{{ route('admin.pos.receipt', $order) }}" target="_blank" class="btn-luxury-outline text-xs py-2.5 px-4">
                            <span>🖨 Buka Struk Kasir / Cetak</span>
                            <span>↗</span>
                        </a>
                    </div>
                </div>
            </div>
        @else
            <div class="bg-white p-12 text-center rounded-2xl border border-[#eeebe5] text-slate-400">
                <span class="text-4xl block mb-3">🔍</span>
                <h3 class="text-base font-bold text-slate-800">Pesanan Tidak Ditemukan</h3>
                <p class="text-xs text-slate-500 mt-1 max-w-sm mx-auto">
                    Nomor pencarian "<strong>{{ $query }}</strong>" tidak cocok dengan data transaksi manapun. Pastikan nomor invoice atau nomor WhatsApp sudah benar.
                </p>
                <div class="mt-6">
                    <a href="{{ route('collection') }}" class="btn-luxury-dark text-xs py-2.5 px-6 inline-flex">
                        <span>Lihat Koleksi Busana ↗</span>
                    </a>
                </div>
            </div>
        @endif
    @endif
</div>
@endsection
