<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('event_notification_batches', function (Blueprint $table) {
            $table->id();
            $table->foreignId('event_id')->constrained('events')->onDelete('cascade');
            $table->integer('users_targeted')->default(0);
            $table->integer('users_sent')->default(0);
            $table->float('radius_km');
            $table->integer('days_window');
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('event_notification_batches');
    }
};
