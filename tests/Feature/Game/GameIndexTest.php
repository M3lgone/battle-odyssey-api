<?php

use App\Models\Game;
use App\Models\User;
use Laravel\Passport\Passport;

it('returns the active game for the authenticated user', function () {

    $player = User::factory()->create(['role' => 'player']);

    Passport::actingAs($player);

    $game = Game::factory()->create([
        'user_id' => $player->id,
        'status' => 'active',
    ]);

    $response = $this->getJson('/api/v1/games');

    $response->assertStatus(200)
             ->assertJsonFragment([
                 'id' => $game->id,
                 'status' => 'active',
                 'user_id' => $player->id,
             ]);
});

it('returns 404 if the user has no active game', function () {

    $player = User::factory()->create(['role' => 'player']);

    Passport::actingAs($player);

    $response = $this->getJson('/api/v1/games');

    $response->assertStatus(404)
             ->assertJson([
                 'message' => 'No active game found.'
             ]);
});

it('fails to get games if user is unauthenticated', function () {

    $response = $this->getJson('/api/v1/games');

    $response->assertStatus(401);
});