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
        Schema::table('return_notes', function (Blueprint $table) {
            $table->unsignedBigInteger('order_id')->nullable()->change();
        });
        Schema::table('return_note_items', function (Blueprint $table) {
            $table->unsignedBigInteger('order_item_id')->nullable()->change();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('return_notes', function (Blueprint $table) {
            $table->unsignedBigInteger('order_id')->nullable(false)->change();
        });
        Schema::table('return_note_items', function (Blueprint $table) {
            $table->unsignedBigInteger('order_item_id')->nullable(false)->change();
        });
    }
};
