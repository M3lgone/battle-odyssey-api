<?php

use App\Models\User;
use App\Models\Game;
use App\Models\Character;
use App\Models\Enemy;
use App\Models\Battle;
use App\Models\Skill;
use Laravel\Passport\Passport;

it('requires a game id to create a battle', function () {

    $user = User::factory()->create(['role' => 'player']);

    Passport::actingAs($user);

    $response = $this->postJson('/api/v1/battles', []);

    $response->assertStatus(422)
             ->assertJsonValidationErrors(['game_id']);
});

it('starts the second battle against the troll after one victory', function () {

    $user = User::factory()->create(['role' => 'player']);

    $character = Character::factory()->create(['character_image_url' => 'warrior.png']);

    $game = Game::factory()->create([
        'user_id' => $user->id,
        'character_id' => $character->id,
        'status' => 'active',
    ]);

    $characterSkill = Skill::factory()->create(['skill_name' => 'Slash']);
    $character->skills()->attach($characterSkill->id);

    Enemy::factory()->create(['enemy_name' => 'Goblin']);
    $troll = Enemy::factory()->create(['enemy_name' => 'Troll']);

    $trollSkill = Skill::factory()->create(['skill_name' => 'Club Smash']);
    $troll->skills()->attach($trollSkill->id);

    Battle::factory()->create([
        'game_id' => $game->id,
        'character_id' => $character->id,
        'result' => 'win', 
    ]);

    Passport::actingAs($user);

    $response = $this->postJson('/api/v1/battles', [
        'game_id' => $game->id,
    ]);

    $response->assertStatus(201)
             ->assertJsonFragment(['enemy_name' => 'Troll'])
             ->assertJsonFragment(['skill_name' => 'Slash'])
             ->assertJsonFragment(['skill_name' => 'Club Smash']);
});

it('starts the battle with the character fully healed and the enemy hp stored in the pivot', function () {

    $user = User::factory()->create(['role' => 'player']);

    $character = Character::factory()->create([
        'max_health_points' => 120,
        'max_magic_points' => 100,
    ]);

    $game = Game::factory()->create([
        'user_id' => $user->id,
        'character_id' => $character->id,
        'status' => 'active',
    ]);

    $enemy = Enemy::factory()->create([
        'max_health_points' => 80,
        'max_magic_points' => 30,
    ]);

    Passport::actingAs($user);

    $response = $this->postJson('/api/v1/battles', [
        'game_id' => $game->id,
    ]);

    $response->assertStatus(201)
             ->assertJsonFragment(['character_current_hp' => 120])
             ->assertJsonFragment(['character_current_mp' => 100])
             ->assertJsonFragment(['current_hp' => 80])
             ->assertJsonFragment(['current_mp' => 30]);

    $this->assertDatabaseHas('battle_has_enemy', [
        'enemy_id' => $enemy->id,
        'current_hp' => 80,
        'current_mp' => 30,
    ]);
});

it('starts the final battle against the orc after two victories', function () {

    $user = User::factory()->create(['role' => 'player']);

    $character = Character::factory()->create([
        'character_image_url' => 'warrior.png'
    ]);

    $game = Game::factory()->create([
        'user_id' => $user->id,
        'character_id' => $character->id,
        'status' => 'active',
    ]);

    Enemy::factory()->create(['enemy_name' => 'Goblin', 'enemy_image_url' => 'url', 'background_image_url' => 'url']);
    Enemy::factory()->create(['enemy_name' => 'Troll', 'enemy_image_url' => 'url', 'background_image_url' => 'url']);
    Enemy::factory()->create(['enemy_name' => 'Orc', 'enemy_image_url' => 'url', 'background_image_url' => 'url']);

    Battle::factory()->count(2)->create([
        'game_id' => $game->id,
        'character_id' => $character->id,
        'result' => 'win',
    ]);

    Passport::actingAs($user);

    $response = $this->postJson('/api/v1/battles', [
        'game_id' => $game->id,
    ]);

    $response->assertStatus(201)
             ->assertJsonFragment([
                 'enemy_name' => 'Orc'
             ]);
});

