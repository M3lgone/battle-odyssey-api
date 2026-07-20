<?php

use App\Models\User;
use App\Models\Game;
use Laravel\Passport\Passport;

it('player can start a new game', function () {

    $player = User::factory()->create(['role' => 'player']);

    Passport::actingAs($player);

    $response = $this->postJson('/api/v1/games');

    $response->assertStatus(201)
             ->assertJsonFragment([
                 'status' => 'active',
                 'user_id' => $player->id,
             ]);

    $this->assertDatabaseHas('games', [
        'user_id' => $player->id,
        'status' => 'active',
    ]);
});

it('fails to start a game if user is unauthenticated', function () {

    $response = $this->postJson('/api/v1/games');

    $response->assertStatus(401);
});

it('fails to start a game if the player already has one in progress', function () {

    $player = User::factory()->create(['role' => 'player']);

    Passport::actingAs($player);

    $game = Game::factory()->create([
        'user_id' => $player->id,
        'status' => 'in_progress',
    ]);

    $response = $this->postJson('/api/v1/games');

    $response->assertStatus(400)
             ->assertJson([
                 'error' => 'You already have a game in progress, you must finish or delete it to start another one.'
             ]);
});

it('admin can start a new game', function () {

    $admin = User::factory()->create(['role' => 'admin']);

    Passport::actingAs($admin);

    $response = $this->postJson('/api/v1/games');

    $response->assertStatus(201)
             ->assertJsonFragment([
                 'status' => 'active',
                 'user_id' => $admin->id,
             ]);
});
