<?php
use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
return new class extends Migration { public function up(): void { Schema::create('catalog_request_growth_stage', function (Blueprint $table) { $table->foreignId('catalog_request_id')->constrained()->cascadeOnDelete(); $table->foreignId('growth_stage_definition_id')->constrained()->cascadeOnDelete(); $table->primary(['catalog_request_id', 'growth_stage_definition_id']); }); } public function down(): void { Schema::dropIfExists('catalog_request_growth_stage'); } };
