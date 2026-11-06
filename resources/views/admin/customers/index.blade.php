@extends('layouts.admin')
@section('title', 'Membership & Pelanggan — VarStore')

@section('content')
<div class="admin-wrap">
    <div class="admin-head">
        <div>
            <p class="admin-kicker">CRM & Loyalty</p>
            <h1 class="text-2xl sm:text-3xl font-extrabold text-slate-900">Membership Pelanggan</h1>
            <p class="text-xs text-slate-500 mt-1">Kelola data pembeli, tingkat loyalitas (Tier), dan saldo poin belanja.</p>
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

    <div class="flex flex-wrap items-center justify-between gap-4 mt-8 pt-4 border-t border-slate-200">
        <div class="flex flex-wrap gap-2">
            <a href="{{ route('admin.customers.index') }}" 
               class="pos-cat-pill {{ empty($tier) ? 'active' : '' }}">
                Semua Member
            </a>
            <a href="{{ route('admin.customers.index', ['tier' => 'gold']) }}" 
               class="pos-cat-pill {{ $tier === 'gold' ? 'active' : '' }}">
                🏆 Gold Royale
            </a>
            <a href="{{ route('admin.customers.index', ['tier' => 'silver']) }}" 
               class="pos-cat-pill {{ $tier === 'silver' ? 'active' : '' }}">
                🥈 Silver VIP
            </a>
            <a href="{{ route('admin.customers.index', ['tier' => 'bronze']) }}" 
               class="pos-cat-pill {{ $tier === 'bronze' ? 'active' : '' }}">
                🥉 Bronze
            </a>
        </div>

        <form method="GET" action="{{ route('admin.customers.index') }}" class="flex items-center gap-2">
            @if($tier) <input type="hidden" name="tier" value="{{ $tier }}"> @endif
            <input type="text" name="search" value="{{ $search }}" 
                   placeholder="Cari nama, telp, atau ID..." 
                   class="text-xs px-3 py-2 rounded-lg border border-slate-300 bg-white outline-none w-52 focus:w-64 focus:border-slate-800 transition-all">
            <button type="submit" class="pos-btn-ghost text-xs py-2 px-3">Cari</button>
            @if($search || $tier)
                <a href="{{ route('admin.customers.index') }}" class="text-xs text-rose-600 font-bold hover:underline">Reset</a>
            @endif
        </form>
    </div>

    <div class="mt-6 bg-white border border-slate-200 rounded-xl overflow-hidden shadow-sm">
        <div class="overflow-x-auto">
            <table class="w-full text-left text-xs border-collapse">
                <thead>
                    <tr class="bg-slate-50 border-b border-slate-200 text-slate-500 font-bold uppercase tracking-wider text-[11px]">
                        <th class="p-4">Kode Member</th>
                        <th class="p-4">Nama Pelanggan</th>
                        <th class="p-4">Kontak & Alamat</th>
                        <th class="p-4">Loyalty Tier</th>
                        <th class="p-4">Saldo Poin</th>
                        <th class="p-4">Total Belanja</th>
                        <th class="p-4 text-right">Aksi</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-slate-100">
                    @forelse($customers as $customer)
                        <tr class="hover:bg-slate-50/60 transition-colors">
                            <td class="p-4">
                                <span class="font-mono font-bold text-xs bg-slate-100 text-slate-800 px-2 py-1 rounded border border-slate-200">
                                    {{ $customer->member_code }}
                                </span>
                            </td>
                            <td class="p-4">
                                <b class="text-slate-900 block">{{ $customer->name }}</b>
                                <span class="text-[11px] text-slate-500">{{ $customer->orders_count }} transaksi tercatat</span>
                            </td>
                            <td class="p-4">
                                <span class="block text-slate-800 font-semibold">{{ $customer->phone ?: '-' }}</span>
                                <small class="text-slate-400 block text-[10px] truncate max-w-xs">{{ $customer->address ?: 'Alamat belum diatur' }}</small>
                            </td>
                            <td class="p-4">
                                <span class="text-[10px] font-extrabold uppercase px-2 py-0.5 rounded border {{ $customer->tier_badge_class }}">
                                    {{ $customer->tier_label }}
                                </span>
                            </td>
                            <td class="p-4">
                                <span class="font-extrabold text-sm text-emerald-600">
                                    ★ {{ number_format($customer->points) }}
                                </span>
                                <small class="text-slate-400 block text-[10px]">Poin Loyalitas</small>
                            </td>
                            <td class="p-4">
                                <strong class="text-slate-900 block text-xs">
                                    {{ $customer->formatted_total_spent }}
                                </strong>
                            </td>
                            <td class="p-4 text-right">
                                <form method="POST" action="{{ route('admin.customers.destroy', $customer) }}" 
                                      onsubmit="return confirm('Hapus data pelanggan {{ $customer->name }}?');" class="inline">
                                    @csrf
                                    @method('DELETE')
                                    <button class="pos-btn-danger-ghost text-[11px] py-1 px-2.5">
                                        Hapus
                                    </button>
                                </form>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="7" class="p-12 text-center text-slate-400">
                                Belum ada data pelanggan yang terdaftar.
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>

    <div class="mt-6">
        {{ $customers->links() }}
    </div>
</div>
@endsection

