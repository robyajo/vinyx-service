<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('tiktok_accounts', function (Blueprint $table) {
            $table->dropUnique('tiktok_accounts_unique_id_unique');
        });
    }

    public function down(): void
    {
        Schema::table('tiktok_accounts', function (Blueprint $table) {
            $table->unique('unique_id');
        });
    }
};
