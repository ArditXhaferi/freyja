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
        Schema::create('network_matches', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->constrained()->onDelete('cascade');
            $table->foreignId('company_id')->constrained('users')->onDelete('cascade');
            $table->enum('action', ['like', 'pass', 'super_like'])->default('like');
            $table->boolean('is_mutual')->default(false);
            $table->timestamps();

            // Ensure a user can only interact with a company once
            $table->unique(['user_id', 'company_id']);
            
            // Index for efficient querying
            $table->index(['user_id', 'action']);
            $table->index(['company_id', 'action']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('network_matches');
    }
};
