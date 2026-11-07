<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Purchase Order & Reorder Sheet — {{ $poNumber }} — VarStore</title>
    <style>
        * { box-sizing: border-box; margin: 0; padding: 0; }
        body {
            font-family: 'Helvetica Neue', Helvetica, Arial, sans-serif;
            background: #f1f5f9;
            color: #0f172a;
            padding: 30px 20px;
            font-size: 13px;
            line-height: 1.4;
        }
        .container {
            max-width: 960px;
            margin: 0 auto;
        }
        .actions-bar {
            display: flex;
            justify-content: space-between;
            align-items: center;
            margin-bottom: 20px;
            background: #ffffff;
            padding: 14px 20px;
            border-radius: 8px;
            box-shadow: 0 1px 3px rgba(0,0,0,0.05);
            flex-wrap: wrap;
            gap: 12px;
        }
        .filter-form {
            display: flex;
            align-items: center;
            gap: 10px;
            font-size: 12px;
        }
        .filter-select {
            padding: 6px 10px;
            border-radius: 6px;
            border: 1px solid #cbd5e1;
            font-size: 12px;
            background: #fff;
        }
        .btn-group {
            display: flex;
            gap: 8px;
        }
        .btn {
            display: inline-flex;
            align-items: center;
            gap: 6px;
            padding: 8px 16px;
            border-radius: 6px;
            font-size: 12px;
            font-weight: 700;
            text-decoration: none;
            cursor: pointer;
            border: none;
        }
        .btn-primary { background: #0f172a; color: #ffffff; }
        .btn-secondary { background: #e2e8f0; color: #1e293b; }
        .btn-print { background: #2563eb; color: #ffffff; }

        .sheet-card {
            background: #ffffff;
            border: 2px solid #0f172a;
            border-radius: 8px;
            padding: 36px;
            box-shadow: 0 4px 12px rgba(0,0,0,0.06);
        }
        .sheet-header {
            display: flex;
            justify-content: space-between;
            align-items: flex-start;
            border-bottom: 2px solid #0f172a;
            padding-bottom: 20px;
            margin-bottom: 24px;
        }
        .brand-title {
            font-size: 24px;
            font-weight: 900;
            letter-spacing: -0.5px;
            text-transform: uppercase;
        }
        .brand-sub {
            font-size: 11px;
            color: #475569;
            margin-top: 2px;
        }
        .doc-meta {
            text-align: right;
        }
        .doc-type {
            font-size: 16px;
            font-weight: 900;
            color: #0f172a;
            letter-spacing: 0.5px;
        }
        .doc-no {
            font-family: monospace;
            font-size: 13px;
            font-weight: 700;
            color: #2563eb;
            margin-top: 2px;
        }
        .kpi-row {
            display: grid;
            grid-template-columns: repeat(3, 1fr);
            gap: 16px;
            margin-bottom: 24px;
        }
        .kpi-card {
            background: #f8fafc;
            border: 1px solid #e2e8f0;
            border-radius: 6px;
            padding: 14px 16px;
        }
        .kpi-label {
            font-size: 10px;
            font-weight: 800;
            text-transform: uppercase;
            color: #64748b;
        }
        .kpi-value {
            font-size: 18px;
            font-weight: 900;
            color: #0f172a;
            margin-top: 4px;
        }

        .sheet-table {
            width: 100%;
            border-collapse: collapse;
            margin-bottom: 28px;
        }
        .sheet-table th {
            background: #0f172a;
            color: #ffffff;
            font-size: 11px;
            text-transform: uppercase;
            letter-spacing: 0.5px;
            padding: 10px 12px;
            text-align: left;
        }
        .sheet-table td {
            padding: 10px 12px;
            border-bottom: 1px solid #e2e8f0;
            font-size: 12px;
            vertical-align: middle;
        }
        .sheet-table tr:nth-child(even) td {
            background: #f8fafc;
        }
        .badge {
            display: inline-block;
            padding: 2px 8px;
            border-radius: 9999px;
            font-size: 10px;
            font-weight: 800;
            text-transform: uppercase;
        }
        .badge-out { background: #fee2e2; color: #991b1b; }
        .badge-critical { background: #ffedd5; color: #9a3412; }
        .badge-low { background: #fef3c7; color: #92400e; }

        .signatures {
            display: grid;
            grid-template-columns: repeat(3, 1fr);
            gap: 20px;
            margin-top: 40px;
            padding-top: 20px;
            border-top: 1px dashed #cbd5e1;
            text-align: center;
        }
        .sig-box {
            font-size: 11px;
            color: #475569;
        }
        .sig-space {
            height: 60px;
            border-bottom: 1px solid #94a3b8;
            margin-bottom: 8px;
        }
        .sig-title {
            font-weight: 800;
            color: #0f172a;
            text-transform: uppercase;
        }

        @media print {
            body { background: #fff; padding: 0; }
            .actions-bar { display: none !important; }
            .sheet-card { border: none; box-shadow: none; padding: 0; }
        }
    </style>
</head>
<body>

<div class="container">
    <!-- Screen Actions Bar -->
    <div class="actions-bar">
        <form method="GET" action="{{ route('admin.products.reorder-sheet') }}" class="filter-form">
            <label for="threshold" class="font-bold">Batas Stok:</label>
            <select name="threshold" id="threshold" class="filter-select" onchange="this.form.submit()">
                <option value="2" {{ $threshold == 2 ? 'selected' : '' }}>Stok Kritis (≤ 2 item)</option>
                <option value="5" {{ $threshold == 5 ? 'selected' : '' }}>Stok Menipis (≤ 5 item)</option>
                <option value="10" {{ $threshold == 10 ? 'selected' : '' }}>Stok Waspada (≤ 10 item)</option>
            </select>

            <label for="category" class="font-bold">Kategori:</label>
            <select name="category" id="category" class="filter-select" onchange="this.form.submit()">
                <option value="">Semua Kategori</option>
                @foreach($categories as $cat)
                    <option value="{{ $cat }}" {{ $category == $cat ? 'selected' : '' }}>{{ $cat }}</option>
                @endforeach
            </select>
        </form>

        <div class="btn-group">
            <button type="button" onclick="window.print()" class="btn btn-print">🖨 Cetak Dokumen PO</button>
            <a href="{{ route('admin.products.index') }}" class="btn btn-secondary">← Kembali ke Katalog</a>
        </div>
    </div>

    <!-- Printable Purchase Order Sheet -->
    <div class="sheet-card">
        <div class="sheet-header">
            <div>
                <h1 class="brand-title">{{ \App\Models\StoreSetting::get('store_name', 'VarStore Boutique') }}</h1>
                <p class="brand-sub">{{ \App\Models\StoreSetting::get('store_address', 'Jl. Merdeka No. 45, Bandung') }} | Telp: {{ \App\Models\StoreSetting::get('store_phone', '0812-3456-7890') }}</p>
                <p class="brand-sub" style="margin-top: 4px;">Atelier & Fashion Retail Inventory Management Division</p>
            </div>
            <div class="doc-meta">
                <div class="doc-type">PURCHASE ORDER & REPLENISHMENT</div>
                <div class="doc-no">{{ $poNumber }}</div>
                <p class="brand-sub" style="margin-top: 4px;">Tanggal: {{ now()->format('d M Y') }}</p>
                <p class="brand-sub">PIC: {{ auth()->user()->name ?? 'Store Manager' }}</p>
            </div>
        </div>

        <!-- KPI Summary Cards -->
        <div class="kpi-row">
            <div class="kpi-card">
                <div class="kpi-label">SKU Perlu Pengadaan</div>
                <div class="kpi-value">{{ $items->count() }} SKU Produk</div>
            </div>
            <div class="kpi-card">
                <div class="kpi-label">Total Unit Rekomendasi</div>
                <div class="kpi-value">{{ number_format($totalRecommendedUnits) }} Pcs</div>
            </div>
            <div class="kpi-card">
                <div class="kpi-label">Est. Kebutuhan Modal</div>
                <div class="kpi-value" style="color: #2563eb;">Rp {{ number_format($totalEstimatedCost, 0, ',', '.') }}</div>
            </div>
        </div>

        @if($items->isEmpty())
            <div style="text-align: center; padding: 40px; background: #f8fafc; border: 1px dashed #cbd5e1; border-radius: 6px; color: #64748b;">
                <p style="font-size: 14px; font-weight: 700; color: #1e293b;">✓ Semua Stok Produk Dalam Kondisi Aman</p>
                <p style="font-size: 12px; margin-top: 4px;">Tidak ada produk dengan sisa stok di bawah atau sama dengan batas {{ $threshold }} unit.</p>
            </div>
        @else
            <!-- Items Table -->
            <table class="sheet-table">
                <thead>
                    <tr>
                        <th style="width: 30px; text-align: center;">Cek</th>
                        <th style="width: 30px; text-align: center;">No</th>
                        <th>Kode / SKU & Nama Busana</th>
                        <th>Kategori</th>
                        <th style="text-align: center;">Stok Ada</th>
                        <th style="text-align: center;">Status</th>
                        <th style="text-align: center;">Target</th>
                        <th style="text-align: center;">Order Qty</th>
                        <th style="text-align: right;">Est. Harga Satuan</th>
                        <th style="text-align: right;">Total Biaya</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach($items as $idx => $row)
                        @php
                            $prod = $row['product'];
                            $badgeClass = $prod->stock <= 0 ? 'badge-out' : ($prod->stock <= 2 ? 'badge-critical' : 'badge-low');
                        @endphp
                        <tr>
                            <td style="text-align: center;"><input type="checkbox" style="width: 14px; height: 14px;"></td>
                            <td style="text-align: center; font-weight: 700;">{{ $idx + 1 }}</td>
                            <td>
                                <strong style="color: #0f172a;">{{ $prod->name }}</strong>
                                <div style="font-size: 10px; color: #64748b; font-family: monospace;">SKU: {{ $prod->sku }} | Size: {{ $prod->size ?? 'All' }} | Color: {{ $prod->color ?? '-' }}</div>
                            </td>
                            <td>{{ $prod->category }}</td>
                            <td style="text-align: center; font-weight: 900; font-family: monospace;">{{ $prod->stock }}</td>
                            <td style="text-align: center;">
                                <span class="badge {{ $badgeClass }}">
                                    {{ $prod->stock <= 0 ? 'Habis' : ($prod->stock <= 2 ? 'Kritis' : 'Menipis') }}
                                </span>
                            </td>
                            <td style="text-align: center; color: #64748b;">{{ $targetBuffer }}</td>
                            <td style="text-align: center; font-weight: 900; color: #2563eb; font-size: 13px;">
                                +{{ $row['recommended_qty'] }}
                            </td>
                            <td style="text-align: right; font-family: monospace;">
                                Rp {{ number_format($row['unit_cost'], 0, ',', '.') }}
                            </td>
                            <td style="text-align: right; font-weight: 700; font-family: monospace;">
                                Rp {{ number_format($row['estimated_cost'], 0, ',', '.') }}
                            </td>
                        </tr>
                    @endforeach
                </tbody>
                <tfoot>
                    <tr style="background: #f1f5f9; font-weight: 900;">
                        <td colspan="7" style="text-align: right; padding: 12px; font-size: 13px;">TOTAL ESTIMASI PENGADAAN :</td>
                        <td style="text-align: center; font-size: 14px; color: #2563eb;">{{ number_format($totalRecommendedUnits) }} Unit</td>
                        <td></td>
                        <td style="text-align: right; font-size: 14px; color: #0f172a;">Rp {{ number_format($totalEstimatedCost, 0, ',', '.') }}</td>
                    </tr>
                </tfoot>
            </table>
        @endif

        <!-- Signatures & Verification -->
        <div class="signatures">
            <div class="sig-box">
                <p>Diajukan Oleh (Gudang & Inventaris)</p>
                <div class="sig-space"></div>
                <p class="sig-title">( {{ auth()->user()->name ?? 'Staff Inventaris' }} )</p>
                <p style="font-size: 9px; color: #94a3b8;">Tgl: {{ now()->format('d/m/Y') }}</p>
            </div>
            <div class="sig-box">
                <p>Disetujui Oleh (Store Manager)</p>
                <div class="sig-space"></div>
                <p class="sig-title">( Head of Store Retail )</p>
                <p style="font-size: 9px; color: #94a3b8;">Tgl: ........................</p>
            </div>
            <div class="sig-box">
                <p>Konfirmasi Terima (Supplier / Vendor)</p>
                <div class="sig-space"></div>
                <p class="sig-title">( PIC Vendor / Penjahit )</p>
                <p style="font-size: 9px; color: #94a3b8;">Tgl: ........................</p>
            </div>
        </div>
    </div>
</div>

</body>
</html>