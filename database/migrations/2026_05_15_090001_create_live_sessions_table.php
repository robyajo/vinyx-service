<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('live_sessions', function (Blueprint $table) {
            $table->id();
            $table->foreignId('account_id')->constrained('tiktok_accounts')->cascadeOnDelete();
            $table->string('status'); // CONNECTED, DISCONNECTED
            $table->timestamp('started_at');
            $table->timestamp('ended_at')->nullable();
            $table->jsonb('metadata')->nullable();
            $table->timestamps();

            $table->index('status');
            $table->index('started_at');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('live_sessions');
    }
};
