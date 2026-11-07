@extends('layouts.admin')
@section('title', 'Katalog Produk — VarStore')

@section('content')
<div class="admin-wrap">
    <!-- Header -->
    <div class="admin-head">
        <div>
            <p class="admin-kicker">Katalog & Inventaris</p>
            <h1 class="text-2xl sm:text-3xl font-extrabold text-slate-900">Katalog Produk</h1>
            <p class="text-xs text-slate-500 mt-1">Kelola daftar busana, penentuan harga, diskon, dan kontrol stok toko.</p>
        </div>

        <div class="flex items-center gap-2">
            <a href="{{ route('admin.products.reorder-sheet') }}" class="inline-flex items-center gap-1.5 px-3.5 py-2 rounded-xl border border-amber-300 bg-amber-50 hover:bg-amber-100 text-amber-900 font-bold text-xs shadow-sm transition">
                <span>📋 PO Lembar Restock</span>
            </a>
            <a href="{{ route('admin.products.create') }}" class="pos-btn-pay text-xs px-4 py-2.5">
                <span>+ Tambah Produk Baru</span>
            </a>
        </div>
    </div>

    @if (session('success'))
        <div class="mt-6 p-4 rounded-xl bg-emerald-50 border border-emerald-200 text-emerald-800 text-xs font-semibold flex items-center justify-between">
            <span class="flex items-center gap-2">
                <span class="text-emerald-600 font-bold text-base">✓</span>
                {{ session('success') }}
            </span>
        </div>
    @endif

    <!-- Filters & Search Bar -->
    <div class="flex flex-wrap items-center justify-between gap-4 mt-8 pt-4 border-t border-slate-200">
        <div class="flex flex-wrap gap-2">
            <a href="{{ route('admin.products.index') }}" 
               class="pos-cat-pill {{ empty($category) && empty($stockFilter) ? 'active' : '' }}">
                Semua Produk
            </a>
            @foreach($categories as $cat)
                <a href="{{ route('admin.products.index', ['category' => $cat]) }}" 
                   class="pos-cat-pill {{ $category === $cat ? 'active' : '' }}">
                    {{ $cat }}
                </a>
            @endforeach
            <span class="text-slate-300">|</span>
            <a href="{{ route('admin.products.index', ['stock' => 'low']) }}" 
               class="pos-cat-pill text-amber-700 {{ $stockFilter === 'low' ? 'active' : '' }}">
                ⚠ Stok Menipis (≤5)
            </a>
        </div>

        <!-- Search input -->
        <form method="GET" action="{{ route('admin.products.index') }}" class="flex items-center gap-2">
            @if($category) <input type="hidden" name="category" value="{{ $category }}"> @endif
            @if($stockFilter) <input type="hidden" name="stock" value="{{ $stockFilter }}"> @endif
            <input type="text" name="search" value="{{ $search }}" 
                   placeholder="Cari produk atau warna..." 
                   class="text-xs px-3 py-2 rounded-lg border border-slate-300 bg-white outline-none w-52 focus:w-64 focus:border-slate-800 transition-all">
            <button type="submit" class="pos-btn-ghost text-xs py-2 px-3">Cari</button>
            @if($search || $category || $stockFilter)
                <a href="{{ route('admin.products.index') }}" class="text-xs text-rose-600 font-bold hover:underline">Reset</a>
            @endif
        </form>
    </div>

    <!-- Products Table -->
    <div class="mt-6 bg-white border border-slate-200 rounded-xl overflow-hidden shadow-sm">
        <div class="overflow-x-auto">
            <table class="w-full text-left text-xs border-collapse">
                <thead>
                    <tr class="bg-slate-50 border-b border-slate-200 text-slate-500 font-bold uppercase tracking-wider text-[11px]">
                        <th class="p-4" style="width: 35%;">Produk & Kategori</th>
                        <th class="p-4">Harga Jual</th>
                        <th class="p-4">Diskon Produk</th>
                        <th class="p-4">Stok Fisik</th>
                        <th class="p-4">Status Fitur</th>
                        <th class="p-4 text-right">Aksi</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-slate-100">
                    @forelse ($products as $product)
                        <tr class="hover:bg-slate-50/60 transition-colors">
                            <!-- Product Details -->
                            <td class="p-4">
                                <div class="flex items-center gap-3">
                                    <img src="{{ $product->image_url }}" alt="{{ $product->name }}" 
                                         class="w-12 h-14 object-cover rounded-lg bg-slate-100 shrink-0 border border-slate-200">
                                    <div>
                                        <b class="text-slate-900 block text-xs">{{ $product->name }}</b>
                                        <span class="text-[11px] text-slate-500">
                                            {{ $product->category }} · {{ $product->color ?? '-' }} ({{ $product->size ?? 'All Size' }})
                                        </span>
                                    </div>
                                </div>
                            </td>

                            <!-- Price -->
                            <td class="p-4">
                                <strong class="text-slate-900 block text-xs">
                                    {{ $product->formatted_final_price }}
                                </strong>
                                @if($product->has_discount)
                                    <span class="text-[10px] text-slate-400 line-through">
                                        {{ $product->formatted_price }}
                                    </span>
                                @endif
                            </td>

                            <!-- Discount % -->
                            <td class="p-4">
                                @if($product->has_discount)
                                    <span class="inline-flex items-center gap-1 text-[11px] font-extrabold text-rose-700 bg-rose-50 border border-rose-200 px-2 py-0.5 rounded">
                                        -{{ $product->discount_percent }}% OFF
                                    </span>
                                    <small class="text-slate-400 block text-[10px] mt-0.5">Hemat Rp {{ number_format($product->discount_savings, 0, ',', '.') }}</small>
                                @else
                                    <span class="text-slate-400 text-[11px]">Harga Normal</span>
                                @endif
                            </td>

                            <!-- Stock -->
                            <td class="p-4">
                                @if($product->stock <= 0)
                                    <span class="inline-flex text-[10px] font-extrabold px-2 py-0.5 rounded bg-rose-100 text-rose-800">
                                        ✕ Habis (0)
                                    </span>
                                @elseif($product->stock <= 5)
                                    <span class="inline-flex text-[10px] font-extrabold px-2 py-0.5 rounded bg-amber-100 text-amber-800">
                                        ⚠ Sisa {{ $product->stock }} pcs
                                    </span>
                                @else
                                    <span class="inline-flex text-[10px] font-extrabold px-2 py-0.5 rounded bg-emerald-50 text-emerald-700">
                                        ✓ {{ $product->stock }} pcs
                                    </span>
                                @endif
                            </td>

                            <!-- Featured -->
                            <td class="p-4">
                                @if($product->is_featured)
                                    <span class="text-[11px] text-amber-700 font-extrabold flex items-center gap-1">
                                        <span>✦</span> Unggulan
                                    </span>
                                @else
                                    <span class="text-slate-400 text-[11px]">Standar</span>
                                @endif
                            </td>

                            <!-- Actions -->
                            <td class="p-4 text-right">
                                <div class="flex items-center justify-end gap-1.5">
                                    <button type="button" class="btn-open-stock-modal pos-btn-ghost text-[11px] py-1 px-2 text-slate-700"
                                            data-id="{{ $product->id }}" 
                                            data-name="{{ $product->name }}" 
                                            data-stock="{{ $product->stock }}">
                                        📦 Opname
                                    </button>
                                    <a href="{{ route('admin.products.barcode-label', $product) }}" target="_blank" 
                                       class="pos-btn-ghost text-[11px] py-1 px-2" title="Cetak Label Barcode">
                                        🏷 Label
                                    </a>
                                    <a href="{{ route('admin.products.edit', $product) }}" class="pos-btn-ghost text-[11px] py-1 px-2.5">
                                        Edit
                                    </a>
                                    <form method="POST" action="{{ route('admin.products.destroy', $product) }}" 
                                          onsubmit="return confirm('Hapus produk {{ $product->name }}?');" class="inline">
                                        @csrf
                                        @method('DELETE')
                                        <button class="pos-btn-danger-ghost text-[11px] py-1 px-2.5">
                                            Hapus
                                        </button>
                                    </form>
                                </div>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="6" class="p-12 text-center text-slate-400">
                                Belum ada produk yang cocok dengan pencarian.
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>

    <div class="mt-6">
        {{ $products->links() }}
    </div>
