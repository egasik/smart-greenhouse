<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('plantings', function (Blueprint $table) {
            $table->engine = 'InnoDB';

            $table->bigIncrements('id');
            $table->unsignedBigInteger('greenhouse_id');
            $table->unsignedBigInteger('variety_id');

            $table->date('planting_date');
            $table->unsignedInteger('quantity')->default(1);
            $table->enum('status', ['active', 'completed', 'archived'])
                ->default('active');
            $table->timestamps();

            $table->foreign('greenhouse_id')
                ->references('id')
                ->on('greenhouses')
                ->cascadeOnDelete();

            $table->foreign('variety_id')
                ->references('id')
                ->on('varieties')
                ->restrictOnDelete();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('plantings');
    }
};