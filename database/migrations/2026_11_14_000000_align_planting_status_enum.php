<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    public function up(): void
    {
        if (DB::getDriverName() === 'mysql') {
            DB::statement("UPDATE plantings SET status = 'completed' WHERE status = 'archived'");
            DB::statement("ALTER TABLE plantings MODIFY status ENUM('active', 'completed', 'cancelled') NOT NULL DEFAULT 'active'");
        }
    }

    public function down(): void
    {
        if (DB::getDriverName() === 'mysql') {
            DB::statement("UPDATE plantings SET status = 'completed' WHERE status = 'cancelled'");
            DB::statement("ALTER TABLE plantings MODIFY status ENUM('active', 'completed', 'archived') NOT NULL DEFAULT 'active'");
        }
    }
};
