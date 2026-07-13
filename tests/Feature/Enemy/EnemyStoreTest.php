<?php

use App\Models\User;
use App\Models\Enemy;
use Illuminate\Support\Facades\Artisan;
use Laravel\Passport\Passport;

beforeEach(function () {
    Artisan::call('passport:client', ['--personal' => true, '--no-interaction' => true]);
});

it('admin can create a new enemy', function () {

    $admin = User::factory()->create(['role' => 'admin']);

    Passport::actingAs($admin);

    $payload = [
        'enemy_name' => 'Hydra',
        'max_health_points' => 150,
        'max_magic_points' => 100,
        'attack' => 40,
        'defense' => 30,
    ];

    $response = $this->postJson('/api/v1/enemies', $payload);

    $response->assertStatus(201)
             ->assertJsonFragment(['enemy_name' => 'Hydra']);

    $this->assertDatabaseHas('enemies', [
        'enemy_name' => 'Hydra',
        'attack' => 40,
    ]);
});

it('player cannot create a new enemy', function () {

    $player = User::factory()->create(['role' => 'player']);

    Passport::actingAs($player);

    $payload = [
        'enemy_name' => 'Goblin',
        'max_health_points' => 100,
        'max_magic_points' => 0,
        'attack' => 15,
        'defense' => 5,
    ];

    $response = $this->postJson('/api/v1/enemies', $payload);

    $response->assertStatus(403);
    $this->assertDatabaseMissing('enemies', ['enemy_name' => 'Goblin']);
});

it('fails to create an enemy if user is unauthenticated', function () {

    $payload = [
        'enemy_name' => 'Ghost',
        'max_health_points' => 50,
        'max_magic_points' => 20,
        'attack' => 10,
        'defense' => 10,
    ];

    $response = $this->postJson('/api/v1/enemies', $payload);

    $response->assertStatus(401);
});

it('validates required fields when creating an enemy', function () {

    $admin = User::factory()->create(['role' => 'admin']);

    Passport::actingAs($admin);

    $response = $this->postJson('/api/v1/enemies', []);

    $response->assertStatus(422)
             ->assertJsonValidationErrors([
                 'enemy_name', 
                 'max_health_points', 
                 'max_magic_points', 
                 'attack', 
                 'defense'
             ]);
});