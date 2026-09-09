@extends('layouts.store')
@section('title', 'About VarStore — Haute Fashion & POS')

@section('content')
    <!-- About Atelier Hero Section -->
    <section class="py-24 px-[clamp(20px,6vw,90px)] bg-[#f7f5f0] border-b border-[#eeebe5] relative overflow-hidden">
        <div class="max-w-6xl mx-auto">
            <div class="reveal-on-scroll">
                <span class="eyebrow-luxury">Our Story & Atelier</span>
                <h1 class="text-4xl sm:text-6xl lg:text-7xl font-extrabold text-[#0d131a] tracking-tight leading-[1.02] mt-3 mb-8">
                    Clothes crafted with a<br>
                    <span class="font-serif italic font-normal text-[#927555]">distinct, quiet conviction.</span>
                </h1>
                <p class="text-base sm:text-lg text-[#5a6672] max-w-2xl leading-relaxed">
                    VarStore lahir dari keinginan mendasar: menghadirkan busana harian yang menghilangkan keraguan di depan cermin. Setiap potongan dirancang dengan perpaduan siluet arsitektural dan kenyamanan tanpa kompromi.
                </p>
            </div>

            <div class="grid grid-cols-1 md:grid-cols-3 gap-6 mt-16 pt-12 border-t border-[#e2ded5] reveal-on-scroll delay-100">
                <div>
                    <span class="text-xs uppercase font-extrabold tracking-widest text-[#c9a982]">Est. 2024</span>
                    <h3 class="text-lg font-bold text-[#0d131a] mt-2">Independent Atelier</h3>
                    <p class="text-xs text-[#7b8893] mt-2 leading-relaxed">Berakar di Jakarta dengan dedikasi penuh pada seni jahit dan siluet kontemporer.</p>
                </div>
                <div>
                    <span class="text-xs uppercase font-extrabold tracking-widest text-[#c9a982]">Direct Point of Sale</span>
                    <h3 class="text-lg font-bold text-[#0d131a] mt-2">Seamless WhatsApp POS</h3>
                    <p class="text-xs text-[#7b8893] mt-2 leading-relaxed">Transaksi tanpa hambatan dengan konfirmasi ketersediaan barang real-time oleh staf kurator kami.</p>
                </div>
                <div>
                    <span class="text-xs uppercase font-extrabold tracking-widest text-[#c9a982]">Quality Standard</span>
                    <h3 class="text-lg font-bold text-[#0d131a] mt-2">Slow Fashion Philosophy</h3>
                    <p class="text-xs text-[#7b8893] mt-2 leading-relaxed">Produksi dalam kuantitas terukur untuk memastikan setiap jahitan memenuhi standar kesempurnaan tertinggi.</p>
                </div>
            </div>
        </div>
    </section>

    <!-- Visual Story & Craftsmanship Split Section -->
    <section class="py-24 px-[clamp(20px,6vw,90px)] bg-[#ffffff]">
        <div class="max-w-6xl mx-auto grid grid-cols-1 lg:grid-cols-2 gap-16 items-center">
            <div class="reveal-scale">
                <div class="relative aspect-[3/4] rounded-lg overflow-hidden shadow-xl bg-[#eeebe5]">
                    <img src="https://images.unsplash.com/photo-1551028719-00167b16eac5?auto=format&fit=crop&w=1200&q=85" 
                         alt="VarStore Studio Details" 
                         class="w-full h-full object-cover">
                    <div class="absolute top-6 left-6 bg-white/90 backdrop-blur-md px-4 py-2 rounded text-[11px] font-bold text-[#0d131a] uppercase tracking-wider">
                        Atelier Studio NO 01
                    </div>
                </div>
            </div>

            <div class="reveal-on-scroll delay-200">
                <span class="eyebrow-luxury">Our Process</span>
                <h2 class="text-3xl sm:text-5xl font-extrabold text-[#0d131a] tracking-tight leading-tight mt-3 mb-6">
                    Built slowly.<br>
                    <span class="font-serif italic font-normal text-[#927555]">Worn continuously.</span>
                </h2>
                <p class="text-sm sm:text-base text-[#5a6672] leading-relaxed mb-6">
                    Dari sketsa pertama hingga jahitan pengunci terakhir, kami menyaring setiap ide dengan cermat. Kami menolak fast fashion dan tren kilat yang usang dalam hitungan pekan. Koleksi kami difokuskan pada fleksibilitas bentuk, ketahanan material, serta warna-warna earthy yang memberi ruang bagi kepribadian Anda untuk bersinar.
                </p>

                <!-- Value List -->
                <div class="space-y-6 pt-6 border-t border-[#eeebe5]">
                    <div class="flex items-start gap-5">
                        <span class="font-mono text-xs font-black text-[#c9a982] bg-[#f7f5f0] px-3 py-1.5 rounded">01</span>
                        <div>
                            <h4 class="text-sm font-bold text-[#0d131a]">Tekstil & Serat Bersertifikasi</h4>
                            <p class="text-xs text-[#7b8893] mt-1 leading-relaxed">Kapas organik, linen alami, dan wol merino yang ramah di kulit serta breathable untuk iklim tropis.</p>
                        </div>
                    </div>
                    <div class="flex items-start gap-5">
                        <span class="font-mono text-xs font-black text-[#c9a982] bg-[#f7f5f0] px-3 py-1.5 rounded">02</span>
                        <div>
                            <h4 class="text-sm font-bold text-[#0d131a]">Jahitan Ganda & French Seam</h4>
                            <p class="text-xs text-[#7b8893] mt-1 leading-relaxed">Konstruksi interior pakaian yang sebersih bagian luarnya, menjamin kekuatan bahkan setelah ratusan kali pencucian.</p>
                        </div>
                    </div>
                    <div class="flex items-start gap-5">
                        <span class="font-mono text-xs font-black text-[#c9a982] bg-[#f7f5f0] px-3 py-1.5 rounded">03</span>
                        <div>
                            <h4 class="text-sm font-bold text-[#0d131a]">Jaminan Kepuasan & Diskon Berkelanjutan</h4>
                            <p class="text-xs text-[#7b8893] mt-1 leading-relaxed">Program diskon voucher berkala untuk pelanggan setia agar busana berkualitas selalu terjangkau.</p>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </section>

    <!-- Atelier Timeline Milestones -->
    <section class="py-24 px-[clamp(20px,6vw,90px)] bg-[#f7f5f0] border-t border-[#eeebe5]">
        <div class="max-w-5xl mx-auto">
            <div class="text-center max-w-xl mx-auto mb-16 reveal-on-scroll">
                <span class="eyebrow-luxury justify-center">The Journey</span>
                <h2 class="text-3xl sm:text-4xl font-extrabold text-[#0d131a] tracking-tight mt-2">
                    Milestones of Evolution
                </h2>
                <p class="text-xs sm:text-sm text-[#718096] mt-2">Langkah demi langkah menuju visi fashion yang penuh arti.</p>
            </div>

            <div class="grid grid-cols-1 md:grid-cols-3 gap-8">
                <div class="bg-white p-8 rounded-xl border border-[#e2ded5] shadow-sm reveal-on-scroll delay-100">
                    <span class="text-3xl font-extrabold font-serif italic text-[#c9a982]">2024</span>
                    <h3 class="text-base font-extrabold text-[#0d131a] mt-4 mb-2">The First Capsule</h3>
                    <p class="text-xs text-[#64748b] leading-relaxed">
                        Meluncurkan 8 pieces esensial pertama yang terjual habis dalam 48 jam melalui pesanan privat komunitas.
                    </p>
                </div>

                <div class="bg-white p-8 rounded-xl border border-[#e2ded5] shadow-sm reveal-on-scroll delay-200">
                    <span class="text-3xl font-extrabold font-serif italic text-[#c9a982]">2025</span>
                    <h3 class="text-base font-extrabold text-[#0d131a] mt-4 mb-2">Studio & POS Expansion</h3>
                    <p class="text-xs text-[#64748b] leading-relaxed">
                        Mendirikan atelier mandiri dan merancang sistem Point of Sale terpadu untuk memudahkan pelayanan pelanggan secara cepat.
                    </p>
                </div>

                <div class="bg-white p-8 rounded-xl border border-[#e2ded5] shadow-sm reveal-on-scroll delay-300">
                    <span class="text-3xl font-extrabold font-serif italic text-[#c9a982]">2026</span>
                    <h3 class="text-base font-extrabold text-[#0d131a] mt-4 mb-2">The Digital Atelier</h3>
                    <p class="text-xs text-[#64748b] leading-relaxed">
                        Menghadirkan platform digital interaktif dengan sistem voucher promo fleksibel dan layanan pengiriman ke seluruh Nusantara.
                    </p>
                </div>
            </div>
        </div>
    </section>

    <!-- Editorial Quote & Director Note -->
    <section class="py-28 px-6 bg-[#0d131a] text-white text-center relative overflow-hidden">
        <div class="max-w-4xl mx-auto relative z-10 reveal-on-scroll">
            <span class="text-4xl sm:text-5xl font-serif text-[#dfbe95] block mb-6">“</span>
            <blockquote class="text-2xl sm:text-4xl lg:text-5xl font-serif italic font-normal tracking-tight leading-snug text-[#f8fafc]">
                The best wardrobe is not the one with the most clothes, but the one where every piece feels unmistakably and effortlessly yours.
            </blockquote>
            <div class="mt-8">
                <span class="block text-xs uppercase font-extrabold tracking-[0.2em] text-[#dfbe95]">
                    VarStore Creative Direction
                </span>
                <span class="text-[11px] text-[#64748b] mt-1 block">Jakarta Atelier Note / SS26</span>
            </div>
        </div>
    </section>

    <!-- Visit Atelier & Stylist Concierge CTA -->
    <section class="py-20 px-[clamp(20px,6vw,90px)] bg-[#ffffff] border-t border-[#eeebe5]">
        <div class="max-w-5xl mx-auto flex flex-col md:flex-row items-center justify-between gap-10 reveal-on-scroll">
            <div>
                <span class="eyebrow-luxury">Private Consultation</span>
                <h2 class="text-2xl sm:text-3xl font-extrabold text-[#0d131a] tracking-tight mt-2">
                    Ingin Konsultasi Ukuran & Klaim Promo?
                </h2>
                <p class="text-xs sm:text-sm text-[#64748b] mt-2 max-w-lg">
                    Hubungi tim kasir & stylist kami di WhatsApp. Kami siap memberikan asistensi privat dan kode promo spesial untuk pesanan pertama Anda.
                </p>
            </div>
            <div class="flex items-center gap-4">
                <a href="{{ route('collection') }}" class="btn-luxury-outline text-xs">
                    Katalog Koleksi ↗
                </a>
                <a href="https://wa.me/6281297577567?text={{ urlencode('Halo VarStore, saya membaca halaman About dan ingin konsultasi pesanan serta promo.') }}" target="_blank" rel="noopener noreferrer" class="btn-luxury-dark text-xs">
                    Chat WhatsApp POS ↗
                </a>
            </div>
        </div>
    </section>
@endsection
