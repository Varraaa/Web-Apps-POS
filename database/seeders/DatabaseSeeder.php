<?php

namespace Database\Seeders;

use App\Models\Product;
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
        // User::factory(10)->create();

        User::factory()->create([
            'name' => 'Test User',
            'email' => 'test@example.com',
        ]);

        // Data contoh agar dashboard langsung memiliki isi saat pertama dijalankan.

        // Akun demo admin dan katalog awal untuk preview toko.
        User::updateOrCreate(['email' => 'admin@varstore'], ['name' => 'VarStore Admin', 'password' => Hash::make('password'), 'role' => 'admin']);
        // Lima produk per kategori agar koleksi storefront terasa lengkap.
        $catalog = [
            'Outerwear' => [
                ['The Essential Overshirt', 649000, 'Cloud white', 'https://images.unsplash.com/photo-1598033129183-c4f50c736f10?auto=format&fit=crop&w=900&q=85'],
                ['Daily Utility Jacket', 799000, 'Graphite', 'https://images.unsplash.com/photo-1551028719-00167b16eac5?auto=format&fit=crop&w=900&q=85'],
                ['Soft Structure Blazer', 899000, 'Stone grey', 'https://images.unsplash.com/photo-1507679799987-c73779587ccf?auto=format&fit=crop&w=900&q=85'],
                ['Lightweight Field Coat', 949000, 'Deep navy', 'https://images.unsplash.com/photo-1544022613-e87ca75a784a?auto=format&fit=crop&w=900&q=85'],
                ['Relaxed Denim Jacket', 729000, 'Washed blue', 'https://images.unsplash.com/photo-1576871337632-b9aef4c17ab9?auto=format&fit=crop&w=900&q=85'],
            ],
            'Tops' => [
                ['Form Heavyweight Tee', 289000, 'Ink navy', 'https://images.unsplash.com/photo-1521572163474-6864f9cf17ab?auto=format&fit=crop&w=900&q=85'],
                ['Everyday Ribbed Tank', 219000, 'Cloud white', 'https://images.unsplash.com/photo-1562157873-818bc0726f68?auto=format&fit=crop&w=900&q=85'],
                ['Relaxed Oxford Shirt', 449000, 'Sky blue', 'https://images.unsplash.com/photo-1602810318383-e386cc2a3ccf?auto=format&fit=crop&w=900&q=85'],
                ['Merino Mock Neck', 529000, 'Charcoal', 'https://images.unsplash.com/photo-1576566588028-4147f3842f27?auto=format&fit=crop&w=900&q=85'],
                ['Studio Boxy Polo', 389000, 'Ecru', 'https://images.unsplash.com/photo-1620799140408-edc6dcb6d633?auto=format&fit=crop&w=900&q=85'],
            ],
            'Bottoms' => [
                ['Studio Pleat Trousers', 579000, 'Stone grey', 'https://images.unsplash.com/photo-1624378439575-d8705ad7ae80?auto=format&fit=crop&w=900&q=85'],
                ['Straight Denim 01', 499000, 'Raw indigo', 'https://images.unsplash.com/photo-1542272604-787c3835535d?auto=format&fit=crop&w=900&q=85'],
                ['Easy Drawstring Pant', 429000, 'Moss green', 'https://images.unsplash.com/photo-1517438476312-10d79c077509?auto=format&fit=crop&w=900&q=85'],
                ['Tailored City Short', 359000, 'Soft khaki', 'https://images.unsplash.com/photo-1591195853828-11db59a44f6b?auto=format&fit=crop&w=900&q=85'],
                ['Wide Leg Studio Pant', 599000, 'Black', 'https://images.unsplash.com/photo-1594633312681-425c7b97ccd1?auto=format&fit=crop&w=900&q=85'],
            ],
            'Accessories' => [
                ['Everyday Canvas Tote', 199000, 'Natural', 'https://images.unsplash.com/photo-1544816155-12df9643f363?auto=format&fit=crop&w=900&q=85'],
                ['Minimal Leather Belt', 249000, 'Dark brown', 'https://images.unsplash.com/photo-1624222247344-550fb60583dc?auto=format&fit=crop&w=900&q=85'],
                ['Ribbed Watch Cap', 179000, 'Heather grey', 'https://images.unsplash.com/photo-1575428652377-a2d80e2277fc?auto=format&fit=crop&w=900&q=85'],
                ['Everyday Crossbody', 399000, 'Ink navy', 'https://images.unsplash.com/photo-1553062407-98eeb64c6a62?auto=format&fit=crop&w=900&q=85'],
                ['Sculptural Metal Frame', 329000, 'Black', 'https://images.unsplash.com/photo-1511499767150-a48a237f0083?auto=format&fit=crop&w=900&q=85'],
            ],
        ];

        foreach ($catalog as $category => $products) {
            foreach ($products as $index => [$name, $price, $color, $imageUrl]) {
                Product::create([
                    'name' => $name,
                    'slug' => Str::slug($name),
                    'category' => $category,
                    'description' => "A considered {$category} piece from the NOVA/FORM collection, designed for daily wear.",
                    'price' => $price,
                    'stock' => 12 + $index,
                    'size' => $category === 'Accessories' ? 'One size' : 'S / M / L / XL',
                    'color' => $color,
                    'image_url' => $imageUrl,
                    'is_featured' => $index === 0,
                ]);
            }
        }
    }
}
