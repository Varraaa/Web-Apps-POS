@extends('layouts.admin')
@section('title', 'POS Kasir Terminal — VarStore')

@section('content')
<div class="pos-container" id="pos-app">
    <!-- Top POS Toolbar -->
    <div class="pos-topbar">
        <div class="flex items-center gap-3">
            <span class="pos-status-pulse"></span>
            <div>
                <h1 class="pos-title">Terminal Kasir POS</h1>
                <p class="pos-subtitle">Shift Aktif: <strong class="text-slate-800">{{ auth()->user()->name ?? 'Kasir Utama' }}</strong> · <span id="pos-live-clock">--:--:--</span></p>
            </div>
        </div>

        <div class="flex items-center gap-4">
            <!-- Shift Status Widget -->
            <div id="pos-shift-widget">
                @if($currentShift)
                    <div class="flex items-center gap-2 bg-emerald-50 border border-emerald-200 px-3 py-1.5 rounded-lg text-xs" id="pos-shift-active-box">
                        <span class="w-2 h-2 rounded-full bg-emerald-500 animate-pulse"></span>
                        <div>
                            <span class="font-bold text-emerald-900 block leading-tight">{{ $currentShift->cashier_name }}</span>
                            <span class="text-emerald-700 block text-[10px]">Modal: Rp {{ number_format($currentShift->starting_cash, 0, ',', '.') }}</span>
                        </div>
                        <button type="button" id="pos-btn-trigger-petty-cash" class="px-2 py-1 bg-white border border-emerald-300 text-emerald-800 font-semibold rounded text-[11px] hover:bg-emerald-100 transition" title="Catat Kas Masuk / Kas Keluar Operasional">
                            💸 Petty Cash
                        </button>
                        <button type="button" id="pos-btn-trigger-close-shift" class="ml-1 px-2.5 py-1 bg-emerald-700 text-white font-semibold rounded text-[11px] hover:bg-emerald-800 transition">
                            Tutup Shift / Z-Report
                        </button>
                    </div>
                @else
                    <div class="flex items-center gap-2 bg-amber-50 border border-amber-200 px-3 py-1.5 rounded-lg text-xs" id="pos-shift-inactive-box">
                        <span class="w-2 h-2 rounded-full bg-amber-500"></span>
                        <div>
                            <span class="font-bold text-amber-900 block leading-tight">Shift Belum Dibuka</span>
                            <span class="text-amber-700 block text-[10px]">Input modal kas awal</span>
                        </div>
                        <button type="button" id="pos-btn-trigger-open-shift" class="ml-2 px-2.5 py-1 bg-amber-600 text-white font-semibold rounded text-[11px] hover:bg-amber-700 transition">
                            Buka Kasir
                        </button>
                    </div>
                @endif
            </div>

            <div class="pos-shift-stat">
                <small>Penjualan POS Hari Ini</small>
                <strong>Rp {{ number_format($todaySales, 0, ',', '.') }}</strong>
                <span class="text-[10px] text-emerald-600 font-semibold">{{ $todayCount }} Transaksi</span>
            </div>
            <a href="{{ route('admin.orders.index') }}" class="pos-btn-ghost text-xs">
                <span>Riwayat Transaksi ↗</span>
            </a>
            <button type="button" id="pos-audio-toggle" class="pos-btn-ghost text-xs py-1.5 px-2.5 flex items-center gap-1.5" title="Suara Terminal Kasir">
                <span id="pos-audio-icon">🔊</span> <span id="pos-audio-label" class="hidden sm:inline text-[11px] font-bold">Audio</span>
            </button>
            <button type="button" id="pos-theme-toggle" class="pos-btn-ghost text-xs py-1.5 px-2.5 flex items-center gap-1.5" title="Ganti Tema Gelap / Terang">
                <span id="pos-theme-icon">🌙</span> <span id="pos-theme-label" class="hidden sm:inline text-[11px] font-bold">Gelap</span>
            </button>
        </div>
    </div>

    <!-- Main Register Workspace (2 Columns) -->
    <div class="pos-grid">
        <!-- Left: Product Catalog & Search (7 Cols) -->
        <section class="pos-catalog-panel">
            <!-- Filter & Search Toolbar -->
            <div class="pos-search-bar">
                <div class="relative flex-1">
                    <input type="text" id="pos-search-input" 
                           placeholder="Cari nama produk, warna, atau scan barcode... (F2)" 
                           class="pos-input-search">
                    <span class="pos-search-icon">🔍</span>
                    <button type="button" id="pos-clear-search" class="pos-clear-btn hidden" title="Hapus pencarian">&times;</button>
                </div>

                <!-- Category Pills -->
                <div class="pos-category-scroller">
                    <button type="button" class="pos-cat-pill active" data-category="">Semua</button>
                    @foreach($categories as $cat)
                        <button type="button" class="pos-cat-pill" data-category="{{ $cat }}">{{ $cat }}</button>
                    @endforeach
                </div>
            </div>

            <!-- Product Grid -->
            <div class="pos-products-grid" id="pos-products-container">
                @forelse($products as $product)
                    <div class="pos-product-card {{ $product->stock <= 0 ? 'is-out-of-stock' : '' }}" 
                         data-id="{{ $product->id }}"
                         data-sku="{{ $product->sku }}"
                         data-barcode="{{ $product->barcode }}"
                         data-name="{{ $product->name }}"
                         data-category="{{ $product->category }}"
                         data-price="{{ $product->final_price }}"
                         data-original-price="{{ $product->price }}"
                         data-stock="{{ $product->stock }}"
                         data-image="{{ $product->image_url }}"
                         data-color="{{ $product->color ?? '-' }}"
                         data-size="{{ $product->size ?? '-' }}"
                         data-wholesale-min="{{ $product->wholesale_min_qty ?? 0 }}"
                         data-wholesale-price="{{ $product->wholesale_price ?? 0 }}"
                         title="{{ $product->name }}">
                        <div class="pos-product-thumb">
                            <img src="{{ $product->image_url }}" alt="{{ $product->name }}" loading="lazy">
                            @if($product->has_discount)
                                <span class="pos-discount-tag">-{{ $product->discount_percent }}%</span>
                            @endif
                            <span class="pos-stock-badge {{ $product->stock <= 2 ? 'is-low' : '' }}">
                                {{ $product->stock > 0 ? $product->stock . ' stok' : 'Habis' }}
                            </span>
                        </div>
                        <div class="pos-product-info">
                            <span class="pos-product-cat">{{ $product->category }}</span>
                            <h3 class="pos-product-name">{{ $product->name }}</h3>
                            <div class="pos-product-pricing">
                                <span class="pos-current-price">Rp {{ number_format($product->final_price, 0, ',', '.') }}</span>
                                @if($product->has_discount)
                                    <span class="pos-old-price">Rp {{ number_format($product->price, 0, ',', '.') }}</span>
                                @endif
                            </div>
                            @if($product->has_wholesale_price)
                                <span class="block text-[10px] text-amber-800 bg-amber-50 border border-amber-200 px-1.5 py-0.5 rounded font-bold mt-1">
                                    🏷 Grosir: Min. {{ $product->wholesale_min_qty }} @ Rp {{ number_format($product->wholesale_price, 0, ',', '.') }}
                                </span>
                            @endif

                            <!-- Available Size Quick Variant Switcher -->
                            @if(!empty($product->available_sizes))
                                <div class="flex flex-wrap items-center gap-1 mt-1.5 pt-1 border-t border-slate-100">
                                    <span class="text-[9px] text-slate-400 font-semibold mr-0.5">Size:</span>
                                    @foreach($product->available_sizes as $sz)
                                        <button type="button" class="btn-quick-variant-size text-[9px] font-bold px-1.5 py-0.5 rounded bg-slate-100 hover:bg-slate-900 hover:text-white transition-colors text-slate-700" data-size="{{ $sz }}">
                                            {{ $sz }}
                                        </button>
                                    @endforeach
                                </div>
                            @endif
                        </div>
                    </div>
                @empty
                    <div class="col-span-full text-center py-12 text-slate-400">
                        Belum ada data produk di katalog.
                    </div>
                @endforelse
            </div>
        </section>

        <!-- Right: Active Ticket / Cart Panel (5 Cols) -->
        <aside class="pos-ticket-panel">
            <div class="pos-ticket-header">
                <div>
                    <h2 class="text-base font-bold text-slate-800">Tiket Pesanan Kasir</h2>
                    <span class="text-xs text-slate-500" id="pos-ticket-count">0 item dipilih</span>
                </div>
                <div class="flex items-center gap-1.5">
                    <button type="button" id="pos-btn-view-held" class="text-xs font-bold px-2 py-1 rounded bg-amber-50 text-amber-800 border border-amber-200 hover:bg-amber-100 transition-colors flex items-center gap-1">
                        <span>📂 Parkir:</span>
                        <span id="pos-held-count-badge" class="bg-amber-800 text-white text-[9px] px-1.5 py-0.2 rounded-full font-mono">0</span>
                    </button>
                    <button type="button" id="pos-btn-hold-bill" class="text-xs font-bold px-2 py-1 rounded bg-slate-100 text-slate-700 hover:bg-slate-200 border border-slate-200 transition-colors" title="Simpan / Parkir Tagihan Sementara">
                        Hold
                    </button>
                    <button type="button" id="pos-btn-clear-cart" class="pos-btn-danger-ghost text-xs">
                        Kosongkan
                    </button>
                </div>
            </div>

            <!-- Customer & Loyalty Member Meta -->
            <div class="pos-ticket-meta">
                <div class="flex items-center justify-between mb-1.5">
                    <span class="pos-meta-label">Pelanggan / Member VIP</span>
                    <button type="button" id="pos-btn-new-customer" class="text-[10px] font-bold text-emerald-700 bg-emerald-50 px-2 py-0.5 rounded border border-emerald-200 hover:bg-emerald-100 transition-colors">
                        + Member Baru
                    </button>
                </div>

                <!-- Member Search & Active Badge -->
                <div class="relative">
                    <input type="text" id="pos-member-search" placeholder="Cari Member (Nama / HP / ID)..." 
                           class="pos-meta-input text-xs" autocomplete="off">
                    <div id="pos-member-results" class="absolute left-0 right-0 top-full mt-1 bg-white border border-slate-200 rounded-lg shadow-xl max-h-48 overflow-y-auto z-30 hidden">
                        <!-- Dynamic Results -->
                    </div>
                </div>

                <!-- Selected Member Profile Card -->
                <div id="pos-selected-member-card" class="mt-2 p-2 bg-slate-100/90 rounded-lg border border-slate-200 hidden">
                    <div class="flex items-center justify-between">
                        <div>
                            <strong class="text-xs text-slate-900 block" id="selected-member-name">-</strong>
                            <div class="flex items-center gap-1.5 mt-0.5">
                                <span class="text-[9px] font-extrabold uppercase px-1.5 py-0.2 rounded bg-amber-100 text-amber-800" id="selected-member-tier">Bronze</span>
                                <span class="text-[10px] font-bold text-emerald-600" id="selected-member-points">★ 0 Poin</span>
                                <span class="text-[10px] font-bold text-blue-600" id="selected-member-credit">💳 Rp 0</span>
                            </div>
                        </div>
                        <button type="button" id="pos-remove-member" class="text-xs text-rose-500 font-bold hover:text-rose-700 p-1" title="Hapus member">
                            ✕
                        </button>
                    </div>

                    <!-- Points Redemption Toggle -->
                    <label class="flex items-center gap-2 mt-2 pt-2 border-t border-slate-200 cursor-pointer select-none">
                        <input type="checkbox" id="pos-redeem-points-check" class="rounded text-emerald-600 focus:ring-0">
                        <span class="text-[11px] text-slate-700 font-semibold" id="label-redeem-points">
                            Tukar Poin Belanja (Hemat Rp 0)
                        </span>
                    </label>

                    <!-- Store Credit Wallet Toggle -->
                    <label class="flex items-center gap-2 mt-1.5 pt-1.5 border-t border-slate-200 cursor-pointer select-none" id="row-store-credit-toggle">
                        <input type="checkbox" id="pos-use-store-credit-check" class="rounded text-blue-600 focus:ring-0">
                        <span class="text-[11px] text-slate-700 font-semibold" id="label-use-store-credit">
                            Gunakan Saldo Dompet Toko (Saldo Rp 0)
                        </span>
                    </label>
                </div>

                <div class="grid grid-cols-2 gap-2 mt-2" id="pos-walkin-inputs">
                    <div>
                        <input type="text" id="pos-customer-name" value="Pelanggan Walk-In" class="pos-meta-input text-[11px]" placeholder="Nama">
                    </div>
                    <div>
                        <input type="text" id="pos-customer-phone" placeholder="08xxxxxxxxxx" class="pos-meta-input text-[11px]">
                    </div>
                </div>

                <!-- Sales Associate / Fashion Consultant Attribution -->
                <div class="mt-2 pt-2 border-t border-slate-200">
                    <label class="block text-[10px] font-bold text-slate-500 uppercase tracking-wider mb-1">Konsultan Fashion / SPG Bertugas</label>
                    <div class="relative">
                        <input list="sales-associates-list" id="pos-sales-assistant" placeholder="Pilih / ketik nama konsultan..." class="pos-meta-input text-xs">
                        <datalist id="sales-associates-list">
                            <option value="Maya (Fashion Advisor)">
                            <option value="Dimas (Style Consultant)">
                            <option value="Aisyah (Senior Stylist)">
                            <option value="Rizky (Floor Associate)">
                        </datalist>
                    </div>
                </div>
            </div>

            <!-- Ticket Items Scrollable List -->
            <div class="pos-ticket-items" id="pos-ticket-items-container">
                <div class="pos-empty-ticket" id="pos-ticket-empty">
                    <span class="text-3xl mb-2">🛒</span>
                    <p class="font-semibold text-slate-700 text-sm">Tiket kasir masih kosong</p>
                    <p class="text-xs text-slate-400 mt-1">Klik item di katalog atau cari produk untuk mulai transaksi.</p>
                </div>
            </div>

            <!-- Ticket Financial Summary & Coupon -->
            <div class="pos-ticket-footer">
                <!-- Promo Coupon Selector -->
                <div class="pos-coupon-row">
                    <div class="flex items-center gap-2">
                        <select id="pos-coupon-select" class="pos-coupon-select">
                            <option value="">-- Pilih Voucher Diskon --</option>
                            @foreach($coupons as $coupon)
                                <option value="{{ $coupon->code }}" data-type="{{ $coupon->type }}" data-value="{{ $coupon->value }}" data-min="{{ $coupon->min_spend }}" data-max="{{ $coupon->max_discount }}">
                                    {{ $coupon->code }} ({{ $coupon->formatted_discount }})
                                </option>
                            @endforeach
                        </select>
                        <button type="button" id="pos-apply-coupon" class="pos-btn-apply-coupon">Pasang</button>
                    </div>
                </div>

                <!-- Custom Manager / Spontaneous Discount Trigger & Box -->
                <div class="mt-2 pt-2 border-t border-slate-100">
                    <button type="button" id="pos-btn-toggle-custom-discount" class="text-[10px] font-bold text-slate-500 hover:text-slate-900 flex items-center justify-between w-full">
                        <span>🏷 Diskon Khusus Kasir / Manajer</span>
                        <span id="pos-custom-disc-icon">▼</span>
                    </button>
                    <div id="pos-custom-discount-pane" class="hidden mt-2 p-2.5 bg-slate-50 rounded-lg border border-slate-200 space-y-2 text-xs">
                        <div class="flex gap-2">
                            <select id="pos-custom-disc-type" class="text-xs font-semibold p-1.5 rounded border border-slate-300 bg-white">
                                <option value="fixed">Nominal (Rp)</option>
                                <option value="percent">Persen (%)</option>
                            </select>
                            <input type="number" id="pos-custom-disc-value" placeholder="Nilai diskon..." min="0" class="w-full text-xs p-1.5 rounded border border-slate-300 bg-white">
                        </div>
                        <input type="text" id="pos-custom-disc-reason" placeholder="Alasan diskon (e.g. Diskon Khusus Manajer / Defect Minor)..." class="w-full text-xs p-1.5 rounded border border-slate-300 bg-white">
                        <div class="flex justify-end gap-1.5 pt-1">
                            <button type="button" id="pos-btn-clear-custom-disc" class="pos-btn-ghost text-[10px] py-1 px-2 text-rose-600">Hapus</button>
                            <button type="button" id="pos-btn-apply-custom-disc" class="pos-btn-pay text-[10px] py-1 px-3">Terapkan Diskon</button>
                        </div>
                    </div>
                </div>

                <div class="pos-summary-table">
                    <div class="pos-summary-row">
                        <span>Subtotal</span>
                        <strong id="pos-val-subtotal">Rp 0</strong>
                    </div>
                    <div class="pos-summary-row text-emerald-600" id="pos-row-discount" style="display: none;">
                        <span>Diskon (<span id="pos-discount-label">-</span>)</span>
                        <strong id="pos-val-discount">- Rp 0</strong>
                    </div>
                    <div class="pos-summary-row text-purple-600" id="pos-row-custom-discount" style="display: none;">
                        <span>Diskon Khusus (<span id="pos-custom-disc-summary-label">-</span>)</span>
                        <strong id="pos-val-custom-discount">- Rp 0</strong>
                    </div>
                    <div class="pos-summary-row text-blue-600" id="pos-row-credit" style="display: none;">
                        <span>Saldo Dompet Toko</span>
                        <strong id="pos-val-credit">- Rp 0</strong>
                    </div>
                    <div class="pos-summary-row pos-grand-total">
                        <span>TOTAL TAGIHAN</span>
                        <strong id="pos-val-grandtotal">Rp 0</strong>
                    </div>
                </div>

                <!-- Pay Button -->
                <button type="button" id="pos-btn-open-payment" class="pos-btn-pay" disabled>
                    <span>Lanjut ke Pembayaran (F4)</span>
                    <span class="text-lg">→</span>
                </button>
            </div>
        </aside>
    </div>
