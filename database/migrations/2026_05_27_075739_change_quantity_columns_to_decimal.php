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
            $table->decimal('quantity', 12, 4)->change();
            $table->decimal('manufactured_quantity', 12, 4)->change()->nullable();
            $table->decimal('rejected_quantity', 12, 4)->change()->nullable();
            $table->decimal('dispatched_quantity', 12, 4)->change()->nullable();
        });

        Schema::table('cart_items', function (Blueprint $table) {
            $table->decimal('quantity', 12, 4)->change();
        });

        Schema::table('invoice_items', function (Blueprint $table) {
            $table->decimal('quantity', 12, 4)->change();
        });

        Schema::table('return_requests', function (Blueprint $table) {
            $table->decimal('quantity', 12, 4)->change();
        });

        Schema::table('return_note_items', function (Blueprint $table) {
            $table->decimal('quantity', 12, 4)->change();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('order_items', function (Blueprint $table) {
            $table->integer('quantity')->change();
            $table->integer('manufactured_quantity')->change();
            $table->integer('rejected_quantity')->change();
            $table->integer('dispatched_quantity')->change();
        });

        Schema::table('cart_items', function (Blueprint $table) {
            $table->integer('quantity')->change();
        });

        Schema::table('invoice_items', function (Blueprint $table) {
            $table->integer('quantity')->change();
        });

        Schema::table('return_requests', function (Blueprint $table) {
            $table->integer('quantity')->change();
        });

        Schema::table('return_note_items', function (Blueprint $table) {
            $table->integer('quantity')->change();
        });
    }
};
