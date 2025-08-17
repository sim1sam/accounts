<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        // Ensure existing invalid values are mapped before altering the enum
        DB::statement("UPDATE expenses SET status = 'pending' WHERE status NOT IN ('pending','paid')");
        // Add 'partial' to the enum
        DB::statement("ALTER TABLE expenses MODIFY COLUMN status ENUM('pending','partial','paid') NOT NULL DEFAULT 'pending'");
    }

    public function down(): void
    {
        // Map 'partial' back to 'pending' before dropping from enum
        DB::statement("UPDATE expenses SET status = 'pending' WHERE status = 'partial'");
        DB::statement("ALTER TABLE expenses MODIFY COLUMN status ENUM('pending','paid') NOT NULL DEFAULT 'pending'");
    }
};
