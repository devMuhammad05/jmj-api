<?php

namespace Database\Factories;

use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\PersonalizedAnnouncement>
 */
class PersonalizedAnnouncementFactory extends Factory
{
    /**
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        return [
            'title' => fake()->sentence(4),
            'message' => fake()->paragraph(),
            'sent_at' => null,
        ];
    }

    public function sent(): static
    {
        return $this->state(['sent_at' => now()]);
    }
}
