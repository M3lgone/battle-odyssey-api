<?php

use App\Models\User;
use App\Models\Game;
use App\Models\Character;
use App\Models\Enemy;
use App\Models\Battle;

it('requires a game id and character id to create a battle', function () {

    $user = User::factory()->create();

    $response = $this->actingAs($user)->postJson('/api/v1/games/battles', []);

    $response->assertStatus(422)
             ->assertJsonValidationErrors(['game_id', 'character_id']);
});

it('starts the first battle against the goblin', function () {

    $user = User::factory()->create();

    $game = Game::factory()->create(['user_id' => $user->id]);
    
    $character = Character::factory()->create([
        'character_image_url' => 'warrior.png'
    ]);

    Enemy::factory()->create([
        'enemy_name' => 'Goblin',
        'enemy_image_url' => 'goblin.png',
        'background_image_url' => 'bg_goblin.png'
    ]);

    $response = $this->actingAs($user)->postJson('/api/v1/games/battles', [
        'game_id' => $game->id,
        'character_id' => $character->id,
    ]);

    $response->assertStatus(201)
             ->assertJsonFragment([
                 'enemy_name' => 'Goblin'
             ]);
});

it('starts the second battle against the troll after one victory', function () {

    $user = User::factory()->create();

    $game = Game::factory()->create(['user_id' => $user->id]);

    $character = Character::factory()->create([
        'character_image_url' => 'warrior.png'
    ]);

    Enemy::factory()->create([
        'enemy_name' => 'Goblin',
        'enemy_image_url' => 'goblin.png',
        'background_image_url' => 'bg_goblin.png'
    ]);
    Enemy::factory()->create([
        'enemy_name' => 'Troll',
        'enemy_image_url' => 'troll.png',
        'background_image_url' => 'bg_troll.png'
    ]);

    Battle::factory()->create([
        'game_id' => $game->id,
        'character_id' => $character->id,
    ]);

    $response = $this->actingAs($user)->postJson('/api/v1/games/battles', [
        'game_id' => $game->id,
        'character_id' => $character->id,
    ]);

    $response->assertStatus(201)
             ->assertJsonFragment([
                 'enemy_name' => 'Troll'
             ]);
});

it('starts the final battle against the orc after two victories', function () {

    $user = User::factory()->create();

    $game = Game::factory()->create(['user_id' => $user->id]);

    $character = Character::factory()->create([
        'character_image_url' => 'warrior.png'
    ]);

    Enemy::factory()->create(['enemy_name' => 'Goblin', 'enemy_image_url' => 'url', 'background_image_url' => 'url']);
    Enemy::factory()->create(['enemy_name' => 'Troll', 'enemy_image_url' => 'url', 'background_image_url' => 'url']);
    Enemy::factory()->create(['enemy_name' => 'Orc', 'enemy_image_url' => 'url', 'background_image_url' => 'url']);

    Battle::factory()->count(2)->create([
        'game_id' => $game->id,
        'character_id' => $character->id,
    ]);

    $response = $this->actingAs($user)->postJson('/api/v1/games/battles', [
        'game_id' => $game->id,
        'character_id' => $character->id,
    ]);

    $response->assertStatus(201)
             ->assertJsonFragment([
                 'enemy_name' => 'Orc'
             ]);
});

it('returns victory when requesting a battle after defeating the orc', function () {

    $user = User::factory()->create();

    $game = Game::factory()->create(['user_id' => $user->id]);

    $character = Character::factory()->create([
        'character_image_url' => 'warrior.png'
    ]);

    Battle::factory()->count(3)->create([
        'game_id' => $game->id,
        'character_id' => $character->id,
    ]);

    $response = $this->actingAs($user)->postJson('/api/v1/games/battles', [
        'game_id' => $game->id,
        'character_id' => $character->id,
    ]);

    $response->assertStatus(200)
             ->assertJsonFragment([
                 'message' => 'Victory' 
             ]);
});