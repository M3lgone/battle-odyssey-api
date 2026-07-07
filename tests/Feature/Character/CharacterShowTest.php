<?php

use App\Models\User;
use App\Models\Character;
use App\Models\Skill;
use Illuminate\Support\Facades\Artisan;
use Laravel\Passport\Passport;

beforeEach(function () {
    Artisan::call('passport:client', ['--personal' => true, '--no-interaction' => true]);
});

it('player can view character details', function () {
    $user = User::factory()->create();

    Passport::actingAs($user);

    $character = Character::factory()->create();

    $skills = Skill::factory()->count(2)->create();

    $character->skills()->attach($skills);

    $character = Character::first();

    $response = $this->getJson('/api/v1/characters/' . $character->id);

    $response->assertStatus(200)
             ->assertJsonStructure([
                'id', 'class', 'attack', 'defense', 
                'max_health_points', 'max_magic_points',
                'skills' => [
                    '*' => ['id', 'skill_name', 'description', 'damage_skill', 'skill_cost_magic_points']
                ]
            ]);
 });

it('returns 404 if character does not exist', function () {
    $user = User::factory()->create();
    Passport::actingAs($user);

    $response = $this->getJson('/api/v1/characters/99999');

    $response->assertStatus(404);
});

it('fails to view character details if user is unauthenticated', function () {
    $character = Character::factory()->create();

    $response = $this->getJson('/api/v1/characters/' . $character->id);

    $response->assertStatus(401);
});
