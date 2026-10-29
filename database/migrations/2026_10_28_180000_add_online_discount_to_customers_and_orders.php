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
        if (Schema::hasTable('customers') && !Schema::hasColumn('customers', 'online_discount')) {
            Schema::table('customers', function (Blueprint $table) {
                $table->decimal('online_discount', 5, 2)->default(0)->after('bank_transfer_discount');
            });
        }

        if (Schema::hasTable('orders') && !Schema::hasColumn('orders', 'online_discount_amount')) {
            Schema::table('orders', function (Blueprint $table) {
                $table->decimal('online_discount_amount', 10, 2)->default(0)->after('bank_transfer_discount_amount');
            });
        }

        if (Schema::hasTable('invoices') && !Schema::hasColumn('invoices', 'online_discount_amount')) {
            Schema::table('invoices', function (Blueprint $table) {
                $table->decimal('online_discount_amount', 10, 2)->default(0)->after('bank_transfer_discount_amount');
            });
        }
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        if (Schema::hasTable('customers') && Schema::hasColumn('customers', 'online_discount')) {
            Schema::table('customers', function (Blueprint $table) {
                $table->dropColumn('online_discount');
            });
        }

        if (Schema::hasTable('orders') && Schema::hasColumn('orders', 'online_discount_amount')) {
            Schema::table('orders', function (Blueprint $table) {
                $table->dropColumn('online_discount_amount');
            });
        }

        if (Schema::hasTable('invoices') && Schema::hasColumn('invoices', 'online_discount_amount')) {
            Schema::table('invoices', function (Blueprint $table) {
                $table->dropColumn('online_discount_amount');
            });
        }
    }
};
