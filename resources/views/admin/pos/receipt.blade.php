<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Struk POS #{{ $order->invoice_number }} — VarStore</title>
    <style>
        * {
            box-sizing: border-box;
            margin: 0;
            padding: 0;
        }
        body {
            font-family: 'Courier New', Courier, monospace;
            background-color: #f1f5f9;
            color: #000;
            font-size: 12px;
            line-height: 1.35;
            padding: 20px 10px;
            display: flex;
            flex-direction: column;
            align-items: center;
        }
        .receipt-card {
            background: #fff;
            width: 320px;
            padding: 24px 18px;
            border-radius: 4px;
            box-shadow: 0 4px 12px rgba(0,0,0,0.08);
            margin-bottom: 20px;
        }
        .receipt-header {
            text-align: center;
            margin-bottom: 14px;
        }
        .receipt-title {
            font-size: 18px;
            font-weight: 900;
            letter-spacing: 1px;
            text-transform: uppercase;
        }
        .receipt-sub {
            font-size: 10px;
            color: #333;
            margin-top: 2px;
        }
        .receipt-divider {
            border-top: 1px dashed #000;
            margin: 10px 0;
        }
        .receipt-double-divider {
            border-top: 2px dashed #000;
            margin: 10px 0;
        }
        .meta-row {
            display: flex;
            justify-content: space-between;
            font-size: 11px;
            margin-bottom: 3px;
        }
        .meta-label {
            color: #444;
        }
        .meta-val {
            font-weight: bold;
        }
        .receipt-items {
            width: 100%;
            margin: 8px 0;
        }
        .receipt-item {
            margin-bottom: 8px;
        }
        .receipt-item-title {
            font-weight: bold;
            font-size: 12px;
        }
        .receipt-item-calc {
            display: flex;
            justify-content: space-between;
            font-size: 11px;
            color: #222;
        }
        .total-row {
            display: flex;
            justify-content: space-between;
            font-size: 12px;
            margin-bottom: 4px;
        }
        .grand-total {
            font-size: 14px;
            font-weight: 900;
            margin-top: 4px;
        }
        .receipt-footer {
            text-align: center;
            margin-top: 14px;
            font-size: 10px;
            color: #333;
        }
        .barcode {
            margin: 12px auto 4px auto;
            text-align: center;
            letter-spacing: 4px;
            font-family: monospace;
            font-size: 14px;
            font-weight: bold;
        }
        .barcode-bars {
            height: 32px;
            display: flex;
            justify-content: center;
            align-items: flex-end;
            gap: 2px;
            margin-bottom: 4px;
        }
        .bar {
            background-color: #000;
            height: 100%;
        }
        .actions-bar {
            display: flex;
            gap: 10px;
            justify-content: center;
        }
        .btn-action {
            background: #0f172a;
            color: #fff;
            border: none;
            padding: 8px 16px;
            border-radius: 6px;
            font-size: 12px;
            font-weight: bold;
            cursor: pointer;
            font-family: system-ui, sans-serif;
            text-decoration: none;
        }
        .btn-secondary {
            background: #e2e8f0;
            color: #1e293b;
        }

        @media print {
            body {
                background: #fff;
                padding: 0;
                margin: 0;
            }
            .receipt-card {
                box-shadow: none;
                width: 100%;
                max-width: 80mm;
                padding: 4mm;
                margin: 0;
            }
            .actions-bar {
                display: none !important;
            }
        }
    </style>
