<?php

use App\Models\User;
use App\Models\Character;
use Illuminate\Support\Facades\Artisan;
use Laravel\Passport\Passport;

beforeEach(function () {
    Artisan::call('passport:client', ['--personal' => true, '--no-interaction' => true]);
});

it('admin can create a new character', function () {

    $admin = User::factory()->create(['role' => 'admin']);

    Passport::actingAs($admin);

    $characterData = [
        'class' => 'Mage',
        'attack' => 30,
        'defense' => 15,
        'max_health_points' => 150,
        'max_magic_points' => 200,
    ];

    $response = $this->postJson('/api/v1/characters', $characterData);

    $response->assertStatus(201)
             ->assertJsonFragment($characterData);

    $this->assertDatabaseHas('characters', $characterData);
});

it('fails to create character with invalid data', function () {

    $admin = User::factory()->create(['role' => 'admin']);

    Passport::actingAs($admin);

    $response = $this->postJson('/api/v1/characters', [
        'class' => 'SuperSaiyan',
        'attack' => -50,
    ]);

    $response->assertStatus(422)
             ->assertJsonValidationErrors(['class', 'attack']);
});

it('player cannot create a character', function () {

    $player = User::factory()->create(['role' => 'player']);

    Passport::actingAs($player);

    $response = $this->postJson('/api/v1/characters', [
        'class' => 'Warrior',
        'attack' => 20,
        'defense' => 20,
        'max_health_points' => 200,
        'max_magic_points' => 50,
    ]);

    $response->assertStatus(403);
});

it('unauthenticated user cannot create a character', function () {

    $response = $this->postJson('/api/v1/characters', [
        'class' => 'Warrior',
        'attack' => 20,
        'defense' => 20,
        'max_health_points' => 200,
        'max_magic_points' => 50,
    ]);

    $response->assertStatus(401);
});

it('fails to create character if required fields are missing', function () {

    $admin = User::factory()->create(['role' => 'admin']);
    
    Passport::actingAs($admin);

    $response = $this->postJson('/api/v1/characters', []);

    $response->assertStatus(422)
             ->assertJsonValidationErrors([
                 'class', 
                 'attack', 
                 'defense', 
                 'max_health_points', 
                 'max_magic_points'
             ]);
});
