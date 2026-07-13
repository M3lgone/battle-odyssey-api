<?php

use App\Models\User;
use App\Models\Enemy;
use App\Models\Skill;
use Illuminate\Support\Facades\Artisan;
use Laravel\Passport\Passport;

beforeEach(function () {
    Artisan::call('passport:client', ['--personal' => true, '--no-interaction' => true]);
});

it('admin can view enemy details', function () {

    $admin = User::factory()->create(['role' => 'admin']);

    Passport::actingAs($admin);

    $enemy = Enemy::factory()->create();

    $skills = Skill::factory()->count(2)->create();

    $enemy->skills()->attach($skills);

    $response = $this->getJson('/api/v1/enemies/' . $enemy->id);

    $response->assertStatus(200)
             ->assertJsonStructure([
                'id', 'enemy_name', 'max_health_points', 'max_magic_points', 
                'attack', 'defense',
                'skills' => [
                    '*' => ['id', 'skill_name', 'description', 'damage_skill', 'skill_cost_magic_points']
                ]
            ]);
 });

 it('player cannot view enemy details', function () {

    $player = User::factory()->create(['role' => 'player']);

    Passport::actingAs($player);

    $enemy = Enemy::factory()->create();

    $skills = Skill::factory()->count(2)->create();

    $enemy->skills()->attach($skills);

    $response = $this->getJson('/api/v1/enemies/' . $enemy->id);

    $response->assertStatus(403);
 });

it('returns 404 if enemy does not exist', function () {

    $admin = User::factory()->create(['role' => 'admin']);

    Passport::actingAs($admin);

    $response = $this->getJson('/api/v1/enemies/99999');

    $response->assertStatus(404);
});

it('fails to view character details if admin is unauthenticated', function () {

    $enemy = Enemy::factory()->create();

    $response = $this->getJson('/api/v1/enemies/' . $enemy->id);

    $response->assertStatus(401);
});
