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
        Schema::disableForeignKeyConstraints();

        Schema::create('reminders', function (Blueprint $table) {
            $table->id();
            $table->foreignId('advisor_id')->constrained('users');
            $table->foreignId('meeting_id')->nullable()->constrained()->nullOnDelete();
            $table->enum('reminder_type', ['week', 'three_days', 'one_day', 'one_hour']);
            $table->text('message');
            $table->timestamp('remind_at');
            $table->boolean('reminder_active')->default(true);
            $table->timestamp('delivered_at')->nullable();
            $table->timestamp('seen_at')->nullable();
            $table->timestamps();
            $table->unique(['advisor_id', 'meeting_id', 'reminder_type']);
        });

        Schema::enableForeignKeyConstraints();
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('reminders');
    }
};


