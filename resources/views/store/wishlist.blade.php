@extends('layouts.store')
@section('title', 'Koleksi Tersimpan (Wishlist) — VarStore')

@section('content')
<div class="py-16 px-[clamp(20px,5vw,80px)] max-w-7xl mx-auto">
    <div class="mb-10 reveal-on-scroll">
        <span class="eyebrow-luxury">Favorit Pilihan Anda</span>
        <h1 class="text-3xl sm:text-5xl font-extrabold text-[#0d131a] tracking-tight mt-2">
            Wishlist & Koleksi Disimpan
        </h1>
        <p class="text-xs sm:text-sm text-[#64748b] mt-1">
            Daftar busana dan aksesori yang Anda tandai untuk dibeli nanti atau saat promo berlangsung.
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

    @if($products->isEmpty())
        <div class="py-20 text-center bg-white border border-[#eeebe5] rounded-2xl p-8 reveal-on-scroll">
            <div class="w-16 h-16 bg-slate-100 text-slate-400 rounded-full flex items-center justify-center text-2xl font-bold mx-auto mb-4">
                ♡
            </div>
            <h3 class="text-lg font-extrabold text-[#0d131a]">Wishlist Anda Masih Kosong</h3>
            <p class="text-xs text-[#64748b] mt-1 max-w-md mx-auto">
                Temukan koleksi pakaian favorit Anda di katalog toko dan klik ikon hati untuk menyimpannya di sini.
            </p>
            <div class="mt-6">
                <a href="{{ route('collection') }}" class="btn-luxury-primary text-xs inline-flex items-center gap-2">
                    <span>Jelajahi Koleksi Busana ↗</span>
                </a>
            </div>
        </div>
    @else
        <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-6">
            @foreach($products as $product)
                <div class="bg-white rounded-2xl border border-[#eeebe5] overflow-hidden shadow-sm flex flex-col justify-between reveal-on-scroll">
                    <div>
                        <div class="relative aspect-3/4 overflow-hidden bg-slate-100 group">
                            <img src="{{ $product->image_url }}" alt="{{ $product->name }}" 
                                 class="w-full h-full object-cover transition duration-500 group-hover:scale-105">

                            @if($product->has_discount)
                                <span class="absolute top-3 left-3 px-2 py-0.5 rounded text-[10px] font-extrabold bg-rose-600 text-white tracking-wider">
                                    -{{ $product->discount_percent }}%
                                </span>
                            @endif

                            <form method="POST" action="{{ route('wishlist.toggle', $product) }}" class="absolute top-3 right-3">
                                @csrf
                                <button type="submit" class="w-8 h-8 rounded-full bg-white/90 text-rose-600 flex items-center justify-center text-sm shadow hover:bg-white transition" title="Hapus dari wishlist">
                                    ♥
                                </button>
                            </form>
                        </div>

                        <div class="p-4 space-y-1">
                            <span class="text-[10px] uppercase font-bold text-slate-400 tracking-wider">{{ $product->category }}</span>
                            <h3 class="font-bold text-sm text-slate-900 leading-snug line-clamp-1">
                                <a href="{{ route('products.show', $product) }}" class="hover:underline">
                                    {{ $product->name }}
                                </a>
                            </h3>
                            <div class="flex items-baseline gap-2 pt-1">
                                <span class="font-extrabold text-sm text-slate-900 font-mono">
                                    Rp {{ number_format($product->final_price, 0, ',', '.') }}
                                </span>
                                @if($product->has_discount)
                                    <span class="text-[11px] text-slate-400 line-through font-mono">
                                        Rp {{ number_format($product->price, 0, ',', '.') }}
                                    </span>
                                @endif
                            </div>
                            <p class="text-[11px] text-slate-500 pt-1">
                                {{ $product->color }} · {{ $product->size }}
                            </p>
                        </div>
                    </div>

                    <div class="p-4 pt-0">
                        <form method="POST" action="{{ route('wishlist.move-to-cart', $product) }}">
                            @csrf
                            <button type="submit" class="w-full py-2 bg-slate-900 hover:bg-black text-white text-xs font-bold rounded-xl transition flex items-center justify-center gap-1.5" {{ $product->stock < 1 ? 'disabled' : '' }}>
                                <span>+ Pindah ke Tas Belanja</span>
                            </button>
                        </form>
                    </div>
                </div>
            @endforeach
        </div>
    @endif
</div>
@endsection

