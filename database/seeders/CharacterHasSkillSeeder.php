<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use App\Models\Character;
use App\Models\Skill;

class CharacterHasSkillSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $warrior = Character::where('class', 'Warrior')->first();
        $mage = Character::where('class', 'Mage')->first();
        $archer = Character::where('class', 'Archer')->first();
        $slash = Skill::where('skill_name', 'Slash')->first();
        $fireball = Skill::where('skill_name', 'Fireball')->first();
        $powerShot = Skill::where('skill_name', 'Power shot')->first();

         DB::table('character_has_skill')->insert([
            ['character_id' => $warrior->id, 'skill_id' => $slash->id],
            ['character_id' => $mage->id, 'skill_id' => $fireball->id],
            ['character_id' => $archer->id, 'skill_id' => $powerShot->id],
        ]);
    }
}
