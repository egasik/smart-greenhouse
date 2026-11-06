<?php
use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
return new class extends Migration { public function up(): void { Schema::create('catalog_requests', function (Blueprint $table) { $table->id(); $table->foreignId('requested_by')->constrained('users')->cascadeOnDelete(); $table->foreignId('crop_id')->nullable()->constrained()->nullOnDelete(); $table->string('crop_name', 100)->nullable(); $table->string('variety_name', 100); $table->enum('status', ['pending', 'approved', 'rejected'])->default('pending'); $table->foreignId('reviewed_by')->nullable()->constrained('users')->nullOnDelete(); $table->timestamp('reviewed_at')->nullable(); $table->text('review_comment')->nullable(); $table->timestamps(); }); } public function down(): void { Schema::dropIfExists('catalog_requests'); } };
