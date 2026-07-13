<?php

namespace Database\Factories;

use App\Models\Enemy;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends Factory<Enemy>
 */
class EnemyFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        return [
            'enemy_name' => fake()->randomElement(['Goblin', 'Troll', 'Orc']),
            'max_health_points' => fake()->numberBetween(50, 500),
            'max_magic_points' => fake()->numberBetween(10, 100),
            'attack' => fake()->numberBetween(10, 40),
            'defense' => fake()->numberBetween(5, 30),
        ];
    }
}