</div>

<!-- Stock Opname & Adjustment Modal -->
<div id="stock-modal" class="pos-modal-backdrop hidden">
    <div class="pos-modal-card max-w-lg">
        <div class="pos-modal-header">
            <div>
                <h3 class="text-base font-extrabold text-slate-900">Opname Stok & Penyesuaian Inventaris</h3>
                <p class="text-xs text-slate-500" id="stock-modal-product-name">Penyesuaian stok produk fisik.</p>
            </div>
            <button type="button" id="close-stock-modal" class="pos-modal-close-btn">&times;</button>
        </div>

        <form id="stock-adjust-form" method="POST" action="" class="pos-modal-body space-y-4">
            @csrf
            <div class="bg-slate-50 border border-slate-200 rounded-xl p-3 flex justify-between items-center text-xs">
                <div>
                    <span class="text-slate-500 block">Stok Sistem Saat Ini:</span>
                    <strong id="stock-modal-current" class="text-base font-mono font-bold text-slate-900">0 pcs</strong>
                </div>
                <div class="text-right">
                    <span class="text-slate-500 block">Estimasi Stok Akhir:</span>
                    <strong id="stock-modal-result" class="text-base font-mono font-bold text-emerald-600">0 pcs</strong>
                </div>
            </div>

            <div>
                <label class="block text-xs font-bold text-slate-700 mb-1">Jenis Penyesuaian</label>
                <select name="type" id="adjust-type" class="pos-input-field text-xs font-semibold" required>
                    <option value="in">Stok Masuk (+) — Pengiriman dari Suplier / Tambah Stok</option>
                    <option value="out">Stok Keluar (-) — Pengambilan Sampel / Keperluan Internal</option>
                    <option value="damage">Barang Rusak / Reject (-) — Cacat Jahitan / Robek</option>
                    <option value="loss">Barang Hilang (-) — Selisih Minus Inventaris</option>
                    <option value="opname_correction">Koreksi Opname Fisik (=) — Samakan dengan Hitungan Fisik</option>
                </select>
            </div>

            <div>
                <label class="block text-xs font-bold text-slate-700 mb-1">
                    <span id="adjust-qty-label">Jumlah / Kuantitas (pcs)</span>
                </label>
                <input type="number" name="quantity" id="adjust-qty" class="pos-input-field text-sm font-mono font-bold" 
                       min="0" value="1" required>
            </div>

            <div>
                <label class="block text-xs font-bold text-slate-700 mb-1">Alasan Penyesuaian (Audit Log)</label>
                <input type="text" name="reason" id="adjust-reason" class="pos-input-field text-xs" 
                       placeholder="Contoh: Restok 2 lusin dari vendor Bandung / Reject kancing lepas" required>
            </div>

            <!-- Audit History Table -->
            <div class="border-t border-slate-200 pt-3">
                <span class="text-[11px] font-bold text-slate-700 uppercase tracking-wider block mb-2">Riwayat Audit Stok Terbaru</span>
                <div class="max-h-36 overflow-y-auto border border-slate-100 rounded-lg">
                    <table class="w-full text-[11px] text-left">
                        <thead class="bg-slate-50 text-slate-500 font-bold sticky top-0">
                            <tr>
                                <th class="p-2">Waktu</th>
                                <th class="p-2">Tipe</th>
                                <th class="p-2 text-center">Qty</th>
                                <th class="p-2 text-center">Stok</th>
                                <th class="p-2">Alasan</th>
                            </tr>
                        </thead>
                        <tbody id="stock-history-tbody" class="divide-y divide-slate-100">
                            <tr>
                                <td colspan="5" class="p-4 text-center text-slate-400">Memuat riwayat...</td>
                            </tr>
                        </tbody>
                    </table>
                </div>
            </div>

            <div class="pos-modal-footer pt-2">
                <button type="submit" class="pos-btn-pay w-full justify-center text-xs">
                    Simpan Perubahan Stok ✓
                </button>
            </div>
        </form>
    </div>
