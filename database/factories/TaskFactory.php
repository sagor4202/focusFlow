<?php

namespace Database\Factories;

use App\Models\Folder;
use App\Models\Task;
use App\Models\User;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends Factory<Task>
 */
class TaskFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        return [
            'user_id' => User::factory(),
            'assigned_to_user_id' => fn (array $attributes) => $attributes['user_id'] ?? null,
            'folder_id' => Folder::factory(),
            'title' => fake()->sentence(4),
            'description' => fake()->boolean(60) ? fake()->sentence(10) : null,
            'priority' => fake()->numberBetween(0, 3),
            'is_completed' => false,
            'completed_at' => null,
            'due_date' => fake()->boolean(70) ? fake()->dateTimeBetween('today', '+10 days') : null,
        ];
    }
}
