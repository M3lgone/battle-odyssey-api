<?php

use App\Models\User;
use App\Models\Game;
use App\Models\Character;
use Laravel\Passport\Passport;

it('player can start a new game', function () {

    $player = User::factory()->create(['role' => 'player']);

    Passport::actingAs($player);

    $character = Character::factory()->create();

    $response = $this->postJson('/api/v1/games', [
        'character_id' => $character->id,
    ]);

    $response->assertStatus(201)
             ->assertJsonFragment([
                 'status' => 'active',
                 'user_id' => $player->id,
                 'character_id' => $character->id,
             ]);

    $this->assertDatabaseHas('games', [
        'user_id' => $player->id,
        'character_id' => $character->id,
        'status' => 'active',
    ]);
});

it('returns the chosen character with its skills when starting a game', function () {

    $player = User::factory()->create(['role' => 'player']);

    Passport::actingAs($player);

    $character = Character::factory()->create(['class' => 'Warrior']);

    $response = $this->postJson('/api/v1/games', [
        'character_id' => $character->id,
    ]);

    $response->assertStatus(201)
             ->assertJsonFragment(['class' => 'Warrior'])
             ->assertJsonStructure([
                 'character' => ['id', 'class', 'skills']
             ]);
});

it('requires a character to start a game', function () {

    $player = User::factory()->create(['role' => 'player']);

    Passport::actingAs($player);

    $response = $this->postJson('/api/v1/games', []);

    $response->assertStatus(422)
             ->assertJsonValidationErrors(['character_id']);
});

it('fails to start a game if user is unauthenticated', function () {

    $response = $this->postJson('/api/v1/games', []);

    $response->assertStatus(401);
});

it('fails to start a game if the player already has an active game', function () {

    $player = User::factory()->create(['role' => 'player']);

    Passport::actingAs($player);

    $game = Game::factory()->create([
        'user_id' => $player->id,
        'status' => 'active', 
    ]);

    $response = $this->postJson('/api/v1/games', [
        'character_id' => $game->character_id,
    ]);

    $response->assertStatus(400)
             ->assertJson([
                 'error' => 'You already have an active game, you must finish it to start another one.'
             ]);
});

it('admin can start a new game', function () {

    $admin = User::factory()->create(['role' => 'admin']);

    Passport::actingAs($admin);

    $character = Character::factory()->create();

    $response = $this->postJson('/api/v1/games', [
        'character_id' => $character->id,
    ]);

    $response->assertStatus(201)
             ->assertJsonFragment([
                 'status' => 'active',
                 'user_id' => $admin->id,
             ]);
});
