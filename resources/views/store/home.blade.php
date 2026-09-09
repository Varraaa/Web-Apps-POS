@extends('layouts.store')
@section('title', 'VarStore — Haute Fashion Essentials & Contemporary Pieces')

@section('content')
    <!-- Hero Section Editorial -->
    <section class="hero-editorial">
        <div class="hero-editorial-copy reveal-on-scroll">
            <span class="eyebrow-luxury">New Season SS26 / Capsule Debut</span>
            <h1 class="headline-editorial">
                Dress the day<br>
                <em>your own way.</em>
            </h1>
            <p class="lead-editorial">
                Siluet kontemporer dengan proporsi sempurna dan detail tersembunyi. Koleksi busana esensial yang dirancang untuk menemani setiap langkah percaya diri Anda.
            </p>
            <div class="flex flex-wrap items-center gap-4">
                <a href="{{ route('collection') }}" class="btn-luxury-dark">
                    <span>Explore Collection</span>
                    <span>↗</span>
                </a>
                <a href="{{ route('collection', ['sale' => 1]) }}" class="btn-luxury-outline">
                    <span>Lihat Promo Diskon</span>
                    <span class="text-[#c94a29]">★</span>
                </a>
            </div>
            
            <div class="mt-12 flex items-center gap-6 pt-8 border-t border-[#e2ded5]">
                <div>
                    <span class="block text-xl font-bold text-[#0d131a]">100%</span>
                    <span class="text-xs text-[#7b8893]">Pure Organic Cotton</span>
                </div>
                <div class="w-px h-8 bg-[#e2ded5]"></div>
                <div>
                    <span class="block text-xl font-bold text-[#0d131a]">SS/26</span>
                    <span class="text-xs text-[#7b8893]">Editorial Edition</span>
                </div>
                <div class="w-px h-8 bg-[#e2ded5]"></div>
                <div>
                    <span class="block text-xl font-bold text-[#0d131a]">Instant</span>
                    <span class="text-xs text-[#7b8893]">WhatsApp Checkout POS</span>
                </div>
            </div>
        </div>

        <div class="hero-editorial-image reveal-scale delay-200">
            <img src="https://images.unsplash.com/photo-1490481651871-ab68de25d43d?auto=format&fit=crop&w=1400&q=85" 
                 alt="VarStore Luxury Fashion Lookbook" 
                 class="w-full h-full object-cover">
            <div class="absolute bottom-8 right-8 bg-[#0d131a]/85 backdrop-blur-md text-white p-5 border-l-2 border-[#dfbe95] max-w-xs hidden sm:block">
                <span class="text-[10px] uppercase font-bold tracking-widest text-[#dfbe95]">Curated Piece</span>
                <p class="text-xs font-semibold mt-1">The Essential Overshirt in Cloud White</p>
                <a href="{{ route('collection') }}" class="text-[11px] text-[#dfbe95] hover:underline mt-2 inline-block font-bold">Discover details ↗</a>
            </div>
        </div>
    </section>

    <!-- Exclusive Promo Vouchers Section (Hal Utama Diskon) -->
    <section class="py-20 px-[clamp(20px,5vw,80px)] bg-[#ffffff] border-y border-[#eeebe5]">
        <div class="max-w-7xl mx-auto">
            <div class="flex flex-col md:flex-row md:items-end justify-between gap-6 mb-12 reveal-on-scroll">
                <div>
                    <span class="eyebrow-luxury">Penawaran Spesial Pembeli</span>
                    <h2 class="text-3xl sm:text-4xl font-extrabold text-[#0d131a] tracking-tight">
                        Klaim Voucher Diskon Hari Ini
                    </h2>
                    <p class="text-sm text-[#64748b] mt-2 max-w-xl">
                        Salin kode voucher di bawah dan gunakan saat checkout di tas belanja untuk mendapatkan harga yang jauh lebih hemat.
                    </p>
                </div>
                <a href="{{ route('collection', ['sale' => 1]) }}" class="text-xs font-extrabold text-[#c9a982] hover:text-[#0d131a] tracking-wider uppercase flex items-center gap-2 transition-colors">
                    <span>Semua Produk Promo</span>
                    <span>→</span>
                </a>
            </div>

            <div class="voucher-grid">
                @forelse($activeCoupons as $index => $voucher)
                    <div class="voucher-card reveal-on-scroll delay-{{ ($index + 1) * 100 }}">
                        <div>
                            <div class="flex items-center justify-between">
                                <span class="voucher-badge">{{ $voucher->formatted_discount }}</span>
                                <span class="text-[11px] text-emerald-700 font-bold bg-emerald-50 px-2 py-0.5 rounded-full">Aktif</span>
                            </div>
                            <h3 class="voucher-title">{{ $voucher->name }}</h3>
                            <p class="voucher-desc">{{ $voucher->description }}</p>
                            @if($voucher->min_spend > 0)
                                <p class="text-[11px] text-[#8c9ba5] mt-2 font-medium">
                                    Min. belanja: <strong>Rp {{ number_format($voucher->min_spend, 0, ',', '.') }}</strong>
                                </p>
                            @endif
                        </div>

                        <div class="voucher-footer">
                            <div class="flex flex-col">
                                <span class="text-[10px] text-[#94a3b8] uppercase tracking-wider font-bold">Kode Promo</span>
                                <span class="voucher-code-display">{{ $voucher->code }}</span>
                            </div>
                            <button type="button" class="btn-copy-code" data-copy-coupon="{{ $voucher->code }}">
                                Salin Kode
                            </button>
                        </div>
                    </div>
                @empty
                    <div class="voucher-card reveal-on-scroll">
                        <div>
                            <span class="voucher-badge">20% OFF</span>
                            <h3 class="voucher-title">Spesial New Season 2026</h3>
                            <p class="voucher-desc">Gunakan voucher spesial untuk potongan harga langsung pada pesanan Anda.</p>
                        </div>
                        <div class="voucher-footer">
                            <span class="voucher-code-display">VARRA20</span>
                            <button type="button" class="btn-copy-code" data-copy-coupon="VARRA20">Salin Kode</button>
                        </div>
                    </div>
                @endforelse
            </div>
        </div>
    </section>

    <!-- Brand Philosophy & Point of View -->
    <section class="py-24 px-[clamp(20px,6vw,90px)] bg-[#f7f5f0]">
        <div class="max-w-7xl mx-auto grid grid-cols-1 lg:grid-cols-2 gap-16 items-center">
            <div class="reveal-on-scroll">
                <span class="eyebrow-luxury">The VarStore Manifesto</span>
                <h2 class="text-4xl sm:text-5xl font-extrabold text-[#0d131a] leading-tight tracking-tight mt-3 mb-6">
                    Less noise.<br>
                    <span class="font-serif italic font-normal text-[#927555]">More intention.</span>
                </h2>
                <p class="text-base text-[#5a6672] leading-relaxed mb-6">
                    Kami percaya bahwa pakaian terbaik bukanlah yang paling mencolok, melainkan yang paling jujur menemani karakter Anda. Setiap rancangan VarStore dirancang dengan ketelitian rasio potong, tekstur kain pilihan, dan ketahanan pakai melintasi tren sesaat.
                </p>
                <div class="grid grid-cols-2 gap-6 pt-6 border-t border-[#e2ded5]">
                    <div>
                        <h4 class="text-xs uppercase font-extrabold text-[#0d131a] tracking-wider mb-1">Tailored Comfort</h4>
                        <p class="text-xs text-[#7b8893] leading-relaxed">Pola pakaian yang mengikuti kontur alami tubuh dengan keleluasaan gerak optimal.</p>
                    </div>
                    <div>
                        <h4 class="text-xs uppercase font-extrabold text-[#0d131a] tracking-wider mb-1">Direct POS Concierge</h4>
                        <p class="text-xs text-[#7b8893] leading-relaxed">Sistem kasir cerdas WhatsApp tanpa birokrasi, cepat, dan transparan.</p>
                    </div>
                </div>
            </div>

            <div class="relative reveal-scale delay-200">
                <div class="aspect-[4/5] rounded-lg overflow-hidden shadow-2xl bg-[#e3ded4]">
                    <img src="https://images.unsplash.com/photo-1483985988355-763728e1935b?auto=format&fit=crop&w=1100&q=85" 
                         alt="VarStore Atelier Craftsmanship" 
                         class="w-full h-full object-cover">
                </div>
                <div class="absolute -bottom-6 -left-6 bg-white p-6 shadow-xl border border-[#eeebe5] rounded-lg max-w-xs hidden sm:block">
                    <span class="text-3xl font-serif italic text-[#c9a982]">“</span>
                    <p class="text-xs text-[#4a5568] font-medium leading-relaxed">
                        Keanggunan busana berawal dari kenyamanan yang tak pernah Anda ragukan.
                    </p>
                    <span class="block mt-2 text-[10px] uppercase font-bold tracking-widest text-[#0d131a]">— Atelier Director</span>
                </div>
            </div>
        </div>
    </section>

    <!-- Featured Pieces Grid with Staggered Scroll Animation -->
    <section class="py-24 px-[clamp(20px,5vw,80px)] bg-[#ffffff]">
        <div class="max-w-7xl mx-auto">
            <div class="flex flex-col sm:flex-row sm:items-end justify-between gap-6 mb-14 reveal-on-scroll">
                <div>
                    <span class="eyebrow-luxury">A Closer Look</span>
                    <h2 class="text-3xl sm:text-4xl font-extrabold text-[#0d131a] tracking-tight mt-2">
                        Featured Creations
                    </h2>
                    <p class="text-sm text-[#718096] mt-1">Busana pilihan musim ini dengan penawaran diskon spesial.</p>
                </div>
                <a href="{{ route('collection') }}" class="btn-luxury-outline text-xs">
                    <span>Lihat Semua Katalog</span>
                    <span>↗</span>
                </a>
            </div>

            <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-3 gap-8">
                @foreach ($featured as $index => $product)
                    <x-product-card :product="$product" :delay="($index % 3 + 1) * 100" />
                @endforeach
            </div>
        </div>
    </section>

    <!-- Brand Numerical Milestones (Animated Counters) -->
    <section class="stat-counter-grid reveal-on-scroll">
        <div class="stat-counter-item">
            <div class="counter-value" data-target="15000" data-suffix="+">0</div>
            <div class="counter-label">Pieces Crafted</div>
        </div>
        <div class="stat-counter-item">
            <div class="counter-value" data-target="99" data-suffix="%">0</div>
            <div class="counter-label">Satisfaction Rate</div>
        </div>
        <div class="stat-counter-item">
            <div class="counter-value" data-target="4" data-suffix=" Ateliers">0</div>
            <div class="counter-label">Creative Studios</div>
        </div>
        <div class="stat-counter-item">
            <div class="counter-value" data-target="24" data-suffix="/7">0</div>
            <div class="counter-label">WhatsApp POS Concierge</div>
        </div>
    </section>

    <!-- Curated On-Sale Spotlight (Diskon Khusus) -->
    @if(isset($saleProducts) && $saleProducts->isNotEmpty())
        <section class="py-24 px-[clamp(20px,5vw,80px)] bg-[#fbfaf8] border-b border-[#eeebe5]">
            <div class="max-w-7xl mx-auto">
                <div class="flex items-center justify-between mb-12 reveal-on-scroll">
                    <div>
                        <span class="text-xs uppercase font-extrabold tracking-widest text-[#c94a29] flex items-center gap-2">
                            <span class="w-2 h-2 rounded-full bg-[#c94a29] animate-ping"></span>
                            Sedang Berlangsung
                        </span>
                        <h2 class="text-3xl font-extrabold text-[#0d131a] tracking-tight mt-2">
                            Limited Season Sale & Discounts
                        </h2>
                    </div>
                    <a href="{{ route('collection', ['sale' => 1]) }}" class="text-xs font-extrabold text-[#c94a29] hover:underline">
                        Lihat Semua Diskon ({{ $saleProducts->count() }}+) ↗
                    </a>
                </div>

                <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-6">
                    @foreach ($saleProducts as $index => $product)
                        <x-product-card :product="$product" :delay="($index + 1) * 100" />
                    @endforeach
                </div>
            </div>
        </section>
    @endif

    <!-- High-Impact Editorial Banner -->
    <section class="py-28 px-[clamp(20px,6vw,100px)] bg-[#0d131a] text-white relative overflow-hidden">
        <div class="absolute -right-20 -bottom-20 w-96 h-96 rounded-full bg-[#c9a982]/10 blur-3xl pointer-events-none"></div>
        <div class="max-w-5xl mx-auto flex flex-col md:flex-row md:items-end justify-between gap-10 relative z-10 reveal-on-scroll">
            <div>
                <span class="text-xs uppercase font-bold tracking-widest text-[#dfbe95]">Exclusive Styling & Concierge</span>
                <h2 class="text-4xl sm:text-6xl font-extrabold tracking-tight mt-3 mb-4 leading-none">
                    A uniform for<br>
                    <span class="font-serif italic font-normal text-[#dfbe95]">real, authentic life.</span>
                </h2>
                <p class="text-sm text-[#94a3b8] max-w-lg leading-relaxed">
                    Dapatkan potongan harga eksklusif untuk pembelian hari ini. Tim stylist kami siap membantu mencocokkan ukuran dan merekomendasikan kombinasi pakaian terbaik.
                </p>
            </div>
            <div class="flex flex-col sm:flex-row gap-4">
                <a href="{{ route('collection') }}" class="btn-luxury-gold">
                    <span>Shop All Pieces</span>
                    <span>↗</span>
                </a>
                <a href="https://wa.me/6281297577567?text={{ urlencode('Halo VarStore Concierge, saya ingin konsultasi promo diskon.') }}" target="_blank" rel="noopener noreferrer" class="btn-luxury-outline border-white text-white hover:bg-white hover:text-[#0d131a]">
                    <span>Chat Stylist</span>
                    <span>✦</span>
                </a>
            </div>
        </div>
    </section>
@endsection
