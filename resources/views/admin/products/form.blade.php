@extends('layouts.admin')
@section('title', isset($product) ? "Edit Produk: {$product->name} — VarStore" : 'Tambah Produk Baru — VarStore')

@section('content')
<div class="admin-wrap narrow-admin">
    <a href="{{ route('admin.products.index') }}" class="back-link">← Kembali ke Katalog Produk</a>

    <div class="admin-head form-head mt-4">
        <div>
            <p class="admin-kicker">Katalog & Inventaris</p>
            <h1 class="text-2xl font-extrabold text-slate-900">{{ isset($product) ? "Edit Produk: {$product->name}" : 'Tambah Produk Busana Baru' }}</h1>
            <p class="text-xs text-slate-500 mt-1">Lengkapi informasi produk, harga ritel, diskon promosi, dan jumlah stok fisik.</p>
        </div>
    </div>

    @if ($errors->any())
        <div class="mt-4 p-4 rounded-xl bg-rose-50 border border-rose-200 text-rose-800 text-xs font-semibold">
            <span class="block font-bold mb-1">Harap periksa kesalahan input berikut:</span>
            <ul class="list-disc list-inside">
                @foreach ($errors->all() as $error)
                    <li>{{ $error }}</li>
                @endforeach
            </ul>
        </div>
    @endif

    <form method="POST"
          action="{{ isset($product) ? route('admin.products.update', $product) : route('admin.products.store') }}"
          class="product-form mt-6">
        @csrf
        @isset($product)
            @method('PUT')
        @endisset

        <!-- Live Image Preview Card -->
        <div class="p-4 bg-slate-50 border border-slate-200 rounded-xl flex flex-col sm:flex-row items-center gap-5">
            <div class="w-24 h-28 rounded-lg bg-slate-200 border border-slate-300 overflow-hidden shrink-0 flex items-center justify-center">
                <img id="form-img-preview" 
                     src="{{ old('image_url', $product->image_url ?? '') }}" 
                     alt="Preview Gambar" 
                     class="w-full h-full object-cover {{ empty(old('image_url', $product->image_url ?? '')) ? 'hidden' : '' }}"
                     onerror="this.classList.add('hidden'); document.getElementById('preview-placeholder').classList.remove('hidden');">
                <span id="preview-placeholder" class="text-xs text-slate-400 font-bold {{ !empty(old('image_url', $product->image_url ?? '')) ? 'hidden' : '' }}">
                    No Image
                </span>
            </div>
            <div class="flex-1 w-full">
                <label>URL Gambar Produk (Format Langsung / CDN)
                    <input type="url" id="form-img-input" name="image_url" 
                           value="{{ old('image_url', $product->image_url ?? '') }}" 
                           placeholder="https://images.unsplash.com/..." required>
                    <small class="text-slate-400 font-normal">Gunakan tautan gambar resolusi tinggi (Unsplash atau CDN).</small>
                </label>
            </div>
        </div>

        <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
            <label>Nama Produk
                <input type="text" name="name" value="{{ old('name', $product->name ?? '') }}" 
                       placeholder="e.g. The Essential Overshirt" required>
            </label>

            <label>Kategori Busana
                <input type="text" name="category" list="category-suggestions" 
                       value="{{ old('category', $product->category ?? '') }}" 
                       placeholder="e.g. Outerwear" required>
                <datalist id="category-suggestions">
                    <option value="Outerwear">
                    <option value="Tops">
                    <option value="Bottoms">
                    <option value="Accessories">
                </datalist>
            </label>
        </div>

        <!-- Pricing & Discount Real-time Calculator -->
        <div class="p-4 bg-slate-50 border border-slate-200 rounded-xl space-y-3">
            <h4 class="text-xs font-bold text-slate-700 uppercase tracking-wider">Penetapan Harga & Diskon Ritel</h4>
            <div class="grid grid-cols-1 sm:grid-cols-3 gap-4">
                <label>Harga Normal (Rp)
                    <input type="number" id="calc-price-input" name="price" 
                           value="{{ old('price', $product->price ?? '') }}" 
                           placeholder="650000" min="0" step="1000" required>
                </label>

                <label>Diskon Promo (%)
                    <input type="number" id="calc-discount-input" name="discount_percent" 
                           value="{{ old('discount_percent', $product->discount_percent ?? '0') }}" 
                           placeholder="0" min="0" max="99">
                    <small class="text-slate-400 font-normal">Isi 0 jika tidak sedang promo.</small>
                </label>

                <div>
                    <span class="block text-[11px] font-bold text-slate-600 mb-1">Harga Jual Akhir di Kasir:</span>
                    <div class="p-2.5 bg-white border border-slate-300 rounded-lg">
                        <strong class="text-sm font-black text-emerald-600" id="calc-final-preview">
                            Rp {{ number_format(old('price', $product->final_price ?? 0), 0, ',', '.') }}
                        </strong>
                        <small class="block text-[10px] text-slate-400" id="calc-savings-preview">Hemat Rp 0</small>
                    </div>
                </div>
            </div>
        </div>

        <!-- Wholesale / Bulk Bundle Pricing -->
        <div class="p-4 bg-amber-50/60 border border-amber-200 rounded-xl space-y-3">
            <div class="flex items-center justify-between">
                <h4 class="text-xs font-bold text-amber-900 uppercase tracking-wider">🏷 Harga Grosir / Pembelian Jumlah Banyak (Bundle Promo)</h4>
                <span class="text-[10px] text-amber-700 bg-amber-100 px-2 py-0.5 rounded font-semibold">Opsional</span>
            </div>
            <p class="text-xs text-amber-800">Tawarkan harga lebih murah per pcs jika pelanggan kasir membeli dalam kuantitas minimal tertentu.</p>
            <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                <label>Minimal Pembelian Grosir (Pcs)
                    <input type="number" name="wholesale_min_qty" 
                           value="{{ old('wholesale_min_qty', $product->wholesale_min_qty ?? '') }}" 
                           placeholder="Contoh: 3" min="2">
                    <small class="text-slate-400 font-normal">Kuantitas minimal untuk mendapatkan harga grosir (misal min. 3 pcs).</small>
                </label>

                <label>Harga Satuan Grosir (Rp / Pcs)
                    <input type="number" name="wholesale_price" 
                           value="{{ old('wholesale_price', $product->wholesale_price ?? '') }}" 
                           placeholder="Contoh: 280000" min="0" step="1000">
                    <small class="text-slate-400 font-normal">Harga khusus per satuan saat syarat minimal kuantitas terpenuhi.</small>
                </label>
            </div>
        </div>

        <div class="grid grid-cols-1 sm:grid-cols-3 gap-4">
            <label>Jumlah Stok Fisik (Pcs)
                <input type="number" name="stock" value="{{ old('stock', $product->stock ?? '10') }}" 
                       min="0" required>
            </label>

            <label>Varian Warna
                <input type="text" name="color" value="{{ old('color', $product->color ?? '') }}" 
                       placeholder="e.g. Cloud white">
            </label>

            <label>Pilihan Ukuran
                <input type="text" name="size" value="{{ old('size', $product->size ?? 'S / M / L / XL') }}" 
                       placeholder="e.g. S / M / L / XL">
            </label>
        </div>

        <label>Deskripsi & Material Pakaian
            <textarea name="description" rows="4" required placeholder="Jelaskan siluet, bahan kain (e.g. 100% Organic Cotton), dan saran gaya...">{{ old('description', $product->description ?? '') }}</textarea>
        </label>

        <label class="check-label">
            <input type="checkbox" name="is_featured" value="1" 
                   @checked(old('is_featured', $product->is_featured ?? false))>
            <span>Tampilkan sebagai Koleksi Unggulan (Featured) di Beranda Depan</span>
        </label>

        <div class="pt-4 border-t border-slate-100 flex items-center justify-between">
            <a href="{{ route('admin.products.index') }}" class="pos-btn-ghost text-xs">Batal</a>
            <button type="submit" class="pos-btn-pay text-xs px-6 py-3">
                <span>{{ isset($product) ? 'Simpan Perubahan Produk' : 'Terbitkan Produk Baru' }} ↗</span>
            </button>
        </div>
    </form>
