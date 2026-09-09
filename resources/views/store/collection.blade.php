@extends('layouts.store')
@section('title', 'Koleksi Busana — VarStore Haute Fashion')

@section('content')
    <!-- Collection Hero Header -->
    <section class="py-20 px-[clamp(20px,6vw,90px)] bg-[#f7f5f0] border-b border-[#eeebe5]">
        <div class="max-w-7xl mx-auto">
            <div class="reveal-on-scroll">
                <span class="eyebrow-luxury">VarStore Capsule / 2026</span>
                <h1 class="text-4xl sm:text-6xl font-extrabold text-[#0d131a] tracking-tight leading-none mt-2 mb-4">
                    The Complete<br>
                    <span class="font-serif italic font-normal text-[#927555]">Wardrobe Edit.</span>
                </h1>
                <p class="text-sm sm:text-base text-[#5a6672] max-w-xl leading-relaxed">
                    Temukan siluet, lapisan pakaian, dan sentuhan akhir yang siap melengkapi rotasi pakaian harian Anda. Seluruh produk dapat dipesan langsung via kasir POS WhatsApp.
                </p>
            </div>

            <!-- Interactive Discount Alert Bar in Collection -->
            <div class="mt-8 p-4 sm:p-5 bg-white border border-[#dfbe95] rounded-xl shadow-sm flex flex-col sm:flex-row items-start sm:items-center justify-between gap-4 reveal-on-scroll delay-100">
                <div class="flex items-center gap-3">
                    <span class="w-8 h-8 rounded-full bg-[#0d131a] text-[#dfbe95] flex items-center justify-center font-bold text-xs shrink-0">
                        %
                    </span>
                    <div>
                        <span class="text-xs font-bold text-[#0d131a] block">
                            Penawaran Hemat: Dapatkan Diskon Hingga 25% Hari Ini
                        </span>
                        <span class="text-[11px] text-[#64748b]">
                            Gunakan kode promo <strong class="text-[#0d131a] bg-[#f7f5f0] px-1.5 py-0.5 rounded border border-[#e2ded5]">VARRA20</strong> atau <strong class="text-[#0d131a] bg-[#f7f5f0] px-1.5 py-0.5 rounded border border-[#e2ded5]">HEMAT50K</strong> saat checkout.
                        </span>
                    </div>
                </div>
                <div class="flex items-center gap-2 w-full sm:w-auto">
                    <button type="button" class="btn-copy-code text-xs w-full sm:w-auto text-center" data-copy-coupon="VARRA20">
                        Salin Voucher VARRA20
                    </button>
                    <a href="{{ route('collection', ['sale' => 1]) }}" class="text-xs font-bold text-[#c94a29] hover:underline px-3 py-2 whitespace-nowrap">
                        Filter Diskon ↗
                    </a>
                </div>
            </div>
        </div>
    </section>

    <!-- Main Collection Filter & Grid Section -->
    <section class="py-16 px-[clamp(20px,5vw,80px)] bg-[#ffffff]">
        <div class="max-w-7xl mx-auto">
            
            <!-- Controls Bar: Category Pills + Search + Sort -->
            <div class="flex flex-col lg:flex-row lg:items-center justify-between gap-6 pb-8 border-b border-[#eeebe5] mb-12">
                
                <!-- Category Tabs -->
                <div class="flex flex-wrap items-center gap-2">
                    <a href="{{ route('collection') }}" 
                       class="px-4 py-2 rounded-full text-xs font-bold transition-all {{ !$category && !$saleOnly ? 'bg-[#0d131a] text-white' : 'bg-[#f4f2ee] text-[#4a5568] hover:bg-[#e2ded5]' }}">
                        Semua Pakaian
                    </a>

                    @foreach (['Outerwear', 'Tops', 'Bottoms', 'Accessories'] as $item)
                        <a href="{{ route('collection', ['category' => $item]) }}" 
                           class="px-4 py-2 rounded-full text-xs font-bold transition-all {{ $category === $item ? 'bg-[#0d131a] text-white' : 'bg-[#f4f2ee] text-[#4a5568] hover:bg-[#e2ded5]' }}">
                            {{ $item }}
                            @if(isset($categoryCounts[$item]))
                                <span class="text-[10px] opacity-70 ml-1">({{ $categoryCounts[$item] }})</span>
                            @endif
                        </a>
                    @endforeach

                    <!-- Special Sale Filter Pill -->
                    <a href="{{ route('collection', ['sale' => 1]) }}" 
                       class="px-4 py-2 rounded-full text-xs font-bold transition-all flex items-center gap-1.5 {{ $saleOnly ? 'bg-[#c94a29] text-white' : 'bg-[#fef2f2] text-[#c94a29] border border-[#fecaca] hover:bg-[#fee2e2]' }}">
                        <span>🔥 Sedang Diskon</span>
                        @if(isset($saleCount) && $saleCount > 0)
                            <span class="text-[10px] bg-white text-[#c94a29] px-1.5 py-0.2 rounded-full font-extrabold">{{ $saleCount }}</span>
                        @endif
                    </a>
                </div>

                <!-- Search & Sort Forms -->
                <div class="flex flex-wrap items-center gap-3">
                    <!-- Search Input -->
                    <form method="GET" action="{{ route('collection') }}" class="relative flex items-center">
                        @if($category)
                            <input type="hidden" name="category" value="{{ $category }}">
                        @endif
                        @if($saleOnly)
                            <input type="hidden" name="sale" value="1">
                        @endif
                        <input type="text" name="search" value="{{ $search }}" 
                               placeholder="Cari pakaian / warna..." 
                               class="pl-9 pr-4 py-2 text-xs rounded-full border border-[#d4cdbf] bg-[#fbfaf8] focus:border-[#0d131a] focus:bg-white outline-none w-52 transition-all">
                        <svg class="absolute left-3 w-3.5 h-3.5 text-[#94a3b8]" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"></path>
                        </svg>
                        @if($search)
                            <a href="{{ route('collection', ['category' => $category, 'sale' => $saleOnly ? 1 : null]) }}" class="absolute right-3 text-xs text-gray-400 hover:text-gray-600">&times;</a>
                        @endif
                    </form>

                    <!-- Sort Select -->
                    <form method="GET" action="{{ route('collection') }}" class="inline-block">
                        @if($category)
                            <input type="hidden" name="category" value="{{ $category }}">
                        @endif
                        @if($saleOnly)
                            <input type="hidden" name="sale" value="1">
                        @endif
                        @if($search)
                            <input type="hidden" name="search" value="{{ $search }}">
                        @endif
                        <select name="sort" onchange="this.form.submit()" 
                                class="px-3 py-2 text-xs rounded-full border border-[#d4cdbf] bg-[#fbfaf8] text-[#334155] font-semibold outline-none cursor-pointer">
                            <option value="latest" @selected($sort === 'latest')>Terbaru</option>
                            <option value="discount" @selected($sort === 'discount')>Diskon Terbesar</option>
                            <option value="price_asc" @selected($sort === 'price_asc')>Harga: Termurah</option>
                            <option value="price_desc" @selected($sort === 'price_desc')>Harga: Termahal</option>
                        </select>
                    </form>
                </div>
            </div>

            <!-- Active Filter Indicators -->
            @if($category || $search || $saleOnly)
                <div class="flex items-center gap-2 mb-8 text-xs text-[#64748b]">
                    <span>Filter aktif:</span>
                    @if($category)
                        <span class="inline-flex items-center gap-1 bg-[#f4f2ee] px-2.5 py-1 rounded-full font-bold text-[#0d131a]">
                            Kategori: {{ $category }}
                            <a href="{{ route('collection', ['search' => $search, 'sale' => $saleOnly ? 1 : null]) }}" class="ml-1 text-gray-400 hover:text-gray-600">&times;</a>
                        </span>
                    @endif
                    @if($saleOnly)
                        <span class="inline-flex items-center gap-1 bg-[#fef2f2] text-[#c94a29] px-2.5 py-1 rounded-full font-bold">
                            Hanya Diskon
                            <a href="{{ route('collection', ['category' => $category, 'search' => $search]) }}" class="ml-1 hover:text-red-800">&times;</a>
                        </span>
                    @endif
                    @if($search)
                        <span class="inline-flex items-center gap-1 bg-[#f4f2ee] px-2.5 py-1 rounded-full font-bold text-[#0d131a]">
                            "{{ $search }}"
                            <a href="{{ route('collection', ['category' => $category, 'sale' => $saleOnly ? 1 : null]) }}" class="ml-1 text-gray-400 hover:text-gray-600">&times;</a>
                        </span>
                    @endif
                    <a href="{{ route('collection') }}" class="text-[#c94a29] underline ml-2 font-semibold">Reset Semua</a>
                </div>
            @endif

            <!-- Products Grid with Smooth Reveal Animation -->
            <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-3 gap-8">
                @forelse($products as $index => $product)
                    <x-product-card :product="$product" :delay="($index % 3 + 1) * 100" />
                @empty
                    <div class="col-span-full py-20 text-center bg-[#fbfaf8] rounded-xl border border-dashed border-[#d4cdbf]">
                        <span class="text-4xl block mb-3">◫</span>
                        <h3 class="text-lg font-bold text-[#0d131a]">Tidak ada pakaian yang sesuai kriteria</h3>
                        <p class="text-xs text-[#7b8893] mt-1 max-w-sm mx-auto">
                            Coba ubah kata kunci pencarian atau reset filter untuk melihat seluruh koleksi kami.
                        </p>
                        <a href="{{ route('collection') }}" class="btn-luxury-outline text-xs mt-6 inline-flex">
                            Lihat Semua Koleksi ↗
                        </a>
                    </div>
                @endforelse
            </div>

            <!-- Pagination -->
            <div class="mt-16 flex justify-center">
                {{ $products->links() }}
            </div>
        </div>
    </section>
@endsection
