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
        Schema::create('order_returns', function (Blueprint $table): void {
            $table->id();
            $table->foreignId('order_id')->constrained()->cascadeOnDelete();
            $table->foreignId('order_item_id')->nullable()->constrained('order_items')->nullOnDelete();
            $table->foreignId('product_id')->constrained()->cascadeOnDelete();
            $table->string('return_number', 40)->unique();
            $table->integer('quantity')->default(1);
            $table->decimal('refund_amount', 12, 2)->default(0);
            $table->string('reason', 50); // wrong_size, defective, customer_change_mind, not_as_expected, other
            $table->string('condition', 50); // intact_with_tags, opened_good, damaged, defect_factory
            $table->string('refund_type', 30)->default('cash'); // cash, store_credit, exchange
            $table->boolean('restock_item')->default(true);
            $table->text('notes')->nullable();
            $table->string('status', 30)->default('approved'); // approved, pending, rejected
            $table->string('processed_by', 100)->nullable();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('order_returns');
    }
};
