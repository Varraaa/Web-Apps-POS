@extends('layouts.admin')
@section('title', 'Pengaturan Toko & Format Struk — VarStore')

@section('content')
<div class="space-y-6">
    <!-- Header -->
    <div class="flex flex-col md:flex-row md:items-center justify-between gap-4 pb-4 border-b border-slate-200">
        <div>
            <div class="flex items-center gap-2">
                <span class="px-2.5 py-0.5 rounded-full text-xs font-bold bg-slate-900 text-white uppercase tracking-wider">
                    Konfigurasi POS
                </span>
                <span class="text-xs text-slate-500 font-medium">Pengaturan Toko</span>
            </div>
            <h1 class="text-2xl font-black text-slate-900 tracking-tight mt-1">Pengaturan & Kustomisasi Struk Kasir</h1>
            <p class="text-xs text-slate-500">Kelola identitas butik, kontak concierge WhatsApp, dan template cetak thermal printer.</p>
        </div>

        <button type="submit" form="settings-form" class="inline-flex items-center gap-2 px-5 py-2.5 bg-slate-900 hover:bg-black text-white text-xs font-bold rounded-xl transition shadow-sm">
            <span>Simpan Perubahan ✓</span>
        </button>
    </div>

    @if(session('success'))
        <div class="p-4 rounded-xl bg-emerald-50 border border-emerald-200 text-emerald-800 text-xs font-semibold flex items-center gap-2">
            <span>✓</span> {{ session('success') }}
        </div>
    @endif

    <!-- 2-Column Grid: Left Form, Right Live Thermal Preview -->
    <div class="grid grid-cols-1 lg:grid-cols-12 gap-6 items-start">
        <!-- Settings Form (7 cols) -->
        <div class="lg:col-span-7 bg-white p-6 rounded-2xl border border-slate-200/80 shadow-sm space-y-6">
            <form id="settings-form" method="POST" action="{{ route('admin.settings.update') }}" class="space-y-6">
                @csrf

                <!-- Section 1: Profil Butik -->
                <div>
                    <h3 class="text-sm font-extrabold text-slate-900 border-b border-slate-100 pb-2 mb-3">
                        1. Identitas Butik & Toko Fisik
                    </h3>

                    <div class="space-y-3">
                        <div>
                            <label class="block text-xs font-bold text-slate-700 mb-1">Nama Toko / Brand</label>
                            <input type="text" name="store_name" id="input-store-name" class="pos-input-field text-xs font-bold" 
                                   value="{{ old('store_name', $settings['store_name']) }}" required>
                        </div>

                        <div>
                            <label class="block text-xs font-bold text-slate-700 mb-1">Slogan / Tagline</label>
                            <input type="text" name="store_tagline" id="input-store-tagline" class="pos-input-field text-xs" 
                                   value="{{ old('store_tagline', $settings['store_tagline']) }}" placeholder="Contoh: Contemporary Haute Couture">
                        </div>

                        <div>
                            <label class="block text-xs font-bold text-slate-700 mb-1">Alamat Lengkap Butik</label>
                            <textarea name="store_address" id="input-store-address" rows="2" class="pos-input-field text-xs resize-none" required>{{ old('store_address', $settings['store_address']) }}</textarea>
                        </div>

                        <div class="grid grid-cols-1 sm:grid-cols-2 gap-3">
                            <div>
                                <label class="block text-xs font-bold text-slate-700 mb-1">No. Telepon / Hotline</label>
                                <input type="text" name="store_phone" id="input-store-phone" class="pos-input-field text-xs font-mono" 
                                       value="{{ old('store_phone', $settings['store_phone']) }}" required>
                            </div>

                            <div>
                                <label class="block text-xs font-bold text-slate-700 mb-1">WhatsApp Concierge (Format 62...)</label>
                                <input type="text" name="store_whatsapp" id="input-store-whatsapp" class="pos-input-field text-xs font-mono" 
                                       value="{{ old('store_whatsapp', $settings['store_whatsapp']) }}" placeholder="6281234567890">
                            </div>
                        </div>

                        <div>
                            <label class="block text-xs font-bold text-slate-700 mb-1">Akun Instagram</label>
                            <input type="text" name="store_instagram" id="input-store-instagram" class="pos-input-field text-xs font-mono" 
                                   value="{{ old('store_instagram', $settings['store_instagram']) }}" placeholder="@varstore.id">
                        </div>
                    </div>
                </div>

                <!-- Section 2: Struk Kasir Termal -->
                <div>
                    <h3 class="text-sm font-extrabold text-slate-900 border-b border-slate-100 pb-2 mb-3">
                        2. Kustomisasi Struk Kasir Thermal (80mm & 58mm)
                    </h3>

                    <div class="space-y-3">
                        <div>
                            <label class="block text-xs font-bold text-slate-700 mb-1">Pesan Sambutan Header</label>
                            <input type="text" name="receipt_header" id="input-receipt-header" class="pos-input-field text-xs" 
                                   value="{{ old('receipt_header', $settings['receipt_header']) }}" placeholder="Terima kasih atas kunjungan Anda">
                        </div>

                        <div>
                            <label class="block text-xs font-bold text-slate-700 mb-1">Catatan Kaki & Ketentuan Retur (Footer)</label>
                            <textarea name="receipt_footer" id="input-receipt-footer" rows="3" class="pos-input-field text-xs resize-none">{{ old('receipt_footer', $settings['receipt_footer']) }}</textarea>
                            <small class="text-[10px] text-slate-500">Dicetak di bagian bawah struk transaksi kasir.</small>
                        </div>
                    </div>
                </div>

                <div class="pt-2">
                    <button type="submit" class="w-full pos-btn-pay justify-center text-xs py-3">
                        Simpan Semua Pengaturan Toko ✓
                    </button>
                </div>
            </form>
        </div>

        <!-- Live Thermal Receipt Preview (5 cols) -->
        <div class="lg:col-span-5 space-y-3 sticky top-4">
            <div class="flex items-center justify-between">
                <span class="text-xs font-bold text-slate-700 uppercase tracking-wider">Live Preview Struk Kasir</span>
                <span class="text-[10px] bg-slate-100 text-slate-600 px-2 py-0.5 rounded font-mono">Format 80mm</span>
            </div>

            <!-- Authentic Thermal Paper Container -->
            <div class="bg-white p-6 rounded-xl border border-slate-300 shadow-md font-mono text-xs text-black space-y-2 select-none" style="font-family: 'Courier New', Courier, monospace;">
                <!-- Store Header -->
                <div class="text-center">
                    <h2 class="text-base font-black tracking-wide uppercase" id="preview-store-name">{{ $settings['store_name'] }}</h2>
                    <p class="text-[10px] text-slate-600" id="preview-store-tagline">{{ $settings['store_tagline'] }}</p>
                    <p class="text-[10px] text-slate-600 mt-0.5" id="preview-store-address">{{ $settings['store_address'] }}</p>
                    <p class="text-[10px] text-slate-600 font-mono mt-0.5">Telp: <span id="preview-store-phone">{{ $settings['store_phone'] }}</span> | IG: <span id="preview-store-instagram">{{ $settings['store_instagram'] }}</span></p>
                </div>

                <div class="border-t border-dashed border-black my-2"></div>

                <!-- Receipt Header message -->
                <p class="text-center text-[10px] italic text-slate-700" id="preview-receipt-header">"{{ $settings['receipt_header'] }}"</p>

                <div class="border-t border-dashed border-black my-2"></div>

                <!-- Mock Order Details -->
                <div class="flex justify-between text-[11px]">
                    <span>No: #INV-{{ date('Ymd') }}-0042</span>
                    <span>{{ date('d/m/Y H:i') }}</span>
                </div>
                <div class="flex justify-between text-[11px]">
                    <span>Kasir: Sari Wardhani</span>
                    <span>POS Terminal 01</span>
                </div>
                <div class="flex justify-between text-[11px]">
                    <span>Pelanggan: Dian Pitaloka</span>
                    <span class="text-emerald-700 font-bold">★ GOLD MEMBER</span>
                </div>

                <div class="border-t border-dashed border-black my-2"></div>

                <!-- Sample Items -->
                <div class="space-y-1.5 text-[11px]">
                    <div>
                        <div class="font-bold">Silk Trench Coat Nocturne</div>
                        <div class="flex justify-between text-[10px] text-slate-600">
                            <span>1 pcs x Rp 850.000 (M / Black)</span>
                            <span class="font-bold text-black font-mono">Rp 850.000</span>
                        </div>
                    </div>
                    <div>
                        <div class="font-bold">Cashmere Knit Sweater</div>
                        <div class="flex justify-between text-[10px] text-slate-600">
                            <span>1 pcs x Rp 420.000 (L / Cream)</span>
                            <span class="font-bold text-black font-mono">Rp 420.000</span>
                        </div>
                    </div>
                </div>

                <div class="border-t border-dashed border-black my-2"></div>

                <!-- Totals -->
                <div class="space-y-1 text-[11px]">
                    <div class="flex justify-between">
                        <span>Subtotal:</span>
                        <span class="font-mono">Rp 1.270.000</span>
                    </div>
                    <div class="flex justify-between text-rose-600 font-semibold">
                        <span>Voucher Diskon (NEWSEASON):</span>
                        <span class="font-mono">-Rp 100.000</span>
                    </div>
                    <div class="flex justify-between text-amber-700 font-semibold">
                        <span>Poin Loyalty (20 Poin):</span>
                        <span class="font-mono">-Rp 20.000</span>
                    </div>
                    <div class="border-t border-black pt-1 flex justify-between font-black text-[13px]">
                        <span>TOTAL AKHIR:</span>
                        <span class="font-mono">Rp 1.150.000</span>
                    </div>
                    <div class="flex justify-between text-[10px] text-slate-600">
                        <span>Pembayaran (CASH):</span>
                        <span class="font-mono">Rp 1.200.000</span>
                    </div>
                    <div class="flex justify-between text-[10px] text-slate-600">
                        <span>Kembalian:</span>
                        <span class="font-mono">Rp 50.000</span>
                    </div>
                </div>

                <div class="border-t border-dashed border-black my-2"></div>

                <!-- Footer text -->
                <div class="text-center text-[10px] text-slate-700 leading-tight">
                    <p id="preview-receipt-footer">{{ $settings['receipt_footer'] }}</p>
                    <div class="mt-2 text-[9px] text-slate-500 font-mono">
                        *** VARSTORE POS CLOUD ***
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<script>
document.addEventListener('DOMContentLoaded', () => {
    const bindLivePreview = (inputId, previewId, fallback, prefix = '', suffix = '') => {
        const input = document.getElementById(inputId);
        const preview = document.getElementById(previewId);
        if (!input || !preview) return;

        input.addEventListener('input', () => {
            const val = input.value.trim();
            preview.textContent = val ? `${prefix}${val}${suffix}` : fallback;
        });
    };

    bindLivePreview('input-store-name', 'preview-store-name', 'VARSTORE APPAREL');
    bindLivePreview('input-store-tagline', 'preview-store-tagline', 'Contemporary Haute Couture & Urban Wear');
    bindLivePreview('input-store-address', 'preview-store-address', 'Jl. Braga No. 88, Kota Bandung');
    bindLivePreview('input-store-phone', 'preview-store-phone', '0812-3456-7890');
    bindLivePreview('input-store-instagram', 'preview-store-instagram', '@varstore.id');
    bindLivePreview('input-receipt-header', 'preview-receipt-header', '"Terima kasih atas kunjungan Anda di butik kami."', '"', '"');
    bindLivePreview('input-receipt-footer', 'preview-receipt-footer', 'Barang yang sudah dibeli dapat ditukar maksimal 3 hari kerja.');
});
</script>
@endsection

