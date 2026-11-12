<?php
use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
return new class extends Migration { public function up(): void { Schema::create('varieties', function (Blueprint $table) { $table->id(); $table->foreignId('crop_id')->constrained()->cascadeOnDelete(); $table->string('name', 100); $table->timestamps(); $table->unique(['crop_id', 'name']); }); } public function down(): void { Schema::dropIfExists('varieties'); } };
