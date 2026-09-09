<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('discounts', function (Blueprint $table): void {
            $table->id();
            $table->string('code', 30)->unique();
            $table->string('name');
            $table->text('description')->nullable();
            $table->enum('type', ['percent', 'fixed'])->default('percent');
            $table->decimal('value', 12, 2);
            $table->decimal('min_spend', 12, 2)->default(0);
            $table->decimal('max_discount', 12, 2)->nullable();
            $table->boolean('is_active')->default(true);
            $table->timestamp('expires_at')->nullable();
            $table->timestamps();
        });

        Schema::table('products', function (Blueprint $table): void {
            $table->unsignedSmallInteger('discount_percent')->default(0)->after('price');
        });

        Schema::table('orders', function (Blueprint $table): void {
            $table->decimal('subtotal', 12, 2)->nullable()->after('shipping_address');
            $table->decimal('discount_amount', 12, 2)->default(0)->after('subtotal');
            $table->string('coupon_code', 30)->nullable()->after('discount_amount');
        });
    }

    public function down(): void
    {
        Schema::table('orders', function (Blueprint $table): void {
            $table->dropColumn(['subtotal', 'discount_amount', 'coupon_code']);
        });

        Schema::table('products', function (Blueprint $table): void {
            $table->dropColumn('discount_percent');
        });

        Schema::dropIfExists('discounts');
    }
};
