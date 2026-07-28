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
            $table->unsignedBigInteger('manufacturing_team_id')->nullable()->after('customer_id');
            $table->foreign('manufacturing_team_id')->references('id')->on('manufacturing_teams')->onDelete('set null');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('return_requests', function (Blueprint $table) {
            $table->dropForeign(['manufacturing_team_id']);
            $table->dropColumn('manufacturing_team_id');
        });
    }
};
