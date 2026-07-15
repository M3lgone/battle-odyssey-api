<?php

namespace Database\Seeders;

use App\Models\User;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Artisan;

class DatabaseSeeder extends Seeder
{
    use WithoutModelEvents;

    /**
     * Seed the application's database.
     */
    public function run(): void
    {
        // User::factory(10)->create();

        User::factory()->create([
            'name' => 'Test User',
            'email' => 'test@example.com',
        ]);

        Artisan::call('passport:client', [
            '--personal' => true,
            '--name' => 'Battle Odyssey Client',
            '--provider' => 'users'
        ]);

        $this->call([
            AdminSeeder::class,
            CharacterSeeder::class,
            SkillSeeder::class,
            CharacterHasSkillSeeder::class,
            EnemySeeder::class,
            EnemyHasSkillSeeder::class,
            GameSeeder::class,

        ]);
    }
}
