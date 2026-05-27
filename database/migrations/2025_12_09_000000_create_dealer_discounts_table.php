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
        Schema::create('dealer_discounts', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('customer_id'); // Reference to the dealer/customer
            $table->unsignedBigInteger('discountable_id'); // ID of the category, subcategory, etc.
            $table->string('discountable_type'); // Type: Category, Subcategory, SubSubcategory, Product
            $table->decimal('discount_percentage', 5, 2);
            $table->boolean('is_active')->default(true);
            $table->timestamps();
            
            $table->foreign('customer_id')->references('id')->on('customers')->onDelete('cascade');
            $table->index(['discountable_id', 'discountable_type']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('dealer_discounts');
    }
};