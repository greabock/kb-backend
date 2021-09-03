<?php

namespace Database\Seeders;

use App\Models\User;
use Illuminate\Database\Seeder;

class DatabaseSeeder extends Seeder
{
    /**
     * Seed the application's database.
     *
     * @return void
     */
    public function run()
    {
        User::factory()->create([
            'name' => 'admin',
            'email' => 'admin@admin.com',
            'login' => 'admin',
            'role' => User::ROLE_ADMIN,
            'password' => 'qazxdews333',
        ]);

        User::factory()->create([
            'name' => 'moderator',
            'email' => 'moderator@moderator.com',
            'login' => 'moderator',
            'role' => User::ROLE_MODERATOR,
            'password' => '123123qw',
        ]);

        User::factory()->create([
            'name' => 'user',
            'email' => 'user@user.com',
            'login' => 'user',
            'role' => User::ROLE_USER,
            'password' => '123123qw',
        ]);
    }
}
