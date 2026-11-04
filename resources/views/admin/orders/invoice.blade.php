<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Invoice #{{ $order->invoice_number }} — VarStore</title>
    <style>
        * { box-sizing: border-box; margin: 0; padding: 0; }
        body {
            font-family: 'Helvetica Neue', Helvetica, Arial, sans-serif;
            background: #f8fafc;
            color: #0f172a;
            padding: 40px 20px;
            font-size: 13px;
            line-height: 1.5;
        }
        .invoice-box {
            max-width: 800px;
            margin: 0 auto;
            background: #ffffff;
            border: 1px solid #e2e8f0;
            border-radius: 12px;
            padding: 48px;
            box-shadow: 0 4px 16px rgba(0,0,0,0.04);
        }
        .invoice-head {
            display: flex;
            justify-content: space-between;
            align-items: flex-start;
            border-bottom: 2px solid #0f172a;
            padding-bottom: 24px;
            margin-bottom: 28px;
        }
        .brand-title {
            font-size: 24px;
            font-weight: 900;
            letter-spacing: -0.5px;
            color: #0f172a;
        }
        .brand-sub {
            font-size: 11px;
            color: #64748b;
            margin-top: 2px;
        }
        .invoice-meta {
            text-align: right;
        }
        .invoice-meta h2 {
            font-size: 20px;
            font-weight: 800;
            color: #0f172a;
            text-transform: uppercase;
        }
        .meta-tag {
            font-size: 11px;
            color: #64748b;
            margin-top: 4px;
        }
        .invoice-parties {
            display: grid;
            grid-template-columns: 1fr 1fr;
            gap: 40px;
            margin-bottom: 32px;
        }
        .party-box h4 {
            font-size: 11px;
            text-transform: uppercase;
            letter-spacing: 0.5px;
            color: #94a3b8;
            margin-bottom: 8px;
            font-weight: 800;
        }
        .party-box p {
            color: #334155;
            line-height: 1.6;
        }
        .party-box strong {
            color: #0f172a;
            font-size: 14px;
        }
        .invoice-table {
            width: 100%;
            border-collapse: collapse;
            margin-bottom: 28px;
        }
        .invoice-table th {
            background: #f8fafc;
            border-bottom: 2px solid #e2e8f0;
            padding: 12px 14px;
            text-align: left;
            font-size: 11px;
            font-weight: 800;
            text-transform: uppercase;
            color: #475569;
        }
        .invoice-table td {
            padding: 14px;
            border-bottom: 1px solid #f1f5f9;
            color: #1e293b;
        }
        .invoice-table td.text-right, .invoice-table th.text-right {
            text-align: right;
        }
        .invoice-summary {
            display: flex;
            justify-content: flex-end;
            margin-bottom: 36px;
        }
        .summary-card {
            width: 320px;
            background: #f8fafc;
            border: 1px solid #e2e8f0;
            border-radius: 8px;
            padding: 16px;
        }
        .summary-line {
            display: flex;
            justify-content: space-between;
            margin-bottom: 8px;
            font-size: 12px;
            color: #475569;
        }
        .summary-line.grand {
            border-top: 1.5px solid #cbd5e1;
            padding-top: 10px;
            margin-top: 8px;
            font-size: 15px;
            font-weight: 900;
            color: #0f172a;
        }
        .invoice-footer {
            border-top: 1px solid #e2e8f0;
            padding-top: 24px;
            display: flex;
            justify-content: space-between;
            align-items: flex-end;
            font-size: 11px;
            color: #64748b;
        }
        .signature-block {
            text-align: center;
            width: 160px;
        }
        .signature-space {
            height: 50px;
            border-bottom: 1px dashed #94a3b8;
            margin-bottom: 4px;
        }
        .no-print-bar {
            max-width: 800px;
            margin: 0 auto 20px auto;
            display: flex;
            justify-content: space-between;
            align-items: center;
        }
        .btn-action {
            background: #0f172a;
            color: #ffffff;
            font-weight: 800;
            padding: 9px 18px;
            border-radius: 8px;
            border: none;
            cursor: pointer;
            text-decoration: none;
            font-size: 12px;
            display: inline-flex;
            align-items: center;
            gap: 6px;
        }
        .btn-ghost {
            background: #e2e8f0;
            color: #1e293b;
        }

        @media print {
            body { background: #fff; padding: 0; }
            .invoice-box { border: none; box-shadow: none; padding: 0; }
            .no-print-bar { display: none !important; }
        }
    </style>
</head>
<body>

    <div class="no-print-bar">
        <a href="{{ route('admin.orders.index') }}" class="btn-action btn-ghost">← Kembali ke Pesanan</a>
        <div style="display: flex; gap: 8px;">
            <a href="{{ route('admin.orders.packing-slip', $order) }}" class="btn-action btn-ghost">📦 Packing Slip</a>
            <button type="button" onclick="window.print()" class="btn-action">🖨 Cetak Invoice Resmi (A4)</button>
        </div>
    </div>

    <div class="invoice-box">
        <div class="invoice-head">
            <div>
                <h1 class="brand-title">VARSTORE ATELIER</h1>
                <p class="brand-sub">Modern Fashion Essentials & Point of Sale System</p>
                <p class="brand-sub">Jl. Senopati No. 45, Kebayoran Baru, Jakarta Selatan 12190</p>
                <p class="brand-sub">support@varstore.com · +62 812-9757-7567</p>
            </div>
            <div class="invoice-meta">
                <h2>INVOICE</h2>
                <div class="meta-tag"><strong>No:</strong> #{{ $order->invoice_number }}</div>
                <div class="meta-tag"><strong>Tanggal:</strong> {{ $order->created_at->format('d F Y') }}</div>
                <div class="meta-tag"><strong>Status:</strong> {{ strtoupper($order->status_label) }}</div>
                <div class="meta-tag"><strong>Metode:</strong> {{ $order->payment_label }}</div>
            </div>
        </div>

        <div class="invoice-parties">
            <div class="party-box">
                <h4>Ditagihkan Kepada:</h4>
                <p><strong>{{ $order->customer_name }}</strong></p>
                <p>{{ $order->customer_phone }}</p>
                <p style="margin-top: 4px; white-space: pre-line;">{{ $order->shipping_address }}</p>
            </div>
            <div class="party-box">
                <h4>Informasi Transaksi:</h4>
                <p><strong>Kanal:</strong> {{ $order->isPos() ? 'POS Kasir Offline' : 'Toko Online WhatsApp' }}</p>
                <p><strong>Kasir / Operator:</strong> {{ $order->cashier_name ?? 'VarStore Team' }}</p>
                @if($order->notes)
                    <p style="margin-top: 4px;"><strong>Catatan:</strong> {{ $order->notes }}</p>
                @endif
            </div>
        </div>

        <table class="invoice-table">
            <thead>
                <tr>
                    <th style="width: 45%;">Deskripsi Produk</th>
                    <th class="text-right" style="width: 15%;">Jumlah</th>
                    <th class="text-right" style="width: 20%;">Harga Satuan</th>
                    <th class="text-right" style="width: 20%;">Total</th>
                </tr>
            </thead>
            <tbody>
                @foreach($order->items as $item)
                    <tr>
                        <td>
                            <strong>{{ $item->product_name }}</strong>
                            @if($item->product)
                                <div style="font-size: 11px; color: #64748b;">
                                    {{ $item->product->category }} · {{ $item->product->color }} ({{ $item->product->size }})
                                </div>
                            @endif
                        </td>
                        <td class="text-right">{{ $item->quantity }} pcs</td>
                        <td class="text-right">Rp {{ number_format($item->price, 0, ',', '.') }}</td>
                        <td class="text-right"><b>Rp {{ number_format($item->quantity * $item->price, 0, ',', '.') }}</b></td>
                    </tr>
                @endforeach
            </tbody>
        </table>

        <div class="invoice-summary">
            <div class="summary-card">
                <div class="summary-line">
                    <span>Subtotal Produk:</span>
                    <span>Rp {{ number_format($order->subtotal ?? $order->total, 0, ',', '.') }}</span>
                </div>
                @if($order->discount_amount > 0)
                    <div class="summary-line" style="color: #16a34a; font-weight: bold;">
                        <span>Diskon Voucher ({{ $order->coupon_code }}):</span>
                        <span>- Rp {{ number_format($order->discount_amount, 0, ',', '.') }}</span>
                    </div>
                @endif
                <div class="summary-line">
                    <span>Biaya Penanganan / Kirim:</span>
                    <span style="color: #16a34a; font-weight: bold;">Gratis</span>
                </div>
                <div class="summary-line grand">
                    <span>TOTAL DITAGIHKAN:</span>
                    <span>Rp {{ number_format($order->total, 0, ',', '.') }}</span>
                </div>
            </div>
        </div>

        <div class="invoice-footer">
            <div>
                <p><strong>Ketentuan & Garansi:</strong></p>
                <p>1. Penukaran ukuran dilayani maksimal 3 hari setelah barang diterima.</p>
                <p>2. Barang wajib dalam kondisi baru dengan tag merek masih terpasang.</p>
                <p>3. Simpan lembar invoice ini sebagai bukti kepemilikan dan garansi resmi.</p>
            </div>
            <div class="signature-block">
                <div class="signature-space"></div>
                <p><strong>VarStore Atelier</strong></p>
                <p>Authorized Cashier</p>
            </div>
        </div>
    </div>

</body>
</html>