</div>

<script>
    document.addEventListener('DOMContentLoaded', () => {
        // Live image preview
        const imgInput = document.getElementById('form-img-input');
        const imgPreview = document.getElementById('form-img-preview');
        const imgPlaceholder = document.getElementById('preview-placeholder');

        imgInput?.addEventListener('input', () => {
            const val = imgInput.value.trim();
            if (val) {
                imgPreview.src = val;
                imgPreview.classList.remove('hidden');
                imgPlaceholder.classList.add('hidden');
            } else {
                imgPreview.classList.add('hidden');
                imgPlaceholder.classList.remove('hidden');
            }
        });

        // Price calculator
        const priceInput = document.getElementById('calc-price-input');
        const discountInput = document.getElementById('calc-discount-input');
        const finalPreview = document.getElementById('calc-final-preview');
        const savingsPreview = document.getElementById('calc-savings-preview');

        const updateCalc = () => {
            const price = parseFloat(priceInput.value) || 0;
            const discount = Math.min(Math.max(parseFloat(discountInput.value) || 0, 0), 99);

            const savings = Math.round(price * (discount / 100));
            const finalPrice = Math.max(0, price - savings);

            finalPreview.textContent = 'Rp ' + finalPrice.toLocaleString('id-ID');
            savingsPreview.textContent = discount > 0 ? `Diskon ${discount}% (Hemat Rp ${savings.toLocaleString('id-ID')})` : 'Harga Normal';
        };

        priceInput?.addEventListener('input', updateCalc);
        discountInput?.addEventListener('input', updateCalc);
    });
</script>
@endsection
