<?php

namespace Database\Factories;

use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\Project>
 */
class ProjectFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        return [
            'title' => fake()->sentence(3), // Membuat judul acak 3 kata
            'description' => fake()->paragraph(), // Membuat deskripsi acak
            'status' => fake()->randomElement(['pending', 'on_progress', 'completed']), // Status acak
            'user_id' => 1, // Sementara kita hubungkan ke user pertama (ID 1)
        ];
    }
}
