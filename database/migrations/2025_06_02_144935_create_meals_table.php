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
        Schema::create('meals', function (Blueprint $table) {
            $table->id();
            $table->string('name')->nullable();
            $table->string('unit')->nullable();
            $table->string('calories')->nullable();
            $table->string('protein')->nullable();
            $table->string('carbs')->nullable();
            $table->string('fats')->nullable();
            $table->string('kcal_with_avg_rice_sauce')->nullable();
            $table->string('protein_with_avg_rice_sauce')->nullable();
            $table->string('carbs_with_avg_rice_sauce')->nullable();
            $table->string('fat_with_avg_rice_sauce')->nullable();
            $table->string('price')->nullable();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('meals');
    }
};