</div>

<!-- Modal Dialog Pembayaran Kasir (Include via separate component) -->
<div id="pos-payment-modal" class="pos-modal-backdrop hidden">
    <div class="pos-modal-card">
        <div class="pos-modal-header">
            <div>
                <h3 class="text-lg font-bold text-slate-900">Pembayaran Kasir POS</h3>
                <p class="text-xs text-slate-500">Pilih metode pembayaran dan masukkan jumlah uang yang diterima.</p>
            </div>
            <button type="button" id="pos-modal-close" class="pos-modal-close-btn">&times;</button>
        </div>

        <div class="pos-modal-body">
            <!-- Amount Due Banner -->
            <div class="pos-due-banner">
                <span class="text-xs text-slate-500 uppercase tracking-wider font-semibold">Total Tagihan</span>
                <h2 class="text-3xl font-extrabold text-slate-900" id="pos-modal-due">Rp 0</h2>
            </div>

            <!-- Payment Methods Tabs -->
            <div class="pos-pay-tabs">
                <button type="button" class="pos-pay-tab active" data-method="cash">💵 Uang Tunai (Cash)</button>
                <button type="button" class="pos-pay-tab" data-method="qris">📱 QRIS Instan</button>
                <button type="button" class="pos-pay-tab" data-method="transfer">🏦 Transfer Bank</button>
                <button type="button" class="pos-pay-tab" data-method="split">🔀 Bayar Campuran (Split)</button>
            </div>

            <!-- Tab 1: Cash Pane -->
            <div class="pos-pay-pane" id="pane-cash">
                <label class="block text-xs font-bold text-slate-700 mb-1">Uang Diterima dari Pelanggan (Rp)</label>
                <div class="relative mb-3">
                    <span class="absolute left-3.5 top-3 text-slate-400 font-bold text-sm">Rp</span>
                    <input type="number" id="pos-cash-input" class="pos-cash-input" placeholder="0" min="0" step="1000">
                </div>

                <!-- Quick Cash Presets -->
                <div class="pos-quick-cash-grid">
                    <button type="button" class="btn-cash-preset" id="preset-exact">Uang Pas</button>
                    <button type="button" class="btn-cash-preset" data-amount="50000">50.000</button>
                    <button type="button" class="btn-cash-preset" data-amount="100000">100.000</button>
                    <button type="button" class="btn-cash-preset" data-amount="200000">200.000</button>
                    <button type="button" class="btn-cash-preset" data-amount="500000">500.000</button>
                </div>

                <!-- Live Change Calculator -->
                <div class="pos-change-box">
                    <span class="text-xs font-bold text-slate-600">UANG KEMBALIAN</span>
                    <strong class="text-2xl font-black text-emerald-600" id="pos-cash-change">Rp 0</strong>
                </div>
            </div>

            <!-- Tab 2: QRIS Pane -->
            <div class="pos-pay-pane hidden" id="pane-qris">
                <div class="text-center py-4 bg-slate-50 rounded-xl border border-dashed border-slate-300">
                    <div class="w-48 h-48 mx-auto bg-white p-3 rounded-lg shadow-sm border border-slate-200 flex flex-col items-center justify-center">
                        <!-- Simulated QRIS Barcode Pattern -->
                        <div class="w-full h-full border-4 border-slate-800 p-2 flex flex-col justify-between">
                            <div class="flex justify-between">
                                <span class="w-6 h-6 bg-slate-800 rounded-sm"></span>
                                <span class="w-6 h-6 bg-slate-800 rounded-sm"></span>
                            </div>
                            <div class="text-center font-bold text-xs tracking-widest text-slate-700">QRIS STANDAR</div>
                            <div class="flex justify-between">
                                <span class="w-6 h-6 bg-slate-800 rounded-sm"></span>
                                <span class="text-[10px] text-slate-500 font-mono">NMID: ID102938</span>
                            </div>
                        </div>
                    </div>
                    <p class="text-xs text-slate-600 font-semibold mt-3">Arahkan kamera aplikasi GoPay, OVO, BCA, atau Livin' ke kode QR</p>
                    <span class="text-[11px] text-slate-400">Verifikasi otomatis langsung oleh terminal</span>
                </div>
            </div>

            <!-- Tab 3: Transfer Pane -->
            <div class="pos-pay-pane hidden" id="pane-transfer">
                <div class="space-y-2">
                    <div class="p-3 rounded-lg border border-slate-200 bg-slate-50 flex items-center justify-between">
                        <div>
                            <span class="text-[10px] font-bold text-blue-600 uppercase">BCA Virtual Account</span>
                            <div class="font-mono font-bold text-sm text-slate-800">8801-9283-7462</div>
                            <small class="text-slate-500 text-[10px]">a.n VarStore Official Store</small>
                        </div>
                        <span class="text-xs bg-blue-100 text-blue-700 px-2 py-1 rounded font-bold">Siap Pakai</span>
                    </div>
                    <div class="p-3 rounded-lg border border-slate-200 bg-slate-50 flex items-center justify-between">
                        <div>
                            <span class="text-[10px] font-bold text-amber-600 uppercase">Bank Mandiri</span>
                            <div class="font-mono font-bold text-sm text-slate-800">132-00-987123-4</div>
                            <small class="text-slate-500 text-[10px]">a.n VarStore Indonesia</small>
                        </div>
                        <span class="text-xs bg-amber-100 text-amber-700 px-2 py-1 rounded font-bold">Siap Pakai</span>
                    </div>
                </div>
            </div>

            <!-- Tab 4: Split Payment Pane -->
            <div class="pos-pay-pane hidden" id="pane-split">
                <div class="p-3 bg-slate-50 border border-slate-200 rounded-xl space-y-3">
                    <div class="text-[11px] text-slate-500 font-medium">Bagi total tagihan ke dua metode pembayaran berbeda (misal Tunai + QRIS).</div>
                    <div>
                        <div class="flex items-center justify-between text-xs mb-1">
                            <span class="font-bold text-slate-700">Metode Utama</span>
                            <span class="text-xs font-semibold px-2 py-0.5 rounded bg-slate-200 text-slate-800">💵 Uang Tunai</span>
                        </div>
                        <div class="relative">
                            <span class="absolute left-3 top-2.5 text-slate-400 font-bold text-xs">Rp</span>
                            <input type="number" id="pos-split-primary-amount" class="w-full pl-9 pr-3 py-2 text-sm font-bold rounded-lg border border-slate-300 focus:ring-2 focus:ring-slate-900 focus:outline-none" placeholder="0" min="0" step="1000">
                        </div>
                    </div>

                    <div class="pt-2 border-t border-slate-200">
                        <div class="flex items-center justify-between text-xs mb-1">
                            <span class="font-bold text-slate-700">Metode Sekunder</span>
                            <select id="pos-split-secondary-method" class="text-xs font-semibold py-1 px-2 border border-slate-300 rounded bg-white focus:outline-none">
                                <option value="qris">📱 QRIS Instan</option>
                                <option value="transfer">🏦 Transfer Bank</option>
                            </select>
                        </div>
                        <div class="relative">
                            <span class="absolute left-3 top-2.5 text-slate-400 font-bold text-xs">Rp</span>
                            <input type="number" id="pos-split-secondary-amount" class="w-full pl-9 pr-3 py-2 text-sm font-bold rounded-lg border border-slate-300 focus:ring-2 focus:ring-slate-900 focus:outline-none" placeholder="0" min="0" step="1000">
                        </div>
                    </div>

                    <!-- Split Balance Status -->
                    <div class="p-2.5 bg-white rounded-lg border border-slate-200 flex items-center justify-between text-xs">
                        <span class="text-slate-600 font-medium">Sisa Tagihan Belum Terpenuhi:</span>
                        <strong class="text-sm font-black text-rose-600" id="pos-split-remaining">Rp 0</strong>
                    </div>
                </div>
            </div>

            <!-- Notes Input -->
            <div class="mt-4">
                <label class="block text-xs font-bold text-slate-600 mb-1">Catatan Tambahan (Opsional)</label>
                <input type="text" id="pos-notes-input" placeholder="e.g. Pembelian langsung di kasir toko" class="w-full text-xs p-2.5 rounded-lg border border-slate-200">
            </div>
        </div>

        <div class="pos-modal-footer">
            <button type="button" id="pos-cancel-pay-btn" class="pos-btn-ghost text-xs">Batal</button>
            <button type="button" id="pos-submit-transaction-btn" class="pos-btn-pay flex-1 justify-center">
                <span>Konfirmasi Pembayaran Selesai ✓</span>
            </button>
        </div>
    </div>
