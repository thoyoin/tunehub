<?php

namespace Database\Factories;

use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\Track>
 */
class TrackFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        return [
            'title' => $this->faker->title(),
            'audio_url' => $this->faker->url(),
            'cover_url' => $this->faker->url(),
            'duration' => $this->faker->numberBetween($min = 10, $max = 100),
        ];
    }
}
