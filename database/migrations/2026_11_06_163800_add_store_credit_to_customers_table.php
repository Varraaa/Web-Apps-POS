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
        Schema::table('customers', function (Blueprint $table): void {
            $table->decimal('store_credit', 12, 2)->default(0)->after('points');
        });

        Schema::table('orders', function (Blueprint $table): void {
            $table->decimal('store_credit_used', 12, 2)->default(0)->after('points_discount_amount');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('customers', function (Blueprint $table): void {
            $table->dropColumn('store_credit');
        });

        Schema::table('orders', function (Blueprint $table): void {
            $table->dropColumn('store_credit_used');
        });
    }
};
