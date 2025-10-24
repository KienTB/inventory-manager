<?php

namespace Database\Seeders;

use App\Models\User;
use Illuminate\Database\Seeder;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;

class UserSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // Update existing users with roles or create new ones
        $users = [
            [
                'name' => 'Admin',
                'username' => 'admin',
                'email' => 'admin@admin.com',
                'role' => 'admin',
            ],
            [
                'name' => 'quest',
                'username' => 'quest',
                'email' => 'quest@quest.com',
                'role' => 'user',
            ],
            [
                'name' => 'user',
                'username' => 'user',
                'email' => 'user@user.com',
                'role' => 'user',
            ]
        ];

        foreach ($users as $userData) {
            User::updateOrCreate(
                ['email' => $userData['email']],
                [
                    'name' => $userData['name'],
                    'username' => $userData['username'],
                    'role' => $userData['role'],
                    'email_verified_at' => now(),
                    'password' => bcrypt('password'),
                    'created_at' => now(),
                    'updated_at' => now()
                ]
            );
        }
    }
}
