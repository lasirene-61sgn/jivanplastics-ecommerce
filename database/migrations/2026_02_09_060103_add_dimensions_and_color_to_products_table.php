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
        Schema::table('products', function (Illuminate\Database\Schema\Blueprint $table) {
            $table->string('size')->nullable()->after('description');
            $table->string('thickness')->nullable()->after('size');
            $table->string('color')->nullable()->after('thickness');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('products', function (Illuminate\Database\Schema\Blueprint $table) {
            $table->dropColumn(['size', 'thickness', 'color']);
        });
    }
};
