<?php

namespace Database\Factories;

use App\Models\Skill;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends Factory<Skill>
 */
class SkillFactory extends Factory
{
    protected $model = Skill::class;
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        return [
            'skill_name' => fake()->unique()->word(), 
            'description' => fake()->sentence(),
            'damage_skill' => fake()->numberBetween(15, 50),
            'skill_cost_magic_points' => fake()->numberBetween(10, 30),
        ];
    }
}
