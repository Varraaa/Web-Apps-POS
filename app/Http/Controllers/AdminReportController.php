<?php

namespace App\Http\Controllers;

use App\Models\Order;
use App\Models\OrderItem;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\View\View;
use Symfony\Component\HttpFoundation\StreamedResponse;

class AdminReportController extends Controller
{
    /**
     * Display the sales and POS performance analytics report.
     */
    public function index(Request $request): View
    {
        $period = $request->input('period', 'this_month');
        [$startDate, $endDate, $periodLabel] = $this->resolveDateRange($period, $request->input('start_date'), $request->input('end_date'));

        $ordersQuery = Order::query()
            ->whereBetween('created_at', [$startDate, $endDate])
            ->whereIn('status', ['completed', 'processed', 'delivered', 'shipped']);

        $totalRevenue = (float) (clone $ordersQuery)->sum('total');
        $totalOrders = (clone $ordersQuery)->count();
        $totalDiscount = (float) (clone $ordersQuery)->sum('discount_amount');
        $totalPointsDiscount = (float) (clone $ordersQuery)->sum('points_discount_amount');
        $totalDiscountsGiven = $totalDiscount + $totalPointsDiscount;
        $averageOrderValue = $totalOrders > 0 ? $totalRevenue / $totalOrders : 0;

        // Payment method breakdown
        $paymentMethods = (clone $ordersQuery)
            ->select('payment_method', DB::raw('count(*) as count'), DB::raw('sum(total) as revenue'))
            ->groupBy('payment_method')
            ->orderByDesc('revenue')
            ->get();

        // Source breakdown (POS vs Web Storefront)
        $sources = (clone $ordersQuery)
            ->select('source', DB::raw('count(*) as count'), DB::raw('sum(total) as revenue'))
            ->groupBy('source')
            ->orderByDesc('revenue')
            ->get();

        // Top Selling Products in period
        $topProducts = OrderItem::query()
            ->join('orders', 'order_items.order_id', '=', 'orders.id')
            ->whereBetween('orders.created_at', [$startDate, $endDate])
            ->whereIn('orders.status', ['completed', 'processed', 'delivered', 'shipped'])
            ->select(
                'order_items.product_id',
                'order_items.product_name',
                DB::raw('sum(order_items.quantity) as total_qty'),
                DB::raw('sum(order_items.subtotal) as total_sales')
            )
            ->groupBy('order_items.product_id', 'order_items.product_name')
            ->orderByDesc('total_qty')
            ->take(8)
            ->get();

        // Recent Orders in period
        $recentOrders = (clone $ordersQuery)
            ->with(['customer', 'items'])
            ->latest()
            ->paginate(15)
            ->withQueryString();

        return view('admin.reports.index', compact(
            'period',
            'periodLabel',
            'startDate',
            'endDate',
            'totalRevenue',
            'totalOrders',
            'totalDiscountsGiven',
            'averageOrderValue',
            'paymentMethods',
            'sources',
            'topProducts',
            'recentOrders'
        ));
    }

    /**
     * Export sales report to CSV spreadsheet.
     */
    public function exportCsv(Request $request): StreamedResponse
    {
        $period = $request->input('period', 'this_month');
        [$startDate, $endDate, $periodLabel] = $this->resolveDateRange($period, $request->input('start_date'), $request->input('end_date'));

        $orders = Order::query()
            ->whereBetween('created_at', [$startDate, $endDate])
            ->whereIn('status', ['completed', 'processed', 'delivered', 'shipped'])
            ->with('customer')
            ->latest()
            ->get();

        $filename = 'Laporan-Penjualan-' . date('Ymd-His') . '.csv';

        return response()->streamDownload(function () use ($orders): void {
            $handle = fopen('php://output', 'w');
            // Write UTF-8 BOM for Excel compatibility
            fwrite($handle, "\xEF\xBB\xBF");

            fputcsv($handle, [
                'No',
                'No. Invoice',
                'Tanggal',
                'Waktu',
                'Pelanggan',
                'No. HP',
                'Kasir',
                'Sumber',
                'Metode Pembayaran',
                'Subtotal',
                'Diskon Kupon',
                'Diskon Poin',
                'Total Akhir',
                'Uang Diterima',
                'Kembalian',
                'Status',
            ]);

            $no = 1;
            foreach ($orders as $o) {
                fputcsv($handle, [
                    $no++,
                    $o->invoice_number,
                    $o->created_at->format('Y-m-d'),
                    $o->created_at->format('H:i:s'),
                    $o->customer ? $o->customer->name : ($o->customer_name ?: 'Pelanggan Umum'),
                    $o->customer ? $o->customer->phone : ($o->customer_phone ?: '-'),
                    $o->cashier_name ?: 'Online Storefront',
                    strtoupper($o->source ?? 'POS'),
                    strtoupper($o->payment_method),
                    (float) $o->subtotal,
                    (float) $o->discount_amount,
                    (float) ($o->points_discount_amount ?? 0),
                    (float) $o->total,
                    (float) ($o->amount_paid ?? $o->total),
                    (float) ($o->change_amount ?? 0),
                    strtoupper($o->status),
                ]);
            }

            fclose($handle);
        }, $filename, [
            'Content-Type' => 'text/csv; charset=UTF-8',
            'Content-Disposition' => "attachment; filename=\"{$filename}\"",
        ]);
    }

    /**
     * Resolve start and end dates from human period.
     */
    private function resolveDateRange(string $period, ?string $customStart, ?string $customEnd): array
    {
        return match ($period) {
            'today' => [
                now()->startOfDay(),
                now()->endOfDay(),
                'Hari Ini (' . now()->translatedFormat('d M Y') . ')',
            ],
            'yesterday' => [
                now()->subDay()->startOfDay(),
                now()->subDay()->endOfDay(),
                'Kemarin (' . now()->subDay()->translatedFormat('d M Y') . ')',
            ],
            'this_week' => [
                now()->startOfWeek(),
                now()->endOfWeek(),
                'Minggu Ini',
            ],
            'last_30_days' => [
                now()->subDays(30)->startOfDay(),
                now()->endOfDay(),
                '30 Hari Terakhir',
            ],
            'custom' => [
                $customStart ? Carbon::parse($customStart)->startOfDay() : now()->startOfMonth(),
                $customEnd ? Carbon::parse($customEnd)->endOfDay() : now()->endOfDay(),
                'Periode Kustom',
            ],
            default => [
                now()->startOfMonth(),
                now()->endOfMonth(),
                'Bulan Ini (' . now()->translatedFormat('F Y') . ')',
            ],
        };
    }
}
