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
            $table->string('invoice_number', 50)->nullable()->unique()->after('id');
            $table->string('payment_method', 30)->default('whatsapp')->after('status');
            $table->decimal('amount_paid', 12, 2)->nullable()->after('payment_method');
            $table->decimal('change_amount', 12, 2)->default(0)->after('amount_paid');
            $table->string('cashier_name', 100)->nullable()->after('change_amount');
            $table->string('source', 20)->default('online')->after('cashier_name');
            $table->text('notes')->nullable()->after('source');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('orders', function (Blueprint $table): void {
            $table->dropColumn([
                'invoice_number',
                'payment_method',
                'amount_paid',
                'change_amount',
                'cashier_name',
                'source',
                'notes',
            ]);
        });
    }
};
