<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('care_tasks', function (Blueprint $table) {
            $table->id();
            $table->foreignId('planting_id')->constrained()->cascadeOnDelete();
            $table->enum('type', ['watering', 'fertilizing', 'ventilation', 'treatment', 'other']);
            $table->dateTime('planned_at');
            $table->enum('status', ['planned', 'completed', 'cancelled'])->default('planned');
            $table->text('description')->nullable();
            $table->dateTime('completed_at')->nullable();
            $table->timestamps();
        });
    }

    public function down(): void { Schema::dropIfExists('care_tasks'); }
};
