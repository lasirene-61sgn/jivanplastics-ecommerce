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
            $table->decimal('other_charges', 10, 2)->default(0)->after('bank_transfer_discount_amount');
        });

        Schema::table('invoices', function (Blueprint $table) {
            $table->decimal('other_charges', 10, 2)->default(0)->after('bank_transfer_discount_amount');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('orders', function (Blueprint $table) {
            $table->dropColumn('other_charges');
        });

        Schema::table('invoices', function (Blueprint $table) {
            $table->dropColumn('other_charges');
        });
    }
};
