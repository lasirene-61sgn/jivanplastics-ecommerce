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
        Schema::table('orders', function (Blueprint $table) {
            $table->decimal('original_subtotal', 10, 2)->after('customer_type')->nullable();
            $table->decimal('discount_amount', 10, 2)->after('original_subtotal')->nullable();
        });

        Schema::table('order_items', function (Blueprint $table) {
            $table->decimal('original_price', 10, 2)->after('dispatched_quantity')->nullable();
            $table->decimal('discount_amount', 10, 2)->after('original_price')->nullable();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('orders', function (Blueprint $table) {
            $table->dropColumn(['original_subtotal', 'discount_amount']);
        });

        Schema::table('order_items', function (Blueprint $table) {
            $table->dropColumn(['original_price', 'discount_amount']);
        });
    }
};
