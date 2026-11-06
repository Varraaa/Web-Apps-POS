<?php

namespace Tests\Feature;

use App\Models\CashierShift;
use App\Models\Customer;
use App\Models\Discount;
use App\Models\Order;
use App\Models\PosHeldBill;
use App\Models\Product;
use App\Models\StoreSetting;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class PosSystemTest extends TestCase
{
    use RefreshDatabase;

    public function test_guest_cannot_access_pos_terminal(): void
    {
        $response = $this->get(route('admin.pos.index'));
        $response->assertRedirect(route('login'));
    }

    public function test_admin_can_access_pos_terminal(): void
    {
        $admin = User::factory()->create(['role' => 'admin']);

        $response = $this->actingAs($admin)->get(route('admin.pos.index'));
        $response->assertOk();
        $response->assertSee('Terminal Kasir POS');
    }

    public function test_pos_checkout_creates_order_and_deducts_stock(): void
    {
        $admin = User::factory()->create(['role' => 'admin']);
        $product = Product::factory()->create([
            'price' => 150000,
            'stock' => 10,
            'discount_percent' => 0,
        ]);

        $payload = [
            'customer_name' => 'Budi Santoso',
            'customer_phone' => '081298765432',
            'payment_method' => 'cash',
            'amount_paid' => 300000,
            'items' => [
                ['product_id' => $product->id, 'quantity' => 2],
            ],
        ];

        $response = $this->actingAs($admin)
            ->postJson(route('admin.pos.checkout'), $payload);

        $response->assertOk();
        $response->assertJson(['success' => true]);

        $this->assertDatabaseHas('orders', [
            'customer_name' => 'Budi Santoso',
            'source' => 'pos',
            'total' => 300000,
            'amount_paid' => 300000,
            'change_amount' => 0,
            'status' => 'completed',
        ]);

        $this->assertEquals(8, $product->fresh()->stock);
    }

    public function test_pos_cash_payment_calculates_change(): void
    {
        $admin = User::factory()->create(['role' => 'admin']);
        $product = Product::factory()->create([
            'price' => 75000,
            'stock' => 5,
            'discount_percent' => 0,
        ]);

        $payload = [
            'customer_name' => 'Siti Walk-in',
            'customer_phone' => '-',
            'payment_method' => 'cash',
            'amount_paid' => 100000,
            'items' => [
                ['product_id' => $product->id, 'quantity' => 1],
            ],
        ];

        $response = $this->actingAs($admin)
            ->postJson(route('admin.pos.checkout'), $payload);

        $response->assertOk();

        $order = Order::latest()->first();
        $this->assertEquals(75000, (float) $order->total);
        $this->assertEquals(100000, (float) $order->amount_paid);
        $this->assertEquals(25000, (float) $order->change_amount);
    }

    public function test_pos_checkout_applies_discount_coupon(): void
    {
        $admin = User::factory()->create(['role' => 'admin']);
        $product = Product::factory()->create([
            'price' => 200000,
            'stock' => 10,
            'discount_percent' => 0,
        ]);

        $coupon = Discount::create([
            'code' => 'DISC10',
            'name' => 'Diskon 10%',
            'type' => 'percent',
            'value' => 10,
            'min_spend' => 100000,
            'is_active' => true,
        ]);

        $payload = [
            'customer_name' => 'Rina Member',
            'payment_method' => 'qris',
            'coupon_code' => 'DISC10',
            'items' => [
                ['product_id' => $product->id, 'quantity' => 1],
            ],
        ];

        $response = $this->actingAs($admin)
            ->postJson(route('admin.pos.checkout'), $payload);

        $response->assertOk();

        $order = Order::latest()->first();
        $this->assertEquals(20000, (float) $order->discount_amount);
        $this->assertEquals(180000, (float) $order->total);
        $this->assertEquals('DISC10', $order->coupon_code);
    }

    public function test_customer_can_track_order_via_public_portal(): void
    {
        $order = Order::create([
            'invoice_number' => 'POS-20261104-TEST1',
            'customer_name' => 'Citra Maharani',
            'customer_phone' => '081234567890',
            'shipping_address' => 'Jl. Kemang Raya No. 10, Jakarta Selatan',
            'subtotal' => 250000,
            'total' => 250000,
            'status' => 'completed',
            'source' => 'pos',
            'payment_method' => 'cash',
        ]);

        $response = $this->get(route('tracking.index', ['q' => 'POS-20261104-TEST1']));
        $response->assertOk();
        $response->assertSee('Citra Maharani');
        $response->assertSee('POS-20261104-TEST1');
    }

    public function test_admin_can_hold_and_recall_bill(): void
    {
        $admin = User::factory()->create(['role' => 'admin']);
        $product = Product::factory()->create(['price' => 100000, 'stock' => 10]);

        $holdPayload = [
            'customer_name' => 'Antrean 02',
            'reference_note' => 'Menunggu fitting room',
            'items' => [
                [
                    'id' => $product->id,
                    'name' => $product->name,
                    'price' => 100000,
                    'quantity' => 2,
                    'stock' => 10,
                ],
            ],
        ];

        $holdResponse = $this->actingAs($admin)->postJson(route('admin.pos.hold-bill'), $holdPayload);
        $holdResponse->assertOk();
        $holdResponse->assertJson(['success' => true]);

        $heldBill = PosHeldBill::latest()->first();
        $this->assertNotNull($heldBill);
        $this->assertEquals(200000, (float) $heldBill->subtotal);

        // Recall bill
        $recallResponse = $this->actingAs($admin)->postJson(route('admin.pos.held-bills.recall', $heldBill));
        $recallResponse->assertOk();
        $recallResponse->assertJson(['success' => true]);

        $this->assertDatabaseMissing('pos_held_bills', ['id' => $heldBill->id]);
    }

    public function test_admin_can_open_and_close_cashier_shift(): void
    {
        $admin = User::factory()->create(['role' => 'admin']);

        // Open shift
        $openResponse = $this->actingAs($admin)->postJson(route('admin.pos.shift.open'), [
            'cashier_name' => 'Sari Wardhani',
            'starting_cash' => 200000,
            'notes' => 'Shift Pagi',
        ]);

        $openResponse->assertOk();
        $openResponse->assertJson(['success' => true]);

        $shift = CashierShift::active()->first();
        $this->assertNotNull($shift);
        $this->assertEquals(200000, (float) $shift->starting_cash);

        // Check active shift status
        $statusResponse = $this->actingAs($admin)->getJson(route('admin.pos.shift.current'));
        $statusResponse->assertOk();
        $statusResponse->assertJson(['active' => true]);

        // Close shift
        $closeResponse = $this->actingAs($admin)->postJson(route('admin.pos.shift.close'), [
            'actual_cash' => 250000,
            'notes' => 'Tutup shift tanpa selisih',
        ]);

        $closeResponse->assertOk();
        $closeResponse->assertJson(['success' => true]);

        $shift->refresh();
        $this->assertEquals('closed', $shift->status);
        $this->assertEquals(250000, (float) $shift->actual_cash);

        // Z-Report
        $zReportResponse = $this->actingAs($admin)->get(route('admin.pos.shift.z-report', $shift));
        $zReportResponse->assertOk();
        $zReportResponse->assertSee('Z-REPORT');
    }

    public function test_customer_loyalty_points_earned_and_redeemed_in_pos(): void
    {
        $admin = User::factory()->create(['role' => 'admin']);
        $customer = Customer::create([
            'name' => 'Maya VIP',
            'phone' => '081234567899',
            'tier' => 'silver',
            'points' => 50,
            'total_spent' => 500000,
        ]);

        $product = Product::factory()->create(['price' => 100000, 'stock' => 10, 'discount_percent' => 0]);

        $payload = [
            'customer_id' => $customer->id,
            'redeem_points' => true,
            'payment_method' => 'qris',
            'items' => [
                ['product_id' => $product->id, 'quantity' => 1],
            ],
        ];

        $response = $this->actingAs($admin)->postJson(route('admin.pos.checkout'), $payload);
        $response->assertOk();

        $order = Order::latest()->first();
        // 50 points = Rp 5.000 discount
        $this->assertEquals(5000, (float) $order->points_discount_amount);
        $this->assertEquals(95000, (float) $order->total);

        $customer->refresh();
        $this->assertEquals(9, $customer->points); // 50 redeemed + 9 earned from 95.000
    }

    public function test_admin_can_adjust_product_stock_opname(): void
    {
        $admin = User::factory()->create(['role' => 'admin']);
        $product = Product::factory()->create(['stock' => 10]);

        $response = $this->actingAs($admin)->post(route('admin.products.adjust-stock', $product), [
            'type' => 'in',
            'quantity' => 5,
            'reason' => 'Restok 5 pcs dari suplier',
        ]);

        $response->assertRedirect();
        $this->assertEquals(15, $product->fresh()->stock);

        $this->assertDatabaseHas('stock_adjustments', [
            'product_id' => $product->id,
            'type' => 'in',
            'quantity' => 5,
            'stock_before' => 10,
            'stock_after' => 15,
        ]);
    }

    public function test_wholesale_pricing_tier_applied_when_quantity_meets_minimum(): void
    {
        $admin = User::factory()->create(['role' => 'admin']);
        $product = Product::factory()->create([
            'price' => 200000,
            'discount_percent' => 0,
            'wholesale_min_qty' => 3,
            'wholesale_price' => 150000,
            'stock' => 10,
        ]);

        $payload = [
            'customer_name' => 'Reseller Grosir',
            'payment_method' => 'cash',
            'amount_paid' => 500000,
            'items' => [
                ['product_id' => $product->id, 'quantity' => 3],
            ],
        ];

        $response = $this->actingAs($admin)->postJson(route('admin.pos.checkout'), $payload);
        $response->assertOk();

        $order = Order::latest()->first();
        // 3 items * 150.000 wholesale = 450.000 (instead of 600.000)
        $this->assertEquals(450000, (float) $order->total);
    }

    public function test_admin_can_view_reports_and_export_csv(): void
    {
        $admin = User::factory()->create(['role' => 'admin']);

        $indexResponse = $this->actingAs($admin)->get(route('admin.reports.index', ['period' => 'this_month']));
        $indexResponse->assertOk();
        $indexResponse->assertSee('Laporan Penjualan', false);

        $csvResponse = $this->actingAs($admin)->get(route('admin.reports.export-csv', ['period' => 'this_month']));
        $csvResponse->assertOk();
        $csvResponse->assertHeader('content-type', 'text/csv; charset=UTF-8');
    }

    public function test_admin_can_update_store_settings(): void
    {
        $admin = User::factory()->create(['role' => 'admin']);

        $response = $this->actingAs($admin)->post(route('admin.settings.update'), [
            'store_name' => 'VARSTORE FLAGSHIP BANDUNG',
            'store_tagline' => 'Luxury Apparel & Haute Couture',
            'store_address' => 'Jl. Braga No. 99, Kota Bandung',
            'store_phone' => '0812-9999-8888',
        ]);

        $response->assertRedirect();
        $this->assertEquals('VARSTORE FLAGSHIP BANDUNG', StoreSetting::get('store_name'));
    }

    public function test_user_can_toggle_and_move_wishlist_to_cart(): void
    {
        $product = Product::factory()->create(['price' => 120000, 'stock' => 5]);

        // Toggle add to wishlist
        $toggleResponse = $this->post(route('wishlist.toggle', $product));
        $toggleResponse->assertRedirect();
        $this->assertEquals([$product->id], session('wishlist'));

        // View wishlist page
        $viewResponse = $this->get(route('wishlist.index'));
        $viewResponse->assertOk();
        $viewResponse->assertSee($product->name);

        // Move to cart
        $moveResponse = $this->post(route('wishlist.move-to-cart', $product));
        $moveResponse->assertRedirect(route('cart.index'));
        $this->assertEmpty(session('wishlist'));
        $this->assertArrayHasKey($product->id, session('cart'));
    }

    public function test_pos_split_payment_records_multiple_tenders_and_breakdown(): void
    {
        $admin = User::factory()->create(['role' => 'admin']);
        $product = Product::factory()->create([
            'price' => 100000,
            'stock' => 5,
            'discount_percent' => 0,
        ]);

        $payload = [
            'customer_name' => 'Doni Multi',
            'customer_phone' => '081234567890',
            'payment_method' => 'split',
            'is_split_payment' => true,
            'amount_paid' => 40000,
            'secondary_payment_method' => 'qris',
            'secondary_amount_paid' => 60000,
            'items' => [
                ['product_id' => $product->id, 'quantity' => 1],
            ],
        ];

        $response = $this->actingAs($admin)
            ->postJson(route('admin.pos.checkout'), $payload);

        $response->assertOk();
        $response->assertJson(['success' => true]);

        $this->assertDatabaseHas('orders', [
            'customer_name' => 'Doni Multi',
            'total' => 100000,
            'is_split_payment' => true,
            'payment_method' => 'cash',
            'amount_paid' => 40000,
            'secondary_payment_method' => 'qris',
            'secondary_amount_paid' => 60000,
        ]);

        $order = Order::where('customer_name', 'Doni Multi')->first();
        $this->assertNotNull($order->payment_breakdown);
        $this->assertStringContainsString('Tunai', $order->payment_label);
        $this->assertStringContainsString('QRIS', $order->payment_label);
    }
}
