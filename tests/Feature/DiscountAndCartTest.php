<?php

namespace Tests\Feature;

use App\Models\Discount;
use App\Models\Product;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class DiscountAndCartTest extends TestCase
{
    use RefreshDatabase;

    public function test_product_discount_calculates_final_price_accurately(): void
    {
        $product = Product::create([
            'name' => 'Signature Linen Shirt',
            'slug' => 'signature-linen-shirt',
            'category' => 'Tops',
            'description' => 'Test shirt',
            'price' => 500000,
            'discount_percent' => 20,
            'stock' => 10,
            'size' => 'M',
            'color' => 'White',
            'image_url' => 'https://example.com/shirt.jpg',
            'is_featured' => true,
        ]);

        $this->assertTrue($product->has_discount);
        $this->assertEquals(400000, $product->final_price);
        $this->assertEquals(100000, $product->discount_savings);
    }

    public function test_coupon_discount_can_be_applied_to_cart_and_reduces_total(): void
    {
        $product = Product::create([
            'name' => 'Overcoat',
            'slug' => 'overcoat',
            'category' => 'Outerwear',
            'description' => 'Coat description',
            'price' => 1000000,
            'discount_percent' => 0,
            'stock' => 5,
            'size' => 'L',
            'color' => 'Black',
            'image_url' => 'https://example.com/coat.jpg',
        ]);

        $coupon = Discount::create([
            'code' => 'VARRA20',
            'name' => 'Diskon 20%',
            'type' => 'percent',
            'value' => 20,
            'min_spend' => 200000,
            'is_active' => true,
        ]);

        // Add to cart
        $this->post(route('cart.add', $product), ['quantity' => 1]);

        // Apply coupon
        $response = $this->post(route('cart.coupon.apply'), ['code' => 'VARRA20']);
        $response->assertSessionHas('coupon', 'VARRA20');
        $response->assertSessionHas('success');

        // Check cart page
        $cartPage = $this->get(route('cart.index'));
        $cartPage->assertStatus(200);
        $cartPage->assertSee('VARRA20');
        $cartPage->assertSee('Diskon Voucher (VARRA20)');
    }

    public function test_invalid_coupon_code_returns_error_feedback(): void
    {
        $product = Product::create([
            'name' => 'Trousers',
            'slug' => 'trousers',
            'category' => 'Bottoms',
            'description' => 'Test trousers',
            'price' => 400000,
            'stock' => 5,
            'image_url' => 'https://example.com/pants.jpg',
        ]);

        $this->post(route('cart.add', $product), ['quantity' => 1]);

        $response = $this->post(route('cart.coupon.apply'), ['code' => 'INVALIDCODE']);
        $response->assertSessionHas('error');
        $this->assertNull(session('coupon'));
    }

    public function test_checkout_records_discount_and_redirects_to_whatsapp(): void
    {
        $product = Product::create([
            'name' => 'Wool Blazer',
            'slug' => 'wool-blazer',
            'category' => 'Outerwear',
            'description' => 'Blazer',
            'price' => 800000,
            'stock' => 5,
            'image_url' => 'https://example.com/blazer.jpg',
        ]);

        Discount::create([
            'code' => 'HEMAT50K',
            'name' => 'Potongan Rp 50.000',
            'type' => 'fixed',
            'value' => 50000,
            'min_spend' => 300000,
            'is_active' => true,
        ]);

        $this->post(route('cart.add', $product), ['quantity' => 1]);
        $this->post(route('cart.coupon.apply'), ['code' => 'HEMAT50K']);

        $checkoutResponse = $this->post(route('checkout'), [
            'customer_name' => 'Jane Doe',
            'customer_phone' => '08123456789',
            'shipping_address' => 'Jl. Sudirman No. 1, Jakarta Pusat',
        ]);

        $checkoutResponse->assertRedirect();
        $this->assertDatabaseHas('orders', [
            'customer_name' => 'Jane Doe',
            'coupon_code' => 'HEMAT50K',
            'discount_amount' => 50000,
            'total' => 750000,
        ]);
    }
}
