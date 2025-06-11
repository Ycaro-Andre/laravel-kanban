<?php

namespace Database\Factories;

use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\Card>
 */
class CardFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        return [
            'list_id' => \App\Models\KanbanList::factory(),
            'title' => $this->faker->sentence(4),
            'description' => $this->faker->paragraph,
            'position' => $this->faker->numberBetween(1, 10),
            'due_date' => $this->faker->optional()->dateTimeBetween('now', '+1 month'),
            'created_at' => now(),
            'updated_at' => now(),
            'deleted_at' => null,
        ];
    }
}
