<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::table('order_items', function (Blueprint $table) {
            $table->string('status')->default('pending')->after('dispatched_quantity');
        });

        // Initialize status for existing order items based on their order status
        DB::table('order_items')
            ->join('orders', 'order_items.order_id', '=', 'orders.id')
            ->whereIn('orders.status', ['accepted', 'processing', 'completed', 'dispatched'])
            ->where('order_items.rejected_quantity', '<=', 0)
            ->update(['order_items.status' => 'accepted']);

        DB::table('order_items')
            ->join('orders', 'order_items.order_id', '=', 'orders.id')
            ->where(function ($q) {
                $q->whereIn('orders.status', ['rejected', 'cancelled'])
                  ->orWhere('order_items.rejected_quantity', '>', 0);
            })
            ->update(['order_items.status' => 'rejected']);
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('order_items', function (Blueprint $table) {
            $table->dropColumn('status');
        });
    }
};

