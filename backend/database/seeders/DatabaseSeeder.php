<?php

declare(strict_types=1);

namespace Database\Seeders;

use App\Models\Role;
use App\Models\User;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class DatabaseSeeder extends Seeder
{
    use WithoutModelEvents;

    /**
     * Seed the application's database.
     */
    public function run(): void
    {
        User::factory()->create([
            'username' => 'TestUser',
            'email' => 'test@example.com',
            'password' => 2324,
        ]);

        Role::insert([
            ['name' => 'User', 'slug' => 'user'],
            ['name' => 'Premium', 'slug' => 'premium'],
            ['name' => 'Admin', 'slug' => 'admin'],
        ]);
    }
}
