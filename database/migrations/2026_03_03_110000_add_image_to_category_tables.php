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
        Schema::table('categories', function (Blueprint $table) {
            $table->string('image')->nullable()->after('description');
        });

        Schema::table('subcategories', function (Blueprint $table) {
            $table->string('image')->nullable()->after('description');
        });

        Schema::table('sub_subcategories', function (Blueprint $table) {
            $table->string('image')->nullable()->after('description');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('categories', function (Blueprint $table) {
            $table->dropColumn('image');
        });

        Schema::table('subcategories', function (Blueprint $table) {
            $table->dropColumn('image');
        });

        Schema::table('sub_subcategories', function (Blueprint $table) {
            $table->dropColumn('image');
        });
    }
};
