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
        Schema::create('cashier_drawer_transactions', function (Blueprint $table): void {
            $table->id();
            $table->foreignId('cashier_shift_id')->constrained('cashier_shifts')->cascadeOnDelete();
            $table->foreignId('user_id')->nullable()->constrained('users')->nullOnDelete();
            $table->string('type', 20); // cash_in, cash_out
            $table->decimal('amount', 12, 2);
            $table->string('category', 50)->default('other'); // supplies, courier_delivery, change_replenishment, cleaning_security, other
            $table->text('notes')->nullable();
            $table->string('performed_by', 100);
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('cashier_drawer_transactions');
    }
};
