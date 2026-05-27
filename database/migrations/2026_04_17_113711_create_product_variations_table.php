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
        Schema::create('product_variations', function (Blueprint $table) {
            $table->foreignId('product_id')->constrained()->onDelete('cascade');
        
        // Product Attributes (Nullable as per your logic)
        $table->string('size')->nullable();
        $table->string('thickness')->nullable();
        $table->string('color')->nullable();
        
        // Pricing Logic
        $table->decimal('piece_price', 15, 2);
        $table->integer('total_pieces')->default(1);
        $table->decimal('gst_percentage', 5, 2)->default(0);
        $table->decimal('total_price', 15, 2);
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('product_variations');
    }
};
