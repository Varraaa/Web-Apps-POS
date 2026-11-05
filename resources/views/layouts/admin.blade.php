<!DOCTYPE html>
<html lang="id">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>@yield('title', 'Admin Studio & POS — VarStore')</title>
    <script>
        if (localStorage.getItem('varstore_theme') === 'dark') {
            document.documentElement.classList.add('theme-dark');
        }
    </script>
    @vite(['resources/css/app.css', 'resources/js/app.js'])
</head>

<body class="admin-body">
    <div class="admin-shell">
        <aside class="admin-sidebar">
            <a class="admin-brand" href="{{ route('admin.dashboard') }}">
                <span>V</span> VarStore <small>POS ATELIER</small>
            </a>

            <div class="admin-profile">
                <div class="admin-avatar">
                    {{ strtoupper(substr(auth()->user()->name ?? 'AD', 0, 2)) }}
                </div>
                <div>
                    <strong>{{ auth()->user()->name ?? 'Administrator' }}</strong>
                    <small>Shift Kasir & Manajer</small>
                </div>
            </div>

            <p class="admin-nav-label">Operasional Kasir</p>
            <nav class="admin-nav">
                <a class="{{ request()->routeIs('admin.pos.*') ? 'admin-nav-active' : '' }}"
                    href="{{ route('admin.pos.index') }}" style="background: rgba(16, 185, 129, 0.08); border-left: 3px solid #10b981;">
                    <span style="color: #10b981;">🖥</span> Terminal Kasir <small class="text-[9px] bg-slate-200 text-slate-700 px-1 py-0.5 rounded font-mono font-bold ml-auto">F4</small>
                </a>
            </nav>

            <p class="admin-nav-label">Manajemen Toko</p>
            <nav class="admin-nav">
                <a class="{{ request()->routeIs('admin.dashboard') ? 'admin-nav-active' : '' }}"
                    href="{{ route('admin.dashboard') }}">
                    <span>▦</span> Ringkasan
                </a>
                <a class="{{ request()->routeIs('admin.products.*') ? 'admin-nav-active' : '' }}"
                    href="{{ route('admin.products.index') }}">
                    <span>◫</span> Katalog Produk
                </a>
                <a class="{{ request()->routeIs('admin.orders.*') ? 'admin-nav-active' : '' }}"
                    href="{{ route('admin.orders.index') }}">
                    <span>□</span> Pesanan & Transaksi
                </a>
                <a class="{{ request()->routeIs('admin.discounts.*') ? 'admin-nav-active' : '' }}"
                    href="{{ route('admin.discounts.index') }}">
                    <span>🏷</span> Voucher Diskon
                </a>
                <a class="{{ request()->routeIs('admin.customers.*') ? 'admin-nav-active' : '' }}"
                    href="{{ route('admin.customers.index') }}">
                    <span>👥</span> Member & Pelanggan
                </a>
                <a class="{{ request()->routeIs('admin.reports.*') ? 'admin-nav-active' : '' }}"
                    href="{{ route('admin.reports.index') }}">
                    <span>📊</span> Laporan & Rekap
                </a>
                <a class="{{ request()->routeIs('admin.settings.*') ? 'admin-nav-active' : '' }}"
                    href="{{ route('admin.settings.index') }}">
                    <span>⚙</span> Pengaturan Toko
                </a>
            </nav>

            <div class="admin-sidebar-bottom">
                <button type="button" id="theme-toggle-btn" class="w-full text-left font-semibold text-xs text-slate-400 hover:text-white flex items-center gap-1.5 mb-3 cursor-pointer">
                    <span id="theme-icon">🌙</span> <span id="theme-text">Mode Gelap</span>
                </button>
                <a href="{{ route('home') }}" class="text-xs font-semibold text-slate-400 hover:text-white flex items-center gap-1.5 mb-3" target="_blank">
                    <span>↗</span> Buka Toko Online
                </a>
                <form method="POST" action="{{ route('logout') }}">
                    @csrf
                    <button class="w-full text-left font-bold text-xs text-rose-400 hover:text-rose-300">
                        🚪 Keluar Akun
                    </button>
                </form>
            </div>
        </aside>

        <main class="admin-main">
            <header class="admin-topbar">
                <div class="admin-mobile-brand">
                    <a href="{{ route('admin.dashboard') }}" class="font-extrabold text-slate-900">
                        VarStore <span>/ POS Admin</span>
                    </a>
                </div>

                <div class="flex items-center gap-3">
                    <a href="{{ route('admin.pos.index') }}" class="pos-btn-pay text-xs py-1.5 px-3 hidden sm:flex items-center gap-1.5">
                        <span>🖥</span> Kasir POS
                    </a>
                    <a href="{{ route('home') }}" target="_blank" class="pos-btn-ghost text-xs py-1.5 px-3 hidden sm:inline-block">
                        Lihat Toko ↗
                    </a>
                    <div class="admin-top-actions">
                        <span class="text-xs font-semibold text-slate-600 hidden md:inline">{{ now()->format('d M Y') }}</span>
                        <div class="admin-avatar">
                            {{ strtoupper(substr(auth()->user()->name ?? 'AD', 0, 2)) }}
                        </div>
                    </div>
                </div>
            </header>

            <div class="admin-content">
                @yield('content')
            </div>
        </main>
    </div>

    <!-- Global Toast Container Script -->
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
</body>

</html>
