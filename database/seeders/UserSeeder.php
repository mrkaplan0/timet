<?php

// database/seeders/UserSeeder.php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;
use App\Models\User;

class UserSeeder extends Seeder
{
    public function run(): void
    {
        // Create admin user
        User::create([
            'name' => 'Admin User',
            'email' => 'a@e.com',
            'password' => Hash::make('password'), 
            'role' => 'admin',
        ]);

        // Create 10 normal users
        for ($i = 1; $i <= 10; $i++) {
            User::create([
                'name' => "Normal User {$i}",
                'email' => "user{$i}@example.com",
                'password' => Hash::make('password'),
                'role' => 'user',
            ]);
        }
    }
}