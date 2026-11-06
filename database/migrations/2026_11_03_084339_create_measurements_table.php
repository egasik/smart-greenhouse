<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('measurements', function (Blueprint $table) {
            $table->id();
            $table->foreignId('greenhouse_id')->constrained()->cascadeOnDelete();
            $table->foreignId('planting_id')->nullable()->constrained()->nullOnDelete();
            $table->double('temperature')->nullable();
            $table->double('illumination')->nullable();
            $table->timestamp('measured_at')->useCurrent();
            $table->timestamps();
        });
    }

    public function down(): void { Schema::dropIfExists('measurements'); }
};
