<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Z-Report Tutup Shift #{{ $shift->id }} — VarStore</title>
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
        .report-card {
            background: #fff;
            width: 320px;
            padding: 24px 18px;
            border-radius: 4px;
            box-shadow: 0 4px 12px rgba(0,0,0,0.08);
            margin-bottom: 20px;
        }
        .report-header {
            text-align: center;
            margin-bottom: 12px;
        }
        .report-title {
            font-size: 16px;
            font-weight: 900;
            letter-spacing: 0.5px;
            text-transform: uppercase;
        }
        .report-sub {
            font-size: 10px;
            color: #333;
            margin-top: 2px;
        }
        .report-tag {
            display: inline-block;
            margin-top: 4px;
            padding: 2px 8px;
            background: #000;
            color: #fff;
            font-size: 9px;
            font-weight: bold;
            letter-spacing: 1px;
            text-transform: uppercase;
        }
        .divider {
            border-top: 1px dashed #000;
            margin: 10px 0;
        }
        .double-divider {
            border-top: 2px dashed #000;
            margin: 10px 0;
        }
        .meta-row {
            display: flex;
            justify-content: space-between;
            font-size: 11px;
            margin-bottom: 3px;
        }
        .bold {
            font-weight: bold;
        }
        .total-box {
            background: #f8fafc;
            padding: 8px 10px;
            border: 1px solid #e2e8f0;
            margin: 8px 0;
        }
        .actions {
            display: flex;
            gap: 10px;
            width: 320px;
        }
        .btn {
            flex: 1;
            padding: 10px;
            border: none;
            cursor: pointer;
            font-weight: bold;
            font-size: 12px;
            border-radius: 4px;
            text-align: center;
            text-decoration: none;
        }
        .btn-print {
            background: #000;
            color: #fff;
        }
        .btn-close {
            background: #cbd5e1;
            color: #1e293b;
        }
        @media print {
            body {
                background: #fff;
                padding: 0;
            }
            .report-card {
                box-shadow: none;
                width: 100%;
                padding: 0;
                margin: 0;
            }
            .actions {
                display: none;
            }
        }
    </style>
