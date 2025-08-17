<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use App\Models\User;
use Illuminate\Support\Facades\Hash;

class AdminUserSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // Create or update super admin user from config
        $email = config('permissions.super_admin_email', 'admin@gmail.com');
        $admin = User::firstOrCreate(
            ['email' => $email],
            [
                'name' => 'Admin User',
                'email_verified_at' => now(),
                'password' => Hash::make('admin123'), // change after first login
            ]
        );

        // Ensure role and protection
        $admin->role = 'admin';
        $admin->is_protected = true;
        $admin->save();

        // Demote any other admins to staff
        User::where('id', '!=', $admin->id)->where('role', 'admin')->update(['role' => 'staff']);

        $this->command->info('Super admin ensured successfully!');
        $this->command->info('Email: ' . $email);
        $this->command->info('Password: admin123');
    }
}
