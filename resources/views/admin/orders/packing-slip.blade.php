<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Packing Slip #{{ $order->invoice_number }} — VarStore</title>
    <style>
        * { box-sizing: border-box; margin: 0; padding: 0; }
        body {
            font-family: 'Helvetica Neue', Helvetica, Arial, sans-serif;
            background: #f8fafc;
            color: #0f172a;
            padding: 30px 20px;
            font-size: 13px;
        }
        .slip-card {
            max-width: 750px;
            margin: 0 auto;
            background: #ffffff;
            border: 2px solid #0f172a;
            border-radius: 8px;
            padding: 36px;
        }
        .slip-header {
            display: flex;
            justify-content: space-between;
            align-items: center;
            border-bottom: 2px solid #0f172a;
            padding-bottom: 16px;
            margin-bottom: 24px;
        }
        .slip-title {
            font-size: 20px;
            font-weight: 900;
            text-transform: uppercase;
        }
        .slip-grid {
            display: grid;
            grid-template-columns: 1fr 1fr;
            gap: 20px;
            margin-bottom: 24px;
            background: #f8fafc;
            border: 1px solid #e2e8f0;
            padding: 18px;
            border-radius: 6px;
        }
        .slip-grid h4 {
            font-size: 11px;
            text-transform: uppercase;
            color: #64748b;
            margin-bottom: 6px;
            font-weight: 800;
        }
        .slip-table {
            width: 100%;
            border-collapse: collapse;
            margin-bottom: 24px;
        }
        .slip-table th {
            background: #0f172a;
            color: #fff;
            padding: 10px 12px;
            font-size: 11px;
            text-transform: uppercase;
            text-align: left;
        }
        .slip-table td {
            padding: 12px;
            border-bottom: 1px solid #e2e8f0;
        }
        .checkbox-box {
            width: 18px;
            height: 18px;
            border: 2px solid #0f172a;
            display: inline-block;
            vertical-align: middle;
        }
        .no-print {
            max-width: 750px;
            margin: 0 auto 16px auto;
            display: flex;
            justify-content: space-between;
        }
        .btn {
            background: #0f172a;
            color: #fff;
            border: none;
            padding: 8px 16px;
            border-radius: 6px;
            font-size: 12px;
            font-weight: bold;
            cursor: pointer;
            text-decoration: none;
        }
        .btn-light {
            background: #e2e8f0;
            color: #1e293b;
        }
        @media print {
            body { background: #fff; padding: 0; }
            .slip-card { border: 2px solid #000; padding: 20px; }
            .no-print { display: none !important; }
        }
    </style>
</head>
<body>

    <div class="no-print">
        <a href="{{ route('admin.orders.index') }}" class="btn btn-light">← Kembali ke Pesanan</a>
        <button type="button" onclick="window.print()" class="btn">🖨 Cetak Packing Slip</button>
    </div>

    <div class="slip-card">
        <div class="slip-header">
            <div>
                <h1 class="slip-title">LEMBAR PENGIRIMAN & PACKING SLIP</h1>
                <p style="font-size: 11px; color: #64748b;">VarStore Official Warehouse & Atelier</p>
            </div>
            <div style="text-align: right;">
                <div style="font-size: 16px; font-weight: 800; font-family: monospace;">#{{ $order->invoice_number }}</div>
                <small style="color: #64748b;">Tgl: {{ $order->created_at->format('d/m/Y') }}</small>
            </div>
        </div>

        <div class="slip-grid">
            <div>
                <h4>Penerima Pesanan:</h4>
                <strong style="font-size: 14px;">{{ $order->customer_name }}</strong>
                <p style="margin-top: 2px; font-weight: 600;">Telp: {{ $order->customer_phone }}</p>
                <p style="margin-top: 6px; white-space: pre-line;">{{ $order->shipping_address }}</p>
            </div>
            <div>
                <h4>Pengirim (Origin):</h4>
                <strong>VarStore Distribution Hub</strong>
                <p>Jl. Senopati No. 45, Jakarta Selatan 12190</p>
                <p>Telp: 0812-9757-7567</p>
                <div style="margin-top: 10px; padding: 6px 10px; background: #e2e8f0; border-radius: 4px; font-size: 11px;">
                    <strong>Kanal:</strong> {{ $order->isPos() ? 'POS Kasir Toko' : 'WhatsApp Delivery' }}
                </div>
            </div>
        </div>

        <table class="slip-table">
            <thead>
                <tr>
                    <th style="width: 10%; text-align: center;">Cek</th>
                    <th style="width: 60%;">Nama Barang & Spesifikasi</th>
                    <th style="width: 15%; text-align: center;">Qty</th>
                    <th style="width: 15%; text-align: center;">Status QC</th>
                </tr>
            </thead>
            <tbody>
                @foreach($order->items as $item)
                    <tr>
                        <td style="text-align: center;">
                            <span class="checkbox-box"></span>
                        </td>
                        <td>
                            <strong>{{ $item->product_name }}</strong>
                            @if($item->product)
                                <div style="font-size: 11px; color: #64748b;">
                                    Kategori: {{ $item->product->category }} · Warna: {{ $item->product->color }} · Ukuran: {{ $item->product->size }}
                                </div>
                            @endif
                        </td>
                        <td style="text-align: center; font-weight: bold; font-size: 14px;">
                            {{ $item->quantity }} pcs
                        </td>
                        <td style="text-align: center; color: #16a34a; font-weight: bold; font-size: 11px;">
                            [ PASSED ]
                        </td>
                    </tr>
                @endforeach
            </tbody>
        </table>

        <div style="display: flex; justify-content: space-between; align-items: flex-end; margin-top: 24px; padding-top: 18px; border-top: 1px dashed #cbd5e1;">
            <div>
                <p style="font-size: 11px; color: #64748b;">Total Kuantitas: <b>{{ $order->items->sum('quantity') }} pcs</b></p>
                <p style="font-size: 11px; color: #64748b;">Catatan Khusus: {{ $order->notes ?: 'Tidak ada' }}</p>
            </div>
            <div style="text-align: center; width: 140px;">
                <div style="height: 40px; border-bottom: 1px solid #000;"></div>
                <small style="font-size: 10px; font-weight: bold;">Petugas Packing</small>
            </div>
        </div>
    </div>

</body>
</html>
