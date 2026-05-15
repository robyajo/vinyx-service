<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('users', function (Blueprint $table) {
            $table->integer('gems')->default(0)->after('is_active');
            $table->integer('events_used')->default(0)->after('gems');
            $table->timestamp('events_cycle_at')->nullable()->after('events_used');
        });
    }

    public function down(): void
    {
        Schema::table('users', function (Blueprint $table) {
            $table->dropColumn(['gems', 'events_used', 'events_cycle_at']);
        });
    }
};
