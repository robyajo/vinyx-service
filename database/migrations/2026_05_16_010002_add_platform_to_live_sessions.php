<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('live_sessions', function (Blueprint $table) {
            $table->dropForeign(['account_id']);
            $table->string('platform')->default('tiktok')->after('id');
            $table->index('platform');
        });
    }

    public function down(): void
    {
        Schema::table('live_sessions', function (Blueprint $table) {
            $table->dropColumn('platform');
            $table->foreign('account_id')->references('id')->on('tiktok_accounts')->cascadeOnDelete();
        });
    }
};
