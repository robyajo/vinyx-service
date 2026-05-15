<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('live_events', function (Blueprint $table) {
            $table->id();
            $table->foreignId('session_id')->constrained('live_sessions')->cascadeOnDelete();
            $table->string('type'); // GIFT, COMMENT, FOLLOW, LIKE, SHARE, MEMBER, BATTLE, ROOM_USER, SOCIAL
            $table->jsonb('data');
            $table->jsonb('raw')->nullable();
            $table->timestamp('timestamp');
            $table->timestamps();

            $table->index('session_id');
            $table->index('type');
            $table->index('timestamp');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('live_events');
    }
};
