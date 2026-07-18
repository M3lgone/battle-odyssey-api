<?php

namespace Database\Factories;

use App\Models\Battle;
use App\Models\Character;
use App\Models\Game;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends Factory<Battle>
 */
class BattleFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        return [
            'result' => 'ongoing',
            'character_id' => Character::factory(),
            'game_id' => Game::factory(),
            'character_current_hp' => fake()->numberBetween(100, 200),
            'character_current_mp' => fake()->numberBetween(100, 200),
            'enemy_current_hp' => fake()->numberBetween(50, 500),
            'enemy_current_mp' => fake()->numberBetween(10, 100),
            'total_damage_dealt' => 0,
            'total_damage_received' => 0,
        ];
    }
}
