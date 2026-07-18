<?php

namespace Database\Factories;

use App\Models\Character;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends Factory<Character>
 */
class CharacterFactory extends Factory
{
    protected $model = Character::class;

    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        return [
            'class' => fake()->randomElement(['Warrior', 'Mage', 'Archer']),
            'attack' => fake()->numberBetween(10, 50),
            'defense' => fake()->numberBetween(10, 30),
            'max_health_points' => fake()->numberBetween(100, 200),
            'max_magic_points' => fake()->numberBetween(100, 200),
            'character_image_url' => fake()->imageUrl(),
        ];
    }
}
