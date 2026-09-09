<?php

namespace Database\Seeders;

use App\Models\Discount;
use App\Models\Product;
use Illuminate\Database\Seeder;

class DiscountSeeder extends Seeder
{
    public function run(): void
    {
        $vouchers = [
            [
                'code' => 'VARRA10',
                'name' => 'Diskon Eksklusif Member',
                'description' => 'Potongan 10% untuk seluruh koleksi pakaian tanpa minimum belanja.',
                'type' => 'percent',
                'value' => 10,
                'min_spend' => 0,
                'max_discount' => 100000,
                'is_active' => true,
            ],
            [
                'code' => 'VARRA20',
                'name' => 'Spesial New Season 2026',
                'description' => 'Diskon 20% khusus pesanan baru minimal belanja Rp 350.000.',
                'type' => 'percent',
                'value' => 20,
                'min_spend' => 350000,
                'max_discount' => 200000,
                'is_active' => true,
            ],
            [
                'code' => 'HEMAT50K',
                'name' => 'Potongan Langsung Rp 50.000',
                'description' => 'Cashback langsung Rp 50.000 dengan pembelian minimal Rp 299.000.',
                'type' => 'fixed',
                'value' => 50000,
                'min_spend' => 299000,
                'max_discount' => 50000,
                'is_active' => true,
            ],
            [
                'code' => 'FASHIONVIP',
                'name' => 'VIP Concierge 15%',
                'description' => 'Potongan 15% maksimal hingga Rp 150.000 untuk pembelian 2 item atau lebih.',
                'type' => 'percent',
                'value' => 15,
                'min_spend' => 400000,
                'max_discount' => 150000,
                'is_active' => true,
            ],
        ];

        foreach ($vouchers as $voucher) {
            Discount::updateOrCreate(['code' => $voucher['code']], $voucher);
        }

        $discountPool = [20, 0, 15, 0, 25];
        Product::all()->each(function (Product $product, int $index) use ($discountPool): void {
            $product->update(['discount_percent' => $discountPool[$index % count($discountPool)]]);
        });
    }
}
