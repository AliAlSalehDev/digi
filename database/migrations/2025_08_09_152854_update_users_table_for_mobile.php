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
        Schema::table('users', function (Blueprint $table) {
            // Make password nullable for mobile users
            $table->string('password')->nullable()->change();
            
            // Add new columns for mobile identification
            $table->string('device_id')->nullable()->after('email');
            $table->string('user_hash')->nullable()->unique()->after('device_id');
            
            // Add physical metrics columns
            $table->integer('age')->nullable();
            $table->decimal('height', 5, 2)->nullable(); // in cm
            $table->decimal('weight', 5, 2)->nullable(); // in kg
            $table->enum('gender', ['male', 'female'])->nullable();
            $table->string('activity_level')->nullable();
            $table->decimal('neck_circumference', 5, 2)->nullable();
            $table->decimal('waist_circumference', 5, 2)->nullable();
            $table->decimal('hip_circumference', 5, 2)->nullable();
            
            // Calculated metrics
            $table->decimal('bmi', 5, 2)->nullable();
            $table->string('bmi_overview')->nullable();
            $table->integer('bmr')->nullable();
            $table->integer('tdee')->nullable();
            $table->decimal('body_fat', 5, 2)->nullable();
            
            // Plan preferences
            $table->integer('plan_period')->default(30);
            $table->enum('goal', ['lose', 'gain', 'maintain'])->nullable();
            
            // Tracking
            $table->timestamp('last_generation_at')->nullable();
            $table->integer('total_plans_generated')->default(0);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('users', function (Blueprint $table) {
            $table->string('password')->nullable(false)->change();
            
            $table->dropColumn([
                'device_id',
                'user_hash',
                'age',
                'height',
                'weight',
                'gender',
                'activity_level',
                'neck_circumference',
                'waist_circumference',
                'hip_circumference',
                'bmi',
                'bmi_overview',
                'bmr',
                'tdee',
                'body_fat',
                'plan_period',
                'goal',
                'last_generation_at',
                'total_plans_generated'
            ]);
        });
    }
};