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
        Schema::table('reward_claims', function (Blueprint $table) {
            $table->unsignedBigInteger('invoice_id')->nullable()->after('reward_id');
            $table->string('dispatch_proof_image')->nullable()->after('processed_at');
            $table->string('invoice_proof_image')->nullable()->after('dispatch_proof_image');
            
            $table->foreign('invoice_id')->references('id')->on('invoices')->onDelete('set null');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('reward_claims', function (Blueprint $table) {
            $table->dropForeign(['invoice_id']);
            $table->dropColumn(['invoice_id', 'dispatch_proof_image', 'invoice_proof_image']);
        });
    }
};
