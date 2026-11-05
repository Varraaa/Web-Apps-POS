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
        Schema::create('customers', function (Blueprint $table): void {
            $table->id();
            $table->string('member_code', 30)->unique();
            $table->string('name');
            $table->string('phone', 30)->nullable()->unique();
            $table->string('email')->nullable();
            $table->enum('tier', ['bronze', 'silver', 'gold'])->default('bronze');
            $table->unsignedInteger('points')->default(0);
            $table->decimal('total_spent', 12, 2)->default(0);
            $table->text('address')->nullable();
            $table->text('notes')->nullable();
            $table->timestamps();
        });

        Schema::table('orders', function (Blueprint $table): void {
            $table->foreignId('customer_id')->nullable()->after('user_id')->constrained('customers')->nullOnDelete();
            $table->unsignedInteger('points_earned')->default(0)->after('change_amount');
            $table->unsignedInteger('points_redeemed')->default(0)->after('points_earned');
            $table->decimal('points_discount_amount', 12, 2)->default(0)->after('points_redeemed');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('orders', function (Blueprint $table): void {
            $table->dropForeign(['customer_id']);
            $table->dropColumn(['customer_id', 'points_earned', 'points_redeemed', 'points_discount_amount']);
        });

        Schema::dropIfExists('customers');
    }
};
