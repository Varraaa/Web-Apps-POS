@extends('layouts.admin')
@section('title', 'Manajemen Pesanan & Transaksi — VarStore')

@section('content')
<div class="admin-wrap">
    <!-- Header -->
    <div class="admin-head">
        <div>
            <p class="admin-kicker">Admin Workspace</p>
            <h1 class="text-2xl sm:text-3xl font-extrabold text-slate-900">Pesanan & Transaksi</h1>
            <p class="text-xs text-slate-500 mt-1">Kelola dan pantau seluruh transaksi kasir POS serta pesanan online toko.</p>
        </div>

        <div class="flex items-center gap-3">
            <a href="{{ route('admin.pos.index') }}" class="pos-btn-pay text-xs px-4 py-2.5">
                <span>+ Buka Kasir POS</span>
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

    <!-- Source & Status Filter Tabs -->
    <div class="flex flex-wrap items-center justify-between gap-4 mt-8 pt-4 border-t border-slate-200">
        <div class="flex flex-wrap gap-2">
            <a href="{{ route('admin.orders.index') }}" 
               class="pos-cat-pill {{ empty($status) && empty($source) ? 'active' : '' }}">
                Semua ({{ $counts['all'] }})
            </a>
            <a href="{{ route('admin.orders.index', ['source' => 'pos']) }}" 
               class="pos-cat-pill {{ $source === 'pos' ? 'active' : '' }}">
                🏬 POS Kasir ({{ $counts['pos'] }})
            </a>
            <a href="{{ route('admin.orders.index', ['source' => 'online']) }}" 
               class="pos-cat-pill {{ $source === 'online' ? 'active' : '' }}">
                🌐 Online Web ({{ $counts['online'] }})
            </a>
            <span class="text-slate-300">|</span>
            <a href="{{ route('admin.orders.index', ['status' => 'pending']) }}" 
               class="pos-cat-pill {{ $status === 'pending' ? 'active' : '' }}">
                ⏳ Menunggu ({{ $counts['pending'] }})
            </a>
            <a href="{{ route('admin.orders.index', ['status' => 'processed']) }}" 
               class="pos-cat-pill {{ $status === 'processed' ? 'active' : '' }}">
                📦 Diproses ({{ $counts['processed'] }})
            </a>
            <a href="{{ route('admin.orders.index', ['status' => 'completed']) }}" 
               class="pos-cat-pill {{ $status === 'completed' ? 'active' : '' }}">
                ✓ Selesai ({{ $counts['completed'] }})
            </a>
        </div>

        <!-- Search Box -->
        <form method="GET" action="{{ route('admin.orders.index') }}" class="flex items-center gap-2">
            @if($status) <input type="hidden" name="status" value="{{ $status }}"> @endif
            @if($source) <input type="hidden" name="source" value="{{ $source }}"> @endif
            <input type="text" name="search" value="{{ $search }}" 
                   placeholder="Cari No. Struk, Nama, Telp..." 
                   class="text-xs px-3 py-2 rounded-lg border border-slate-300 bg-white outline-none w-52 focus:w-64 focus:border-slate-800 transition-all">
            <button type="submit" class="pos-btn-ghost text-xs py-2 px-3">Cari</button>
            @if($search || $status || $source)
                <a href="{{ route('admin.orders.index') }}" class="text-xs text-rose-600 font-bold hover:underline">Reset</a>
            @endif
        </form>
    </div>

    <!-- Orders Table -->
    <div class="mt-6 bg-white border border-slate-200 rounded-xl overflow-hidden shadow-sm">
        <div class="overflow-x-auto">
            <table class="w-full text-left text-xs border-collapse">
                <thead>
                    <tr class="bg-slate-50 border-b border-slate-200 text-slate-500 font-bold uppercase tracking-wider text-[11px]">
                        <th class="p-4">No. Invoice / Waktu</th>
                        <th class="p-4">Tipe / Kasir</th>
                        <th class="p-4">Pelanggan</th>
                        <th class="p-4">Item Pesanan</th>
                        <th class="p-4">Total & Pembayaran</th>
                        <th class="p-4">Status Pesanan</th>
                        <th class="p-4 text-right">Aksi</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-slate-100">
                    @forelse($orders as $order)
                        <tr class="hover:bg-slate-50/60 transition-colors">
                            <!-- Invoice & Time -->
                            <td class="p-4">
                                <strong class="text-slate-900 block font-mono text-xs">#{{ $order->invoice_number }}</strong>
                                <span class="text-[11px] text-slate-500">{{ $order->created_at->format('d M Y, H:i') }}</span>
                            </td>

                            <!-- Source & Cashier -->
                            <td class="p-4">
                                @if($order->isPos())
                                    <span class="inline-flex items-center gap-1 text-[10px] font-extrabold uppercase px-2 py-0.5 rounded bg-blue-50 text-blue-700 border border-blue-200">
                                        🏬 POS Kasir
                                    </span>
                                @else
                                    <span class="inline-flex items-center gap-1 text-[10px] font-extrabold uppercase px-2 py-0.5 rounded bg-amber-50 text-amber-700 border border-amber-200">
                                        🌐 Online WA
                                    </span>
                                @endif
                                <small class="text-slate-500 block mt-1 text-[10px]">{{ $order->cashier_name ?? 'Web Store' }}</small>
                            </td>

                            <!-- Customer -->
                            <td class="p-4">
                                <b class="text-slate-900 block">{{ $order->customer_name }}</b>
                                @if($order->customer_phone && $order->customer_phone !== '-')
                                    <a href="https://wa.me/{{ preg_replace('/[^0-9]/', '', $order->customer_phone) }}" 
                                       target="_blank" rel="noopener noreferrer" 
                                       class="text-[11px] text-emerald-600 font-semibold hover:underline inline-flex items-center gap-1">
                                        <span>💬</span> {{ $order->customer_phone }}
                                    </a>
                                @else
                                    <span class="text-slate-400 text-[11px]">Walk-In Customer</span>
                                @endif
                            </td>

                            <!-- Items -->
                            <td class="p-4">
                                <span class="font-bold text-slate-800">{{ $order->items->sum('quantity') }} pcs</span>
                                <span class="text-slate-400 text-[11px]">({{ $order->items->count() }} variasi)</span>
                                <div class="text-[11px] text-slate-500 truncate max-w-xs mt-0.5">
                                    {{ $order->items->pluck('product_name')->join(', ') }}
                                </div>
                            </td>

                            <!-- Total & Payment -->
                            <td class="p-4">
                                <strong class="text-slate-900 block text-xs">{{ $order->formatted_total }}</strong>
                                <span class="text-[10px] text-slate-500 block">{{ $order->payment_label }}</span>
                                @if($order->discount_amount > 0)
                                    <span class="text-[10px] text-emerald-600 font-bold block">
                                        Hemat -{{ $order->formatted_discount }}
                                    </span>
                                @endif
                            </td>

                            <!-- Status Dropdown Form -->
                            <td class="p-4">
                                <form method="POST" action="{{ route('admin.orders.update', $order) }}" class="inline">
                                    @csrf
                                    @method('PATCH')
                                    <select name="status" onchange="this.form.submit()"
                                            class="text-xs font-semibold py-1 px-2.5 rounded-lg border outline-none cursor-pointer
                                                   {{ $order->status === 'completed' ? 'bg-emerald-50 text-emerald-700 border-emerald-300' : '' }}
                                                   {{ $order->status === 'processed' ? 'bg-blue-50 text-blue-700 border-blue-300' : '' }}
                                                   {{ $order->status === 'pending' ? 'bg-amber-50 text-amber-700 border-amber-300' : '' }}
                                                   {{ $order->status === 'cancelled' ? 'bg-rose-50 text-rose-700 border-rose-300' : '' }}">
                                        <option value="pending" @selected($order->status === 'pending')>⏳ Menunggu</option>
                                        <option value="processed" @selected($order->status === 'processed')>📦 Diproses</option>
                                        <option value="completed" @selected($order->status === 'completed')>✓ Selesai</option>
                                        <option value="cancelled" @selected($order->status === 'cancelled')>✕ Dibatalkan</option>
                                    </select>
                                </form>
                            </td>

                            <!-- Actions -->
                            <td class="p-4 text-right">
                                <div class="flex items-center justify-end gap-2">
                                    <a href="{{ route('admin.pos.receipt', $order) }}" target="_blank" 
                                       class="pos-btn-ghost text-[11px] py-1 px-2" title="Cetak Struk Kasir POS">
                                        🖨 Struk
                                    </a>
                                    <button type="button" class="btn-open-order-detail pos-btn-ghost text-[11px] py-1 px-2"
                                            data-order-id="{{ $order->id }}"
                                            data-invoice="{{ $order->invoice_number }}"
                                            data-customer="{{ $order->customer_name }}"
                                            data-phone="{{ $order->customer_phone }}"
                                            data-address="{{ $order->shipping_address }}"
                                            data-date="{{ $order->created_at->format('d M Y, H:i') }}"
                                            data-total="{{ $order->formatted_total }}"
                                            data-subtotal="{{ $order->formatted_subtotal }}"
                                            data-discount="{{ $order->formatted_discount }}"
                                            data-coupon="{{ $order->coupon_code }}"
                                            data-payment="{{ $order->payment_label }}"
                                            data-paid="{{ $order->formatted_amount_paid }}"
                                            data-change="{{ $order->formatted_change }}"
                                            data-cashier="{{ $order->cashier_name ?? '-' }}"
                                            data-notes="{{ $order->notes ?? '-' }}"
                                            data-items='@json($order->items)'>
                                        Detail ↗
                                    </button>
                                </div>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="7" class="p-12 text-center text-slate-400">
                                <span class="text-3xl block mb-2">📋</span>
                                Tidak ada transaksi atau pesanan yang cocok dengan filter pencarian.
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>

    <div class="mt-6">
        {{ $orders->links() }}
    </div>