</div>

<!-- Modal Registrasi Member Baru di POS -->
<div id="pos-customer-modal" class="pos-modal-backdrop hidden">
    <div class="pos-modal-card max-w-sm">
        <div class="pos-modal-header">
            <div>
                <h3 class="text-base font-extrabold text-slate-900">Daftarkan Member Baru</h3>
                <p class="text-xs text-slate-500">Pelanggan baru langsung mendapat bonus 10 Poin Loyalitas.</p>
            </div>
            <button type="button" id="pos-close-customer-modal" class="pos-modal-close-btn">&times;</button>
        </div>

        <form id="pos-quick-customer-form" class="pos-modal-body space-y-3">
            <div>
                <label class="block text-xs font-bold text-slate-700 mb-1">Nama Lengkap *</label>
                <input type="text" id="cust-name" class="pos-meta-input text-xs" placeholder="Nama Pelanggan" required>
            </div>
            <div>
                <label class="block text-xs font-bold text-slate-700 mb-1">Nomor WhatsApp / HP *</label>
                <input type="tel" id="cust-phone" class="pos-meta-input text-xs" placeholder="08xxxxxxxxxx" required>
            </div>
            <div>
                <label class="block text-xs font-bold text-slate-700 mb-1">Email (Opsional)</label>
                <input type="email" id="cust-email" class="pos-meta-input text-xs" placeholder="nama@email.com">
            </div>
            <div>
                <label class="block text-xs font-bold text-slate-700 mb-1">Alamat Singkat (Opsional)</label>
                <input type="text" id="cust-address" class="pos-meta-input text-xs" placeholder="Kota / Alamat">
            </div>
            <div class="pt-3 border-t border-slate-100 flex gap-2">
                <button type="button" id="pos-cancel-customer-modal" class="pos-btn-ghost text-xs flex-1">Batal</button>
                <button type="submit" id="pos-submit-customer-btn" class="pos-btn-pay text-xs flex-1 justify-center">
                    Simpan Member ↗
                </button>
            </div>
        </form>
    </div>
