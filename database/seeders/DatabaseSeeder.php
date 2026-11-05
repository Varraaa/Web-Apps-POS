<?php

namespace Database\Seeders;

use App\Models\Customer;
use App\Models\Product;
use App\Models\StoreSetting;
use App\Models\User;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Str;

class DatabaseSeeder extends Seeder
{
    use WithoutModelEvents;

    /**
     * Seed the application's database.
     */
    public function run(): void
    {
        // 1. Akun Admin & Kasir Demo
        User::updateOrCreate(
            ['email' => 'admin@varstore.id'],
            ['name' => 'VarStore Admin', 'password' => Hash::make('password'), 'role' => 'admin']
        );

        User::updateOrCreate(
            ['email' => 'kasir@varstore.id'],
            ['name' => 'Sari Wardhani (Kasir 01)', 'password' => Hash::make('password'), 'role' => 'admin']
        );

        // 2. Pengaturan Default Toko
        StoreSetting::set('store_name', 'VARSTORE APPAREL');
        StoreSetting::set('store_tagline', 'Contemporary Haute Couture & Urban Wear');
        StoreSetting::set('store_address', 'Jl. Braga No. 88, Sumur Bandung, Kota Bandung, Jawa Barat 40111');
        StoreSetting::set('store_phone', '0812-3456-7890');
        StoreSetting::set('store_instagram', '@varstore.id');
        StoreSetting::set('store_whatsapp', '6281234567890');
        StoreSetting::set('receipt_header', 'Terima kasih atas kunjungan Anda di butik kami.');
        StoreSetting::set('receipt_footer', 'Barang yang sudah dibeli dapat ditukar maksimal 3 hari kerja dengan menyertakan struk kasir asli. Selamat berbelanja kembali!');

        // 3. Member Loyalitas Pelanggan (CRM)
        Customer::firstOrCreate(['phone' => '081298765432'], [
            'member_code' => 'MBR-GLD-001',
            'name' => 'Siti Nurhaliza',
            'phone' => '081298765432',
            'email' => 'siti@example.com',
            'tier' => 'gold',
            'points' => 120,
            'total_spent' => 2450000,
            'address' => 'Jl. Dago Asri No. 12, Bandung',
        ]);

        Customer::firstOrCreate(['phone' => '081345678901'], [
            'member_code' => 'MBR-SLV-002',
            'name' => 'Rizky Pratama',
            'phone' => '081345678901',
            'email' => 'rizky@example.com',
            'tier' => 'silver',
            'points' => 45,
            'total_spent' => 890000,
            'address' => 'Jl. Riau No. 45, Bandung',
        ]);

        Customer::firstOrCreate(['phone' => '081122334455'], [
            'member_code' => 'MBR-BRZ-003',
            'name' => 'Dian Pitaloka',
            'phone' => '081122334455',
            'email' => 'dian@example.com',
            'tier' => 'bronze',
            'points' => 15,
            'total_spent' => 320000,
            'address' => 'Jl. Buah Batu No. 108, Bandung',
        ]);

        // 4. Voucher Diskon Promosi
        $this->call(DiscountSeeder::class);

        // 5. Katalog Produk Fashion Lengkap
        $catalog = [
            'Outerwear' => [
                ['The Essential Overshirt', 649000, 'Cloud white', 'https://images.unsplash.com/photo-1598033129183-c4f50c736f10?auto=format&fit=crop&w=900&q=85', 3, 580000],
                ['Daily Utility Jacket', 799000, 'Graphite', 'https://images.unsplash.com/photo-1551028719-00167b16eac5?auto=format&fit=crop&w=900&q=85', 3, 720000],
                ['Soft Structure Blazer', 899000, 'Stone grey', 'https://images.unsplash.com/photo-1507679799987-c73779587ccf?auto=format&fit=crop&w=900&q=85', 2, 820000],
                ['Lightweight Field Coat', 949000, 'Deep navy', 'https://images.unsplash.com/photo-1544022613-e87ca75a784a?auto=format&fit=crop&w=900&q=85', 2, 870000],
                ['Relaxed Denim Jacket', 729000, 'Washed blue', 'https://images.unsplash.com/photo-1576871337632-b9aef4c17ab9?auto=format&fit=crop&w=900&q=85', 3, 650000],
            ],
            'Tops' => [
                ['Form Heavyweight Tee', 289000, 'Ink navy', 'https://images.unsplash.com/photo-1521572163474-6864f9cf17ab?auto=format&fit=crop&w=900&q=85', 3, 249000],
                ['Everyday Ribbed Tank', 219000, 'Cloud white', 'https://images.unsplash.com/photo-1562157873-818bc0726f68?auto=format&fit=crop&w=900&q=85', 4, 189000],
                ['Relaxed Oxford Shirt', 449000, 'Sky blue', 'https://images.unsplash.com/photo-1602810318383-e386cc2a3ccf?auto=format&fit=crop&w=900&q=85', 3, 399000],
                ['Merino Mock Neck', 529000, 'Charcoal', 'https://images.unsplash.com/photo-1576566588028-4147f3842f27?auto=format&fit=crop&w=900&q=85', 2, 479000],
                ['Studio Boxy Polo', 389000, 'Ecru', 'https://images.unsplash.com/photo-1620799140408-edc6dcb6d633?auto=format&fit=crop&w=900&q=85', 3, 340000],
            ],
            'Bottoms' => [
                ['Studio Pleat Trousers', 579000, 'Stone grey', 'https://images.unsplash.com/photo-1624378439575-d8705ad7ae80?auto=format&fit=crop&w=900&q=85', 3, 510000],
                ['Straight Denim 01', 499000, 'Raw indigo', 'https://images.unsplash.com/photo-1542272604-787c3835535d?auto=format&fit=crop&w=900&q=85', 3, 440000],
                ['Easy Drawstring Pant', 429000, 'Moss green', 'https://images.unsplash.com/photo-1517438476312-10d79c077509?auto=format&fit=crop&w=900&q=85', 3, 379000],
                ['Tailored City Short', 359000, 'Soft khaki', 'https://images.unsplash.com/photo-1591195853828-11db59a44f6b?auto=format&fit=crop&w=900&q=85', 3, 310000],
                ['Wide Leg Studio Pant', 599000, 'Black', 'https://images.unsplash.com/photo-1594633312681-425c7b97ccd1?auto=format&fit=crop&w=900&q=85', 3, 530000],
            ],
            'Accessories' => [
                ['Everyday Canvas Tote', 199000, 'Natural', 'https://images.unsplash.com/photo-1544816155-12df9643f363?auto=format&fit=crop&w=900&q=85', 3, 169000],
                ['Minimal Leather Belt', 249000, 'Dark brown', 'https://images.unsplash.com/photo-1624222247344-550fb60583dc?auto=format&fit=crop&w=900&q=85', 2, 219000],
                ['Ribbed Watch Cap', 179000, 'Heather grey', 'https://images.unsplash.com/photo-1575428652377-a2d80e2277fc?auto=format&fit=crop&w=900&q=85', 3, 149000],
                ['Everyday Crossbody', 399000, 'Ink navy', 'https://images.unsplash.com/photo-1553062407-98eeb64c6a62?auto=format&fit=crop&w=900&q=85', 2, 349000],
                ['Sculptural Metal Frame', 329000, 'Black', 'https://images.unsplash.com/photo-1511499767150-a48a237f0083?auto=format&fit=crop&w=900&q=85', 2, 289000],
            ],
        ];

        $discountPool = [20, 0, 15, 0, 25];

        foreach ($catalog as $category => $products) {
            foreach ($products as $index => [$name, $price, $color, $imageUrl, $wholesaleMin, $wholesalePrice]) {
                Product::create([
                    'name' => $name,
                    'slug' => Str::slug($name),
                    'category' => $category,
                    'description' => "Potongan artikel {$category} eksklusif dari atelier VARSTORE, dirancang dengan material premium berkualitas tinggi.",
                    'price' => $price,
                    'discount_percent' => $discountPool[$index % count($discountPool)],
                    'stock' => 15 + ($index * 3),
                    'wholesale_min_qty' => $wholesaleMin,
                    'wholesale_price' => $wholesalePrice,
                    'size' => $category === 'Accessories' ? 'One size' : 'S / M / L / XL',
                    'color' => $color,
                    'image_url' => $imageUrl,
                    'is_featured' => $index === 0,
                ]);
            }
        }
    }
}