</div>

<script>
document.addEventListener('DOMContentLoaded', () => {
    const stockModal = document.getElementById('stock-modal');
    const closeBtn = document.getElementById('close-stock-modal');
    const form = document.getElementById('stock-adjust-form');
    const nameLabel = document.getElementById('stock-modal-product-name');
    const currentLabel = document.getElementById('stock-modal-current');
    const resultLabel = document.getElementById('stock-modal-result');
    const typeSelect = document.getElementById('adjust-type');
    const qtyInput = document.getElementById('adjust-qty');
    const historyTbody = document.getElementById('stock-history-tbody');

    let currentStock = 0;

    const calculateEstimatedStock = () => {
        const type = typeSelect.value;
        const qty = parseInt(qtyInput.value, 10) || 0;
        let final = currentStock;

        if (type === 'in') {
            final = currentStock + qty;
        } else if (['out', 'damage', 'loss'].includes(type)) {
            final = Math.max(0, currentStock - qty);
        } else if (type === 'opname_correction') {
            final = qty;
        }

        resultLabel.textContent = `${final} pcs`;
    };

    typeSelect?.addEventListener('change', calculateEstimatedStock);
    qtyInput?.addEventListener('input', calculateEstimatedStock);

    document.querySelectorAll('.btn-open-stock-modal').forEach(btn => {
        btn.addEventListener('click', async () => {
            const id = btn.dataset.id;
            const name = btn.dataset.name;
            currentStock = parseInt(btn.dataset.stock, 10);

            nameLabel.textContent = `${name} (ID #${id})`;
            currentLabel.textContent = `${currentStock} pcs`;
            form.action = `/admin/products/${id}/adjust-stock`;
            qtyInput.value = 1;
            calculateEstimatedStock();

            // Fetch history
            historyTbody.innerHTML = '<tr><td colspan="5" class="p-4 text-center text-slate-400">Memuat riwayat...</td></tr>';
            stockModal.classList.remove('hidden');

            try {
                const res = await fetch(`/admin/products/${id}/stock-history`);
                const data = await res.json();
                if (data.history && data.history.length > 0) {
                    historyTbody.innerHTML = '';
                    data.history.forEach(h => {
                        const tr = document.createElement('tr');
                        tr.className = 'hover:bg-slate-50 text-[10px]';
                        tr.innerHTML = `
                            <td class="p-2 text-slate-500">${h.time_ago}</td>
                            <td class="p-2 font-bold">${h.type_label}</td>
                            <td class="p-2 text-center font-mono">${h.quantity}</td>
                            <td class="p-2 text-center font-mono">${h.stock_before} → <strong>${h.stock_after}</strong></td>
                            <td class="p-2 text-slate-600 truncate max-w-[120px]" title="${h.reason}">${h.reason}</td>
                        `;
                        historyTbody.appendChild(tr);
                    });
                } else {
                    historyTbody.innerHTML = '<tr><td colspan="5" class="p-4 text-center text-slate-400">Belum ada audit penyesuaian stok.</td></tr>';
                }
            } catch (e) {
                historyTbody.innerHTML = '<tr><td colspan="5" class="p-4 text-center text-rose-500">Gagal memuat log audit.</td></tr>';
            }
        });
    });

    closeBtn?.addEventListener('click', () => {
        stockModal.classList.add('hidden');
    });
});
</script>
@endsection
