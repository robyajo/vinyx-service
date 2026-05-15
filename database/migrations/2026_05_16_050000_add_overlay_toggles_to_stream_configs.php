<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('stream_configs', function (Blueprint $table) {
            $table->boolean('alert_overlay_enabled')->default(true)->after('active_platform');
            $table->boolean('member_ticker_enabled')->default(true)->after('alert_overlay_enabled');
            $table->boolean('gift_overlay_enabled')->default(true)->after('member_ticker_enabled');
        });
    }

    public function down(): void
    {
        Schema::table('stream_configs', function (Blueprint $table) {
            $table->dropColumn(['alert_overlay_enabled', 'member_ticker_enabled', 'gift_overlay_enabled']);
        });
    }
};
