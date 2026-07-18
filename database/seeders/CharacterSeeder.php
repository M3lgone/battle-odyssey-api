<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use App\Models\Character;

class CharacterSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        Character::create([
            'class' => 'Warrior',
            'attack' => 15,
            'defense' => 20,
            'max_health_points' => 120,
            'max_magic_points' => 100,
            'character_image_url' => 'images/characters/warrior.png',
        ]);

        Character::create([
            'class' => 'Mage',
            'attack' => 10,
            'defense' => 15,
            'max_health_points' => 100,
            'max_magic_points' => 120,
            'character_image_url' => 'images/characters/mage.png',
        ]);

        Character::create([
            'class' => 'Archer',
            'attack' => 25,
            'defense' => 15,
            'max_health_points' => 100,
            'max_magic_points' => 100,
            'character_image_url' => 'images/characters/archer.png',
        ]);
    }
}
