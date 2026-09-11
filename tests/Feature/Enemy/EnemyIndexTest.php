<?php

use App\Models\Enemy;
use App\Models\Skill;
use App\Models\User;
use Laravel\Passport\Passport;

it('admin can list all enemies', function () {

    $admin = User::factory()->create(['role' => 'admin']);

    Enemy::factory()->count(3)->create();

    Passport::actingAs($admin);

    $response = $this->getJson('/api/v1/enemies');

    $response->assertStatus(200)
             ->assertJsonCount(3)
             ->assertJsonStructure([
                '*' => ['id', 'enemy_name', 'max_health_points', 'max_magic_points', 'attack', 'defense', 'skills']
             ]);
});

it('player cannot list enemies', function () {

    $player = User::factory()->create(['role' => 'player']);

    Enemy::factory()->count(3)->create();

    Passport::actingAs($player);

    $response = $this->getJson('/api/v1/enemies');

    $response->assertStatus(403);
});

it('unauthenticated user cannot list enemies', function () {

    Enemy::factory()->count(3)->create();

    $response = $this->getJson('/api/v1/enemies');

    $response->assertStatus(401);
});

it('enemy index returns associated skills with correct count', function () {

    $admin = User::factory()->create(['role' => 'admin']);

    Passport::actingAs($admin);

    $enemy = Enemy::factory()->create();

    $skills = Skill::factory()->count(2)->create();

    $enemy->skills()->attach($skills);

    $response = $this->getJson('/api/v1/enemies');

    $response->assertStatus(200)
             ->assertJsonCount(1)
             ->assertJsonStructure([
                '*' => [
                    'id', 'enemy_name', 'max_health_points', 'max_magic_points', 'attack', 'defense',
                    'skills' => [
                        '*' => ['id', 'skill_name', 'description', 'damage_skill', 'skill_cost_magic_points']
                    ]
                ]
             ]);

    $data = $response->json();

    expect($data[0]['skills'])->toHaveCount(2);
    expect(collect($data[0]['skills'])->pluck('id')->sort()->values()->all())
        ->toEqual($skills->pluck('id')->sort()->values()->all());
});

it('enemy without skills returns empty skills array', function () {

    $admin = User::factory()->create(['role' => 'admin']);

    Passport::actingAs($admin);

    Enemy::factory()->create();

    $response = $this->getJson('/api/v1/enemies');

    $response->assertStatus(200)
             ->assertJsonCount(1)
             ->assertJsonPath('0.skills', []);

    $data = $response->json();

    expect(array_key_exists('skills', $data[0]))->toBeTrue();
    expect($data[0]['skills'])->toBe([]);
});
