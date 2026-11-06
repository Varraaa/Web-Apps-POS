<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>SLIP RETUR - {{ $orderReturn->return_number }}</title>
    <style>
        * {
            box-sizing: border-box;
            margin: 0;
            padding: 0;
        }
        body {
            font-family: -apple-system, BlinkMacSystemFont, "Segoe UI", Roboto, "Helvetica Neue", Arial, monospace;
            background: #f1f5f9;
            color: #000;
            padding: 20px;
            display: flex;
            flex-direction: column;
            align-items: center;
        }
        .slip-card {
            background: #fff;
            width: 80mm;
            padding: 6mm;
            border: 1px solid #e2e8f0;
            box-shadow: 0 4px 6px -1px rgba(0, 0, 0, 0.1);
        }
        .slip-header {
            text-align: center;
            margin-bottom: 8px;
        }
        .slip-title {
            font-size: 14px;
            font-weight: 900;
            text-transform: uppercase;
        }
        .slip-sub {
            font-size: 10px;
            color: #444;
            margin-top: 2px;
        }
        .slip-divider {
            border-top: 1px dashed #000;
            margin: 8px 0;
        }
        .slip-double-divider {
            border-top: 2px dashed #000;
            margin: 8px 0;
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
        .item-box {
            margin: 8px 0;
        }
        .item-title {
            font-size: 12px;
            font-weight: bold;
        }
        .item-meta {
            font-size: 10px;
            color: #333;
            margin-top: 2px;
        }
        .refund-total {
            font-size: 13px;
            font-weight: 900;
            display: flex;
            justify-content: space-between;
            margin-top: 4px;
        }
        .signatures {
            display: flex;
            justify-content: space-between;
            margin-top: 20px;
            font-size: 10px;
            text-align: center;
        }
        .sign-col {
            width: 45%;
        }
        .sign-space {
            height: 40px;
            border-bottom: 1px dotted #000;
            margin-bottom: 4px;
        }
        .actions-bar {
            margin-top: 16px;
            display: flex;
            gap: 8px;
        }
        .btn-print {
            background: #0f172a;
            color: #fff;
            padding: 8px 16px;
            border-radius: 6px;
            font-size: 12px;
            font-weight: bold;
            cursor: pointer;
            border: none;
        }
        @media print {
            body {
                background: #fff;
                padding: 0;
            }
            .slip-card {
                box-shadow: none;
                border: none;
                width: 100%;
                max-width: 80mm;
                padding: 3mm;
            }
            .actions-bar {
                display: none !important;
            }
        }
    </style>
</head>
<body>

    <div class="slip-card">
        <div class="slip-header">
            <h1 class="slip-title">{{ \App\Models\StoreSetting::get('store_name', 'VARSTORE APPAREL') }}</h1>
            <p class="slip-sub">BUKTI RETUR & PENGEMBALIAN DANA</p>
            <p class="slip-sub">Telp: {{ \App\Models\StoreSetting::get('store_phone', '0812-3456-7890') }}</p>
        </div>

        <div class="slip-divider"></div>

        <div class="meta-row">
            <span class="meta-label">No. Retur:</span>
            <span class="meta-val">#{{ $orderReturn->return_number }}</span>
        </div>
        <div class="meta-row">
            <span class="meta-label">No. Invoice Asal:</span>
            <span class="meta-val">#{{ $orderReturn->order->invoice_number }}</span>
        </div>
        <div class="meta-row">
            <span class="meta-label">Waktu:</span>
            <span class="meta-val">{{ $orderReturn->created_at->format('d/m/Y H:i:s') }}</span>
        </div>
        <div class="meta-row">
            <span class="meta-label">Kasir:</span>
            <span class="meta-val">{{ $orderReturn->processed_by ?? 'Kasir Utama' }}</span>
        </div>
        <div class="meta-row">
            <span class="meta-label">Pelanggan:</span>
            <span class="meta-val">{{ $orderReturn->order->customer_name }}</span>
        </div>

        <div class="slip-divider"></div>

        <div class="item-box">
            <div class="item-title">{{ $orderReturn->product->name }}</div>
            <div class="item-meta">Jumlah: {{ $orderReturn->quantity }} item</div>
            <div class="item-meta">Alasan: {{ $orderReturn->reason_label }}</div>
            <div class="item-meta">Kondisi: {{ $orderReturn->condition_label }}</div>
            <div class="item-meta">Restok Etalase: {{ $orderReturn->restock_item ? 'Ya (Kembali ke Stok)' : 'Tidak' }}</div>
        </div>

        <div class="slip-double-divider"></div>

        <div class="refund-total">
            <span>TOTAL REFUND:</span>
            <span>{{ $orderReturn->formatted_refund_amount }}</span>
        </div>
        <div class="meta-row" style="margin-top: 4px;">
            <span class="meta-label">Metode Refund:</span>
            <span class="meta-val">{{ $orderReturn->refund_type_label }}</span>
        </div>

        <div class="signatures">
            <div class="sign-col">
                <span>Pelanggan</span>
                <div class="sign-space"></div>
                <span>({{ $orderReturn->order->customer_name }})</span>
            </div>
            <div class="sign-col">
                <span>Kasir / Petugas</span>
                <div class="sign-space"></div>
                <span>({{ $orderReturn->processed_by ?? 'Petugas Toko' }})</span>
            </div>
        </div>

        <div class="slip-divider" style="margin-top: 14px;"></div>
        <p style="text-align: center; font-size: 9px; color: #555;">Simpan slip ini sebagai bukti sah pengembalian barang dan pencairan dana.</p>
    </div>

    <div class="actions-bar">
        <button class="btn-print" onclick="window.print()">🖨 Cetak Bukti Retur</button>
        <button class="btn-print" style="background: #64748b;" onclick="window.close()">Tutup</button>
    </div>

</body>
</html>
