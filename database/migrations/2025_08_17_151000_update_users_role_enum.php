<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    public function up(): void
    {
        // Remap existing 'user' role to 'staff' before changing enum
        DB::table('users')->where('role', 'user')->update(['role' => 'staff']);

        // Update users.role enum to admin, staff, supervisor with default staff
        DB::statement("ALTER TABLE `users` MODIFY `role` ENUM('admin','staff','supervisor') NOT NULL DEFAULT 'staff'");
    }

    public function down(): void
    {
        // Before reverting enum, map staff/supervisor back to 'user'
        DB::table('users')->whereIn('role', ['staff','supervisor'])->update(['role' => 'user']);

        // Revert to previous enum (admin, user) with default user
        DB::statement("ALTER TABLE `users` MODIFY `role` ENUM('admin','user') NOT NULL DEFAULT 'user'");
    }
};
