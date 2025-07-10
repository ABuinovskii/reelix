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
    Schema::create('telegram_sessions', function (Blueprint $table) {
        $table->id();
        $table->string('chat_id')->unique(); // Telegram chat ID
        $table->foreignId('user_id')->constrained()->onDelete('cascade'); // Связь с user
        $table->timestamps();
    });
}

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('telegram_sessions');
    }
};
