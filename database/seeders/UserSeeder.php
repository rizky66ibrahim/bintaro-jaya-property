<?php

namespace Database\Seeders;

use App\Models\User;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;

class UserSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
          // Create Superadmin
          User::create([
            'name' => 'Superadmin',
            'username' => 'superadmin',
            'email' => 'superadmin@localhost.com',
            'phone_number' => '081234567890',
            'address' => 'Jl. Superadmin No. 1',
            'subdistrict' => 'Superadmin',
            'district' => 'Superadmin',
            'city' => 'Superadmin',
            'province' => 'Superadmin',
            'postal_code' => '12345',
            'profile_picture' => 'superadmin.jpg',
            'date_of_birth' => '1990-01-01',
            'place_of_birth' => 'Superadmin',
            'position' => 'superadmin',
            'status' => 'active',
            'email_verified_at' => now(),
            'password' => Hash::make('superadmin'),
        ]);

        // Create Admin
        User::create([
            'name' => 'Admin',
            'username' => 'admin',
            'email' => 'admin@localhost.com',
            'phone_number' => '081234567891',
            'address' => 'Jl. Admin No. 1',
            'subdistrict' => 'Admin',
            'district' => 'Admin',
            'city' => 'Admin',
            'province' => 'Admin',
            'postal_code' => '12345',
            'profile_picture' => 'admin.jpg',
            'date_of_birth' => '1990-01-01',
            'place_of_birth' => 'Admin',
            'position' => 'admin',
            'status' => 'active',
            'email_verified_at' => now(),
            'password' => Hash::make('admin'),
        ]);

        // Create User
        User::create([
            'name' => 'User',
            'username' => 'user',
            'email' => 'user@localhost.com',
            'phone_number' => '081234567892',
            'address' => 'Jl. User No. 1',
            'subdistrict' => 'User',
            'district' => 'User',
            'city' => 'User',
            'province' => 'User',
            'postal_code' => '12345',
            'profile_picture' => 'user.jpg',
            'date_of_birth' => '1990-01-01',
            'place_of_birth' => 'User',
            'position' => 'user',
            'status' => 'active',
            'email_verified_at' => now(),
            'password' => Hash::make('user'),
        ]);

        // Call 10 Fake Users
        User::factory(10)->create();
    }
}
