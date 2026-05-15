<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('stream_configs', function (Blueprint $table) {
            $table->string('active_platform')->default('tiktok')->after('user_id');
        });
    }

    public function down(): void
    {
        Schema::table('stream_configs', function (Blueprint $table) {
            $table->dropColumn('active_platform');
        });
    }
};
