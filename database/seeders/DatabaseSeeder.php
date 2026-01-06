<?php

namespace Database\Seeders;

use App\Models\User;
// use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class DatabaseSeeder extends Seeder
{
    /**
     * Seed the application's database.
     */
    public function run(): void
    {
        // 1. Membuat 1 User tetap untuk kamu login nanti
        $user = \App\Models\User::factory()->create([
            'name' => 'Belajar Laravel',
            'email' => 'test@example.com',
            'password' => bcrypt('password123'), // Password kamu nanti
        ]);

        // 2. Membuat 10 Project acak yang dimiliki oleh user tersebut
        \App\Models\Project::factory(10)->create([
            'user_id' => $user->id,
        ]);
    }
}
