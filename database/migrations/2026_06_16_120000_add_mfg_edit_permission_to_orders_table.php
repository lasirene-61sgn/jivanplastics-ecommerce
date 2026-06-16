<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('orders', function (Blueprint $table) {
            $table->text('mfg_edit_request_note')->nullable()->after('tentative_dispatch_date');
            $table->timestamp('mfg_edit_request_at')->nullable()->after('mfg_edit_request_note');
            $table->boolean('mfg_edit_permission_granted')->default(false)->after('mfg_edit_request_at');
            $table->tinyInteger('mfg_edit_permission_count')->default(0)->after('mfg_edit_permission_granted');
        });
    }

    public function down(): void
    {
        Schema::table('orders', function (Blueprint $table) {
            $table->dropColumn([
                'mfg_edit_request_note',
                'mfg_edit_request_at',
                'mfg_edit_permission_granted',
                'mfg_edit_permission_count',
            ]);
        });
    }
};
