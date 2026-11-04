@extends('layouts.admin')
@section('title', isset($discount) ? "Edit Voucher {$discount->code} — VarStore" : 'Buat Voucher Baru — VarStore')

@section('content')
<div class="admin-wrap narrow-admin">
    <a href="{{ route('admin.discounts.index') }}" class="back-link">← Kembali ke Daftar Voucher</a>

    <div class="admin-head form-head mt-4">
        <div>
            <p class="admin-kicker">Marketing Studio</p>
            <h1 class="text-2xl font-extrabold text-slate-900">{{ isset($discount) ? "Edit Voucher: {$discount->code}" : 'Buat Voucher Promo Baru' }}</h1>
            <p class="text-xs text-slate-500 mt-1">Konfigurasikan kode kupon dan syarat diskon untuk pelanggan toko.</p>
        </div>
    </div>

    @if ($errors->any())
        <div class="mt-4 p-4 rounded-xl bg-rose-50 border border-rose-200 text-rose-800 text-xs font-semibold">
            <span class="block font-bold mb-1">Harap periksa kesalahan berikut:</span>
            <ul class="list-disc list-inside">
                @foreach ($errors->all() as $error)
                    <li>{{ $error }}</li>
                @endforeach
            </ul>
        </div>
    @endif

    <form method="POST" 
          action="{{ isset($discount) ? route('admin.discounts.update', $discount) : route('admin.discounts.store') }}" 
          class="product-form mt-6">
        @csrf
        @isset($discount)
            @method('PUT')
        @endisset

        <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
            <label>Kode Voucher (Kapital)
                <input type="text" name="code" value="{{ old('code', $discount->code ?? '') }}" 
                       placeholder="e.g. HEMAT30K" required style="text-transform: uppercase; font-family: monospace; font-weight: bold;">
                <small class="text-slate-400 font-normal">Hanya huruf dan angka tanpa spasi.</small>
            </label>

            <label>Nama Program Promo
                <input type="text" name="name" value="{{ old('name', $discount->name ?? '') }}" 
                       placeholder="e.g. Diskon Spesial Gajian" required>
            </label>
        </div>

        <div class="grid grid-cols-1 sm:grid-cols-3 gap-4">
            <label>Tipe Potongan
                <select name="type" required>
                    <option value="percent" @selected(old('type', $discount->type ?? '') === 'percent')>Persentase (%)</option>
                    <option value="fixed" @selected(old('type', $discount->type ?? '') === 'fixed')>Nominal Tetap (Rp)</option>
                </select>
            </label>

            <label>Nilai Diskon
                <input type="number" name="value" value="{{ old('value', $discount->value ?? '') }}" 
                       placeholder="e.g. 20 atau 50000" min="0" step="any" required>
                <small class="text-slate-400 font-normal">Isi angka diskon (persen atau rupiah).</small>
            </label>

            <label>Maksimal Potongan (Rp)
                <input type="number" name="max_discount" value="{{ old('max_discount', $discount->max_discount ?? '') }}" 
                       placeholder="e.g. 150000 (Kosongkan jika bebas)">
                <small class="text-slate-400 font-normal">Batas atas diskon (khusus tipe persen).</small>
            </label>
        </div>

        <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
            <label>Minimal Belanja (Rp)
                <input type="number" name="min_spend" value="{{ old('min_spend', $discount->min_spend ?? '0') }}" 
                       placeholder="e.g. 250000" min="0" required>
                <small class="text-slate-400 font-normal">Isi 0 jika tidak ada batas minimal belanja.</small>
            </label>

            <label>Tanggal Kadaluarsa (Opsional)
                <input type="date" name="expires_at" 
                       value="{{ old('expires_at', isset($discount->expires_at) ? $discount->expires_at->format('Y-m-d') : '') }}">
                <small class="text-slate-400 font-normal">Biarkan kosong jika voucher berlaku permanen.</small>
            </label>
        </div>

        <label>Deskripsi & Syarat Penggunaan
            <textarea name="description" rows="3" placeholder="Jelaskan secara singkat syarat dan ketentuan voucher ini...">{{ old('description', $discount->description ?? '') }}</textarea>
        </label>

        <label class="check-label">
            <input type="checkbox" name="is_active" value="1" 
                   @checked(old('is_active', $discount->is_active ?? true))>
            <span>Aktifkan voucher ini agar dapat langsung digunakan oleh kasir dan pembeli</span>
        </label>

        <div class="pt-4 border-t border-slate-100 flex items-center justify-between">
            <a href="{{ route('admin.discounts.index') }}" class="pos-btn-ghost text-xs">Batal</a>
            <button type="submit" class="pos-btn-pay text-xs px-6 py-3">
                <span>{{ isset($discount) ? 'Simpan Perubahan Voucher' : 'Terbitkan Voucher Baru' }} ↗</span>
            </button>
        </div>
    </form>
</div>
@endsection
