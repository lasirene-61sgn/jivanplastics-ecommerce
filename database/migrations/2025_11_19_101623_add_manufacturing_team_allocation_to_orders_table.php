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
            $table->unsignedBigInteger('manufacturing_team_id')->nullable()->after('status');
            $table->string('manufacturing_status')->default('pending')->after('status');
            $table->timestamp('allocated_at')->nullable()->after('manufacturing_status');
            $table->timestamp('completed_at')->nullable()->after('allocated_at');
            $table->timestamp('dispatched_at')->nullable()->after('completed_at');
            
            $table->foreign('manufacturing_team_id')->references('id')->on('manufacturing_teams')->onDelete('set null');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('orders', function (Blueprint $table) {
            $table->dropForeign(['manufacturing_team_id']);
            $table->dropColumn(['manufacturing_team_id', 'manufacturing_status', 'allocated_at', 'completed_at', 'dispatched_at']);
        });
    }
};