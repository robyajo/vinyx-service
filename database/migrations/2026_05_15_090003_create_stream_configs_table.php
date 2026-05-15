<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('stream_configs', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->constrained()->cascadeOnDelete();
            $table->string('tiktok_username')->nullable();
            $table->integer('listener_port')->nullable();
            $table->boolean('is_active')->default(true);
            $table->string('new_user_sound')->nullable();
            $table->boolean('new_user_sound_enabled')->default(true);
            $table->string('chat_sound')->nullable();
            $table->boolean('chat_sound_enabled')->default(true);
            $table->boolean('tts_active')->default(true);
            $table->string('tts_voice')->default('female');
            $table->timestamps();

            $table->unique('user_id');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('stream_configs');
    }
};