</div>

<!-- Modal Order Detail -->
<div id="order-detail-modal" class="pos-modal-backdrop hidden">
    <div class="pos-modal-card max-w-lg">
        <div class="pos-modal-header">
            <div>
                <h3 class="text-base font-extrabold text-slate-900" id="detail-modal-title">Detail Pesanan</h3>
                <p class="text-xs text-slate-500" id="detail-modal-sub">-</p>
            </div>
            <button type="button" id="btn-close-detail-modal" class="pos-modal-close-btn">&times;</button>
        </div>

        <div class="pos-modal-body space-y-4 max-h-[75vh] overflow-y-auto">
            <!-- Customer Meta Box -->
            <div class="p-3 bg-slate-50 rounded-xl border border-slate-200 text-xs space-y-1.5">
                <div class="flex justify-between">
                    <span class="text-slate-500">Nama Pelanggan:</span>
                    <strong class="text-slate-900" id="detail-customer-name">-</strong>
                </div>
                <div class="flex justify-between">
                    <span class="text-slate-500">Telepon / WhatsApp:</span>
                    <strong class="text-slate-900" id="detail-customer-phone">-</strong>
                </div>
                <div class="flex justify-between">
                    <span class="text-slate-500">Alamat / Lokasi:</span>
                    <span class="text-slate-700 text-right max-w-xs" id="detail-shipping-address">-</span>
                </div>
                <div class="flex justify-between">
                    <span class="text-slate-500">Metode Pembayaran:</span>
                    <strong class="text-slate-900" id="detail-payment-method">-</strong>
                </div>
                <div class="flex justify-between">
                    <span class="text-slate-500">Kasir / Operator:</span>
                    <span class="text-slate-700" id="detail-cashier">-</span>
                </div>
                <div class="flex justify-between">
                    <span class="text-slate-500">Catatan:</span>
                    <span class="text-slate-700 italic" id="detail-notes">-</span>
                </div>
            </div>

            <!-- Items List -->
            <div>
                <h4 class="text-xs font-bold text-slate-700 mb-2 uppercase tracking-wider">Item yang Dibeli:</h4>
                <div class="divide-y divide-slate-100 border border-slate-200 rounded-lg overflow-hidden" id="detail-items-container">
                    <!-- Dynamic -->
                </div>
            </div>

            <!-- Financials -->
            <div class="p-3 bg-slate-50 rounded-xl border border-slate-200 text-xs space-y-1.5">
                <div class="flex justify-between">
                    <span class="text-slate-500">Subtotal:</span>
                    <strong id="detail-subtotal">Rp 0</strong>
                </div>
                <div class="flex justify-between text-emerald-600" id="detail-row-discount">
                    <span>Diskon Promo:</span>
                    <strong id="detail-discount">- Rp 0</strong>
                </div>
                <div class="flex justify-between text-sm font-black border-t border-slate-200 pt-2 text-slate-900">
                    <span>Total Pembayaran:</span>
                    <strong id="detail-total">Rp 0</strong>
                </div>
            </div>
        </div>

        <div class="pos-modal-footer justify-between">
            <div class="flex items-center gap-2">
                <a href="#" id="detail-btn-receipt" target="_blank" class="pos-btn-pay text-xs">
                    🖨 Cetak Struk
                </a>
                <button type="button" id="btn-open-return-form" class="pos-btn-ghost text-xs border border-rose-300 text-rose-700 hover:bg-rose-50">
                    ↩ Proses Retur
                </button>
            </div>
            <button type="button" id="btn-dismiss-detail-modal" class="pos-btn-ghost text-xs">
                Tutup
            </button>
        </div>
    </div>
