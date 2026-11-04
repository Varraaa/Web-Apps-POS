<?php

namespace Tests\Feature;

use App\Models\Discount;
use App\Models\Order;
use App\Models\Product;
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
}