</head>
<body>

    <div class="report-card">
        <div class="report-header">
            <h1 class="report-title">VARSTORE APPAREL</h1>
            <p class="report-sub">Butik & POS Retail Modern</p>
            <span class="report-tag">LAPORAN TUTUP REGISTER (Z-REPORT)</span>
        </div>

        <div class="divider"></div>

        <div class="meta-row">
            <span>No. Shift:</span>
            <span class="bold">#SH-{{ str_pad($shift->id, 5, '0', STR_PAD_LEFT) }}</span>
        </div>
        <div class="meta-row">
            <span>Kasir Bertugas:</span>
            <span class="bold">{{ $shift->cashier_name }}</span>
        </div>
        <div class="meta-row">
            <span>Buka Shift:</span>
            <span>{{ $shift->opened_at->format('d/m/Y H:i') }}</span>
        </div>
        <div class="meta-row">
            <span>Tutup Shift:</span>
            <span>{{ $shift->closed_at ? $shift->closed_at->format('d/m/Y H:i') : 'SEKARANG (ACTIVE)' }}</span>
        </div>
        <div class="meta-row">
            <span>Total Transaksi:</span>
            <span class="bold">{{ $shift->total_transactions }} Bon</span>
        </div>

        <div class="divider"></div>

        <div class="meta-row">
            <span>Modal Kas Awal (Float):</span>
            <span>Rp {{ number_format($shift->starting_cash, 0, ',', '.') }}</span>
        </div>
        <div class="meta-row">
            <span>Penjualan Tunai (Cash):</span>
            <span>+ Rp {{ number_format($shift->total_cash_sales, 0, ',', '.') }}</span>
        </div>
        @if($shift->totalCashIn() > 0)
            <div class="meta-row">
                <span>Kas Masuk Operasional (+):</span>
                <span>+ Rp {{ number_format($shift->totalCashIn(), 0, ',', '.') }}</span>
            </div>
        @endif
        @if($shift->totalCashOut() > 0)
            <div class="meta-row">
                <span>Kas Keluar Petty Cash (-):</span>
                <span>- Rp {{ number_format($shift->totalCashOut(), 0, ',', '.') }}</span>
            </div>
        @endif
        <div class="divider"></div>
        <div class="meta-row bold">
            <span>Total Uang Kas Seharusnya:</span>
            <span>Rp {{ number_format($shift->expected_cash, 0, ',', '.') }}</span>
        </div>
        <div class="meta-row bold">
            <span>Uang Kas Fisik Aktual:</span>
            <span>Rp {{ number_format($shift->actual_cash ?? 0, 0, ',', '.') }}</span>
        </div>

        <div class="total-box">
            <div class="meta-row bold">
                <span>SELISIH KAS (VARIANCE):</span>
                <span>
                    @php $diff = $shift->difference ?? 0; @endphp
                    @if($diff == 0)
                        Rp 0 (PAS ✓)
                    @elseif($diff > 0)
                        +Rp {{ number_format($diff, 0, ',', '.') }} (LEBIH)
                    @else
                        -Rp {{ number_format(abs($diff), 0, ',', '.') }} (KURANG)
                    @endif
                </span>
            </div>
        </div>

        <div class="divider"></div>
        <div style="font-size: 10px; font-weight: bold; margin-bottom: 6px; text-transform: uppercase;">
            Rincian Pembayaran Non-Tunai:
        </div>
        @forelse($byPayment as $method => $data)
            @if(strtolower($method) !== 'cash')
                <div class="meta-row">
                    <span style="text-transform: uppercase;">{{ $method }} ({{ $data['count'] }}x):</span>
                    <span>Rp {{ number_format($data['total'], 0, ',', '.') }}</span>
                </div>
            @endif
        @empty
            <div class="meta-row" style="color: #64748b;">
                <span>Tidak ada transaksi non-tunai</span>
            </div>
        @endforelse

        @if($shift->drawerTransactions && $shift->drawerTransactions->count() > 0)
            <div class="divider"></div>
            <div style="font-size: 10px; font-weight: bold; margin-bottom: 6px; text-transform: uppercase;">
                Mutasi Kas Laci (Petty Cash):
            </div>
            @foreach($shift->drawerTransactions as $dt)
                <div class="meta-row" style="font-size: 10px;">
                    <span>{{ $dt->created_at->format('H:i') }} - {{ $dt->notes }}:</span>
                    <span class="bold">
                        {{ $dt->type === 'cash_in' ? '+' : '-' }} {{ $dt->formatted_amount }}
                    </span>
                </div>
            @endforeach
        @endif

        <div class="double-divider"></div>

        <div class="meta-row bold" style="font-size: 13px;">
            <span>TOTAL OMZET SHIFT:</span>
            <span>Rp {{ number_format($shift->total_cash_sales + $shift->total_non_cash_sales, 0, ',', '.') }}</span>
        </div>

        @if($shift->notes)
            <div class="divider"></div>
            <div style="font-size: 10px; margin-top: 4px;">
                <span class="bold">Catatan Kasir:</span><br>
                <em>"{{ $shift->notes }}"</em>
            </div>
        @endif

        <div class="double-divider"></div>

        <div style="margin-top: 25px; display: flex; justify-content: space-between; font-size: 10px; text-align: center;">
            <div style="width: 45%;">
                Kasir Bertugas,<br><br><br><br>
                ( <strong>{{ $shift->cashier_name }}</strong> )
            </div>
            <div style="width: 45%;">
                Head Store / Spv,<br><br><br><br>
                ( .................... )
            </div>
        </div>

        <div style="text-align: center; font-size: 9px; color: #64748b; margin-top: 20px;">
            Dicetak otomatis oleh VarStore POS Cloud System<br>
            {{ now()->format('d/m/Y H:i:s') }}
        </div>
    </div>

    <div class="actions">
        <button onclick="window.print()" class="btn btn-print">🖨 Cetak Z-Report</button>
        <button onclick="window.close()" class="btn btn-close">Tutup</button>
    </div>

</body>
</html>

