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
        Schema::table('order_items', function (Blueprint $table) {
            $table->integer('per_unit_pieces')->default(1)->after('quantity');
            $table->integer('total_pieces')->default(0)->after('per_unit_pieces');
            $table->integer('manufactured_pieces')->default(0)->after('manufactured_quantity');
            $table->integer('rejected_pieces')->default(0)->after('rejected_quantity');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('order_items', function (Blueprint $table) {
            $table->dropColumn(['per_unit_pieces', 'total_pieces', 'manufactured_pieces', 'rejected_pieces']);
        });
    }
};