</div>

<!-- Modal Daftar Tagihan Parkir / Disimpan -->
<div id="pos-held-modal" class="pos-modal-backdrop hidden">
    <div class="pos-modal-card max-w-md">
        <div class="pos-modal-header">
            <div>
                <h3 class="text-base font-extrabold text-slate-900">Tagihan Disimpan (Parkir Transaksi)</h3>
                <p class="text-xs text-slate-500">Buka kembali tagihan yang sempat ditunda atau hapus bila tidak dilanjutkan.</p>
            </div>
            <button type="button" id="pos-close-held-modal" class="pos-modal-close-btn">&times;</button>
        </div>

        <div class="pos-modal-body max-h-[60vh] overflow-y-auto">
            <div id="pos-held-list" class="divide-y divide-slate-100">
                <!-- Dynamic Held Bills -->
            </div>
        </div>

        <div class="pos-modal-footer">
            <button type="button" id="pos-dismiss-held-modal" class="pos-btn-ghost text-xs w-full">Tutup</button>
        </div>
    </div>
</div>

<!-- Receipt Popup Modal (After Success) -->
<div id="pos-receipt-modal" class="pos-modal-backdrop hidden">
    <div class="pos-modal-card max-w-sm">
        <div class="p-4 text-center">
            <div class="w-12 h-12 bg-emerald-100 text-emerald-600 rounded-full flex items-center justify-center text-xl font-bold mx-auto mb-2">
                ✓
            </div>
            <h3 class="text-base font-extrabold text-slate-900">Transaksi Berhasil!</h3>
            <p class="text-xs text-slate-500" id="receipt-modal-sub">Struk pembayaran telah dicatat.</p>
        </div>

        <div class="px-6 py-4 border-y border-slate-100 bg-slate-50 text-center">
            <iframe id="receipt-frame" src="" class="w-full h-80 border-0 rounded bg-white shadow-inner"></iframe>
        </div>

        <div class="p-4 flex gap-2">
            <button type="button" id="btn-print-receipt" class="pos-btn-pay flex-1 justify-center text-xs">
                🖨 Cetak Struk Kasir
            </button>
            <button type="button" id="btn-close-receipt-modal" class="pos-btn-ghost text-xs">
                Tutup (Esc)
            </button>
        </div>
    </div>
