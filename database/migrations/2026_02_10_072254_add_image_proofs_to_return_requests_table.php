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
        Schema::table('return_requests', function (Blueprint $table) {
            $table->string('damage_proof_image')->nullable()->after('description');
            $table->string('another_image')->nullable()->after('damage_proof_image');
            $table->string('dispatch_proof_image')->nullable()->after('another_image');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('return_requests', function (Blueprint $table) {
            $table->dropColumn(['damage_proof_image', 'another_image', 'dispatch_proof_image']);
        });
    }
};
