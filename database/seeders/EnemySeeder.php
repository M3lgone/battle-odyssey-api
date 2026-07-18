<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use App\Models\Enemy;

class EnemySeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        Enemy::create([
            'enemy_name' => 'Goblin',
            'max_health_points' => 80,
            'max_magic_points' => 30,
            'attack' => 10,
            'defense' => 5,
            'enemy_image_url' => 'images/enemies/goblin.png',
            'background_image_url' => 'images/backgrounds/bg-goblin.png',
        ]);

        Enemy::create([
            'enemy_name' => 'Troll',
            'max_health_points' => 100,
            'max_magic_points' => 50,
            'attack' => 18,
            'defense' => 12,
            'enemy_image_url' => 'images/enemies/troll.png',
            'background_image_url' => 'images/backgrounds/bg-troll.png',
        ]);

        Enemy::create([
            'enemy_name' => 'Orc',
            'max_health_points' => 180,
            'max_magic_points' => 80,
            'attack' => 22,
            'defense' => 15,
            'enemy_image_url' => 'images/enemies/orc.png',
            'background_image_url' => 'images/backgrounds/bg-orc.png',
        ]);
    }
}
