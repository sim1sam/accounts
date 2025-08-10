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
        // Create admin user
        User::create([
            'name' => 'Admin User',
            'email' => 'admin@accounts.com',
            'email_verified_at' => now(),
            'password' => Hash::make('admin123'),
        ]);

        $this->command->info('Admin user created successfully!');
        $this->command->info('Email: admin@accounts.com');
        $this->command->info('Password: admin123');
    }
}
