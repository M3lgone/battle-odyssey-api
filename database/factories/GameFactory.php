<?php

namespace Database\Factories;

use App\Models\Game;
use App\Models\User;
use App\Models\Character;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends Factory<Game>
 */
class GameFactory extends Factory
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
            'character_id' => Character::factory(),
            'status' => fake()->randomElement(['active', 'finished']),
        ];
    }
}
