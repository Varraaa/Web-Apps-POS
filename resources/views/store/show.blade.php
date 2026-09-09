@extends('layouts.store')
@section('title', $product->name . ' — VarStore')

@section('content')
<div class="py-16 px-[clamp(20px,5vw,80px)] max-w-7xl mx-auto">
    <div class="mb-8">
        <a href="{{ route('collection') }}" class="text-xs font-bold text-[#64748b] hover:text-[#0d131a] inline-flex items-center gap-2">
            <span>←</span>
            <span>Kembali ke Koleksi</span>
        </a>
    </div>

    <div class="grid grid-cols-1 lg:grid-cols-12 gap-12 lg:gap-16 items-start">
        <!-- Product Image Showcase (7 cols) -->
        <div class="lg:col-span-7 relative aspect-[3/4] rounded-xl overflow-hidden bg-[#eeebe5] shadow-lg reveal-scale">
            <img src="{{ $product->image_url }}" alt="{{ $product->name }}" class="w-full h-full object-cover">
            
            @if($product->has_discount)
                <span class="product-badge-sale text-xs px-3 py-1.5">
                    SALE -{{ $product->discount_percent }}% OFF
                </span>
            @endif

            <span class="product-badge-category">
                {{ $product->category }}
            </span>
        </div>

        <!-- Product Information & Actions (5 cols) -->
        <div class="lg:col-span-5 flex flex-col justify-center reveal-on-scroll delay-100">
            <span class="eyebrow-luxury">{{ $product->category }} · SS26</span>
            <h1 class="text-3xl sm:text-4xl font-extrabold text-[#0d131a] tracking-tight leading-tight mt-1 mb-4">
                {{ $product->name }}
            </h1>

            <!-- Pricing Box -->
            <div class="flex items-baseline gap-3 mb-6">
                @if($product->has_discount)
                    <span class="text-2xl sm:text-3xl font-extrabold text-[#c94a29]">
                        Rp {{ number_format($product->final_price, 0, ',', '.') }}
                    </span>
                    <span class="text-base text-[#94a3b8] line-through font-medium">
                        Rp {{ number_format($product->price, 0, ',', '.') }}
                    </span>
                    <span class="text-xs font-bold bg-rose-50 text-rose-700 px-2 py-1 rounded">
                        Hemat Rp {{ number_format($product->discount_savings, 0, ',', '.') }}
                    </span>
                @else
                    <span class="text-2xl sm:text-3xl font-extrabold text-[#0d131a]">
                        Rp {{ number_format($product->price, 0, ',', '.') }}
                    </span>
                @endif
            </div>

            <!-- Description -->
            <p class="text-sm text-[#5a6672] leading-relaxed mb-6">
                {{ $product->description }}
            </p>

            <!-- Product Specs Table -->
            <div class="space-y-3 py-5 border-y border-[#eeebe5] text-xs mb-8">
                <div class="flex justify-between">
                    <span class="text-[#7b8893]">Pilihan Warna:</span>
                    <strong class="text-[#0d131a]">{{ $product->color }}</strong>
                </div>
                <div class="flex justify-between">
                    <span class="text-[#7b8893]">Ukuran Tersedia:</span>
                    <strong class="text-[#0d131a]">{{ $product->size }}</strong>
                </div>
                <div class="flex justify-between">
                    <span class="text-[#7b8893]">Status Ketersediaan:</span>
                    @if($product->stock > 3)
                        <strong class="text-emerald-700 font-bold">Stok Tersedia ({{ $product->stock }} pcs)</strong>
                    @elseif($product->stock > 0)
                        <strong class="text-amber-600 font-bold">Stok Terbatas (Sisa {{ $product->stock }} pcs)</strong>
                    @else
                        <strong class="text-rose-600 font-bold">Habis Terjual</strong>
                    @endif
                </div>
            </div>

            <!-- Active Promo Hint on Product Page -->
            <div class="p-4 bg-[#fbfaf8] border border-[#dfbe95] rounded-lg mb-8">
                <div class="flex items-center justify-between">
                    <span class="text-xs font-bold text-[#0d131a]">Gunakan Kode Promo VARRA20</span>
                    <button type="button" class="btn-copy-code text-[10px] py-1 px-2.5" data-copy-coupon="VARRA20">
                        Salin Kode
                    </button>
                </div>
                <p class="text-[11px] text-[#64748b] mt-1">
                    Dapatkan tambahan diskon 20% saat menyelesaikan pesanan di keranjang belanja.
                </p>
            </div>

            <!-- Action Forms -->
            <div class="space-y-3">
                <form method="GET" action="{{ route('products.buy', $product) }}" target="_blank">
                    <div class="flex items-center gap-3 mb-3">
                        <label for="buy-qty" class="text-xs font-bold text-[#4a5568]">Jumlah:</label>
                        <input type="number" id="buy-qty" name="quantity" min="1" max="{{ $product->stock }}" value="1" 
                               class="w-16 px-2 py-1.5 text-center text-xs font-bold border border-[#d4cdbf] rounded outline-none">
                    </div>
                    <button type="submit" class="btn-luxury-dark w-full justify-center text-xs py-4" 
                            {{ $product->stock < 1 ? 'disabled' : '' }}>
                        <svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" fill="currentColor" viewBox="0 0 24 24">
                            <path d="M.057 24l1.687-6.163c-1.041-1.804-1.588-3.849-1.587-5.946.003-6.556 5.338-11.891 11.893-11.891 3.181.001 6.167 1.24 8.413 3.488 2.245 2.248 3.481 5.236 3.48 8.414-.003 6.557-5.338 11.892-11.893 11.892-1.99-.001-3.951-.5-5.688-1.448l-6.305 1.654zm6.597-3.807c1.676.995 3.276 1.591 5.392 1.592 5.448 0 9.886-4.434 9.889-9.885.002-5.462-4.415-9.89-9.881-9.892-5.452 0-9.887 4.434-9.889 9.884-.001 2.225.651 3.891 1.746 5.634l-.999 3.648 3.742-.981z"/>
                        </svg>
                        <span>{{ $product->stock ? 'Beli Langsung via WhatsApp POS ↗' : 'Stok Habis' }}</span>
                    </button>
                </form>

                <form method="POST" action="{{ route('cart.add', $product) }}">
                    @csrf
                    <input type="hidden" name="quantity" value="1">
                    <button type="submit" class="btn-luxury-outline w-full justify-center text-xs py-3.5" 
                            {{ $product->stock < 1 ? 'disabled' : '' }}>
                        <span>+ Tambahkan ke Tas Belanja</span>
                    </button>
                </form>
            </div>
        </div>
    </div>

    <!-- Related Products -->
    @if(isset($related) && $related->isNotEmpty())
        <div class="mt-28 pt-16 border-t border-[#eeebe5]">
            <div class="flex items-center justify-between mb-10">
                <div>
                    <span class="eyebrow-luxury">Koleksi Terkait</span>
                    <h3 class="text-2xl font-extrabold text-[#0d131a] tracking-tight mt-1">
                        Padanan yang Direkomendasikan
                    </h3>
                </div>
                <a href="{{ route('collection', ['category' => $product->category]) }}" class="text-xs font-bold text-[#c9a982] hover:underline">
                    Semua {{ $product->category }} ↗
                </a>
            </div>

            <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-6">
                @foreach($related as $relProduct)
                    <x-product-card :product="$relProduct" />
                @endforeach
            </div>
        </div>
    @endif
</div>
@endsection
