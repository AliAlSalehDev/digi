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
        Schema::create('meal_sessions', function (Blueprint $table) {
            $table->uuid('id')->primary();
            $table->foreignId('user_id')->constrained()->cascadeOnDelete();
            $table->enum('status', ['pending', 'processing', 'completed', 'failed'])->default('pending');
            $table->integer('current_day')->default(0);
            $table->integer('total_days')->default(30);
            $table->enum('goal', ['lose', 'gain', 'maintain'])->nullable();
            $table->text('goal_explanation')->nullable();
            $table->json('meal_data')->nullable(); // Stores the complete meal plan
            $table->json('daily_totals')->nullable(); // Stores daily totals
            $table->decimal('total_calories', 10, 2)->nullable();
            $table->decimal('total_protein', 8, 2)->nullable();
            $table->decimal('total_carbs', 8, 2)->nullable();
            $table->decimal('total_fat', 8, 2)->nullable();
            $table->decimal('total_price', 10, 2)->nullable();
            $table->integer('total_meals')->default(0);
            $table->text('error_message')->nullable();
            $table->timestamp('started_at')->nullable();
            $table->timestamp('completed_at')->nullable();
            $table->timestamps();
            
            // Indexes for faster queries
            $table->index('status');
            $table->index('user_id');
            $table->index('created_at');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('meal_sessions');
    }
};