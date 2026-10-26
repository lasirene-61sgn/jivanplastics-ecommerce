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
        // 1. Add manufacturer timeline timestamps to orders table
        Schema::table('orders', function (Blueprint $table) {
            if (!Schema::hasColumn('orders', 'mfg_accepted_at')) {
                $table->timestamp('mfg_accepted_at')->nullable()->after('allocated_at');
            }
            if (!Schema::hasColumn('orders', 'mfg_completed_at')) {
                $table->timestamp('mfg_completed_at')->nullable()->after('completed_at');
            }
        });

        // 2. Add dispatched_at to invoices table if not exists
        Schema::table('invoices', function (Blueprint $table) {
            if (!Schema::hasColumn('invoices', 'dispatched_at')) {
                $table->timestamp('dispatched_at')->nullable()->after('total');
            }
        });

        // 3. Create order_production_logs table to track partial manufacturing completions
        if (!Schema::hasTable('order_production_logs')) {
            Schema::create('order_production_logs', function (Blueprint $table) {
                $table->id();
                $table->unsignedBigInteger('order_id');
                $table->unsignedBigInteger('order_item_id')->nullable();
                $table->unsignedBigInteger('manufacturing_team_id')->nullable();
                $table->integer('pieces_completed')->default(0);
                $table->integer('pieces_rejected')->default(0);
                $table->decimal('quantity_completed', 10, 4)->default(0);
                $table->decimal('quantity_rejected', 10, 4)->default(0);
                $table->string('rejection_reason')->nullable();
                $table->timestamps();

                $table->foreign('order_id')->references('id')->on('orders')->onDelete('cascade');
                $table->foreign('order_item_id')->references('id')->on('order_items')->onDelete('cascade');
                $table->foreign('manufacturing_team_id')->references('id')->on('manufacturing_teams')->onDelete('set null');
            });
        }
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('order_production_logs');

        Schema::table('invoices', function (Blueprint $table) {
            if (Schema::hasColumn('invoices', 'dispatched_at')) {
                $table->dropColumn('dispatched_at');
            }
        });

        Schema::table('orders', function (Blueprint $table) {
            if (Schema::hasColumn('orders', 'mfg_accepted_at')) {
                $table->dropColumn('mfg_accepted_at');
            }
            if (Schema::hasColumn('orders', 'mfg_completed_at')) {
                $table->dropColumn('mfg_completed_at');
            }
        });
    }
};