</head>
<body>

    <div class="receipt-card">
        <div class="receipt-header">
            <h1 class="receipt-title">{{ \App\Models\StoreSetting::get('store_name', 'VARSTORE APPAREL') }}</h1>
            <p class="receipt-sub">{{ \App\Models\StoreSetting::get('store_tagline', 'HAUTE POS & READY-TO-WEAR') }}</p>
            <p class="receipt-sub">{{ \App\Models\StoreSetting::get('store_address', 'Jl. Braga No. 88, Bandung') }}</p>
            <p class="receipt-sub">Telp: {{ \App\Models\StoreSetting::get('store_phone', '0812-3456-7890') }} | IG: {{ \App\Models\StoreSetting::get('store_instagram', '@varstore.id') }}</p>
        </div>

        <div class="receipt-divider"></div>

        <div class="meta-row">
            <span class="meta-label">No. Struk</span>
            <span class="meta-val">#{{ $order->invoice_number }}</span>
        </div>
        <div class="meta-row">
            <span class="meta-label">Waktu</span>
            <span class="meta-val">{{ $order->created_at->format('d/m/Y H:i:s') }}</span>
        </div>
        <div class="meta-row">
            <span class="meta-label">Kasir</span>
            <span class="meta-val">{{ $order->cashier_name ?? 'Kasir Utama' }}</span>
        </div>
        <div class="meta-row">
            <span class="meta-label">Pelanggan</span>
            <span class="meta-val">{{ $order->customer_name }}</span>
        </div>

        <div class="receipt-divider"></div>

        <div class="receipt-items">
            @foreach($order->items as $item)
                <div class="receipt-item">
                    <div class="receipt-item-title">{{ $item->product_name }}</div>
                    <div class="receipt-item-calc">
                        <span>{{ $item->quantity }} x Rp {{ number_format($item->price, 0, ',', '.') }}</span>
                        <b>Rp {{ number_format($item->quantity * $item->price, 0, ',', '.') }}</b>
                    </div>
                </div>
            @endforeach
        </div>

        <div class="receipt-divider"></div>

        <div class="total-row">
            <span>Subtotal:</span>
            <span>Rp {{ number_format($order->subtotal ?? $order->total, 0, ',', '.') }}</span>
        </div>

        @if($order->discount_amount > 0)
            <div class="total-row">
                <span>Diskon ({{ $order->coupon_code ?? 'Promo' }}):</span>
                <span>- Rp {{ number_format($order->discount_amount, 0, ',', '.') }}</span>
            </div>
        @endif

        <div class="receipt-double-divider"></div>

        <div class="total-row grand-total">
            <span>TOTAL:</span>
            <span>Rp {{ number_format($order->total, 0, ',', '.') }}</span>
        </div>

        <div class="meta-row" style="margin-top: 8px;">
            <span class="meta-label">Metode Bayar:</span>
            <span class="meta-val">{{ $order->payment_label }}</span>
        </div>

        @if($order->is_split_payment && $order->payment_breakdown)
            <div class="meta-row">
                <span class="meta-label">Pembayaran 1 ({{ strtoupper($order->payment_breakdown['primary']['method'] ?? 'TUNAI') }}):</span>
                <span class="meta-val">Rp {{ number_format($order->payment_breakdown['primary']['amount'] ?? 0, 0, ',', '.') }}</span>
            </div>
            <div class="meta-row">
                <span class="meta-label">Pembayaran 2 ({{ strtoupper($order->payment_breakdown['secondary']['method'] ?? 'DIGITAL') }}):</span>
                <span class="meta-val">Rp {{ number_format($order->payment_breakdown['secondary']['amount'] ?? 0, 0, ',', '.') }}</span>
            </div>
            @if(($order->change_amount ?? 0) > 0)
                <div class="meta-row">
                    <span class="meta-label">Kembalian:</span>
                    <span class="meta-val">Rp {{ number_format($order->change_amount ?? 0, 0, ',', '.') }}</span>
                </div>
            @endif
        @elseif($order->payment_method === 'cash')
            <div class="meta-row">
                <span class="meta-label">Tunai Diterima:</span>
                <span class="meta-val">Rp {{ number_format($order->amount_paid ?? $order->total, 0, ',', '.') }}</span>
            </div>
            <div class="meta-row">
                <span class="meta-label">Kembalian:</span>
                <span class="meta-val">Rp {{ number_format($order->change_amount ?? 0, 0, ',', '.') }}</span>
            </div>
        @endif

        @if($order->notes)
            <div class="receipt-divider"></div>
            <div class="meta-row">
                <span class="meta-label">Catatan:</span>
                <span class="meta-val" style="font-size: 10px;">{{ $order->notes }}</span>
            </div>
        @endif

        <div class="receipt-divider"></div>

        <!-- Barcode pattern -->
        <div class="barcode">
            <div class="barcode-bars">
                <span class="bar" style="width: 2px;"></span>
                <span class="bar" style="width: 1px;"></span>
                <span class="bar" style="width: 3px;"></span>
                <span class="bar" style="width: 1px;"></span>
                <span class="bar" style="width: 2px;"></span>
                <span class="bar" style="width: 4px;"></span>
                <span class="bar" style="width: 1px;"></span>
                <span class="bar" style="width: 3px;"></span>
                <span class="bar" style="width: 2px;"></span>
                <span class="bar" style="width: 1px;"></span>
                <span class="bar" style="width: 4px;"></span>
                <span class="bar" style="width: 2px;"></span>
                <span class="bar" style="width: 1px;"></span>
                <span class="bar" style="width: 3px;"></span>
            </div>
            <span>*{{ $order->invoice_number }}*</span>
        </div>

        <div class="receipt-footer">
            <p>{{ \App\Models\StoreSetting::get('receipt_header', 'Terima kasih telah berbelanja di VarStore!') }}</p>
            <p>{{ \App\Models\StoreSetting::get('receipt_footer', 'Barang dapat ditukar maks. 3 hari setelah transaksi dengan membawa struk fisik ini.') }}</p>
            <p style="margin-top: 4px;">{{ \App\Models\StoreSetting::get('store_instagram', '@varstore.id') }}</p>
        </div>
    </div>

    <div class="actions-bar">
        <button type="button" onclick="window.print()" class="btn-action">🖨 Cetak Struk</button>
        <a href="{{ route('admin.pos.index') }}" class="btn-action btn-secondary">Kembali ke POS</a>
    </div>

</body>
</html>