</div>
<!-- Open Shift Modal -->
<div id="pos-open-shift-modal" class="pos-modal-backdrop hidden">
    <div class="pos-modal-card max-w-sm">
        <div class="pos-modal-header">
            <div>
                <h3 class="text-base font-extrabold text-slate-900">Buka Shift Kasir Baru</h3>
                <p class="text-xs text-slate-500">Mulai sesi register dan catat uang modal kasir.</p>
            </div>
            <button type="button" id="pos-close-open-shift-modal" class="pos-modal-close-btn">&times;</button>
        </div>

        <form id="pos-open-shift-form" class="pos-modal-body space-y-3">
            <div>
                <label class="block text-xs font-bold text-slate-700 mb-1">Nama Kasir Bertugas</label>
                <input type="text" id="open-shift-cashier-name" class="pos-input-field text-xs" 
                       value="{{ auth()->user()->name ?? 'Kasir VarStore' }}" required>
            </div>
            <div>
                <label class="block text-xs font-bold text-slate-700 mb-1">Modal Awal Kas di Laci (Rp)</label>
                <input type="number" id="open-shift-starting-cash" class="pos-input-field text-xs font-mono font-bold" 
                       value="200000" min="0" step="10000" required>
                <small class="text-[10px] text-slate-500">Uang kembalian pecahan kecil yang ada di laci kasir.</small>
            </div>
            <div>
                <label class="block text-xs font-bold text-slate-700 mb-1">Catatan Shift (Opsional)</label>
                <input type="text" id="open-shift-notes" class="pos-input-field text-xs" placeholder="Contoh: Shift Pagi / Buka Toko">
            </div>

            <div class="pos-modal-footer pt-3">
                <button type="submit" id="btn-submit-open-shift" class="pos-btn-pay w-full justify-center text-xs">
                    Buka Kasir Sekarang ✓
                </button>
            </div>
        </form>
    </div>
