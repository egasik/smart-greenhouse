<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('growth_stages', function (Blueprint $table) {
            $table->id();
            $table->foreignId('planting_id')->constrained()->cascadeOnDelete();
            $table->foreignId('growth_stage_definition_id')->constrained()->restrictOnDelete();
            $table->enum('condition', ['good', 'attention', 'critical'])->nullable();
            $table->timestamp('recorded_at')->useCurrent();
            $table->text('comment')->nullable();
            $table->timestamps();
        });
    }

    public function down(): void { Schema::dropIfExists('growth_stages'); }
};
