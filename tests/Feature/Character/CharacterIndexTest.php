<?php

use App\Models\User;
use App\Models\Character;
use Illuminate\Support\Facades\Artisan;
use Laravel\Passport\Passport;

beforeEach(function () {
    Artisan::call('passport:client', ['--personal' => true, '--no-interaction' => true]);
});

it('player can list all characters', function () {

    $player = User::factory()->create(['role' => 'player']);

    Passport::actingAs($player);

    Character::factory()->count(3)->create();

    $response = $this->getJson('/api/v1/characters');

    $response->assertStatus(200)
             ->assertJsonCount(3)
             ->assertJsonStructure([
                '*' => ['id', 'class', 'attack', 'defense', 'max_health_points', 'max_magic_points']]);
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
                 '*' => ['id', 'class', 'attack', 'defense', 'max_health_points', 'max_magic_points']
             ]);
});