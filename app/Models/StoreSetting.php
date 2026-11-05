<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Schema;

class StoreSetting extends Model
{
    use HasFactory;

    protected $fillable = [
        'key',
        'value',
    ];

    /**
     * Default fallback configuration values.
     */
    public static array $defaults = [
        'store_name' => 'VARSTORE APPAREL',
        'store_tagline' => 'Contemporary Haute Couture & Urban Wear',
        'store_address' => 'Jl. Braga No. 88, Sumur Bandung, Kota Bandung, Jawa Barat 40111',
        'store_phone' => '0812-3456-7890',
        'store_instagram' => '@varstore.id',
        'store_whatsapp' => '6281234567890',
        'receipt_header' => 'Terima kasih atas kunjungan Anda di butik kami.',
        'receipt_footer' => 'Barang yang sudah dibeli dapat ditukar maksimal 3 hari kerja dengan menyertakan struk kasir asli. Selamat berbelanja kembali!',
        'enable_tax' => '0',
        'tax_percent' => '11',
    ];

    /**
     * Retrieve a setting value by key with optional fallback.
     */
    public static function get(string $key, mixed $default = null): mixed
    {
        try {
            if (! Schema::hasTable('store_settings')) {
                return $default ?? self::$defaults[$key] ?? null;
            }

            $record = static::where('key', $key)->first();

            return $record ? $record->value : ($default ?? self::$defaults[$key] ?? null);
        } catch (\Throwable) {
            return $default ?? self::$defaults[$key] ?? null;
        }
    }

    /**
     * Update or create a setting.
     */
    public static function set(string $key, mixed $value): void
    {
        static::updateOrCreate(
            ['key' => $key],
            ['value' => is_bool($value) ? ($value ? '1' : '0') : (string) $value]
        );
    }

    /**
     * Get all settings merged with defaults as key-value array.
     */
    public static function allAsMap(): array
    {
        try {
            if (! Schema::hasTable('store_settings')) {
                return self::$defaults;
            }

            $dbSettings = static::pluck('value', 'key')->toArray();

            return array_merge(self::$defaults, $dbSettings);
        } catch (\Throwable) {
            return self::$defaults;
        }
    }
}