it('does not count losses or flees towards enemy progression', function () {

    $user = User::factory()->create(['role' => 'player']);

    $character = Character::factory()->create();

    $game = Game::factory()->create([
        'user_id' => $user->id,
        'character_id' => $character->id,
        'status' => 'active',
    ]);

    Enemy::factory()->create(['enemy_name' => 'Goblin']);
    Enemy::factory()->create(['enemy_name' => 'Troll']);

    Battle::factory()->create([
        'game_id' => $game->id,
        'character_id' => $character->id,
        'result' => 'loss',
    ]);

    Passport::actingAs($user);

    $response = $this->postJson('/api/v1/battles', [
        'game_id' => $game->id,
    ]);

    $response->assertStatus(201)
             ->assertJsonFragment(['enemy_name' => 'Goblin']);
});

it('returns victory and finishes the game when no enemies remain', function () {

    $user = User::factory()->create(['role' => 'player']);

    $character = Character::factory()->create([
        'character_image_url' => 'warrior.png'
    ]);

    $game = Game::factory()->create([
        'user_id' => $user->id,
        'character_id' => $character->id,
        'status' => 'active',
    ]);

    Battle::factory()->count(3)->create([
        'game_id' => $game->id,
        'character_id' => $character->id,
        'result' => 'win',
    ]);

    Passport::actingAs($user);

    $response = $this->postJson('/api/v1/battles', [
        'game_id' => $game->id,
    ]);

    $response->assertStatus(200)
             ->assertJsonFragment([
                 'message' => 'Victory' 
             ]);

    $this->assertDatabaseHas('games', [
        'id' => $game->id,
        'status' => 'finished',
    ]);
});

it('prevents creating a battle on a finished game', function () {

    $user = User::factory()->create(['role' => 'player']);

    $game = Game::factory()->create([
        'user_id' => $user->id,
        'status' => 'finished',
    ]);

    Enemy::factory()->create(['enemy_name' => 'Goblin']);

    Passport::actingAs($user);

    $response = $this->postJson('/api/v1/battles', [
        'game_id' => $game->id,
    ]);

    $response->assertStatus(400)
             ->assertJsonFragment([
                 'message' => 'This game is already finished.'
             ]);
});

it('prevents creating a battle while another one is ongoing', function () {

    $user = User::factory()->create(['role' => 'player']);

    $character = Character::factory()->create();

    $game = Game::factory()->create([
        'user_id' => $user->id,
        'character_id' => $character->id,
        'status' => 'active',
    ]);

    Enemy::factory()->create(['enemy_name' => 'Goblin']);

    Battle::factory()->create([
        'game_id' => $game->id,
        'character_id' => $character->id,
        'result' => 'ongoing',
    ]);

    Passport::actingAs($user);

    $response = $this->postJson('/api/v1/battles', [
        'game_id' => $game->id,
    ]);

    $response->assertStatus(400)
             ->assertJsonFragment([
                 'message' => 'You already have an ongoing battle.'
             ]);
});

it('prevents a user from creating a battle for a game they do not own', function () {

    $playerOne = User::factory()->create(['role' => 'player']);

    $playerTwo = User::factory()->create(['role' => 'player']);
    
    $game = Game::factory()->create(['user_id' => $playerTwo->id]);

    Passport::actingAs($playerOne);

    $response = $this->postJson('/api/v1/battles', [
        'game_id' => $game->id,
    ]);

    $response->assertStatus(403);
});

it('returns validation errors if the game does not exist', function () {
    
    $user = User::factory()->create(['role' => 'player']);

    Passport::actingAs($user);

    $response = $this->postJson('/api/v1/battles', [
        'game_id' => 99999, 
    ]);

    $response->assertStatus(422)
             ->assertJsonValidationErrors(['game_id']);
});

it('prevents an unauthenticated user from creating a battle', function () {

    $response = $this->postJson('/api/v1/battles', [
        'game_id' => 1,
    ]);

    $response->assertStatus(401);
});