</div>

<!-- Order Return Modal -->
<div id="order-return-modal" class="pos-modal-backdrop hidden">
    <div class="pos-modal-card max-w-lg">
        <div class="pos-modal-header">
            <div>
                <h3 class="text-base font-black text-slate-900" id="return-modal-title">Form Retur Barang</h3>
                <p class="text-xs text-slate-500">Pilih barang yang diretur, kondisi fisik, dan nominal pengembalian dana.</p>
            </div>
            <button type="button" id="btn-close-return-modal" class="pos-modal-close-btn">&times;</button>
        </div>
        <form id="order-return-form" method="POST" action="">
            @csrf
            <div class="pos-modal-body space-y-3">
                <div>
                    <label class="block text-xs font-bold text-slate-700 mb-1">Pilih Produk yang Diretur</label>
                    <select name="order_item_id" id="return-item-select" required class="w-full text-xs p-2.5 rounded-lg border border-slate-300 focus:ring-2 focus:ring-slate-900 focus:outline-none">
                        <!-- Populated by JS -->
                    </select>
                </div>

                <div class="grid grid-cols-2 gap-3">
                    <div>
                        <label class="block text-xs font-bold text-slate-700 mb-1">Jumlah Item (Qty)</label>
                        <input type="number" name="quantity" id="return-qty" value="1" min="1" required class="w-full text-xs p-2.5 rounded-lg border border-slate-300 focus:ring-2 focus:ring-slate-900 focus:outline-none">
                    </div>
                    <div>
                        <label class="block text-xs font-bold text-slate-700 mb-1">Nominal Refund (Rp)</label>
                        <input type="number" name="refund_amount" id="return-amount" value="0" min="0" step="1000" required class="w-full text-xs p-2.5 rounded-lg border border-slate-300 focus:ring-2 focus:ring-slate-900 focus:outline-none">
                    </div>
                </div>

                <div class="grid grid-cols-2 gap-3">
                    <div>
                        <label class="block text-xs font-bold text-slate-700 mb-1">Kondisi Fisik Barang</label>
                        <select name="condition" required class="w-full text-xs p-2.5 rounded-lg border border-slate-300 focus:ring-2 focus:ring-slate-900 focus:outline-none">
                            <option value="intact_with_tags">Utuh & Tag Lengkap (Layak Jual)</option>
                            <option value="opened_good">Buka Segel (Kondisi Bagus)</option>
                            <option value="damaged">Rusak Pemakaian Pelanggan</option>
                            <option value="defect_factory">Cacat Pabrik / Jahitan Rusak</option>
                        </select>
                    </div>
                    <div>
                        <label class="block text-xs font-bold text-slate-700 mb-1">Alasan Retur</label>
                        <select name="reason" required class="w-full text-xs p-2.5 rounded-lg border border-slate-300 focus:ring-2 focus:ring-slate-900 focus:outline-none">
                            <option value="wrong_size">Ukuran Tidak Pas / Salah Size</option>
                            <option value="defective">Cacat / Rusak</option>
                            <option value="not_as_expected">Tidak Sesuai Ekspektasi</option>
                            <option value="customer_change_mind">Ingin Ganti Model / Batal</option>
                            <option value="other">Lainnya</option>
                        </select>
                    </div>
                </div>

                <div>
                    <label class="block text-xs font-bold text-slate-700 mb-1">Metode Pengembalian Kompensasi</label>
                    <select name="refund_type" required class="w-full text-xs p-2.5 rounded-lg border border-slate-300 focus:ring-2 focus:ring-slate-900 focus:outline-none">
                        <option value="cash">Kembali Tunai dari Kasir (Cash)</option>
                        <option value="store_credit">Saldo Dompet Pelanggan (Store Credit)</option>
                        <option value="exchange">Tukar Barang Sejenis / Ukuran Lain</option>
                    </select>
                </div>

                <div class="flex items-center gap-2 pt-1">
                    <input type="checkbox" name="restock_item" id="return-restock" value="1" checked class="rounded border-slate-300">
                    <label for="return-restock" class="text-xs text-slate-700 font-semibold cursor-pointer">
                        Kembalikan barang ini ke stok etalase (Restock otomatis)
                    </label>
                </div>

                <div>
                    <label class="block text-xs font-bold text-slate-700 mb-1">Catatan Tambahan (Opsional)</label>
                    <textarea name="notes" rows="2" placeholder="e.g. Pelanggan menukar dari size M ke L" class="w-full text-xs p-2 rounded-lg border border-slate-300 focus:ring-2 focus:ring-slate-900 focus:outline-none"></textarea>
                </div>
            </div>

            <div class="pos-modal-footer">
                <button type="button" id="btn-dismiss-return-modal" class="pos-btn-ghost text-xs">Batal</button>
                <button type="submit" class="pos-btn-pay text-xs bg-rose-600 hover:bg-rose-700">Simpan & Cetak Bukti Retur ✓</button>
            </div>
        </form>
    </div>
