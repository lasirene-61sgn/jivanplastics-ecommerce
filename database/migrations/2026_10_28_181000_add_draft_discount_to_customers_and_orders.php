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
        if (Schema::hasTable('customers') && !Schema::hasColumn('customers', 'draft_discount')) {
            Schema::table('customers', function (Blueprint $table) {
                $table->decimal('draft_discount', 5, 2)->default(0)->after('online_discount');
            });
        }

        if (Schema::hasTable('orders') && !Schema::hasColumn('orders', 'draft_discount_amount')) {
            Schema::table('orders', function (Blueprint $table) {
                $table->decimal('draft_discount_amount', 10, 2)->default(0)->after('online_discount_amount');
            });
        }

        if (Schema::hasTable('invoices') && !Schema::hasColumn('invoices', 'draft_discount_amount')) {
            Schema::table('invoices', function (Blueprint $table) {
                $table->decimal('draft_discount_amount', 10, 2)->default(0)->after('online_discount_amount');
            });
        }
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        if (Schema::hasTable('customers') && Schema::hasColumn('customers', 'draft_discount')) {
            Schema::table('customers', function (Blueprint $table) {
                $table->dropColumn('draft_discount');
            });
        }

        if (Schema::hasTable('orders') && Schema::hasColumn('orders', 'draft_discount_amount')) {
            Schema::table('orders', function (Blueprint $table) {
                $table->dropColumn('draft_discount_amount');
            });
        }

        if (Schema::hasTable('invoices') && Schema::hasColumn('invoices', 'draft_discount_amount')) {
            Schema::table('invoices', function (Blueprint $table) {
                $table->dropColumn('draft_discount_amount');
            });
        }
    }
};