</div>

<!-- Close Shift Settlement Modal -->
<div id="pos-close-shift-modal" class="pos-modal-backdrop hidden">
    <div class="pos-modal-card max-w-md">
        <div class="pos-modal-header">
            <div>
                <h3 class="text-base font-extrabold text-slate-900">Tutup Shift Kasir & Settlement</h3>
                <p class="text-xs text-slate-500">Hitung uang fisik laci kasir dan cetak laporan Z-Report.</p>
            </div>
            <button type="button" id="pos-close-close-shift-modal" class="pos-modal-close-btn">&times;</button>
        </div>

        <form id="pos-close-shift-form" class="pos-modal-body space-y-3">
            <!-- Settlement Summary Card -->
            <div class="bg-slate-50 border border-slate-200 rounded-lg p-3 space-y-1.5 text-xs">
                <div class="flex justify-between">
                    <span class="text-slate-500">Kasir:</span>
                    <strong id="close-shift-cashier" class="text-slate-900 font-bold">-</strong>
                </div>
                <div class="flex justify-between">
                    <span class="text-slate-500">Waktu Buka Shift:</span>
                    <span id="close-shift-opened-at" class="text-slate-700">-</span>
                </div>
                <div class="flex justify-between">
                    <span class="text-slate-500">Modal Awal:</span>
                    <span id="close-shift-starting-cash" class="font-mono text-slate-800">Rp 0</span>
                </div>
                <div class="flex justify-between">
                    <span class="text-slate-500">Penjualan Tunai:</span>
                    <span id="close-shift-cash-sales" class="font-mono text-emerald-600 font-semibold">+Rp 0</span>
                </div>
                <div class="flex justify-between">
                    <span class="text-slate-500">Kas Masuk Operasional (+):</span>
                    <span id="close-shift-cash-in" class="font-mono text-emerald-600 font-semibold">+Rp 0</span>
                </div>
                <div class="flex justify-between">
                    <span class="text-slate-500">Kas Keluar Petty Cash (-):</span>
                    <span id="close-shift-cash-out" class="font-mono text-rose-600 font-semibold">-Rp 0</span>
                </div>
                <div class="flex justify-between border-t border-slate-200 pt-1.5 font-bold">
                    <span class="text-slate-800">Uang Seharusnya di Laci:</span>
                    <span id="close-shift-expected-cash" class="font-mono text-slate-900">Rp 0</span>
                </div>
            </div>

            <div>
                <label class="block text-xs font-bold text-slate-700 mb-1">Hitung Uang Fisik di Laci Kasir (Rp)</label>
                <input type="number" id="close-shift-actual-cash" class="pos-input-field text-sm font-mono font-bold" 
                       placeholder="Masukkan total uang cash yang dihitung" min="0" required>
            </div>

            <!-- Real-time Variance Box -->
            <div id="close-shift-variance-box" class="p-2.5 rounded-lg border text-xs flex justify-between items-center bg-slate-100 border-slate-200">
                <span class="font-bold">Selisih Kas:</span>
                <span id="close-shift-variance-val" class="font-mono font-bold">Rp 0 (Pas)</span>
            </div>

            <div>
                <label class="block text-xs font-bold text-slate-700 mb-1">Catatan Penutupan Kasir (Opsional)</label>
                <textarea id="close-shift-notes" class="pos-input-field text-xs h-16 resize-none" placeholder="Alasan selisih jika ada..."></textarea>
            </div>

            <div class="pos-modal-footer pt-2">
                <button type="submit" id="btn-submit-close-shift" class="pos-btn-pay w-full justify-center text-xs">
                    Tutup Shift & Cetak Z-Report ✓
                </button>
            </div>
        </form>
    </div>
