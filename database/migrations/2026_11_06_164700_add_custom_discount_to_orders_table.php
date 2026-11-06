<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::table('orders', function (Blueprint $table): void {
            $table->string('custom_discount_type', 20)->nullable()->after('coupon_code');
            $table->decimal('custom_discount_value', 12, 2)->nullable()->after('custom_discount_type');
            $table->decimal('custom_discount_amount', 12, 2)->default(0)->after('custom_discount_value');
            $table->string('custom_discount_reason', 255)->nullable()->after('custom_discount_amount');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('orders', function (Blueprint $table): void {
            $table->dropColumn([
                'custom_discount_type',
                'custom_discount_value',
                'custom_discount_amount',
                'custom_discount_reason',
            ]);
        });
    }
};
