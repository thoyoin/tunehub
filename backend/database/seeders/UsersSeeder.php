<?php

namespace Database\Seeders;

use App\Models\User;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class UsersSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        for ($day = 29; $day >= 0; $day--) {
            $usersCount = rand(1, 4);

            for ($i = 0; $i < $usersCount; $i++) {
                $date = now()->subDays($day)->setTime(rand(0, 23), rand(0, 59));

                User::factory()->create([
                    'created_at' => $date,
                    'updated_at' => $date,
                ]);
            }
        }
    }
}