</div>

<!-- Petty Cash In / Out Modal -->
<div id="pos-petty-cash-modal" class="pos-modal-backdrop hidden">
    <div class="pos-modal-card max-w-sm">
        <div class="pos-modal-header">
            <div>
                <h3 class="text-base font-extrabold text-slate-900">Mutasi Kas Laci (Petty Cash)</h3>
                <p class="text-xs text-slate-500">Catat kas masuk atau kas keluar operasional toko.</p>
            </div>
            <button type="button" id="pos-close-petty-cash-modal" class="pos-modal-close-btn">&times;</button>
        </div>
        <form id="pos-petty-cash-form" class="pos-modal-body space-y-3">
            <div>
                <label class="block text-xs font-bold text-slate-700 mb-1">Tipe Mutasi</label>
                <div class="grid grid-cols-2 gap-2">
                    <label class="flex items-center gap-1.5 p-2 rounded-lg border border-slate-200 cursor-pointer bg-slate-50 text-xs font-bold">
                        <input type="radio" name="petty_type" value="cash_in" class="text-emerald-600 focus:ring-0">
                        <span class="text-emerald-700">➕ Kas Masuk</span>
                    </label>
                    <label class="flex items-center gap-1.5 p-2 rounded-lg border border-slate-200 cursor-pointer bg-slate-50 text-xs font-bold">
                        <input type="radio" name="petty_type" value="cash_out" checked class="text-rose-600 focus:ring-0">
                        <span class="text-rose-700">➖ Kas Keluar</span>
                    </label>
                </div>
            </div>

            <div>
                <label class="block text-xs font-bold text-slate-700 mb-1">Nominal (Rp)</label>
                <input type="number" id="petty-cash-amount" required min="1000" step="1000" placeholder="10.000" class="pos-input-field text-xs font-mono font-bold">
            </div>

            <div>
                <label class="block text-xs font-bold text-slate-700 mb-1">Kategori Keperluan</label>
                <select id="petty-cash-category" required class="pos-input-field text-xs bg-white">
                    <option value="supplies">Beli Perlengkapan / Kantong / ATK</option>
                    <option value="courier_delivery">Ongkir Kurir Pickup / Logistik</option>
                    <option value="change_replenishment">Tambah Uang Kembalian (Kas Masuk)</option>
                    <option value="cleaning_security">Kebersihan & Keamanan Toko</option>
                    <option value="snack_refreshment">Konsumsi / Air Galon Toko</option>
                    <option value="other">Operasional Lain-lain</option>
                </select>
            </div>

            <div>
                <label class="block text-xs font-bold text-slate-700 mb-1">Keterangan / Catatan</label>
                <input type="text" id="petty-cash-notes" required placeholder="e.g. Beli lakban & kantong kresek" class="pos-input-field text-xs">
            </div>

            <div class="pos-modal-footer pt-2">
                <button type="button" id="pos-dismiss-petty-cash-modal" class="pos-btn-ghost text-xs">Batal</button>
                <button type="submit" class="pos-btn-pay text-xs">Simpan Mutasi Kas ✓</button>
            </div>
        </form>
    </div>
</div>

@endsection
