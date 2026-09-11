<?php

use App\Models\User;
use App\Models\Character;
use App\Models\Skill;
use Laravel\Passport\Passport;

it('player can list all characters', function () {

    $player = User::factory()->create(['role' => 'player']);

    Passport::actingAs($player);

    Character::factory()->count(3)->create();

    $response = $this->getJson('/api/v1/characters');

    $response->assertStatus(200)
             ->assertJsonCount(3)
             ->assertJsonStructure([
                '*' => ['id', 'class', 'attack', 'defense', 'max_health_points', 'max_magic_points', 'skills']]);
});

it('unauthenticated user cannot list characters', function () {
    Character::factory()->count(3)->create();

    $response = $this->getJson('/api/v1/characters');

    $response->assertStatus(401);
});

it('admin can list all characters', function () {

    $admin = User::factory()->create(['role' => 'admin']);

    Passport::actingAs($admin);
    
    Character::factory()->count(3)->create();

    $response = $this->getJson('/api/v1/characters');

    $response->assertStatus(200)
             ->assertJsonCount(3)
             ->assertJsonStructure([
                 '*' => ['id', 'class', 'attack', 'defense', 'max_health_points', 'max_magic_points', 'skills']
             ]);
});

it('character index returns associated skills with correct count', function () {

    $player = User::factory()->create(['role' => 'player']);

    Passport::actingAs($player);

    $character = Character::factory()->create();

    $skills = Skill::factory()->count(2)->create();

    $character->skills()->attach($skills);

    $response = $this->getJson('/api/v1/characters');

    $response->assertStatus(200)
             ->assertJsonCount(1)
             ->assertJsonStructure([
                '*' => [
                    'id', 'class', 'attack', 'defense', 'max_health_points', 'max_magic_points',
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

it('character without skills returns empty skills array', function () {

    $player = User::factory()->create(['role' => 'player']);

    Passport::actingAs($player);

    $character = Character::factory()->create();

    $response = $this->getJson('/api/v1/characters');

    $response->assertStatus(200)
             ->assertJsonCount(1)
             ->assertJsonPath('0.skills', []);

    $data = $response->json();

    expect(array_key_exists('skills', $data[0]))->toBeTrue();
    expect($data[0]['skills'])->toBe([]);
});
