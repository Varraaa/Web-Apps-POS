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
            $table->string('sales_assistant_name', 100)->nullable()->after('cashier_name');
            $table->decimal('sales_commission_amount', 12, 2)->default(0)->after('sales_assistant_name');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('orders', function (Blueprint $table): void {
            $table->dropColumn([
                'sales_assistant_name',
                'sales_commission_amount',
            ]);
        });
    }
};
