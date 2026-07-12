<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use App\Models\Enemy;
use App\Models\Skill;

class EnemyHasSkillSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $enemy1 = Enemy::where('enemy_name', 'Goblin')->first();
        $enemy2 = Enemy::where('enemy_name', 'Troll')->first();
        $enemy3 = Enemy::where('enemy_name', 'Orc')->first();

        $hack = Skill::where('skill_name', 'Hack')->first();
        $smash = Skill::where('skill_name', 'Smash')->first();
        $rampage = Skill::where('skill_name', 'Rampage')->first();

        DB::table('enemy_has_skill')->insert([
            ['enemy_id' => $enemy1->id, 'skill_id' => $hack->id],
            ['enemy_id' => $enemy2->id, 'skill_id' => $smash->id],
            ['enemy_id' => $enemy3->id, 'skill_id' => $rampage->id],
        ]);
    }
}
