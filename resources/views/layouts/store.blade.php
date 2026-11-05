<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>@yield('title', 'VarStore — Haute Fashion Essentials & POS')</title>
    <meta name="description" content="Koleksi busana fashion kontemporer dengan siluet modern, potongan presisi, dan material terbaik. Belanja online & konfirmasi instan via POS WhatsApp Concierge.">
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    @vite(['resources/css/app.css', 'resources/js/app.js'])
</head>
<body class="bg-[#fbfaf8] text-[#111a24] antialiased selection:bg-[#dfbe95] selection:text-[#0d131a]">

    <!-- Infinite Scrolling Promo Marquee -->
    <aside class="top-marquee-bar" aria-label="Pengumuman promo">
        <div class="marquee-track">
            <div class="marquee-item">
                <span>✦ NEW SEASON SS26 DEBUT</span>
                <span>•</span>
                <span>GUNAKAN KODE <span class="highlight">VARRA20</span> UNTUK DISKON 20%</span>
                <span>•</span>
                <span>GRATIS ONGKOS KIRIM SE-INDONESIA MIN. BELANJA RP 400.000</span>
                <span>•</span>
                <span>KODE <span class="highlight">HEMAT50K</span> POTONGAN RP 50.000</span>
                <span>•</span>
                <span>WHATSAPP CONCIERGE POS READY 24/7</span>
            </div>
            <div class="marquee-item" aria-hidden="true">
                <span>✦ NEW SEASON SS26 DEBUT</span>
                <span>•</span>
                <span>GUNAKAN KODE <span class="highlight">VARRA20</span> UNTUK DISKON 20%</span>
                <span>•</span>
                <span>GRATIS ONGKOS KIRIM SE-INDONESIA MIN. BELANJA RP 400.000</span>
                <span>•</span>
                <span>KODE <span class="highlight">HEMAT50K</span> POTONGAN RP 50.000</span>
                <span>•</span>
                <span>WHATSAPP CONCIERGE POS READY 24/7</span>
            </div>
        </div>
    </aside>

    <!-- Luxury Glassmorphic Header -->
    <header class="store-header">
        <div class="brand-wrapper">
            <a class="wordmark" href="{{ route('home') }}">
                Var<span class="text-[#c9a982]">Store</span>
            </a>
            <span class="brand-subline">Haute POS & Atelier</span>
        </div>

        <nav class="desktop-nav" aria-label="Navigasi Utama">
            <a class="{{ request()->routeIs('home') ? 'nav-link-active' : '' }}" href="{{ route('home') }}">Home</a>
            <a class="{{ request()->routeIs('about') ? 'nav-link-active' : '' }}" href="{{ route('about') }}">About Atelier</a>
            <a class="{{ request()->routeIs('collection', 'shop') && !request('sale') ? 'nav-link-active' : '' }}" href="{{ route('collection') }}">Collection</a>
            <a class="{{ request('sale') ? 'nav-link-active' : '' }}" href="{{ route('collection', ['sale' => 1]) }}">
                Sale & Promo <span class="nav-badge-sale">Diskon</span>
            </a>
            <a class="{{ request()->routeIs('tracking.*') ? 'nav-link-active' : '' }}" href="{{ route('tracking.index') }}">
                Lacak Pesanan
            </a>
            @auth
                @if(auth()->user()->isAdmin())
                    <a href="{{ route('admin.pos.index') }}" class="px-3 py-1 rounded-full bg-slate-900 text-white font-extrabold text-xs inline-flex items-center gap-1.5 shadow-sm hover:bg-slate-800 transition-colors">
                        <span>🖥</span> Kasir POS
                    </a>
                    <a href="{{ route('admin.dashboard') }}" class="text-[#2563eb] font-extrabold text-xs">Admin Studio ↗</a>
                @endif
            @endauth
        </nav>

        <div class="header-actions">
            <a href="{{ route('wishlist.index') }}" class="bag-link text-slate-700 hover:text-slate-900" aria-label="Wishlist">
                <span class="text-rose-500 text-sm">♥</span>
                <span>Wishlist</span>
                @php $wishlistCount = count(session('wishlist', [])); @endphp
                <span class="bag-badge {{ $wishlistCount > 0 ? '' : 'hidden' }}" id="nav-wishlist-count">{{ $wishlistCount }}</span>
            </a>

            <a href="{{ route('cart.index') }}" class="bag-link" aria-label="Tas Belanja">
                <svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                    <path stroke-linecap="round" stroke-linejoin="round" d="M16 11V7a4 4 0 00-8 0v4M5 9h14l1 12H4L5 9z" />
                </svg>
                <span>Bag</span>
                <span class="bag-badge">{{ collect(session('cart', []))->sum('quantity') }}</span>
            </a>

            @auth
                <form method="POST" action="{{ route('logout') }}" class="inline">
                    @csrf
                    <button class="logout-link">
                        <span>Sign out</span>
                    </button>
                </form>
            @else
                <a href="{{ route('login') }}" class="login-link">Sign in</a>
            @endauth

            <button class="mobile-menu-button" type="button" aria-controls="mobile-menu" aria-expanded="false" aria-label="Buka Menu">
                <span></span>
                <span></span>
                <span></span>
            </button>
        </div>
    </header>

    <!-- Mobile Drawer Navigation -->
    <nav id="mobile-menu" class="mobile-nav" aria-label="Navigasi Mobile" hidden>
        <a class="{{ request()->routeIs('home') ? 'font-black text-[#c9a982]' : '' }}" href="{{ route('home') }}">Home</a>
        <a class="{{ request()->routeIs('about') ? 'font-black text-[#c9a982]' : '' }}" href="{{ route('about') }}">About Atelier</a>
        <a class="{{ request()->routeIs('collection', 'shop') ? 'font-black text-[#c9a982]' : '' }}" href="{{ route('collection') }}">All Collection</a>
        <a href="{{ route('collection', ['sale' => 1]) }}" class="flex items-center justify-between text-[#c94a29] font-bold">
            <span>Sale & Promo Vouchers</span>
            <span class="bg-[#c94a29] text-white text-[10px] px-2 py-0.5 rounded-full">Hemat s/d 25%</span>
        </a>
        <a href="{{ route('tracking.index') }}" class="{{ request()->routeIs('tracking.*') ? 'font-black text-[#c9a982]' : '' }}">Lacak Pesanan Pelanggan</a>
        <a href="{{ route('cart.index') }}" class="flex items-center justify-between">
            <span>Tas Belanja</span>
            <span class="bg-[#0d131a] text-white text-xs px-2 py-0.5 rounded-full">{{ collect(session('cart', []))->sum('quantity') }}</span>
        </a>
        @auth
            @if(auth()->user()->isAdmin())
                <a href="{{ route('admin.pos.index') }}" class="font-extrabold text-emerald-700 bg-emerald-50 px-3 py-1.5 rounded-lg flex items-center justify-between">
                    <span>🖥 Buka Terminal Kasir POS</span>
                    <span>→</span>
                </a>
                <a href="{{ route('admin.dashboard') }}" class="text-[#2563eb]">Admin Studio ↗</a>
            @endif
            <form method="POST" action="{{ route('logout') }}" class="pt-2">
                @csrf
                <button class="text-sm font-semibold text-gray-500">Sign out ({{ auth()->user()->name }})</button>
            </form>
        @else
            <a href="{{ route('login') }}">Sign in / Akun</a>
        @endauth
    </nav>

    <!-- Flash message alerts auto-triggering toast -->
    @if(session('success'))
        <script>
            document.addEventListener('DOMContentLoaded', () => {
                if (window.showToast) window.showToast(@json(session('success')), 'success');
            });
        </script>
    @endif
    @if(session('error'))
        <script>
            document.addEventListener('DOMContentLoaded', () => {
                if (window.showToast) window.showToast(@json(session('error')), 'error');
            });
        </script>
    @endif

    <!-- Main Content Injection -->
    <main>
        @yield('content')
    </main>

    <!-- Luxury Footer -->
    <footer class="store-footer">
        <div class="footer-grid">
            <div>
                <a class="footer-brand" href="{{ route('home') }}">
                    <span class="text-white font-extrabold text-2xl tracking-tight">Var</span><span class="brand-accent text-[#dfbe95] font-extrabold text-2xl tracking-tight">Store</span>
                </a>
                <p class="mt-4 text-sm text-[#94a3b8] leading-relaxed max-w-sm">
                    Fashion house kontemporer yang mengutamakan proporsi sempurna, detail tersembunyi, dan kenyamanan sehari-hari. Didukung sistem Point of Sale instan via WhatsApp Concierge.
                </p>
                <div class="mt-6 flex items-center gap-3">
                    <span class="inline-block w-2.5 h-2.5 rounded-full bg-emerald-500 animate-pulse"></span>
                    <span class="text-xs text-emerald-400 font-semibold">POS Concierge Active & Ready</span>
                </div>
            </div>

            <div>
                <p class="footer-label">Navigation</p>
                <div class="footer-links">
                    <a href="{{ route('home') }}">Home Front</a>
                    <a href="{{ route('about') }}">About Atelier</a>
                    <a href="{{ route('collection') }}">Browse All Pieces</a>
                    <a href="{{ route('collection', ['sale' => 1]) }}">Promo & Diskon Eksklusif</a>
                    <a href="{{ route('cart.index') }}">Tas Belanja & Checkout</a>
                </div>
            </div>

            <div>
                <p class="footer-label">Kategori</p>
                <div class="footer-links">
                    <a href="{{ route('collection', ['category' => 'Outerwear']) }}">Outerwear & Blazers</a>
                    <a href="{{ route('collection', ['category' => 'Tops']) }}">Tops & Shirts</a>
                    <a href="{{ route('collection', ['category' => 'Bottoms']) }}">Trousers & Denim</a>
                    <a href="{{ route('collection', ['category' => 'Accessories']) }}">Accessories & Leather</a>
                </div>
            </div>

            <div>
                <p class="footer-label">VIP Concierge & Order</p>
                <p class="text-xs text-[#94a3b8] leading-relaxed mb-4">
                    Butuh bantuan memilih ukuran, styling busana, atau klaim voucher diskon? Hubungi fashion stylist kami secara langsung.
                </p>
                <a href="https://wa.me/6281297577567?text={{ urlencode('Halo VarStore Concierge, saya ingin konsultasi produk dan klaim promo.') }}" target="_blank" rel="noopener noreferrer" class="btn-luxury-gold text-xs w-full">
                    Chat WhatsApp Stylist ↗
                </a>
                <p class="text-[11px] text-[#64748b] mt-3">Layanan setiap hari pukul 09:00 - 22:00 WIB</p>
            </div>
        </div>

        <div class="footer-bottom">
            <p>© {{ date('Y') }} VarStore. All rights reserved. Crafted for the intentional wardrobe.</p>
            <div class="flex items-center gap-6">
                <span>Secure WhatsApp POS</span>
                <span>•</span>
                <span>Garansi Tukar Ukuran 7 Hari</span>
                <span>•</span>
                <span>100% Original Craftsmanship</span>
            </div>
        </div>
    </footer>

    <!-- Floating Back to Top Button -->
    <button class="back-to-top-btn" aria-label="Kembali ke atas">
        <svg xmlns="http://www.w3.org/2000/svg" width="18" height="18" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2.5">
            <path stroke-linecap="round" stroke-linejoin="round" d="M5 10l7-7m0 0l7 7m-7-7v18" />
        </svg>
    </button>

    <!-- Modern Mobile Bottom Navigation Bar -->
    <nav class="md:hidden fixed bottom-0 left-0 right-0 z-50 bg-white/95 backdrop-blur-md border-t border-[#e2ded5] px-4 py-2 flex items-center justify-around shadow-lg">
        <a href="{{ route('home') }}" class="flex flex-col items-center text-[10px] {{ request()->routeIs('home') ? 'font-black text-[#0d131a]' : 'text-slate-400' }}">
            <span class="text-base">🏠</span>
            <span>Home</span>
        </a>
        <a href="{{ route('collection') }}" class="flex flex-col items-center text-[10px] {{ request()->routeIs('collection', 'shop') ? 'font-black text-[#0d131a]' : 'text-slate-400' }}">
            <span class="text-base">👗</span>
            <span>Koleksi</span>
        </a>
        <a href="{{ route('tracking.index') }}" class="flex flex-col items-center text-[10px] {{ request()->routeIs('tracking.*') ? 'font-black text-[#0d131a]' : 'text-slate-400' }}">
            <span class="text-base">🔍</span>
            <span>Lacak</span>
        </a>
        <a href="{{ route('cart.index') }}" class="flex flex-col items-center text-[10px] relative {{ request()->routeIs('cart.*') ? 'font-black text-[#0d131a]' : 'text-slate-400' }}">
            <span class="text-base">🛍</span>
            <span>Tas</span>
            @if(collect(session('cart', []))->sum('quantity') > 0)
                <span class="absolute -top-1 -right-1 w-4 h-4 bg-[#0d131a] text-white rounded-full flex items-center justify-center text-[9px] font-bold">
                    {{ collect(session('cart', []))->sum('quantity') }}
                </span>
            @endif
        </a>
        @auth
            @if(auth()->user()->isAdmin())
                <a href="{{ route('admin.pos.index') }}" class="flex flex-col items-center text-[10px] font-bold text-emerald-700">
                    <span class="text-base">🖥</span>
                    <span>POS</span>
                </a>
            @else
                <form method="POST" action="{{ route('logout') }}" class="inline">
                    @csrf
                    <button class="flex flex-col items-center text-[10px] text-slate-400">
                        <span class="text-base">🚪</span>
                        <span>Keluar</span>
                    </button>
                </form>
            @endif
        @else
            <a href="{{ route('login') }}" class="flex flex-col items-center text-[10px] {{ request()->routeIs('login') ? 'font-black text-[#0d131a]' : 'text-slate-400' }}">
                <span class="text-base">👤</span>
                <span>Masuk</span>
            </a>
        @endauth
    </nav>

</body>
</html>
