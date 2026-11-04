@extends('layouts.store')
@section('title', 'Tas Belanja & Checkout — VarStore')

@section('content')
<div class="py-16 px-[clamp(20px,5vw,80px)] max-w-7xl mx-auto">
    <div class="mb-10 reveal-on-scroll">
        <span class="eyebrow-luxury">Keranjang & Pembayaran</span>
        <h1 class="text-3xl sm:text-5xl font-extrabold text-[#0d131a] tracking-tight mt-2">
            Tas Belanja Anda
        </h1>
        <p class="text-xs sm:text-sm text-[#64748b] mt-1">
            Tinjau pesanan Anda, pasang kode diskon promo, dan selesaikan transaksi secara instan via WhatsApp POS.
        </p>
    </div>

    @if(session('success'))
        <div class="p-4 mb-8 bg-emerald-50 border border-emerald-200 text-emerald-800 text-xs font-semibold rounded-lg flex items-center justify-between">
            <span class="flex items-center gap-2">
                <span class="text-emerald-600 font-bold text-base">✓</span>
                {{ session('success') }}
            </span>
        </div>
    @endif

    @if(session('error'))
        <div class="p-4 mb-8 bg-rose-50 border border-rose-200 text-rose-800 text-xs font-semibold rounded-lg flex items-center justify-between">
            <span class="flex items-center gap-2">
                <span class="text-rose-600 font-bold text-base">✕</span>
                {{ session('error') }}
            </span>
        </div>
    @endif

    <div class="grid grid-cols-1 lg:grid-cols-12 gap-12">
        <!-- Cart Items List (8 cols) -->
        <div class="lg:col-span-7 space-y-6">
            @forelse($cart['items'] as $item)
                <div class="flex gap-5 p-5 bg-white rounded-xl border border-[#eeebe5] shadow-sm relative reveal-on-scroll">
                    <img src="{{ $item['product']->image_url }}" alt="{{ $item['product']->name }}" 
                         class="w-24 h-32 sm:w-28 sm:h-36 object-cover rounded-lg bg-[#eeebe5] shrink-0">

                    <div class="flex-1 flex flex-col justify-between">
                        <div>
                            <div class="flex items-start justify-between gap-3">
                                <div>
                                    <span class="text-[10px] uppercase font-bold tracking-wider text-[#94a3b8]">{{ $item['product']->category }}</span>
                                    <h3 class="text-base font-bold text-[#0d131a] leading-snug">
                                        <a href="{{ route('products.show', $item['product']) }}" class="hover:underline">
                                            {{ $item['product']->name }}
                                        </a>
                                    </h3>
                                </div>
                                <form method="POST" action="{{ route('cart.remove', $item['product']) }}">
                                    @csrf
                                    @method('DELETE')
                                    <button class="text-[11px] font-bold text-rose-600 hover:text-rose-800 p-1" title="Hapus dari tas">
                                        Hapus &times;
                                    </button>
                                </form>
                            </div>

                            <p class="text-xs text-[#64748b] mt-1">
                                Warna: {{ $item['product']->color }} · Ukuran: {{ $item['product']->size }}
                            </p>
                        </div>

                        <div class="flex items-center justify-between mt-4 pt-3 border-t border-[#f4f2ee]">
                            <!-- Quantity Adjust Stepper -->
                            <div class="flex items-center gap-2">
                                <form method="POST" action="{{ route('cart.update', $item['product']) }}" class="inline">
                                    @csrf
                                    @method('PATCH')
                                    <input type="hidden" name="quantity" value="{{ $item['quantity'] - 1 }}">
                                    <button type="submit" class="w-7 h-7 rounded border border-[#d4cdbf] bg-[#fbfaf8] hover:bg-[#0d131a] hover:text-white text-xs font-bold transition-colors flex items-center justify-center">
                                        −
                                    </button>
                                </form>

                                <span class="w-8 text-center text-xs font-black text-[#0d131a]">
                                    {{ $item['quantity'] }}
                                </span>

                                <form method="POST" action="{{ route('cart.update', $item['product']) }}" class="inline">
                                    @csrf
                                    @method('PATCH')
                                    <input type="hidden" name="quantity" value="{{ $item['quantity'] + 1 }}">
                                    <button type="submit" 
                                            class="w-7 h-7 rounded border border-[#d4cdbf] bg-[#fbfaf8] hover:bg-[#0d131a] hover:text-white text-xs font-bold transition-colors flex items-center justify-center disabled:opacity-30 disabled:pointer-events-none"
                                            {{ $item['quantity'] >= $item['product']->stock ? 'disabled' : '' }}>
                                        +
                                    </button>
                                </form>

                                <span class="text-[10px] text-[#94a3b8] ml-1">
                                    (stok: {{ $item['product']->stock }})
                                </span>
                            </div>

                            <div class="text-right">
                                @if($item['product']->has_discount)
                                    <span class="text-[11px] text-[#94a3b8] line-through block">
                                        Rp {{ number_format($item['original_price'] * $item['quantity'], 0, ',', '.') }}
                                    </span>
                                    <span class="text-sm font-extrabold text-[#c94a29]">
                                        Rp {{ number_format($item['subtotal'], 0, ',', '.') }}
                                    </span>
                                @else
                                    <span class="text-sm font-extrabold text-[#0d131a]">
                                        Rp {{ number_format($item['subtotal'], 0, ',', '.') }}
                                    </span>
                                @endif
                            </div>
                        </div>
                    </div>
                </div>
            @empty
                <div class="py-20 text-center bg-white rounded-xl border border-dashed border-[#d4cdbf] p-8">
                    <span class="text-5xl block mb-4">♧</span>
                    <h3 class="text-xl font-bold text-[#0d131a]">Tas Belanja Anda Masih Kosong</h3>
                    <p class="text-xs text-[#7b8893] mt-2 max-w-sm mx-auto">
                        Pilihlah busana yang sesuai dengan karakter Anda dari koleksi terbaru kami dan klaim promo diskon yang tersedia.
                    </p>
                    <a href="{{ route('collection') }}" class="btn-luxury-dark text-xs mt-6 inline-flex">
                        Jelajahi Koleksi Sekarang ↗
                    </a>
                </div>
            @endforelse

            <!-- Available Promo Vouchers Recommendation (1-Click Apply) -->
            @if(isset($availableCoupons) && $availableCoupons->isNotEmpty() && $cart['items']->isNotEmpty())
                <div class="p-6 bg-[#fbfaf8] border border-[#dfbe95] rounded-xl">
                    <span class="eyebrow-luxury">Pilihan Kupon Diskon Aktif</span>
                    <h4 class="text-sm font-bold text-[#0d131a] mt-1 mb-3">Klik untuk pasang voucher diskon secara instan:</h4>
                    
                    <div class="grid grid-cols-1 sm:grid-cols-2 gap-3">
                        @foreach($availableCoupons as $coupon)
                            <div class="p-3 bg-white border border-[#e2ded5] rounded-lg flex items-center justify-between gap-3 shadow-2xs hover:border-[#0d131a] transition-all">
                                <div>
                                    <span class="text-[10px] font-mono font-bold bg-[#0d131a] text-[#dfbe95] px-1.5 py-0.5 rounded">
                                        {{ $coupon->code }}
                                    </span>
                                    <span class="text-xs font-bold text-[#0d131a] ml-1.5">{{ $coupon->formatted_discount }}</span>
                                    <p class="text-[10px] text-[#7b8893] mt-1 line-clamp-1">{{ $coupon->name }}</p>
                                </div>
                                <form method="POST" action="{{ route('cart.coupon.apply') }}">
                                    @csrf
                                    <input type="hidden" name="code" value="{{ $coupon->code }}">
                                    <button type="submit" class="text-[11px] font-extrabold bg-[#f4f2ee] hover:bg-[#0d131a] hover:text-white text-[#0d131a] px-3 py-1 rounded transition-colors whitespace-nowrap">
                                        Pakai
                                    </button>
                                </form>
                            </div>
                        @endforeach
                    </div>
                </div>
            @endif
        </div>

        <!-- Checkout & Discount Summary (5 cols) -->
        @if($cart['items']->isNotEmpty())
            <aside class="lg:col-span-5">
                <div class="bg-white p-6 sm:p-8 rounded-xl border border-[#eeebe5] shadow-md sticky top-28">
                    <span class="eyebrow-luxury">Rincian Pembayaran</span>
                    <h2 class="text-2xl font-extrabold text-[#0d131a] tracking-tight mt-1 mb-6">
                        Order Summary
                    </h2>

                    <!-- Voucher Promo Code Input Form -->
                    <div class="coupon-box">
                        <label for="coupon-input" class="text-xs font-bold text-[#0d131a] block">
                            Punya Kode Promo / Voucher Diskon?
                        </label>
                        
                        @if($cart['coupon'])
                            <div class="coupon-applied-card">
                                <div class="coupon-applied-info">
                                    <strong>✓ {{ $cart['coupon']->code }}</strong>
                                    <p>{{ $cart['coupon']->name }} (Diskon {{ $cart['coupon']->formatted_discount }})</p>
                                </div>
                                <form method="POST" action="{{ route('cart.coupon.remove') }}">
                                    @csrf
                                    @method('DELETE')
                                    <button type="submit" class="btn-remove-coupon" title="Hapus voucher">
                                        Batalkan
                                    </button>
                                </form>
                            </div>
                        @else
                            <form method="POST" action="{{ route('cart.coupon.apply') }}" class="coupon-form">
                                @csrf
                                <input type="text" id="coupon-input" name="code" 
                                       placeholder="Misal: VARRA20" 
                                       class="coupon-input" required>
                                <button type="submit" class="btn-apply-coupon">
                                    Terapkan
                                </button>
                            </form>
                            <p class="text-[11px] text-[#7b8893] mt-2">
                                Tip: Coba gunakan kode <strong>VARRA20</strong> atau <strong>HEMAT50K</strong> untuk harga lebih murah!
                            </p>
                        @endif
                    </div>

                    <!-- Price Calculations -->
                    <div class="space-y-3 py-4 border-y border-[#eeebe5] text-xs">
                        <div class="flex justify-between text-[#64748b]">
                            <span>Subtotal Barang</span>
                            <span class="font-bold text-[#0d131a]">
                                Rp {{ number_format($cart['subtotal'], 0, ',', '.') }}
                            </span>
                        </div>

                        @if($cart['discount_amount'] > 0)
                            <div class="flex justify-between text-emerald-700 font-bold bg-emerald-50 p-2 rounded">
                                <span class="flex items-center gap-1.5">
                                    <span>✦</span>
                                    <span>Diskon Voucher ({{ $cart['coupon']->code }})</span>
                                </span>
                                <span>
                                    - Rp {{ number_format($cart['discount_amount'], 0, ',', '.') }}
                                </span>
                            </div>
                        @endif

                        <div class="flex justify-between text-[#64748b]">
                            <span>Estimasi Pengiriman</span>
                            <span class="font-bold text-emerald-600 uppercase">Gratis Ongkir</span>
                        </div>
                    </div>

                    <!-- Final Total -->
                    <div class="flex items-baseline justify-between py-4">
                        <div>
                            <span class="text-xs uppercase font-extrabold tracking-wider text-[#94a3b8] block">
                                Total Pembayaran
                            </span>
                            @if($cart['discount_amount'] > 0)
                                <span class="text-[11px] text-emerald-600 font-semibold">
                                    Hemat Rp {{ number_format($cart['discount_amount'], 0, ',', '.') }}!
                                </span>
                            @endif
                        </div>
                        <span class="text-2xl font-extrabold text-[#0d131a] tracking-tight">
                            Rp {{ number_format($cart['total'], 0, ',', '.') }}
                        </span>
                    </div>

                    <!-- WhatsApp POS Checkout Form -->
                    <div class="mt-4 pt-4 border-t border-[#eeebe5]">
                        <p class="text-xs text-[#64748b] mb-4">
                            Konfirmasi data pengiriman. Anda akan dialihkan ke WhatsApp kasir resmi VarStore untuk memverifikasi pesanan berdiskon ini.
                        </p>

                        <form method="POST" action="{{ route('checkout') }}" class="space-y-3">
                            @csrf
                            <div>
                                <label class="text-[11px] font-bold text-[#334155] block mb-1">Nama Lengkap</label>
                                <input type="text" name="customer_name" 
                                       value="{{ auth()->user()?->name }}" 
                                       placeholder="Nama pemesan" 
                                       class="w-full px-3 py-2.5 text-xs rounded border border-[#d4cdbf] outline-none focus:border-[#0d131a]" required>
                            </div>

                            <div>
                                <label class="text-[11px] font-bold text-[#334155] block mb-1">Nomor WhatsApp</label>
                                <input type="tel" name="customer_phone" 
                                       placeholder="Contoh: 08123456789" 
                                       class="w-full px-3 py-2.5 text-xs rounded border border-[#d4cdbf] outline-none focus:border-[#0d131a]" required>
                            </div>

                            <div>
                                <label class="text-[11px] font-bold text-[#334155] block mb-1">Alamat Lengkap Pengiriman</label>
                                <textarea name="shipping_address" rows="3" 
                                          placeholder="Jalan, nomor rumah, kecamatan, kota, kode pos" 
                                          class="w-full px-3 py-2 text-xs rounded border border-[#d4cdbf] outline-none focus:border-[#0d131a] resize-none" required></textarea>
                            </div>

                            <button type="submit" class="btn-luxury-dark w-full justify-center text-xs py-3.5 mt-2" formtarget="_blank">
                                <svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" fill="currentColor" viewBox="0 0 24 24">
                                    <path d="M.057 24l1.687-6.163c-1.041-1.804-1.588-3.849-1.587-5.946.003-6.556 5.338-11.891 11.893-11.891 3.181.001 6.167 1.24 8.413 3.488 2.245 2.248 3.481 5.236 3.48 8.414-.003 6.557-5.338 11.892-11.893 11.892-1.99-.001-3.951-.5-5.688-1.448l-6.305 1.654zm6.597-3.807c1.676.995 3.276 1.591 5.392 1.592 5.448 0 9.886-4.434 9.889-9.885.002-5.462-4.415-9.89-9.881-9.892-5.452 0-9.887 4.434-9.889 9.884-.001 2.225.651 3.891 1.746 5.634l-.999 3.648 3.742-.981z"/>
                                </svg>
                                <span>Kirim Pesanan ke WhatsApp POS ↗</span>
                            </button>
                        </form>
                    </div>
                </div>
            </aside>
        @endif
    </div>
</div>
@endsection
