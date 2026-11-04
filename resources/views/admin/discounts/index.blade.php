@extends('layouts.admin')
@section('title', 'Voucher & Promo Diskon — VarStore')

@section('content')
<div class="admin-wrap">
    <div class="admin-head">
        <div>
            <p class="admin-kicker">Marketing & Loyalty</p>
            <h1 class="text-2xl sm:text-3xl font-extrabold text-slate-900">Voucher & Diskon Toko</h1>
            <p class="text-xs text-slate-500 mt-1">Atur kupon potongan harga yang dapat digunakan di POS Kasir maupun Checkout Online.</p>
        </div>

        <a href="{{ route('admin.discounts.create') }}" class="pos-btn-pay text-xs px-4 py-2.5">
            <span>+ Buat Voucher Baru</span>
        </a>
    </div>

    @if (session('success'))
        <div class="mt-6 p-4 rounded-xl bg-emerald-50 border border-emerald-200 text-emerald-800 text-xs font-semibold flex items-center justify-between">
            <span class="flex items-center gap-2">
                <span class="text-emerald-600 font-bold text-base">✓</span>
                {{ session('success') }}
            </span>
        </div>
    @endif

    <div class="mt-8 bg-white border border-slate-200 rounded-xl overflow-hidden shadow-sm">
        <div class="overflow-x-auto">
            <table class="w-full text-left text-xs border-collapse">
                <thead>
                    <tr class="bg-slate-50 border-b border-slate-200 text-slate-500 font-bold uppercase tracking-wider text-[11px]">
                        <th class="p-4">Kode Voucher</th>
                        <th class="p-4">Nama Program & Ketentuan</th>
                        <th class="p-4">Besaran Diskon</th>
                        <th class="p-4">Min. Belanja</th>
                        <th class="p-4">Masa Berlaku</th>
                        <th class="p-4">Status</th>
                        <th class="p-4 text-right">Aksi</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-slate-100">
                    @forelse($discounts as $discount)
                        <tr class="hover:bg-slate-50/60 transition-colors">
                            <td class="p-4">
                                <span class="font-mono font-extrabold text-sm px-2.5 py-1 rounded bg-slate-900 text-white tracking-wider">
                                    {{ $discount->code }}
                                </span>
                            </td>
                            <td class="p-4">
                                <strong class="text-slate-900 block">{{ $discount->name }}</strong>
                                <span class="text-[11px] text-slate-500 line-clamp-1">{{ $discount->description ?: 'Tidak ada deskripsi' }}</span>
                            </td>
                            <td class="p-4">
                                <span class="text-sm font-black text-rose-600">
                                    {{ $discount->formatted_discount }}
                                </span>
                                @if($discount->max_discount)
                                    <small class="text-slate-400 block text-[10px]">Maks. Rp {{ number_format($discount->max_discount, 0, ',', '.') }}</small>
                                @endif
                            </td>
                            <td class="p-4">
                                <span class="text-slate-800 font-semibold">
                                    Rp {{ number_format($discount->min_spend, 0, ',', '.') }}
                                </span>
                            </td>
                            <td class="p-4">
                                @if($discount->expires_at)
                                    <span class="{{ $discount->expires_at->isPast() ? 'text-rose-600 font-bold' : 'text-slate-600' }}">
                                        {{ $discount->expires_at->format('d M Y') }}
                                        @if($discount->expires_at->isPast()) (Kadaluarsa) @endif
                                    </span>
                                @else
                                    <span class="text-emerald-600 font-semibold">Selalu Berlaku (Permanen)</span>
                                @endif
                            </td>
                            <td class="p-4">
                                <form method="POST" action="{{ route('admin.discounts.toggle', $discount) }}" class="inline">
                                    @csrf
                                    @method('PATCH')
                                    <button type="submit" class="text-[10px] font-bold px-2.5 py-1 rounded-full cursor-pointer transition-colors
                                        {{ $discount->is_active ? 'bg-emerald-100 text-emerald-800 hover:bg-emerald-200' : 'bg-slate-100 text-slate-500 hover:bg-slate-200' }}">
                                        {{ $discount->is_active ? '● Aktif' : '○ Nonaktif' }}
                                    </button>
                                </form>
                            </td>
                            <td class="p-4 text-right">
                                <div class="flex items-center justify-end gap-2">
                                    <a href="{{ route('admin.discounts.edit', $discount) }}" class="pos-btn-ghost text-[11px] py-1 px-2.5">
                                        Edit
                                    </a>
                                    <form method="POST" action="{{ route('admin.discounts.destroy', $discount) }}" 
                                          onsubmit="return confirm('Hapus voucher {{ $discount->code }}?');" class="inline">
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
                            <td colspan="7" class="p-12 text-center text-slate-400">
                                Belum ada kode voucher promo yang dibuat.
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>

    <div class="mt-6">
        {{ $discounts->links() }}
    </div>
</div>
@endsection
