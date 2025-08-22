<?php

namespace Database\Factories;

use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\TimeEntry>
 */
class TimeEntryFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        $startTime = fake()->dateTimeBetween('-30 days', 'now');
        $endTime = (clone $startTime)->modify('+' . fake()->numberBetween(1, 8) . ' hours');
        $totalHours = round(($endTime->getTimestamp() - $startTime->getTimestamp()) / 3600, 2);

        return [
            'user_id' => \App\Models\User::factory(),
            'start_time' => $startTime,
            'end_time' => $endTime,
            'total_hours' => $totalHours,
            'note' => fake()->optional(0.7)->sentence(),
        ];
    }
}
