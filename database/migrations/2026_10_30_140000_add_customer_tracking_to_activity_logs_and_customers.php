<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('activity_logs', function (Blueprint $table) {
            if (!Schema::hasColumn('activity_logs', 'customer_id')) {
                $table->foreignId('customer_id')->nullable()->after('admin_id')->constrained('customers')->nullOnDelete();
            }
            if (!Schema::hasColumn('activity_logs', 'user_type')) {
                $table->string('user_type', 20)->default('admin')->after('admin_id'); // admin, b2b, b2c
            }
        });

        Schema::table('customers', function (Blueprint $table) {
            if (!Schema::hasColumn('customers', 'last_login_at')) {
                $table->timestamp('last_login_at')->nullable()->after('is_active');
            }
            if (!Schema::hasColumn('customers', 'current_login_ip')) {
                $table->string('current_login_ip', 45)->nullable()->after('last_login_at');
            }
            if (!Schema::hasColumn('customers', 'last_login_ip')) {
                $table->string('last_login_ip', 45)->nullable()->after('current_login_ip');
            }
            if (!Schema::hasColumn('customers', 'login_count')) {
                $table->unsignedInteger('login_count')->default(0)->after('last_login_ip');
            }
            if (!Schema::hasColumn('customers', 'last_device')) {
                $table->string('last_device')->nullable()->after('login_count');
            }
        });
    }

    public function down(): void
    {
        Schema::table('activity_logs', function (Blueprint $table) {
            if (Schema::hasColumn('activity_logs', 'customer_id')) {
                $table->dropForeign(['customer_id']);
                $table->dropColumn('customer_id');
            }
            if (Schema::hasColumn('activity_logs', 'user_type')) {
                $table->dropColumn('user_type');
            }
        });

        Schema::table('customers', function (Blueprint $table) {
            $table->dropColumn(['last_login_at', 'current_login_ip', 'last_login_ip', 'login_count', 'last_device']);
        });
    }
};

