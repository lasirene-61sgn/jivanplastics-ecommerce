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
        Schema::table('products', function (Blueprint $table) {
            $table->unsignedInteger('min_order_qty_b2b')->nullable()->after('max_order_qty');
            $table->unsignedInteger('max_order_qty_b2b')->nullable()->after('min_order_qty_b2b');
            $table->unsignedInteger('min_order_qty_b2c')->nullable()->after('max_order_qty_b2b');
            $table->unsignedInteger('max_order_qty_b2c')->nullable()->after('min_order_qty_b2c');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('products', function (Blueprint $table) {
            $table->dropColumn(['min_order_qty_b2b', 'max_order_qty_b2b', 'min_order_qty_b2c', 'max_order_qty_b2c']);
        });
    }
};
