<?php
use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
return new class extends Migration { public function up(): void { Schema::create('growth_stage_definitions', function (Blueprint $table) { $table->id(); $table->enum('code', ['seed', 'germination', 'seedling', 'vegetative', 'budding', 'flowering', 'fruiting', 'harvest'])->unique(); $table->string('name', 60)->unique(); $table->unsignedTinyInteger('sort_order'); $table->timestamps(); }); } public function down(): void { Schema::dropIfExists('growth_stage_definitions'); } };
