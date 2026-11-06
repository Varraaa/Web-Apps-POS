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
            $table->boolean('is_split_payment')->default(false)->after('payment_method');
            $table->string('secondary_payment_method', 30)->nullable()->after('is_split_payment');
            $table->decimal('secondary_amount_paid', 12, 2)->nullable()->after('secondary_payment_method');
            $table->json('payment_breakdown')->nullable()->after('secondary_amount_paid');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('orders', function (Blueprint $table): void {
            $table->dropColumn([
                'is_split_payment',
                'secondary_payment_method',
                'secondary_amount_paid',
                'payment_breakdown',
            ]);
        });
    }
};