</div>

<script>
    document.addEventListener('DOMContentLoaded', () => {
        const modal = document.getElementById('order-detail-modal');
        const closeBtn = document.getElementById('btn-close-detail-modal');
        const dismissBtn = document.getElementById('btn-dismiss-detail-modal');

        const title = document.getElementById('detail-modal-title');
        const sub = document.getElementById('detail-modal-sub');
        const custName = document.getElementById('detail-customer-name');
        const custPhone = document.getElementById('detail-customer-phone');
        const address = document.getElementById('detail-shipping-address');
        const payment = document.getElementById('detail-payment-method');
        const cashier = document.getElementById('detail-cashier');
        const notes = document.getElementById('detail-notes');
        const subtotal = document.getElementById('detail-subtotal');
        const discountRow = document.getElementById('detail-row-discount');
        const discount = document.getElementById('detail-discount');
        const total = document.getElementById('detail-total');
        const itemsContainer = document.getElementById('detail-items-container');
        const btnReceipt = document.getElementById('detail-btn-receipt');

        let activeOrderId = null;

        document.querySelectorAll('.btn-open-order-detail').forEach(btn => {
            btn.addEventListener('click', () => {
                const inv = btn.getAttribute('data-invoice');
                const orderId = btn.getAttribute('data-order-id');
                activeOrderId = orderId;
                title.textContent = `Pesanan #${inv}`;
                sub.textContent = `Waktu Transaksi: ${btn.getAttribute('data-date')}`;
                custName.textContent = btn.getAttribute('data-customer');
                custPhone.textContent = btn.getAttribute('data-phone');
                address.textContent = btn.getAttribute('data-address');
                payment.textContent = btn.getAttribute('data-payment');
                cashier.textContent = btn.getAttribute('data-cashier');
                notes.textContent = btn.getAttribute('data-notes');
                subtotal.textContent = btn.getAttribute('data-subtotal');
                total.textContent = btn.getAttribute('data-total');

                const discVal = btn.getAttribute('data-discount');
                if (discVal && discVal !== 'Rp 0') {
                    discountRow.style.display = 'flex';
                    discount.textContent = `- ${discVal} (${btn.getAttribute('data-coupon') || 'Promo'})`;
                } else {
                    discountRow.style.display = 'none';
                }

                btnReceipt.href = `/admin/pos/receipt/${orderId}`;

                // Items list
                const items = JSON.parse(btn.getAttribute('data-items') || '[]');
                currentOrderItems = items;
                itemsContainer.innerHTML = '';
                items.forEach(it => {
                    const el = document.createElement('div');
                    el.className = 'p-2.5 flex justify-between items-center text-xs';
                    el.innerHTML = `
                        <div>
                            <strong class="text-slate-900 block">${it.product_name}</strong>
                            <span class="text-slate-500">${it.quantity}x @ Rp ${Number(it.price).toLocaleString('id-ID')}</span>
                        </div>
                        <strong class="text-slate-800">Rp ${Number(it.quantity * it.price).toLocaleString('id-ID')}</strong>
                    `;
                    itemsContainer.appendChild(el);
                });

                modal.classList.remove('hidden');
            });
        });

        const closeModal = () => modal.classList.add('hidden');
        closeBtn?.addEventListener('click', closeModal);
        dismissBtn?.addEventListener('click', closeModal);
        modal?.addEventListener('click', (e) => {
            if (e.target === modal) closeModal();
        });

        // Return Modal Logic
        const returnModal = document.getElementById('order-return-modal');
        const btnOpenReturn = document.getElementById('btn-open-return-form');
        const btnCloseReturn = document.getElementById('btn-close-return-modal');
        const btnDismissReturn = document.getElementById('btn-dismiss-return-modal');
        const returnForm = document.getElementById('order-return-form');
        const returnSelect = document.getElementById('return-item-select');
        const returnQty = document.getElementById('return-qty');
        const returnAmount = document.getElementById('return-amount');
        let currentOrderItems = [];

        btnOpenReturn?.addEventListener('click', () => {
            if (!activeOrderId) return;
            returnForm.action = `/admin/orders/${activeOrderId}/returns`;
            returnSelect.innerHTML = '';
            currentOrderItems.forEach(it => {
                const opt = document.createElement('option');
                opt.value = it.id;
                opt.dataset.price = it.price;
                opt.dataset.maxQty = it.quantity;
                opt.textContent = `${it.product_name} (${it.quantity} pcs @ Rp ${Number(it.price).toLocaleString('id-ID')})`;
                returnSelect.appendChild(opt);
            });
            updateReturnFormValues();
            returnModal.classList.remove('hidden');
        });

        const updateReturnFormValues = () => {
            const selectedOpt = returnSelect.selectedOptions[0];
            if (!selectedOpt) return;
            const price = parseFloat(selectedOpt.dataset.price) || 0;
            const maxQty = parseInt(selectedOpt.dataset.maxQty, 10) || 1;
            returnQty.max = maxQty;
            const qty = Math.min(parseInt(returnQty.value, 10) || 1, maxQty);
            returnQty.value = qty;
            returnAmount.value = qty * price;
        };

        returnSelect?.addEventListener('change', updateReturnFormValues);
        returnQty?.addEventListener('input', updateReturnFormValues);

        const closeReturnModal = () => returnModal.classList.add('hidden');
        btnCloseReturn?.addEventListener('click', closeReturnModal);
        btnDismissReturn?.addEventListener('click', closeReturnModal);
        returnModal?.addEventListener('click', (e) => {
            if (e.target === returnModal) closeReturnModal();
        });
    });
</script>
@endsection
