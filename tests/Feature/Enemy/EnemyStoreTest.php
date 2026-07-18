<?php

use App\Models\User;
use App\Models\Enemy;
use Laravel\Passport\Passport;

it('admin can create a new enemy', function () {

    $admin = User::factory()->create(['role' => 'admin']);

    Passport::actingAs($admin);

    $data = [
        'enemy_name' => 'Hydra',
        'max_health_points' => 150,
        'max_magic_points' => 100,
        'attack' => 40,
        'defense' => 30,
        'enemy_image_url' => 'hydra.png',
        'background_image_url' => 'bg_hydra.png'
    ];

    $response = $this->postJson('/api/v1/enemies', $data);

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

    $data = [
        'enemy_name' => 'Goblin',
        'max_health_points' => 100,
        'max_magic_points' => 0,
        'attack' => 15,
        'defense' => 5,
        'enemy_image_url' => 'goblin.png',
        'background_image_url' => 'bg-goblin.png'
    ];

    $response = $this->postJson('/api/v1/enemies', $data);

    $response->assertStatus(403);
    $this->assertDatabaseMissing('enemies', ['enemy_name' => 'Goblin']);
});

it('fails to create an enemy if user is unauthenticated', function () {

    $data = [
        'enemy_name' => 'Ghost',
        'max_health_points' => 50,
        'max_magic_points' => 20,
        'attack' => 10,
        'defense' => 10,
        'enemy_image_url' => 'ghost.png',
        'background_image_url' => 'bg-ghost.png'
    ];

    $response = $this->postJson('/api/v1/enemies', $data);

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
                 'defense',
                 'enemy_image_url',
                 'background_image_url'
             ]);
});