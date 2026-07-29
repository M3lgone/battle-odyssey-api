<?php

use App\Models\User;
use App\Models\Game;
use App\Models\Character;
use App\Models\Enemy;
use App\Models\Battle;
use App\Models\Skill;
use Laravel\Passport\Passport;

it('requires a game id and character id to create a battle', function () {

    $user = User::factory()->create(['role' => 'player']);

    Passport::actingAs($user);

    $response = $this->postJson('/api/v1/battles', []);

    $response->assertStatus(422)
             ->assertJsonValidationErrors(['game_id', 'character_id']);
});

it('starts the second battle against the troll after one victory', function () {

    $user = User::factory()->create(['role' => 'player']);

    $game = Game::factory()->create(['user_id' => $user->id]);

    $character = Character::factory()->create(['character_image_url' => 'warrior.png']);

    $characterSkill = Skill::factory()->create(['skill_name' => 'Slash']);
    $character->skills()->attach($characterSkill->id);

    Enemy::factory()->create(['enemy_name' => 'Goblin']);
    $troll = Enemy::factory()->create(['enemy_name' => 'Troll']);

    $trollSkill = Skill::factory()->create(['skill_name' => 'Smash']);
    $troll->skills()->attach($trollSkill->id);

    Battle::factory()->create([
        'game_id' => $game->id,
        'character_id' => $character->id,
        'result' => 'win', 
    ]);

    Passport::actingAs($user);

    $response = $this->postJson('/api/v1/battles', [
        'game_id' => $game->id,
        'character_id' => $character->id,
    ]);

    $response->assertStatus(201)
             ->assertJsonFragment(['enemy_name' => 'Troll'])
             ->assertJsonFragment(['skill_name' => 'Slash'])
             ->assertJsonFragment(['skill_name' => 'Smash']);
});

it('starts the final battle against the orc after two victories', function () {

    $user = User::factory()->create(['role' => 'player']);

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
        'result' => 'win',
    ]);

    Passport::actingAs($user);

    $response = $this->postJson('/api/v1/battles', [
        'game_id' => $game->id,
        'character_id' => $character->id,
    ]);

    $response->assertStatus(201)
             ->assertJsonFragment([
                 'enemy_name' => 'Orc'
             ]);
});

it('returns victory when requesting a battle after defeating the orc', function () {

    $user = User::factory()->create(['role' => 'player']);

    $game = Game::factory()->create(['user_id' => $user->id]);

    $character = Character::factory()->create([
        'character_image_url' => 'warrior.png'
    ]);

   Battle::factory()->count(3)->create([
        'game_id' => $game->id,
        'character_id' => $character->id,
        'result' => 'win',
    ]);

    Passport::actingAs($user);

    $response = $this->postJson('/api/v1/battles', [
        'game_id' => $game->id,
        'character_id' => $character->id,
    ]);

    $response->assertStatus(200)
             ->assertJsonFragment([
                 'message' => 'Victory' 
             ]);
});

it('prevents a user from creating a battle for a game they do not own', function () {

    $playerOne = User::factory()->create(['role' => 'player']);

    $playerTwo = User::factory()->create(['role' => 'player']);
    
    $game = Game::factory()->create(['user_id' => $playerTwo->id]);

    $character = Character::factory()->create();

    Passport::actingAs($playerOne);

    $response = $this->postJson('/api/v1/battles', [
        'game_id' => $game->id,
        'character_id' => $character->id,
    ]);

    $response->assertStatus(403);
});

it('returns validation errors if the game or character does not exist', function () {
    
    $user = User::factory()->create(['role' => 'player']);

    Passport::actingAs($user);

    $response = $this->postJson('/api/v1/battles', [
        'game_id' => 99999, 
        'character_id' => 99999, 
    ]);

    $response->assertStatus(422)
             ->assertJsonValidationErrors(['game_id', 'character_id']);
});

it('prevents an unauthenticated user from creating a battle', function () {

    $response = $this->postJson('/api/v1/battles', [
        'game_id' => 1,
        'character_id' => 1,
    ]);

    $response->assertStatus(401);
});