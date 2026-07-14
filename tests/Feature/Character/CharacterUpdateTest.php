<?php

use App\Models\User;
use App\Models\Character;
use Laravel\Passport\Passport;

it('admin can update an existing character', function () {

    $admin = User::factory()->create(['role' => 'admin']);

    Passport::actingAs($admin);

    $character = Character::factory()->create([
        'class' => 'Archer',
        'attack' => 25,
    ]);

    $updateData = [
        'class' => 'Mage',
        'attack' => 99,
    ];

    $response = $this->putJson('/api/v1/characters/' . $character->id, $updateData);

    $response->assertStatus(200)
             ->assertJsonFragment($updateData);

    $this->assertDatabaseHas('characters', [
        'id' => $character->id,
        'class' => 'Mage',
        'attack' => 99,
    ]);
});

it('player cannot update a character', function () {

    $player = User::factory()->create(['role' => 'player']);

    Passport::actingAs($player);

    $character = Character::factory()->create();

    $response = $this->putJson('/api/v1/characters/' . $character->id, [
        'attack' => 99,
    ]);

    $response->assertStatus(403);
});

it('unauthenticated user cannot update a character', function () {
    $character = Character::factory()->create();

    $response = $this->putJson('/api/v1/characters/' . $character->id, [
        'attack' => 99,
    ]);

    $response->assertStatus(401);
});

it('fails to update character with invalid data', function () {

    $admin = User::factory()->create(['role' => 'admin']);

    Passport::actingAs($admin);
    
    $character = Character::factory()->create();

    $response = $this->putJson('/api/v1/characters/' . $character->id, [
        'attack' => -50,
    ]);

    $response->assertStatus(422)
             ->assertJsonValidationErrors(['attack']);
});

it('returns 404 if admin tries to update a non-existent character', function () {

    $admin = User::factory()->create(['role' => 'admin']);

    Passport::actingAs($admin);

    $response = $this->putJson('/api/v1/characters/99999', [
        'attack' => 99, 
    ]);

    $response->assertStatus(404);
});
