<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use App\Models\Skill;

class SkillSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // Warrior skill
        Skill::create([
            'skill_name' => 'Slash',
            'description' => 'Strikes the enemy with a swift sword slash',
            'damage_skill' => 20,
            'skill_cost_magic_points' => 15,
        ]);

        // Mage skill
        Skill::create([
            'skill_name' => 'Fireball',
            'description' => 'Conjures a ball of fire to incinerate',
            'damage_skill' => 30,
            'skill_cost_magic_points' => 10,
        ]);

        // Archer skill
        Skill::create([
            'skill_name' => 'Power shot',
            'description' => 'Shoots a charged arrow with devastating force',
            'damage_skill' => 35,
            'skill_cost_magic_points' => 25,
        ]);
    }
}
