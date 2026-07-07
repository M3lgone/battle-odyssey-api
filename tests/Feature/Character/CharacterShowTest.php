<?php

use App\Models\User;
use App\Models\Character;
use Illuminate\Support\Facades\Artisan;
use Laravel\Passport\Passport;

beforeEach(function () {
    Artisan::call('passport:client', ['--personal' => true, '--no-interaction' => true]);
});

it('player can view character details', function () {
    $user = User::factory()->create();

    Passport::actingAs($user);

    Artisan::call('db:seed', ['--class' => 'CharacterSeeder']);
    Artisan::call('db:seed', ['--class' => 'SkillSeeder']);
    Artisan::call('db:seed', ['--class' => 'CharacterHasSkillSeeder']);

    $character = Character::first();

    $response = $this->getJson('/api/v1/characters/' . $character->id);

    $response->assertStatus(200)
             ->assertJsonStructure([
                'id', 'class', 'attack', 'defense', 
                'max_health_points', 'max_magic_points',
                'skills' => [['id', 'skill_name', 'description', 'damage_skill', 'skill_cost_magic_points']]
             ]);
});
