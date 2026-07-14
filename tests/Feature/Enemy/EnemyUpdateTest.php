<?php

use App\Models\User;
use App\Models\Enemy;
use Laravel\Passport\Passport;

it('admin can update an existing enemy', function () {

    $admin = User::factory()->create(['role' => 'admin']);

    Passport::actingAs($admin);

    $enemy = Enemy::factory()->create([
        'enemy_name' => 'Goblin',
        'max_health_points' => 100,
    ]);

    $data = [
        'enemy_name' => 'Hobgoblin',
        'max_health_points' => 300,
        'max_magic_points' => $enemy->max_magic_points,
        'attack' => $enemy->attack,
        'defense' => $enemy->defense,
    ];

    $response = $this->putJson('/api/v1/enemies/' . $enemy->id, $data);

    $response->assertStatus(200)
             ->assertJsonFragment(['enemy_name' => 'Hobgoblin']);

    $this->assertDatabaseHas('enemies', [
        'id' => $enemy->id,
        'enemy_name' => 'Hobgoblin',
        'max_health_points' => 300,
    ]);
});

it('player cannot update an enemy', function () {

    $player = User::factory()->create(['role' => 'player']);

    Passport::actingAs($player);

    $enemy = Enemy::factory()->create([
        'enemy_name' => 'Troll',
    ]);

    $data = [
        'enemy_name' => 'Updated Troll',
        'attack' => 9999,
    ];

    $response = $this->putJson('/api/v1/enemies/' . $enemy->id, $data);

    $response->assertStatus(403);
    $this->assertDatabaseHas('enemies', [
        'id' => $enemy->id,
        'enemy_name' => 'Troll', 
    ]);
});

it('returns 404 if enemy to update does not exist', function () {

    $admin = User::factory()->create(['role' => 'admin']);

    Passport::actingAs($admin);

    $data = [
        'enemy_name' => 'Ghost',
        'max_health_points' => 50,
        'max_magic_points' => 20,
        'attack' => 10,
        'defense' => 10,
    ];

    $response = $this->putJson('/api/v1/enemies/99999', $data);

    $response->assertStatus(404);
});

it('fails to update if user is unauthenticated', function () {

    $enemy = Enemy::factory()->create();

    $response = $this->putJson('/api/v1/enemies/' . $enemy->id, [
        'enemy_name' => 'Updated Enemy'
    ]);

    $response->assertStatus(401);
});

it('validates data when updating an enemy', function () {

    $admin = User::factory()->create(['role' => 'admin']);

    Passport::actingAs($admin);

    $enemy = Enemy::factory()->create();

    $data = [
        'enemy_name' => '', 
        'max_health_points' => -50,
        'max_magic_points' => $enemy->max_magic_points,
        'attack' => $enemy->attack,
        'defense' => $enemy->defense,
    ];

    $response = $this->putJson('/api/v1/enemies/' . $enemy->id, $data);

    $response->assertStatus(422)
             ->assertJsonValidationErrors(['enemy_name', 'max_health_points']);
});
