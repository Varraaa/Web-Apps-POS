<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Label Barcode Hangtag — {{ $product->name }}</title>
    <style>
        * { box-sizing: border-box; margin: 0; padding: 0; }
        body {
            font-family: 'Helvetica Neue', Helvetica, Arial, sans-serif;
            background: #f1f5f9;
            color: #000;
            padding: 30px 10px;
            display: flex;
            flex-direction: column;
            align-items: center;
        }
        .hangtag {
            background: #ffffff;
            width: 260px;
            border: 2px solid #000000;
            border-radius: 8px;
            padding: 20px 16px;
            text-align: center;
            box-shadow: 0 4px 12px rgba(0,0,0,0.06);
            position: relative;
        }
        .punch-hole {
            width: 14px;
            height: 14px;
            border: 2px solid #000000;
            border-radius: 50%;
            margin: 0 auto 12px auto;
            background: #f1f5f9;
        }
        .store-brand {
            font-size: 15px;
            font-weight: 900;
            letter-spacing: 2px;
            text-transform: uppercase;
        }
        .store-sub {
            font-size: 8px;
            text-transform: uppercase;
            letter-spacing: 1.5px;
            color: #444;
            margin-top: 1px;
            border-bottom: 1px solid #000;
            padding-bottom: 6px;
            margin-bottom: 8px;
        }
        .item-name {
            font-size: 13px;
            font-weight: 800;
            line-height: 1.25;
            margin-bottom: 4px;
        }
        .item-spec {
            font-size: 10px;
            color: #333;
            margin-bottom: 10px;
        }
        .price-box {
            background: #000;
            color: #fff;
            padding: 6px 10px;
            border-radius: 4px;
            margin-bottom: 12px;
        }
        .price-label {
            font-size: 8px;
            text-transform: uppercase;
            letter-spacing: 1px;
            color: #ccc;
            display: block;
        }
        .price-value {
            font-size: 16px;
            font-weight: 900;
        }
        .old-price {
            font-size: 10px;
            text-decoration: line-through;
            color: #aaa;
            margin-left: 4px;
        }
        .barcode-container {
            margin-top: 6px;
            border-top: 1px dashed #000;
            padding-top: 10px;
        }
        .barcode-lines {
            height: 38px;
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
        .barcode-num {
            font-family: monospace;
            font-size: 11px;
            font-weight: bold;
            letter-spacing: 2px;
        }
        .sku-tag {
            font-size: 9px;
            font-family: monospace;
            color: #555;
            margin-top: 2px;
        }
        .no-print {
            margin-top: 20px;
            display: flex;
            gap: 8px;
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
            .hangtag { border: 1.5px solid #000; box-shadow: none; margin: 0; }
            .punch-hole { background: #fff; }
            .no-print { display: none !important; }
        }
    </style>
</head>
<body>

    <div class="hangtag">
        <div class="punch-hole"></div>

        <div class="store-brand">VARSTORE</div>
        <div class="store-sub">ATELIER & READY-TO-WEAR</div>

        <div class="item-name">{{ $product->name }}</div>
        <div class="item-spec">
            <span>{{ $product->category }}</span> · 
            <span>{{ $product->color ?? 'Standar' }}</span> · 
            <b>{{ $product->size ?? 'All Size' }}</b>
        </div>

        <div class="price-box">
            <span class="price-label">Retail Price</span>
            <span class="price-value">{{ $product->formatted_final_price }}</span>
            @if($product->has_discount)
                <span class="old-price">{{ $product->formatted_price }}</span>
            @endif
        </div>

        <div class="barcode-container">
            <div class="barcode-lines">
                <span class="bar" style="width: 2px;"></span>
                <span class="bar" style="width: 1px;"></span>
                <span class="bar" style="width: 3px;"></span>
                <span class="bar" style="width: 2px;"></span>
                <span class="bar" style="width: 1px;"></span>
                <span class="bar" style="width: 4px;"></span>
                <span class="bar" style="width: 1px;"></span>
                <span class="bar" style="width: 3px;"></span>
                <span class="bar" style="width: 2px;"></span>
                <span class="bar" style="width: 1px;"></span>
                <span class="bar" style="width: 3px;"></span>
                <span class="bar" style="width: 4px;"></span>
                <span class="bar" style="width: 1px;"></span>
                <span class="bar" style="width: 2px;"></span>
                <span class="bar" style="width: 3px;"></span>
                <span class="bar" style="width: 1px;"></span>
            </div>
            <div class="barcode-num">{{ $product->barcode ?: '899' . str_pad($product->id, 9, '0', STR_PAD_LEFT) }}</div>
            <div class="sku-tag">SKU: {{ $product->sku ?: 'VAR-' . str_pad($product->id, 5, '0', STR_PAD_LEFT) }}</div>
        </div>
    </div>

    <div class="no-print">
        <button type="button" onclick="window.print()" class="btn">🖨 Cetak Label Hangtag</button>
        <a href="{{ route('admin.products.index') }}" class="btn btn-light">Kembali ke Katalog</a>
    </div>

</body>
</html>
