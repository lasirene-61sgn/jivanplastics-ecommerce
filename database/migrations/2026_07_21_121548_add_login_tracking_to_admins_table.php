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
        Schema::table('admins', function (Blueprint $table) {
            $table->string('current_login_ip', 45)->nullable()->after('remember_token');
            $table->string('last_login_ip', 45)->nullable()->after('current_login_ip');
            $table->timestamp('last_login_at')->nullable()->after('last_login_ip');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('admins', function (Blueprint $table) {
            $table->dropColumn(['current_login_ip', 'last_login_ip', 'last_login_at']);
        });
    }
};
