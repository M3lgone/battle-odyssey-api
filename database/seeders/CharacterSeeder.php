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
            'class' => 'warrior',
            'attack' => 15,
            'defense' => 20,
            'max_health_points' => 120,
            'max_magic_points' => 100,
        ]);

        Character::create([
            'class' => 'mage',
            'attack' => 10,
            'defense' => 15,
            'max_health_points' => 100,
            'max_magic_points' => 120,
        ]);

        Character::create([
            'class' => 'archer',
            'attack' => 25,
            'defense' => 15,
            'max_health_points' => 100,
            'max_magic_points' => 100,
        ]);
    }
}