it('creates the next battle carrying over HP/MP from the previous battle', function () {

    $user = User::factory()->create(['role' => 'player']);

    $character = Character::factory()->create([
        'max_health_points' => 120,
        'max_magic_points' => 100,
    ]);

    $game = Game::factory()->create([
        'user_id' => $user->id,
        'character_id' => $character->id,
        'status' => 'active',
    ]);

    Enemy::factory()->create(['enemy_name' => 'Goblin']);
    Enemy::factory()->create(['enemy_name' => 'Troll']);

    Battle::factory()->create([
        'game_id' => $game->id,
        'character_id' => $character->id,
        'result' => 'win',
        'character_current_hp' => 45,
        'character_current_mp' => 30,
    ]);

    Passport::actingAs($user);

    $response = $this->postJson('/api/v1/battles', [
        'game_id' => $game->id,
        'character_current_hp' => 45,
        'character_current_mp' => 30,
    ]);

    $response->assertStatus(201)
             ->assertJsonFragment(['character_current_hp' => 45])
             ->assertJsonFragment(['character_current_mp' => 30]);

    $this->assertDatabaseHas('battles', [
        'game_id' => $game->id,
        'result' => 'ongoing',
        'character_current_hp' => 45,
        'character_current_mp' => 30,
    ]);
});

it('creates the next battle at full HP/MP when no values are provided (rest)', function () {

    $user = User::factory()->create(['role' => 'player']);

    $character = Character::factory()->create([
        'max_health_points' => 120,
        'max_magic_points' => 100,
    ]);

    $game = Game::factory()->create([
        'user_id' => $user->id,
        'character_id' => $character->id,
        'status' => 'active',
    ]);

    Enemy::factory()->create(['enemy_name' => 'Goblin']);
    Enemy::factory()->create(['enemy_name' => 'Troll']);

    Battle::factory()->create([
        'game_id' => $game->id,
        'character_id' => $character->id,
        'result' => 'win',
        'character_current_hp' => 45,
        'character_current_mp' => 30,
    ]);

    Passport::actingAs($user);

    $response = $this->postJson('/api/v1/battles', [
        'game_id' => $game->id,
    ]);

    $response->assertStatus(201)
             ->assertJsonFragment(['character_current_hp' => 120])
             ->assertJsonFragment(['character_current_mp' => 100]);

    $this->assertDatabaseHas('battles', [
        'game_id' => $game->id,
        'result' => 'ongoing',
        'character_current_hp' => 120,
        'character_current_mp' => 100,
    ]);
});

it('clamps carried HP/MP to the character maximum', function () {

    $user = User::factory()->create(['role' => 'player']);

    $character = Character::factory()->create([
        'max_health_points' => 120,
        'max_magic_points' => 100,
    ]);

    $game = Game::factory()->create([
        'user_id' => $user->id,
        'character_id' => $character->id,
        'status' => 'active',
    ]);

    Enemy::factory()->create(['enemy_name' => 'Goblin']);

    Passport::actingAs($user);

    $response = $this->postJson('/api/v1/battles', [
        'game_id' => $game->id,
        'character_current_hp' => 999,
        'character_current_mp' => 999,
    ]);

    $response->assertStatus(201)
             ->assertJsonFragment(['character_current_hp' => 120])
             ->assertJsonFragment(['character_current_mp' => 100]);

    $this->assertDatabaseHas('battles', [
        'game_id' => $game->id,
        'result' => 'ongoing',
        'character_current_hp' => 120,
        'character_current_mp' => 100,
    ]);
});

it('rejects invalid carried HP/MP values', function () {

    $user = User::factory()->create(['role' => 'player']);

    $character = Character::factory()->create();

    $game = Game::factory()->create([
        'user_id' => $user->id,
        'character_id' => $character->id,
        'status' => 'active',
    ]);

    Enemy::factory()->create(['enemy_name' => 'Goblin']);

    Passport::actingAs($user);

    $this->postJson('/api/v1/battles', [
        'game_id' => $game->id,
        'character_current_hp' => -5,
        'character_current_mp' => 30,
    ])->assertStatus(422)->assertJsonValidationErrors(['character_current_hp']);

    $this->postJson('/api/v1/battles', [
        'game_id' => $game->id,
        'character_current_hp' => 45,
        'character_current_mp' => -1,
    ])->assertStatus(422)->assertJsonValidationErrors(['character_current_mp']);

    $this->postJson('/api/v1/battles', [
        'game_id' => $game->id,
        'character_current_hp' => 'full',
        'character_current_mp' => 30,
    ])->assertStatus(422)->assertJsonValidationErrors(['character_current_hp']);
});
